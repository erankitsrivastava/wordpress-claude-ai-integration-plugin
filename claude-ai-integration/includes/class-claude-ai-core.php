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
