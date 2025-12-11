<?php
/**
 * Plugin Name: Claude AI Integration
 * Plugin URI: https://github.com/erankitsrivastava/haxcode-shopify-landing-page
 * Description: Integrate Claude AI to generate, create, and modify WordPress pages, posts, and content using Anthropic's Claude API.
 * Version: 1.0.0
 * Author: HaxCode
 * Author URI: https://github.com/erankitsrivastava
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: claude-ai-integration
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('CLAUDE_AI_VERSION', '1.0.0');
define('CLAUDE_AI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CLAUDE_AI_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CLAUDE_AI_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_claude_ai_integration() {
    require_once CLAUDE_AI_PLUGIN_DIR . 'includes/class-claude-ai-activator.php';
    Claude_AI_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_claude_ai_integration() {
    require_once CLAUDE_AI_PLUGIN_DIR . 'includes/class-claude-ai-deactivator.php';
    Claude_AI_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_claude_ai_integration');
register_deactivation_hook(__FILE__, 'deactivate_claude_ai_integration');

/**
 * The core plugin class
 */
require CLAUDE_AI_PLUGIN_DIR . 'includes/class-claude-ai-core.php';

/**
 * Begins execution of the plugin.
 */
function run_claude_ai_integration() {
    $plugin = new Claude_AI_Core();
    $plugin->run();
}

run_claude_ai_integration();
