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

    /**
     * Analyze website design and extract theme information
     */
    public function analyze_website_design() {
        // Get theme information
        $theme = wp_get_theme();
        $theme_info = array(
            'name' => $theme->get('Name'),
            'version' => $theme->get('Version'),
            'description' => $theme->get('Description')
        );

        // Extract color palette from theme
        $colors = $this->extract_theme_colors();

        // Get common design elements
        $design_elements = $this->detect_design_patterns();

        return array(
            'theme' => $theme_info,
            'colors' => $colors,
            'design_elements' => $design_elements
        );
    }

    /**
     * Extract color palette from current theme
     */
    private function extract_theme_colors() {
        $colors = array(
            'primary' => get_theme_mod('primary_color', '#0073aa'),
            'secondary' => get_theme_mod('secondary_color', '#005177'),
            'accent' => get_theme_mod('accent_color', '#00a0d2'),
            'text' => get_theme_mod('text_color', '#32373c'),
            'background' => get_theme_mod('background_color', '#ffffff'),
            'link' => get_theme_mod('link_color', '#0073aa')
        );

        // Try to get colors from custom CSS if theme mods not available
        if ($colors['primary'] === '#0073aa') {
            $custom_css = wp_get_custom_css();
            if (!empty($custom_css)) {
                // Extract colors from CSS using regex
                preg_match_all('/#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b/', $custom_css, $matches);
                if (!empty($matches[0])) {
                    $extracted_colors = array_unique($matches[0]);
                    if (count($extracted_colors) > 0) {
                        $colors['primary'] = $extracted_colors[0];
                    }
                    if (count($extracted_colors) > 1) {
                        $colors['secondary'] = $extracted_colors[1];
                    }
                    if (count($extracted_colors) > 2) {
                        $colors['accent'] = $extracted_colors[2];
                    }
                }
            }
        }

        return $colors;
    }

    /**
     * Detect common design patterns from the website
     */
    private function detect_design_patterns() {
        $patterns = array(
            'has_sidebar' => is_active_sidebar('sidebar-1'),
            'menu_locations' => get_nav_menu_locations(),
            'widget_areas' => wp_get_sidebars_widgets(),
            'image_sizes' => get_intermediate_image_sizes(),
            'supports' => array(
                'custom_header' => current_theme_supports('custom-header'),
                'custom_background' => current_theme_supports('custom-background'),
                'post_thumbnails' => current_theme_supports('post-thumbnails'),
                'custom_logo' => current_theme_supports('custom-logo'),
                'title_tag' => current_theme_supports('title-tag')
            )
        );

        return $patterns;
    }

    /**
     * Generate a complete page with form using AI and website design
     */
    public function generate_page_with_form($page_config) {
        $design_info = $this->analyze_website_design();

        $form_type = isset($page_config['form_type']) ? $page_config['form_type'] : 'contact';
        $page_purpose = isset($page_config['purpose']) ? $page_config['purpose'] : 'Contact Us';
        $include_elements = isset($page_config['elements']) ? $page_config['elements'] : array();

        // Build design context
        $design_context = "Website Design Context:\n";
        $design_context .= "- Theme: {$design_info['theme']['name']}\n";
        $design_context .= "- Primary Color: {$design_info['colors']['primary']}\n";
        $design_context .= "- Secondary Color: {$design_info['colors']['secondary']}\n";
        $design_context .= "- Accent Color: {$design_info['colors']['accent']}\n";
        $design_context .= "- Text Color: {$design_info['colors']['text']}\n";
        $design_context .= "- Background Color: {$design_info['colors']['background']}\n";

        $system_prompt = "You are an expert web designer and developer. Create modern, responsive page layouts with forms that match the website's existing design.\n\n";
        $system_prompt .= "IMPORTANT Design Requirements:\n";
        $system_prompt .= "- Use the provided color palette consistently\n";
        $system_prompt .= "- Create mobile-first, responsive designs\n";
        $system_prompt .= "- Follow modern web design trends (2025 standards)\n";
        $system_prompt .= "- Use proper HTML5 semantic elements\n";
        $system_prompt .= "- Include inline CSS for styling that matches the theme\n";
        $system_prompt .= "- Create accessible forms with proper labels and ARIA attributes\n";
        $system_prompt .= "- Add form validation attributes (required, pattern, etc.)\n";
        $system_prompt .= "- Include modern UI elements (gradients, shadows, animations)\n";
        $system_prompt .= "- Return complete HTML with embedded CSS\n\n";
        $system_prompt .= $design_context;

        $prompt = $this->build_page_with_form_prompt($form_type, $page_purpose, $include_elements, $design_info['colors']);

        return $this->send_message($prompt, $system_prompt);
    }

    /**
     * Build prompt for page with form generation
     */
    private function build_page_with_form_prompt($form_type, $purpose, $elements, $colors) {
        $prompt = "Create a complete, modern landing page for: {$purpose}\n\n";

        $prompt .= "Page Requirements:\n";
        $prompt .= "1. HERO SECTION:\n";
        $prompt .= "   - Compelling headline and subheadline\n";
        $prompt .= "   - Eye-catching design with gradient background using theme colors\n";
        $prompt .= "   - Clear call-to-action\n\n";

        $prompt .= "2. FORM SECTION:\n";
        $prompt .= "   - Form Type: " . ucfirst($form_type) . " Form\n";
        $prompt .= "   - Include appropriate fields for this form type\n";
        $prompt .= "   - Modern form styling with:\n";
        $prompt .= "     * Floating labels or placeholder text\n";
        $prompt .= "     * Focus states with theme colors\n";
        $prompt .= "     * Input validation (HTML5)\n";
        $prompt .= "     * Submit button with hover effects\n";
        $prompt .= "     * Success/error message areas\n\n";

        // Form-specific fields
        switch ($form_type) {
            case 'contact':
                $prompt .= "   Form Fields:\n";
                $prompt .= "   - Full Name (required)\n";
                $prompt .= "   - Email Address (required, validated)\n";
                $prompt .= "   - Phone Number (optional)\n";
                $prompt .= "   - Subject (dropdown)\n";
                $prompt .= "   - Message (textarea, required)\n";
                break;
            case 'newsletter':
                $prompt .= "   Form Fields:\n";
                $prompt .= "   - Email Address (required, validated)\n";
                $prompt .= "   - First Name (required)\n";
                $prompt .= "   - Preferences (checkboxes for interests)\n";
                $prompt .= "   - Privacy policy agreement (required checkbox)\n";
                break;
            case 'registration':
                $prompt .= "   Form Fields:\n";
                $prompt .= "   - Full Name (required)\n";
                $prompt .= "   - Email Address (required, validated)\n";
                $prompt .= "   - Username (required)\n";
                $prompt .= "   - Password (required, with strength indicator)\n";
                $prompt .= "   - Confirm Password (required)\n";
                $prompt .= "   - Terms & Conditions (required checkbox)\n";
                break;
            case 'quote':
                $prompt .= "   Form Fields:\n";
                $prompt .= "   - Company Name (required)\n";
                $prompt .= "   - Contact Name (required)\n";
                $prompt .= "   - Email Address (required, validated)\n";
                $prompt .= "   - Phone Number (required)\n";
                $prompt .= "   - Service Interested In (dropdown)\n";
                $prompt .= "   - Budget Range (select)\n";
                $prompt .= "   - Project Description (textarea, required)\n";
                $prompt .= "   - Preferred Contact Method (radio buttons)\n";
                break;
            case 'survey':
                $prompt .= "   Form Fields:\n";
                $prompt .= "   - Email Address (optional)\n";
                $prompt .= "   - Multiple choice questions (radio buttons)\n";
                $prompt .= "   - Rating scales (1-5 stars)\n";
                $prompt .= "   - Open-ended questions (textareas)\n";
                $prompt .= "   - Checkboxes for multi-select options\n";
                break;
            default:
                $prompt .= "   - Include appropriate fields for a " . $form_type . " form\n";
        }

        $prompt .= "\n3. ADDITIONAL SECTIONS:\n";
        if (in_array('benefits', $elements) || empty($elements)) {
            $prompt .= "   - Benefits/Features section with icons\n";
        }
        if (in_array('testimonials', $elements) || empty($elements)) {
            $prompt .= "   - Testimonials section (3 testimonials)\n";
        }
        if (in_array('faq', $elements)) {
            $prompt .= "   - FAQ section (5-7 questions)\n";
        }
        if (in_array('trust', $elements) || empty($elements)) {
            $prompt .= "   - Trust indicators (security badges, guarantees)\n";
        }

        $prompt .= "\n4. DESIGN SPECIFICATIONS:\n";
        $prompt .= "   - Use these exact colors:\n";
        $prompt .= "     * Primary: {$colors['primary']}\n";
        $prompt .= "     * Secondary: {$colors['secondary']}\n";
        $prompt .= "     * Accent: {$colors['accent']}\n";
        $prompt .= "     * Text: {$colors['text']}\n";
        $prompt .= "     * Background: {$colors['background']}\n";
        $prompt .= "   - Modern CSS features:\n";
        $prompt .= "     * CSS Grid and Flexbox for layout\n";
        $prompt .= "     * Smooth transitions and hover effects\n";
        $prompt .= "     * Box shadows for depth\n";
        $prompt .= "     * Border radius for modern look\n";
        $prompt .= "     * Responsive typography (clamp() for fluid sizing)\n";
        $prompt .= "   - Mobile-responsive breakpoints:\n";
        $prompt .= "     * Desktop: 1200px+\n";
        $prompt .= "     * Tablet: 768px - 1199px\n";
        $prompt .= "     * Mobile: < 768px\n\n";

        $prompt .= "5. MODERN UI TRENDS (2025):\n";
        $prompt .= "   - Glassmorphism effects where appropriate\n";
        $prompt .= "   - Subtle animations on scroll (CSS only)\n";
        $prompt .= "   - Micro-interactions on buttons\n";
        $prompt .= "   - Card-based layouts with subtle shadows\n";
        $prompt .= "   - Ample white space for clean look\n";
        $prompt .= "   - Bold typography for headlines\n\n";

        $prompt .= "6. FORM HANDLING:\n";
        $prompt .= "   - Add form action attribute (can be empty for now)\n";
        $prompt .= "   - Include method='post'\n";
        $prompt .= "   - Add hidden field for form type\n";
        $prompt .= "   - Include nonce field placeholder\n";
        $prompt .= "   - Add data attributes for potential AJAX submission\n\n";

        $prompt .= "Return ONLY the complete HTML with embedded CSS (in <style> tags within the HTML). ";
        $prompt .= "Make it production-ready, modern, and visually stunning while matching the theme colors perfectly.";

        return $prompt;
    }

    /**
     * Generate specific form HTML
     */
    public function generate_form($form_type, $options = array()) {
        $design_info = $this->analyze_website_design();

        $system_prompt = "You are an expert form designer. Create modern, accessible forms with beautiful styling.\n\n";
        $system_prompt .= "Requirements:\n";
        $system_prompt .= "- HTML5 semantic form elements\n";
        $system_prompt .= "- Proper labels and ARIA attributes\n";
        $system_prompt .= "- Inline CSS styling that matches theme colors\n";
        $system_prompt .= "- Validation attributes (required, pattern, minlength, etc.)\n";
        $system_prompt .= "- Responsive design\n";
        $system_prompt .= "- Modern UI with focus states\n";
        $system_prompt .= "- Submit button with loading state\n\n";
        $system_prompt .= "Theme Colors:\n";
        $system_prompt .= "- Primary: {$design_info['colors']['primary']}\n";
        $system_prompt .= "- Accent: {$design_info['colors']['accent']}\n";
        $system_prompt .= "- Text: {$design_info['colors']['text']}\n";

        $prompt = "Create a modern {$form_type} form with the following specifications:\n\n";

        // Add form-specific requirements
        switch ($form_type) {
            case 'contact':
                $prompt .= "Fields: Name, Email, Phone (optional), Subject, Message\n";
                break;
            case 'newsletter':
                $prompt .= "Fields: Email, First Name, Consent checkbox\n";
                $prompt .= "Style: Inline form suitable for sidebars or footers\n";
                break;
            case 'feedback':
                $prompt .= "Fields: Name, Email, Rating (stars), Comment\n";
                break;
            case 'application':
                $prompt .= "Fields: Personal info, Education, Experience, Upload Resume\n";
                break;
        }

        $prompt .= "\nStyle it with modern design trends, use theme colors, and make it mobile-responsive.";
        $prompt .= "\nReturn complete HTML with embedded CSS in <style> tags.";

        return $this->send_message($prompt, $system_prompt);
    }

    /**
     * Generate landing page sections
     */
    public function generate_page_section($section_type, $options = array()) {
        $design_info = $this->analyze_website_design();

        $content = isset($options['content']) ? $options['content'] : '';
        $heading = isset($options['heading']) ? $options['heading'] : '';

        $system_prompt = "You are a professional web designer creating modern page sections.\n\n";
        $system_prompt .= "Design Requirements:\n";
        $system_prompt .= "- Modern, clean design\n";
        $system_prompt .= "- Match theme colors: Primary {$design_info['colors']['primary']}, Secondary {$design_info['colors']['secondary']}\n";
        $system_prompt .= "- Responsive layout\n";
        $system_prompt .= "- Use CSS Grid/Flexbox\n";
        $system_prompt .= "- Include animations and transitions\n";
        $system_prompt .= "- Return HTML with embedded CSS\n";

        $prompts = array(
            'hero' => "Create a stunning hero section with gradient background, compelling headline, subheadline, and CTA button. Heading: {$heading}",
            'features' => "Create a 3-column features section with icons, headings, and descriptions. Use card design with hover effects.",
            'testimonials' => "Create a testimonials carousel/grid with 3 testimonials, photos (use placeholder), names, and quotes. Modern card design.",
            'cta' => "Create a call-to-action section with background, heading, description, and prominent button. {$content}",
            'pricing' => "Create a 3-tier pricing table with feature comparison, highlighted popular plan, and purchase buttons.",
            'faq' => "Create an FAQ section with accordion-style questions and answers. Modern, clean design.",
            'stats' => "Create a statistics section with 4 key metrics, large numbers, icons, and descriptions.",
            'team' => "Create a team members section with photos (placeholders), names, roles, and social links. Grid layout."
        );

        $prompt = isset($prompts[$section_type]) ? $prompts[$section_type] : "Create a {$section_type} section with modern design.";
        $prompt .= "\n\nContent: {$content}\n";
        $prompt .= "Make it visually stunning with theme colors and modern 2025 design trends.";

        return $this->send_message($prompt, $system_prompt);
    }

    /**
     * Generate complete multi-page website structure
     */
    public function generate_complete_website($website_config) {
        $design_info = $this->analyze_website_design();

        $business_type = isset($website_config['business_type']) ? $website_config['business_type'] : 'business';
        $pages_needed = isset($website_config['pages']) ? $website_config['pages'] : array('home', 'about', 'services', 'contact');
        $business_name = isset($website_config['business_name']) ? $website_config['business_name'] : 'Your Business';
        $description = isset($website_config['description']) ? $website_config['description'] : '';

        $system_prompt = "You are an expert web architect creating complete website structures.\n\n";
        $system_prompt .= "Website Theme Design:\n";
        $system_prompt .= "- Primary Color: {$design_info['colors']['primary']}\n";
        $system_prompt .= "- Secondary Color: {$design_info['colors']['secondary']}\n";
        $system_prompt .= "- Use modern, professional design\n";
        $system_prompt .= "- Mobile-first responsive\n";
        $system_prompt .= "- Consistent styling across all pages\n\n";
        $system_prompt .= "Return a JSON structure with page recommendations, structure, and content outline.";

        $prompt = "Create a complete website structure for: {$business_name}\n";
        $prompt .= "Business Type: {$business_type}\n";
        $prompt .= "Description: {$description}\n";
        $prompt .= "Pages Needed: " . implode(', ', $pages_needed) . "\n\n";
        $prompt .= "For each page, provide:\n";
        $prompt .= "1. Page name and slug\n";
        $prompt .= "2. Main sections needed\n";
        $prompt .= "3. Content outline\n";
        $prompt .= "4. Recommended elements (forms, CTAs, etc.)\n";
        $prompt .= "5. SEO keywords\n\n";
        $prompt .= "Format as JSON with structure: {\"pages\": [{\"name\": \"\", \"slug\": \"\", \"sections\": [], \"content_outline\": \"\", \"elements\": [], \"seo_keywords\": []}]}";

        return $this->send_message($prompt, $system_prompt);
    }
}
