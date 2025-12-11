<?php
/**
 * Fired during plugin deactivation
 */
class Claude_AI_Deactivator {

    /**
     * Deactivation hook
     */
    public static function deactivate() {
        // Remove custom capability
        $role = get_role('administrator');
        if ($role) {
            $role->remove_cap('manage_claude_ai');
        }

        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
