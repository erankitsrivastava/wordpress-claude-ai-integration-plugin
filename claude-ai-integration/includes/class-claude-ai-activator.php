<?php
/**
 * Fired during plugin activation
 */
class Claude_AI_Activator {

    /**
     * Activation hook
     */
    public static function activate() {
        // Set default options
        if (!get_option('claude_ai_api_key')) {
            add_option('claude_ai_api_key', '');
        }

        if (!get_option('claude_ai_model')) {
            add_option('claude_ai_model', 'claude-sonnet-4-5-20250929');
        }

        if (!get_option('claude_ai_max_tokens')) {
            add_option('claude_ai_max_tokens', '4096');
        }

        if (!get_option('claude_ai_temperature')) {
            add_option('claude_ai_temperature', '1.0');
        }

        // Create custom capability
        $role = get_role('administrator');
        if ($role) {
            $role->add_cap('manage_claude_ai');
        }

        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
