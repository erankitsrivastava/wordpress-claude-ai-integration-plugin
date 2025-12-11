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

    public function __construct() {
        $this->api_key = get_option('claude_ai_api_key');
        $this->model = get_option('claude_ai_model', 'claude-sonnet-4-5-20250929');
        $this->max_tokens = intval(get_option('claude_ai_max_tokens', '4096'));
        $this->temperature = floatval(get_option('claude_ai_temperature', '1.0'));
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
     * Generate content for a post or page
     */
    public function generate_content($content_type, $topic, $length = 'medium', $tone = 'professional') {
        $length_guide = array(
            'short' => '300-500 words',
            'medium' => '700-1000 words',
            'long' => '1500-2000 words'
        );

        $word_count = isset($length_guide[$length]) ? $length_guide[$length] : $length_guide['medium'];

        $system_prompt = "You are a professional WordPress content writer. Generate high-quality, SEO-friendly content in HTML format suitable for WordPress {$content_type}. Use proper HTML tags like <p>, <h2>, <h3>, <ul>, <ol>, <strong>, <em>, etc. Do not include <html>, <head>, or <body> tags.";

        $prompt = "Create a {$tone} {$content_type} about: {$topic}\n\n";
        $prompt .= "Length: {$word_count}\n";
        $prompt .= "Include a compelling title as an <h1> tag at the beginning.\n";
        $prompt .= "Structure the content with proper headings, paragraphs, and formatting.\n";
        $prompt .= "Make it engaging, informative, and ready to publish.";

        return $this->send_message($prompt, $system_prompt);
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
}
