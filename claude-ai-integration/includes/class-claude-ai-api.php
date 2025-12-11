<?php
/**
 * Claude API Client
 */
class Claude_AI_API {

    private $api_key;
    private $model;
    private $max_tokens;
    private $temperature;
    private $api_url = 'https://api.anthropic.com/v1/messages';
    private $unsplash_api_key;
    private $pexels_api_key;

    public function __construct() {
        $this->api_key = get_option('claude_ai_api_key');
        $this->model = get_option('claude_ai_model', 'claude-sonnet-4-5-20250929');
        $this->max_tokens = intval(get_option('claude_ai_max_tokens', '4096'));
        $this->temperature = floatval(get_option('claude_ai_temperature', '1.0'));
        $this->unsplash_api_key = get_option('claude_ai_unsplash_key', '');
        $this->pexels_api_key = get_option('claude_ai_pexels_key', '');
    }

    /**
     * Send a message to Claude API
     */
    public function send_message($prompt, $system_prompt = '') {
        if (empty($this->api_key)) {
            return new WP_Error('no_api_key', __('Claude API key is not configured.', 'claude-ai-integration'));
        }

        $messages = array(
            array(
                'role' => 'user',
                'content' => $prompt
            )
        );

        $body = array(
            'model' => $this->model,
            'max_tokens' => $this->max_tokens,
            'temperature' => $this->temperature,
            'messages' => $messages
        );

        if (!empty($system_prompt)) {
            $body['system'] = $system_prompt;
        }

        $args = array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'x-api-key' => $this->api_key,
                'anthropic-version' => '2023-06-01'
            ),
            'body' => wp_json_encode($body),
            'timeout' => 120,
            'method' => 'POST'
        );

        $response = wp_remote_post($this->api_url, $args);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['error'])) {
            return new WP_Error('api_error', $data['error']['message']);
        }

        if (isset($data['content'][0]['text'])) {
            return $data['content'][0]['text'];
        }

        return new WP_Error('invalid_response', __('Invalid response from Claude API.', 'claude-ai-integration'));
    }

    /**
     * Generate content for a post or page with human-like writing
     */
    public function generate_content($content_type, $topic, $length = 'medium', $tone = 'professional', $options = array()) {
        $length_guide = array(
            'short' => '300-500 words',
            'medium' => '700-1000 words',
            'long' => '1500-2000 words'
        );

        $word_count = isset($length_guide[$length]) ? $length_guide[$length] : $length_guide['medium'];

        // Enhanced options
        $add_images = isset($options['add_images']) ? $options['add_images'] : get_option('claude_ai_auto_images', false);
        $human_style = isset($options['human_style']) ? $options['human_style'] : get_option('claude_ai_human_style', true);
        $custom_prompt = isset($options['custom_prompt']) ? $options['custom_prompt'] : '';

        // Human-like writing system prompt
        $system_prompt = "You are an experienced human content writer, not an AI. Write naturally with a personal touch.\n\n";

        if ($human_style) {
            $system_prompt .= "IMPORTANT - Write like a real human:\n";
            $system_prompt .= "- Use natural, conversational language with slight imperfections\n";
            $system_prompt .= "- Vary sentence structure (mix short and long sentences)\n";
            $system_prompt .= "- Include personal observations, experiences, or opinions where appropriate\n";
            $system_prompt .= "- Use transitional phrases naturally (honestly, in my experience, to be fair, etc.)\n";
            $system_prompt .= "- Add subtle personality and voice\n";
            $system_prompt .= "- Avoid overly perfect, robotic patterns\n";
            $system_prompt .= "- Use contractions naturally (don't, we'll, it's)\n";
            $system_prompt .= "- Include rhetorical questions occasionally\n";
            $system_prompt .= "- Add examples from real-world scenarios\n";
            $system_prompt .= "- Vary paragraph lengths for natural flow\n\n";
        }

        $system_prompt .= "Generate high-quality, SEO-friendly content in HTML format suitable for WordPress {$content_type}.\n";
        $system_prompt .= "Use proper HTML tags like <p>, <h2>, <h3>, <ul>, <ol>, <strong>, <em>, <blockquote>, etc.\n";
        $system_prompt .= "Do not include <html>, <head>, or <body> tags.\n";

        if ($add_images) {
            $system_prompt .= "Include image placeholders with format: [IMAGE: descriptive keyword] at relevant positions in the content.";
        }

        // Enhanced prompt
        $prompt = !empty($custom_prompt) ? $custom_prompt : $this->build_content_prompt($topic, $content_type, $tone, $word_count, $add_images);

        $content = $this->send_message($prompt, $system_prompt);

        if (is_wp_error($content)) {
            return $content;
        }

        // Process images if enabled
        if ($add_images && !is_wp_error($content)) {
            $content = $this->process_image_placeholders($content, $topic);
        }

        return $content;
    }

    /**
     * Build detailed content generation prompt
     */
    private function build_content_prompt($topic, $content_type, $tone, $word_count, $add_images) {
        $prompt = "Write a {$tone} {$content_type} about: {$topic}\n\n";
        $prompt .= "Requirements:\n";
        $prompt .= "- Length: {$word_count}\n";
        $prompt .= "- Start with a compelling <h1> title\n";
        $prompt .= "- Include an engaging introduction that hooks the reader\n";
        $prompt .= "- Use descriptive subheadings (<h2>, <h3>) to organize content\n";
        $prompt .= "- Add specific examples, statistics, or case studies where relevant\n";
        $prompt .= "- Include actionable takeaways or tips\n";
        $prompt .= "- End with a strong conclusion or call-to-action\n";
        $prompt .= "- Make it conversational and engaging, not robotic\n";
        $prompt .= "- Use active voice primarily\n";
        $prompt .= "- Add bullet points or numbered lists where appropriate\n";

        if ($add_images) {
            $prompt .= "- Insert [IMAGE: keyword] placeholders at 2-3 strategic points\n";
        }

        $prompt .= "\nWrite naturally as if you're sharing knowledge with a friend or colleague. Be informative but personable.";

        return $prompt;
    }

    /**
     * Process image placeholders and fetch actual images
     */
    private function process_image_placeholders($content, $topic) {
        // Find all image placeholders
        preg_match_all('/\[IMAGE:\s*([^\]]+)\]/', $content, $matches);

        if (empty($matches[0])) {
            return $content;
        }

        foreach ($matches[0] as $index => $placeholder) {
            $keyword = trim($matches[1][$index]);
            $image_html = $this->fetch_and_insert_image($keyword, $topic);

            if ($image_html) {
                $content = str_replace($placeholder, $image_html, $content);
            } else {
                // Remove placeholder if image fetch fails
                $content = str_replace($placeholder, '', $content);
            }
        }

        return $content;
    }

    /**
     * Fetch image from Unsplash or Pexels and create HTML
     */
    private function fetch_and_insert_image($keyword, $fallback_topic) {
        $image_url = null;
        $image_alt = $keyword;
        $image_credit = '';

        // Try Unsplash first
        if (!empty($this->unsplash_api_key)) {
            $unsplash_data = $this->fetch_unsplash_image($keyword);
            if ($unsplash_data) {
                $image_url = $unsplash_data['url'];
                $image_alt = $unsplash_data['alt'];
                $image_credit = $unsplash_data['credit'];
            }
        }

        // Fallback to Pexels
        if (!$image_url && !empty($this->pexels_api_key)) {
            $pexels_data = $this->fetch_pexels_image($keyword);
            if ($pexels_data) {
                $image_url = $pexels_data['url'];
                $image_alt = $pexels_data['alt'];
                $image_credit = $pexels_data['credit'];
            }
        }

        // Use Pixabay (no API key required)
        if (!$image_url) {
            $pixabay_data = $this->fetch_pixabay_image($keyword);
            if ($pixabay_data) {
                $image_url = $pixabay_data['url'];
                $image_alt = $pixabay_data['alt'];
                $image_credit = $pixabay_data['credit'];
            }
        }

        if (!$image_url) {
            return null;
        }

        // Create HTML with proper formatting
        $html = '<figure class="wp-block-image size-large">';
        $html .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt) . '" loading="lazy" />';
        if ($image_credit) {
            $html .= '<figcaption>' . esc_html($image_credit) . '</figcaption>';
        }
        $html .= '</figure>';

        return $html;
    }

    /**
     * Fetch image from Unsplash
     */
    private function fetch_unsplash_image($keyword) {
        $url = 'https://api.unsplash.com/photos/random?query=' . urlencode($keyword) . '&orientation=landscape';

        $response = wp_remote_get($url, array(
            'headers' => array(
                'Authorization' => 'Client-ID ' . $this->unsplash_api_key
            ),
            'timeout' => 15
        ));

        if (is_wp_error($response)) {
            return null;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        if (!isset($data['urls']['regular'])) {
            return null;
        }

        return array(
            'url' => $data['urls']['regular'],
            'alt' => isset($data['alt_description']) ? $data['alt_description'] : $keyword,
            'credit' => 'Photo by ' . $data['user']['name'] . ' on Unsplash'
        );
    }

    /**
     * Fetch image from Pexels
     */
    private function fetch_pexels_image($keyword) {
        $url = 'https://api.pexels.com/v1/search?query=' . urlencode($keyword) . '&per_page=1&orientation=landscape';

        $response = wp_remote_get($url, array(
            'headers' => array(
                'Authorization' => $this->pexels_api_key
            ),
            'timeout' => 15
        ));

        if (is_wp_error($response)) {
            return null;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        if (!isset($data['photos'][0]['src']['large'])) {
            return null;
        }

        $photo = $data['photos'][0];

        return array(
            'url' => $photo['src']['large'],
            'alt' => isset($photo['alt']) ? $photo['alt'] : $keyword,
            'credit' => 'Photo by ' . $photo['photographer'] . ' on Pexels'
        );
    }

    /**
     * Fetch image from Pixabay (free, no API key required for basic usage)
     */
    private function fetch_pixabay_image($keyword) {
        // Pixabay allows anonymous API access with rate limits
        $pixabay_key = get_option('claude_ai_pixabay_key', '');

        if (empty($pixabay_key)) {
            return null; // Require API key for reliability
        }

        $url = 'https://pixabay.com/api/?key=' . $pixabay_key . '&q=' . urlencode($keyword) . '&image_type=photo&orientation=horizontal&per_page=3';

        $response = wp_remote_get($url, array('timeout' => 15));

        if (is_wp_error($response)) {
            return null;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);

        if (!isset($data['hits'][0]['largeImageURL'])) {
            return null;
        }

        $image = $data['hits'][0];

        return array(
            'url' => $image['largeImageURL'],
            'alt' => $keyword,
            'credit' => 'Image from Pixabay'
        );
    }

    /**
     * Modify existing content
     */
    public function modify_content($content, $instruction) {
        $system_prompt = "You are a professional content editor. Modify the provided content according to the user's instructions while maintaining HTML formatting and structure. Return only the modified content in HTML format without any explanations.";

        $prompt = "Current content:\n{$content}\n\n";
        $prompt .= "Modification instruction: {$instruction}\n\n";
        $prompt .= "Please modify the content accordingly and return the complete modified version.";

        return $this->send_message($prompt, $system_prompt);
    }

    /**
     * Generate a title
     */
    public function generate_title($topic_or_content) {
        $system_prompt = "You are a professional content writer. Generate compelling, SEO-friendly titles. Return only the title text without any additional formatting or explanation.";

        $prompt = "Generate a catchy, SEO-friendly title for: {$topic_or_content}";

        return $this->send_message($prompt, $system_prompt);
    }

    /**
     * Generate an excerpt
     */
    public function generate_excerpt($content) {
        $system_prompt = "You are a professional content writer. Create concise, engaging excerpts. Return only the excerpt text without any HTML tags or additional formatting.";

        $prompt = "Create a brief excerpt (2-3 sentences) for the following content:\n\n{$content}";

        return $this->send_message($prompt, $system_prompt);
    }

    /**
     * Generate SEO meta description
     */
    public function generate_meta_description($content_or_topic, $max_length = 155) {
        $system_prompt = "You are an SEO specialist. Create compelling meta descriptions that encourage clicks while accurately describing the content. Return only the meta description text, no additional formatting.";

        $prompt = "Create an SEO-optimized meta description (max {$max_length} characters) for:\n\n{$content_or_topic}\n\nMake it compelling and include relevant keywords naturally.";

        return $this->send_message($prompt, $system_prompt);
    }

    /**
     * Generate focus keyphrase suggestions
     */
    public function generate_keyphrases($topic, $count = 5) {
        $system_prompt = "You are an SEO specialist. Generate relevant focus keyphrases for SEO optimization. Return only a comma-separated list of keyphrases.";

        $prompt = "Generate {$count} relevant SEO focus keyphrases for a post about: {$topic}\n\nReturn only the keyphrases as a comma-separated list.";

        $result = $this->send_message($prompt, $system_prompt);

        if (is_wp_error($result)) {
            return $result;
        }

        // Parse comma-separated list
        return array_map('trim', explode(',', $result));
    }

    /**
     * Generate content variations to avoid AI detection
     */
    public function humanize_content($content) {
        $system_prompt = "You are an expert editor who makes AI-generated content sound more human and natural.\n\n";
        $system_prompt .= "Your task:\n";
        $system_prompt .= "- Rewrite to sound more conversational and personal\n";
        $system_prompt .= "- Add natural imperfections and variations\n";
        $system_prompt .= "- Include transitional phrases and casual expressions\n";
        $system_prompt .= "- Vary sentence structure and length dramatically\n";
        $system_prompt .= "- Add personality and voice\n";
        $system_prompt .= "- Use contractions naturally\n";
        $system_prompt .= "- Maintain the same HTML structure and formatting\n";
        $system_prompt .= "- Keep all headings, links, and images intact\n";

        $prompt = "Rewrite this content to sound more human and natural while keeping the same structure:\n\n{$content}";

        return $this->send_message($prompt, $system_prompt);
    }

    /**
     * Generate internal linking suggestions
     */
    public function suggest_internal_links($content, $existing_posts = array()) {
        if (empty($existing_posts)) {
            return array();
        }

        $posts_list = '';
        foreach ($existing_posts as $post) {
            $posts_list .= "- {$post['title']} (ID: {$post['id']}, URL: {$post['url']})\n";
        }

        $system_prompt = "You are an SEO specialist. Suggest relevant internal links to improve SEO and user experience. Return suggestions in JSON format.";

        $prompt = "Given this content and list of existing posts, suggest 3-5 places where internal links would be valuable.\n\n";
        $prompt .= "Content:\n{$content}\n\n";
        $prompt .= "Existing Posts:\n{$posts_list}\n\n";
        $prompt .= "Return JSON array with format: [{\"anchor_text\": \"text to link\", \"post_id\": 123, \"reason\": \"why this link is relevant\"}]";

        $result = $this->send_message($prompt, $system_prompt);

        if (is_wp_error($result)) {
            return array();
        }

        $suggestions = json_decode($result, true);
        return is_array($suggestions) ? $suggestions : array();
    }

    /**
     * Fetch and set featured image
     */
    public function fetch_featured_image($topic, $post_id = 0) {
        $keyword = $topic;

        // Try different image sources
        $image_data = null;

        if (!empty($this->unsplash_api_key)) {
            $image_data = $this->fetch_unsplash_image($keyword);
        }

        if (!$image_data && !empty($this->pexels_api_key)) {
            $image_data = $this->fetch_pexels_image($keyword);
        }

        if (!$image_data) {
            $image_data = $this->fetch_pixabay_image($keyword);
        }

        if (!$image_data || !isset($image_data['url'])) {
            return false;
        }

        // Download image and set as featured image
        if ($post_id > 0) {
            return $this->set_post_featured_image($image_data['url'], $post_id, $image_data['alt']);
        }

        return $image_data;
    }

    /**
     * Download and attach image to post
     */
    private function set_post_featured_image($image_url, $post_id, $image_alt = '') {
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // Download image to temp file
        $temp_file = download_url($image_url);

        if (is_wp_error($temp_file)) {
            return false;
        }

        // Prepare file array
        $file_array = array(
            'name' => basename($image_url),
            'tmp_name' => $temp_file
        );

        // Upload to media library
        $attachment_id = media_handle_sideload($file_array, $post_id, $image_alt);

        // Delete temp file
        @unlink($temp_file);

        if (is_wp_error($attachment_id)) {
            return false;
        }

        // Set as featured image
        set_post_thumbnail($post_id, $attachment_id);

        // Update alt text
        if (!empty($image_alt)) {
            update_post_meta($attachment_id, '_wp_attachment_image_alt', sanitize_text_field($image_alt));
        }

        return $attachment_id;
    }

    /**
     * Generate content outline before writing
     */
    public function generate_outline($topic, $content_type = 'post') {
        $system_prompt = "You are a professional content strategist. Create detailed content outlines that organize ideas logically.";

        $prompt = "Create a detailed outline for a {$content_type} about: {$topic}\n\n";
        $prompt .= "Include:\n";
        $prompt .= "- Main title\n";
        $prompt .= "- Introduction hook\n";
        $prompt .= "- 4-6 main sections with subpoints\n";
        $prompt .= "- Conclusion key points\n";
        $prompt .= "- Potential call-to-action\n\n";
        $prompt .= "Format with headings and bullet points for clarity.";

        return $this->send_message($prompt, $system_prompt);
    }

    /**
     * Analyze content readability and suggest improvements
     */
    public function analyze_readability($content) {
        $system_prompt = "You are a content editor focused on readability and clarity. Analyze content and provide specific improvement suggestions.";

        $prompt = "Analyze this content for readability and suggest specific improvements:\n\n{$content}\n\n";
        $prompt .= "Provide:\n";
        $prompt .= "1. Overall readability score (estimate)\n";
        $prompt .= "2. Specific issues found\n";
        $prompt .= "3. 3-5 actionable suggestions for improvement\n";
        $prompt .= "4. Difficult sentences that should be simplified (if any)";

        return $this->send_message($prompt, $system_prompt);
    }
}
