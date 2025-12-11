<?php
/**
 * Content Generator page template
 */

// Security check
if (!current_user_can('edit_posts')) {
    wp_die(__('You do not have sufficient permissions to access this page.', 'claude-ai-integration'));
}

// Check if API key is configured
$api_key = get_option('claude_ai_api_key');
if (empty($api_key)) {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <div class="notice notice-warning">
            <p>
                <?php esc_html_e('Please configure your API key in the', 'claude-ai-integration'); ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=claude-ai-settings')); ?>">
                    <?php esc_html_e('settings', 'claude-ai-integration'); ?>
                </a>
                <?php esc_html_e('before using the content generator.', 'claude-ai-integration'); ?>
            </p>
        </div>
    </div>
    <?php
    return;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="claude-ai-generator-container">
        <div class="claude-ai-generator-form">
            <h2><?php esc_html_e('Generate New Content', 'claude-ai-integration'); ?></h2>

            <form id="claude-content-generator-form">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="content-type">
                                <?php esc_html_e('Content Type', 'claude-ai-integration'); ?>
                            </label>
                        </th>
                        <td>
                            <select id="content-type" name="content_type" class="regular-text">
                                <option value="post"><?php esc_html_e('Blog Post', 'claude-ai-integration'); ?></option>
                                <option value="page"><?php esc_html_e('Page', 'claude-ai-integration'); ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="topic">
                                <?php esc_html_e('Topic / Subject', 'claude-ai-integration'); ?> *
                            </label>
                        </th>
                        <td>
                            <input type="text" id="topic" name="topic" class="regular-text" required
                                   placeholder="<?php esc_attr_e('Enter the topic or subject for your content', 'claude-ai-integration'); ?>">
                            <p class="description">
                                <?php esc_html_e('Be specific for better results (e.g., "Benefits of meditation for stress relief")', 'claude-ai-integration'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="length">
                                <?php esc_html_e('Content Length', 'claude-ai-integration'); ?>
                            </label>
                        </th>
                        <td>
                            <select id="length" name="length" class="regular-text">
                                <option value="short"><?php esc_html_e('Short (300-500 words)', 'claude-ai-integration'); ?></option>
                                <option value="medium" selected><?php esc_html_e('Medium (700-1000 words)', 'claude-ai-integration'); ?></option>
                                <option value="long"><?php esc_html_e('Long (1500-2000 words)', 'claude-ai-integration'); ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="tone">
                                <?php esc_html_e('Tone / Style', 'claude-ai-integration'); ?>
                            </label>
                        </th>
                        <td>
                            <select id="tone" name="tone" class="regular-text">
                                <option value="professional" selected><?php esc_html_e('Professional', 'claude-ai-integration'); ?></option>
                                <option value="casual"><?php esc_html_e('Casual', 'claude-ai-integration'); ?></option>
                                <option value="friendly"><?php esc_html_e('Friendly', 'claude-ai-integration'); ?></option>
                                <option value="formal"><?php esc_html_e('Formal', 'claude-ai-integration'); ?></option>
                                <option value="enthusiastic"><?php esc_html_e('Enthusiastic', 'claude-ai-integration'); ?></option>
                                <option value="informative"><?php esc_html_e('Informative', 'claude-ai-integration'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary button-large" id="generate-content-btn">
                        <?php esc_html_e('Generate Content', 'claude-ai-integration'); ?>
                    </button>
                </p>
            </form>

            <div id="claude-generation-status" class="claude-status-message" style="display:none;"></div>
        </div>

        <div class="claude-ai-generator-preview">
            <h2><?php esc_html_e('Generated Content', 'claude-ai-integration'); ?></h2>
            <div id="claude-generated-content" class="claude-content-preview">
                <p class="description">
                    <?php esc_html_e('Your generated content will appear here. You can then save it as a draft or publish it.', 'claude-ai-integration'); ?>
                </p>
            </div>

            <div id="claude-content-actions" style="display:none;">
                <p class="submit">
                    <button type="button" class="button button-primary button-large" id="save-as-draft-btn">
                        <?php esc_html_e('Save as Draft', 'claude-ai-integration'); ?>
                    </button>
                    <button type="button" class="button button-secondary" id="copy-content-btn">
                        <?php esc_html_e('Copy to Clipboard', 'claude-ai-integration'); ?>
                    </button>
                </p>
            </div>
        </div>
    </div>
</div>
