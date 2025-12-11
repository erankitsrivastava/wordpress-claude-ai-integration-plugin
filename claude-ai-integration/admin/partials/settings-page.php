<?php
/**
 * Settings page template
 */

// Security check
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.', 'claude-ai-integration'));
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="claude-ai-settings-container">
        <div class="claude-ai-settings-main">
            <form method="post" action="options.php">
                <?php
                settings_fields('claude_ai_settings');
                do_settings_sections('claude_ai_settings');
                submit_button();
                ?>
            </form>
        </div>

        <div class="claude-ai-settings-sidebar">
            <div class="claude-ai-info-box">
                <h3><?php esc_html_e('Getting Started', 'claude-ai-integration'); ?></h3>
                <ol>
                    <li><?php esc_html_e('Get your API key from', 'claude-ai-integration'); ?> <a href="https://console.anthropic.com/" target="_blank">Anthropic Console</a></li>
                    <li><?php esc_html_e('Enter your API key in the settings', 'claude-ai-integration'); ?></li>
                    <li><?php esc_html_e('Configure your preferred model and parameters', 'claude-ai-integration'); ?></li>
                    <li><?php esc_html_e('Start generating content!', 'claude-ai-integration'); ?></li>
                </ol>
            </div>

            <div class="claude-ai-info-box">
                <h3><?php esc_html_e('Features', 'claude-ai-integration'); ?></h3>
                <ul>
                    <li>✓ <?php esc_html_e('Generate complete posts and pages', 'claude-ai-integration'); ?></li>
                    <li>✓ <?php esc_html_e('Modify existing content', 'claude-ai-integration'); ?></li>
                    <li>✓ <?php esc_html_e('Generate titles and excerpts', 'claude-ai-integration'); ?></li>
                    <li>✓ <?php esc_html_e('SEO-friendly content', 'claude-ai-integration'); ?></li>
                    <li>✓ <?php esc_html_e('Multiple content tones', 'claude-ai-integration'); ?></li>
                    <li>✓ <?php esc_html_e('Flexible content length', 'claude-ai-integration'); ?></li>
                </ul>
            </div>

            <div class="claude-ai-info-box">
                <h3><?php esc_html_e('Need Help?', 'claude-ai-integration'); ?></h3>
                <p>
                    <a href="https://github.com/erankitsrivastava/haxcode-shopify-landing-page" target="_blank">
                        <?php esc_html_e('Documentation & Support', 'claude-ai-integration'); ?>
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
