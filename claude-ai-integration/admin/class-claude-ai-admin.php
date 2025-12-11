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

        // Page Builder submenu
        add_submenu_page(
            'claude-ai-integration',
            __('Page Builder', 'claude-ai-integration'),
            __('Page Builder', 'claude-ai-integration'),
            'edit_pages',
            'claude-ai-page-builder',
            array($this, 'display_page_builder_page')
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
        // Claude API Settings
        register_setting('claude_ai_settings', 'claude_ai_api_key');
        register_setting('claude_ai_settings', 'claude_ai_model');
        register_setting('claude_ai_settings', 'claude_ai_max_tokens');
        register_setting('claude_ai_settings', 'claude_ai_temperature');

        // Image API Settings
        register_setting('claude_ai_settings', 'claude_ai_unsplash_key');
        register_setting('claude_ai_settings', 'claude_ai_pexels_key');
        register_setting('claude_ai_settings', 'claude_ai_pixabay_key');
        register_setting('claude_ai_settings', 'claude_ai_auto_images');
        register_setting('claude_ai_settings', 'claude_ai_auto_featured_image');

        // Content Options
        register_setting('claude_ai_settings', 'claude_ai_human_style');
        register_setting('claude_ai_settings', 'claude_ai_auto_seo');

        // API Configuration Section
        add_settings_section(
            'claude_ai_api_section',
            __('Claude API Configuration', 'claude-ai-integration'),
            array($this, 'api_section_callback'),
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

        // Image Integration Section
        add_settings_section(
            'claude_ai_image_section',
            __('Image Integration', 'claude-ai-integration'),
            array($this, 'image_section_callback'),
            'claude_ai_settings'
        );

        add_settings_field(
            'claude_ai_auto_images',
            __('Auto-Insert Images', 'claude-ai-integration'),
            array($this, 'auto_images_field_callback'),
            'claude_ai_settings',
            'claude_ai_image_section'
        );

        add_settings_field(
            'claude_ai_auto_featured_image',
            __('Auto Featured Image', 'claude-ai-integration'),
            array($this, 'auto_featured_image_field_callback'),
            'claude_ai_settings',
            'claude_ai_image_section'
        );

        add_settings_field(
            'claude_ai_unsplash_key',
            __('Unsplash API Key', 'claude-ai-integration'),
            array($this, 'unsplash_key_field_callback'),
            'claude_ai_settings',
            'claude_ai_image_section'
        );

        add_settings_field(
            'claude_ai_pexels_key',
            __('Pexels API Key', 'claude-ai-integration'),
            array($this, 'pexels_key_field_callback'),
            'claude_ai_settings',
            'claude_ai_image_section'
        );

        add_settings_field(
            'claude_ai_pixabay_key',
            __('Pixabay API Key', 'claude-ai-integration'),
            array($this, 'pixabay_key_field_callback'),
            'claude_ai_settings',
            'claude_ai_image_section'
        );

        // Content Options Section
        add_settings_section(
            'claude_ai_content_section',
            __('Content Generation Options', 'claude-ai-integration'),
            array($this, 'content_section_callback'),
            'claude_ai_settings'
        );

        add_settings_field(
            'claude_ai_human_style',
            __('Human-Like Writing', 'claude-ai-integration'),
            array($this, 'human_style_field_callback'),
            'claude_ai_settings',
            'claude_ai_content_section'
        );

        add_settings_field(
            'claude_ai_auto_seo',
            __('Auto SEO Optimization', 'claude-ai-integration'),
            array($this, 'auto_seo_field_callback'),
            'claude_ai_settings',
            'claude_ai_content_section'
        );
    }

    public function api_section_callback() {
        echo '<p>' . esc_html__('Configure your Anthropic Claude API settings.', 'claude-ai-integration') . '</p>';
    }

    public function image_section_callback() {
        echo '<p>' . esc_html__('Configure image integration. At least one image API key is recommended for automatic image insertion.', 'claude-ai-integration') . '</p>';
        echo '<p><small>';
        echo esc_html__('Get API keys: ', 'claude-ai-integration');
        echo '<a href="https://unsplash.com/developers" target="_blank">Unsplash</a> | ';
        echo '<a href="https://www.pexels.com/api/" target="_blank">Pexels</a> | ';
        echo '<a href="https://pixabay.com/api/docs/" target="_blank">Pixabay</a>';
        echo '</small></p>';
    }

    public function content_section_callback() {
        echo '<p>' . esc_html__('Advanced content generation options to create more natural, human-like content.', 'claude-ai-integration') . '</p>';
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

    // Image API Fields
    public function auto_images_field_callback() {
        $value = get_option('claude_ai_auto_images', false);
        echo '<label><input type="checkbox" name="claude_ai_auto_images" value="1"' . checked($value, 1, false) . ' /> ';
        echo esc_html__('Automatically insert relevant images into generated content', 'claude-ai-integration') . '</label>';
        echo '<p class="description">' . esc_html__('Requires at least one image API key configured below.', 'claude-ai-integration') . '</p>';
    }

    public function auto_featured_image_field_callback() {
        $value = get_option('claude_ai_auto_featured_image', false);
        echo '<label><input type="checkbox" name="claude_ai_auto_featured_image" value="1"' . checked($value, 1, false) . ' /> ';
        echo esc_html__('Automatically set featured image for generated posts', 'claude-ai-integration') . '</label>';
        echo '<p class="description">' . esc_html__('Downloads and sets a relevant featured image based on the topic.', 'claude-ai-integration') . '</p>';
    }

    public function unsplash_key_field_callback() {
        $value = get_option('claude_ai_unsplash_key', '');
        echo '<input type="password" name="claude_ai_unsplash_key" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Free API key from Unsplash. 50 requests/hour. ', 'claude-ai-integration');
        echo '<a href="https://unsplash.com/developers" target="_blank">' . esc_html__('Get API Key', 'claude-ai-integration') . '</a></p>';
    }

    public function pexels_key_field_callback() {
        $value = get_option('claude_ai_pexels_key', '');
        echo '<input type="password" name="claude_ai_pexels_key" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Free API key from Pexels. 200 requests/hour. ', 'claude-ai-integration');
        echo '<a href="https://www.pexels.com/api/" target="_blank">' . esc_html__('Get API Key', 'claude-ai-integration') . '</a></p>';
    }

    public function pixabay_key_field_callback() {
        $value = get_option('claude_ai_pixabay_key', '');
        echo '<input type="password" name="claude_ai_pixabay_key" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">' . esc_html__('Free API key from Pixabay. 100 requests/minute. ', 'claude-ai-integration');
        echo '<a href="https://pixabay.com/api/docs/" target="_blank">' . esc_html__('Get API Key', 'claude-ai-integration') . '</a></p>';
    }

    // Content Option Fields
    public function human_style_field_callback() {
        $value = get_option('claude_ai_human_style', true);
        echo '<label><input type="checkbox" name="claude_ai_human_style" value="1"' . checked($value, 1, false) . ' /> ';
        echo esc_html__('Enable human-like writing style', 'claude-ai-integration') . '</label>';
        echo '<p class="description">' . esc_html__('Generates content that sounds more natural and conversational, with varied sentence structure and personal touches. This helps avoid AI detection.', 'claude-ai-integration') . '</p>';
    }

    public function auto_seo_field_callback() {
        $value = get_option('claude_ai_auto_seo', false);
        echo '<label><input type="checkbox" name="claude_ai_auto_seo" value="1"' . checked($value, 1, false) . ' /> ';
        echo esc_html__('Automatically generate SEO meta descriptions', 'claude-ai-integration') . '</label>';
        echo '<p class="description">' . esc_html__('Generates optimized meta descriptions for better search engine visibility.', 'claude-ai-integration') . '</p>';
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

    /**
     * Display page builder page
     */
    public function display_page_builder_page() {
        require_once CLAUDE_AI_PLUGIN_DIR . 'admin/partials/page-builder-page.php';
    }
}
