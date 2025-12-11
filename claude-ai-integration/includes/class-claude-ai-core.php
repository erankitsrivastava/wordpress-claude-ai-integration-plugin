<?php
/**
 * The core plugin class
 */
class Claude_AI_Core {

    protected $loader;
    protected $plugin_name;
    protected $version;

    public function __construct() {
        $this->version = CLAUDE_AI_VERSION;
        $this->plugin_name = 'claude-ai-integration';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies
     */
    private function load_dependencies() {
        require_once CLAUDE_AI_PLUGIN_DIR . 'includes/class-claude-ai-api.php';
        require_once CLAUDE_AI_PLUGIN_DIR . 'admin/class-claude-ai-admin.php';
        require_once CLAUDE_AI_PLUGIN_DIR . 'admin/class-claude-ai-content-generator.php';
    }

    /**
     * Register all hooks related to admin area
     */
    private function define_admin_hooks() {
        $admin = new Claude_AI_Admin($this->get_plugin_name(), $this->get_version());
        $content_generator = new Claude_AI_Content_Generator();

        // Admin menu and settings
        add_action('admin_menu', array($admin, 'add_admin_menu'));
        add_action('admin_init', array($admin, 'register_settings'));

        // Enqueue scripts and styles
        add_action('admin_enqueue_scripts', array($admin, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($admin, 'enqueue_scripts'));

        // Meta boxes
        add_action('add_meta_boxes', array($content_generator, 'add_meta_boxes'));

        // AJAX handlers
        add_action('wp_ajax_claude_generate_content', array($content_generator, 'ajax_generate_content'));
        add_action('wp_ajax_claude_modify_content', array($content_generator, 'ajax_modify_content'));
        add_action('wp_ajax_claude_generate_title', array($content_generator, 'ajax_generate_title'));
        add_action('wp_ajax_claude_generate_excerpt', array($content_generator, 'ajax_generate_excerpt'));
        add_action('wp_ajax_claude_humanize_content', array($content_generator, 'ajax_humanize_content'));
        add_action('wp_ajax_claude_generate_meta_description', array($content_generator, 'ajax_generate_meta_description'));
        add_action('wp_ajax_claude_analyze_readability', array($content_generator, 'ajax_analyze_readability'));
        add_action('wp_ajax_claude_add_featured_image', array($content_generator, 'ajax_add_featured_image'));
        add_action('wp_ajax_claude_generate_page_with_form', array($content_generator, 'ajax_generate_page_with_form'));
        add_action('wp_ajax_claude_generate_form', array($content_generator, 'ajax_generate_form'));
        add_action('wp_ajax_claude_generate_page_section', array($content_generator, 'ajax_generate_page_section'));
        add_action('wp_ajax_claude_analyze_design', array($content_generator, 'ajax_analyze_design'));
    }

    /**
     * Register all hooks related to public-facing functionality
     */
    private function define_public_hooks() {
        // No public hooks needed for now
    }

    /**
     * Run the loader to execute all hooks
     */
    public function run() {
        // Plugin is now running
    }

    /**
     * The name of the plugin
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Retrieve the version number
     */
    public function get_version() {
        return $this->version;
    }
}
