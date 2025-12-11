<?php
/**
 * The admin-specific functionality of the plugin
 */
class Claude_AI_Admin {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            CLAUDE_AI_PLUGIN_URL . 'assets/css/claude-ai-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            CLAUDE_AI_PLUGIN_URL . 'assets/js/claude-ai-admin.js',
            array('jquery'),
            $this->version,
            false
        );

        // Localize script
        wp_localize_script(
            $this->plugin_name,
            'claudeAI',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('claude_ai_nonce'),
                'strings' => array(
                    'generating' => __('Generating...', 'claude-ai-integration'),
                    'success' => __('Content generated successfully!', 'claude-ai-integration'),
                    'error' => __('An error occurred. Please try again.', 'claude-ai-integration'),
                    'confirm_replace' => __('This will replace the current content. Continue?', 'claude-ai-integration')
                )
            )
        );
    }

    /**
     * Add admin menu items
     */
    public function add_admin_menu() {
        // Main menu page
        add_menu_page(
            __('Claude AI', 'claude-ai-integration'),
            __('Claude AI', 'claude-ai-integration'),
            'manage_options',
            'claude-ai-integration',
            array($this, 'display_content_generator_page'),
            'dashicons-robot',
            30
        );

        // Content Generator submenu
        add_submenu_page(
            'claude-ai-integration',
            __('Content Generator', 'claude-ai-integration'),
            __('Content Generator', 'claude-ai-integration'),
            'manage_options',
            'claude-ai-integration',
            array($this, 'display_content_generator_page')
        );

        // Settings submenu
        add_submenu_page(
            'claude-ai-integration',
            __('Settings', 'claude-ai-integration'),
            __('Settings', 'claude-ai-integration'),
            'manage_options',
            'claude-ai-settings',
            array($this, 'display_settings_page')
        );
    }

    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting('claude_ai_settings', 'claude_ai_api_key');
        register_setting('claude_ai_settings', 'claude_ai_model');
        register_setting('claude_ai_settings', 'claude_ai_max_tokens');
        register_setting('claude_ai_settings', 'claude_ai_temperature');

        add_settings_section(
            'claude_ai_api_section',
            __('API Configuration', 'claude-ai-integration'),
            array($this, 'settings_section_callback'),
            'claude_ai_settings'
        );

        add_settings_field(
            'claude_ai_api_key',
            __('API Key', 'claude-ai-integration'),
            array($this, 'api_key_field_callback'),
            'claude_ai_settings',
            'claude_ai_api_section'
        );

        add_settings_field(
            'claude_ai_model',
            __('Model', 'claude-ai-integration'),
            array($this, 'model_field_callback'),
            'claude_ai_settings',
            'claude_ai_api_section'
        );

        add_settings_field(
            'claude_ai_max_tokens',
            __('Max Tokens', 'claude-ai-integration'),
            array($this, 'max_tokens_field_callback'),
            'claude_ai_settings',
            'claude_ai_api_section'
        );

        add_settings_field(
            'claude_ai_temperature',
            __('Temperature', 'claude-ai-integration'),
            array($this, 'temperature_field_callback'),
            'claude_ai_settings',
            'claude_ai_api_section'
        );
    }

    public function settings_section_callback() {
        echo '<p>' . esc_html__('Configure your Claude AI API settings below.', 'claude-ai-integration') . '</p>';
    }

    public function api_key_field_callback() {
        $value = get_option('claude_ai_api_key', '');
        echo '<input type="password" name="claude_ai_api_key" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Enter your Anthropic API key. Get one at https://console.anthropic.com/', 'claude-ai-integration') . '</p>';
    }

    public function model_field_callback() {
        $value = get_option('claude_ai_model', 'claude-sonnet-4-5-20250929');
        $models = array(
            'claude-sonnet-4-5-20250929' => 'Claude Sonnet 4.5 (Latest)',
            'claude-3-7-sonnet-20250219' => 'Claude 3.7 Sonnet',
            'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet',
            'claude-3-opus-20240229' => 'Claude 3 Opus',
            'claude-3-haiku-20240307' => 'Claude 3 Haiku'
        );

        echo '<select name="claude_ai_model">';
        foreach ($models as $model_id => $model_name) {
            echo '<option value="' . esc_attr($model_id) . '"' . selected($value, $model_id, false) . '>' . esc_html($model_name) . '</option>';
        }
        echo '</select>';
    }

    public function max_tokens_field_callback() {
        $value = get_option('claude_ai_max_tokens', '4096');
        echo '<input type="number" name="claude_ai_max_tokens" value="' . esc_attr($value) . '" class="small-text" min="256" max="8192" />';
        echo '<p class="description">' . esc_html__('Maximum tokens to generate (256-8192)', 'claude-ai-integration') . '</p>';
    }

    public function temperature_field_callback() {
        $value = get_option('claude_ai_temperature', '1.0');
        echo '<input type="number" name="claude_ai_temperature" value="' . esc_attr($value) . '" class="small-text" min="0" max="2" step="0.1" />';
        echo '<p class="description">' . esc_html__('Temperature for content generation (0-2). Higher = more creative.', 'claude-ai-integration') . '</p>';
    }

    /**
     * Display settings page
     */
    public function display_settings_page() {
        require_once CLAUDE_AI_PLUGIN_DIR . 'admin/partials/settings-page.php';
    }

    /**
     * Display content generator page
     */
    public function display_content_generator_page() {
        require_once CLAUDE_AI_PLUGIN_DIR . 'admin/partials/content-generator-page.php';
    }
}
