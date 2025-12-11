<?php
/**
 * Page Builder admin page
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap claude-ai-page-builder">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="claude-page-builder-container">

        <!-- Theme Analysis Section -->
        <div class="card">
            <h2>🎨 <?php esc_html_e('Your Website Design', 'claude-ai-integration'); ?></h2>
            <p><?php esc_html_e('AI analyzes your current theme colors and design patterns to create perfectly matching pages.', 'claude-ai-integration'); ?></p>

            <button type="button" class="button button-secondary claude-analyze-design" id="analyze-design">
                <?php esc_html_e('Analyze Current Design', 'claude-ai-integration'); ?>
            </button>

            <div id="design-analysis-result" style="margin-top: 15px; display: none;">
                <h3><?php esc_html_e('Design Analysis:', 'claude-ai-integration'); ?></h3>
                <div id="design-info" class="design-preview"></div>
            </div>
        </div>

        <!-- Page with Form Generator -->
        <div class="card">
            <h2>📄 <?php esc_html_e('Generate Complete Page with Form', 'claude-ai-integration'); ?></h2>
            <p><?php esc_html_e('Create a professional landing page with an integrated form, matching your website design.', 'claude-ai-integration'); ?></p>

            <form id="page-with-form-generator">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="page_purpose"><?php esc_html_e('Page Purpose', 'claude-ai-integration'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="page_purpose" name="page_purpose" class="regular-text"
                                   placeholder="<?php esc_attr_e('e.g., Contact Us, Get a Quote, Join Newsletter', 'claude-ai-integration'); ?>" required />
                            <p class="description"><?php esc_html_e('What is this page for?', 'claude-ai-integration'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="form_type"><?php esc_html_e('Form Type', 'claude-ai-integration'); ?></label>
                        </th>
                        <td>
                            <select id="form_type" name="form_type" class="regular-text">
                                <option value="contact"><?php esc_html_e('Contact Form', 'claude-ai-integration'); ?></option>
                                <option value="newsletter"><?php esc_html_e('Newsletter Signup', 'claude-ai-integration'); ?></option>
                                <option value="registration"><?php esc_html_e('Registration Form', 'claude-ai-integration'); ?></option>
                                <option value="quote"><?php esc_html_e('Quote Request', 'claude-ai-integration'); ?></option>
                                <option value="survey"><?php esc_html_e('Survey Form', 'claude-ai-integration'); ?></option>
                                <option value="feedback"><?php esc_html_e('Feedback Form', 'claude-ai-integration'); ?></option>
                            </select>
                            <p class="description"><?php esc_html_e('Type of form to include', 'claude-ai-integration'); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label><?php esc_html_e('Include Sections', 'claude-ai-integration'); ?></label>
                        </th>
                        <td>
                            <fieldset>
                                <label><input type="checkbox" name="elements[]" value="benefits" checked /> <?php esc_html_e('Benefits/Features', 'claude-ai-integration'); ?></label><br>
                                <label><input type="checkbox" name="elements[]" value="testimonials" checked /> <?php esc_html_e('Testimonials', 'claude-ai-integration'); ?></label><br>
                                <label><input type="checkbox" name="elements[]" value="faq" /> <?php esc_html_e('FAQ Section', 'claude-ai-integration'); ?></label><br>
                                <label><input type="checkbox" name="elements[]" value="trust" checked /> <?php esc_html_e('Trust Indicators', 'claude-ai-integration'); ?></label>
                            </fieldset>
                            <p class="description"><?php esc_html_e('Additional sections to include', 'claude-ai-integration'); ?></p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary button-large">
                        <span class="dashicons dashicons-admin-page"></span>
                        <?php esc_html_e('Generate Page with Form', 'claude-ai-integration'); ?>
                    </button>
                </p>
            </form>

            <div id="page-with-form-result" style="margin-top: 20px; display: none;">
                <h3><?php esc_html_e('Generated Page:', 'claude-ai-integration'); ?></h3>
                <div class="preview-controls">
                    <button type="button" class="button button-secondary" id="preview-page-btn">
                        <?php esc_html_e('👁️ Preview', 'claude-ai-integration'); ?>
                    </button>
                    <button type="button" class="button button-primary" id="create-page-btn">
                        <?php esc_html_e('✅ Create Page', 'claude-ai-integration'); ?>
                    </button>
                    <button type="button" class="button" id="copy-html-btn">
                        <?php esc_html_e('📋 Copy HTML', 'claude-ai-integration'); ?>
                    </button>
                </div>
                <textarea id="generated-page-html" class="large-text code" rows="10" readonly></textarea>
            </div>
        </div>

        <!-- Standalone Form Generator -->
        <div class="card">
            <h2>📋 <?php esc_html_e('Generate Standalone Form', 'claude-ai-integration'); ?></h2>
            <p><?php esc_html_e('Create a beautiful, responsive form that matches your theme design.', 'claude-ai-integration'); ?></p>

            <form id="standalone-form-generator">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="standalone_form_type"><?php esc_html_e('Form Type', 'claude-ai-integration'); ?></label>
                        </th>
                        <td>
                            <select id="standalone_form_type" name="form_type" class="regular-text">
                                <option value="contact"><?php esc_html_e('Contact Form', 'claude-ai-integration'); ?></option>
                                <option value="newsletter"><?php esc_html_e('Newsletter Signup', 'claude-ai-integration'); ?></option>
                                <option value="feedback"><?php esc_html_e('Feedback Form', 'claude-ai-integration'); ?></option>
                                <option value="application"><?php esc_html_e('Application Form', 'claude-ai-integration'); ?></option>
                                <option value="quote"><?php esc_html_e('Quote Request', 'claude-ai-integration'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <span class="dashicons dashicons-feedback"></span>
                        <?php esc_html_e('Generate Form', 'claude-ai-integration'); ?>
                    </button>
                </p>
            </form>

            <div id="standalone-form-result" style="margin-top: 20px; display: none;">
                <h3><?php esc_html_e('Generated Form:', 'claude-ai-integration'); ?></h3>
                <div class="preview-controls">
                    <button type="button" class="button" id="copy-form-html-btn">
                        <?php esc_html_e('📋 Copy HTML', 'claude-ai-integration'); ?>
                    </button>
                </div>
                <textarea id="generated-form-html" class="large-text code" rows="8" readonly></textarea>
                <div id="form-preview" class="html-preview" style="margin-top: 15px;"></div>
            </div>
        </div>

        <!-- Page Section Generator -->
        <div class="card">
            <h2>🧩 <?php esc_html_e('Generate Page Sections', 'claude-ai-integration'); ?></h2>
            <p><?php esc_html_e('Create individual page sections like hero, features, testimonials, etc.', 'claude-ai-integration'); ?></p>

            <form id="page-section-generator">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="section_type"><?php esc_html_e('Section Type', 'claude-ai-integration'); ?></label>
                        </th>
                        <td>
                            <select id="section_type" name="section_type" class="regular-text">
                                <option value="hero"><?php esc_html_e('Hero Section', 'claude-ai-integration'); ?></option>
                                <option value="features"><?php esc_html_e('Features (3 Columns)', 'claude-ai-integration'); ?></option>
                                <option value="testimonials"><?php esc_html_e('Testimonials', 'claude-ai-integration'); ?></option>
                                <option value="cta"><?php esc_html_e('Call-to-Action', 'claude-ai-integration'); ?></option>
                                <option value="pricing"><?php esc_html_e('Pricing Table', 'claude-ai-integration'); ?></option>
                                <option value="faq"><?php esc_html_e('FAQ (Accordion)', 'claude-ai-integration'); ?></option>
                                <option value="stats"><?php esc_html_e('Statistics', 'claude-ai-integration'); ?></option>
                                <option value="team"><?php esc_html_e('Team Members', 'claude-ai-integration'); ?></option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="section_heading"><?php esc_html_e('Heading', 'claude-ai-integration'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="section_heading" name="heading" class="regular-text"
                                   placeholder="<?php esc_attr_e('Section heading (optional)', 'claude-ai-integration'); ?>" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="section_content"><?php esc_html_e('Content/Context', 'claude-ai-integration'); ?></label>
                        </th>
                        <td>
                            <textarea id="section_content" name="content" class="large-text" rows="3"
                                      placeholder="<?php esc_attr_e('Additional context or content (optional)', 'claude-ai-integration'); ?>"></textarea>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" class="button button-primary">
                        <span class="dashicons dashicons-align-left"></span>
                        <?php esc_html_e('Generate Section', 'claude-ai-integration'); ?>
                    </button>
                </p>
            </form>

            <div id="page-section-result" style="margin-top: 20px; display: none;">
                <h3><?php esc_html_e('Generated Section:', 'claude-ai-integration'); ?></h3>
                <div class="preview-controls">
                    <button type="button" class="button" id="copy-section-html-btn">
                        <?php esc_html_e('📋 Copy HTML', 'claude-ai-integration'); ?>
                    </button>
                </div>
                <textarea id="generated-section-html" class="large-text code" rows="8" readonly></textarea>
                <div id="section-preview" class="html-preview" style="margin-top: 15px;"></div>
            </div>
        </div>

    </div>
</div>

<style>
.claude-page-builder-container {
    max-width: 1200px;
    margin: 20px 0;
}

.claude-page-builder .card {
    max-width: none;
    margin-bottom: 20px;
    padding: 20px;
}

.claude-page-builder .card h2 {
    margin-top: 0;
    font-size: 18px;
    font-weight: 600;
}

.claude-page-builder .card p.description {
    margin-top: 0;
    color: #666;
}

.claude-page-builder .form-table th {
    width: 200px;
}

.claude-page-builder .button .dashicons {
    font-size: 16px;
    width: 16px;
    height: 16px;
    margin-right: 5px;
    line-height: 1.2;
}

.design-preview {
    padding: 15px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.design-preview .color-swatch {
    display: inline-block;
    width: 40px;
    height: 40px;
    border-radius: 4px;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px #ddd;
    margin-right: 10px;
    vertical-align: middle;
}

.design-preview .color-info {
    display: inline-block;
    vertical-align: middle;
}

.preview-controls {
    margin-bottom: 10px;
}

.preview-controls .button {
    margin-right: 10px;
}

.html-preview {
    padding: 20px;
    background: #fff;
    border: 2px dashed #ddd;
    border-radius: 4px;
    overflow-x: auto;
}

.claude-ai-status {
    padding: 10px 15px;
    margin-top: 15px;
    border-radius: 4px;
    display: none;
}

.claude-ai-status.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.claude-ai-status.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.claude-ai-status.loading {
    background: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Analyze design
    $('#analyze-design').on('click', function() {
        const $button = $(this);
        const $result = $('#design-analysis-result');
        const $designInfo = $('#design-info');

        $button.prop('disabled', true).text('Analyzing...');

        $.ajax({
            url: claudeAI.ajax_url,
            method: 'POST',
            data: {
                action: 'claude_analyze_design',
                nonce: claudeAI.nonce
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    let html = '<div class="design-details">';

                    // Theme info
                    html += '<h4>Theme: ' + data.theme.name + ' (v' + data.theme.version + ')</h4>';

                    // Colors
                    html += '<h4>Color Palette:</h4><div class="color-palette">';
                    for (let colorName in data.colors) {
                        const color = data.colors[colorName];
                        html += '<div style="margin-bottom: 10px;">';
                        html += '<span class="color-swatch" style="background-color: ' + color + ';"></span>';
                        html += '<span class="color-info"><strong>' + colorName.charAt(0).toUpperCase() + colorName.slice(1) + ':</strong> ' + color + '</span>';
                        html += '</div>';
                    }
                    html += '</div></div>';

                    $designInfo.html(html);
                    $result.slideDown();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            complete: function() {
                $button.prop('disabled', false).text('Analyze Current Design');
            }
        });
    });

    // Generate page with form
    $('#page-with-form-generator').on('submit', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $submit = $form.find('button[type="submit"]');
        const $result = $('#page-with-form-result');
        const $textarea = $('#generated-page-html');

        const elements = [];
        $form.find('input[name="elements[]"]:checked').each(function() {
            elements.push($(this).val());
        });

        $submit.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span> Generating...');

        $.ajax({
            url: claudeAI.ajax_url,
            method: 'POST',
            data: {
                action: 'claude_generate_page_with_form',
                nonce: claudeAI.nonce,
                form_type: $('#form_type').val(),
                page_purpose: $('#page_purpose').val(),
                elements: elements
            },
            success: function(response) {
                if (response.success) {
                    $textarea.val(response.data.html);
                    $result.slideDown();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            complete: function() {
                $submit.prop('disabled', false).html('<span class="dashicons dashicons-admin-page"></span> Generate Page with Form');
            }
        });
    });

    // Generate standalone form
    $('#standalone-form-generator').on('submit', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $submit = $form.find('button[type="submit"]');
        const $result = $('#standalone-form-result');
        const $textarea = $('#generated-form-html');
        const $preview = $('#form-preview');

        $submit.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span> Generating...');

        $.ajax({
            url: claudeAI.ajax_url,
            method: 'POST',
            data: {
                action: 'claude_generate_form',
                nonce: claudeAI.nonce,
                form_type: $('#standalone_form_type').val()
            },
            success: function(response) {
                if (response.success) {
                    $textarea.val(response.data.html);
                    $preview.html(response.data.html);
                    $result.slideDown();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            complete: function() {
                $submit.prop('disabled', false).html('<span class="dashicons dashicons-feedback"></span> Generate Form');
            }
        });
    });

    // Generate page section
    $('#page-section-generator').on('submit', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $submit = $form.find('button[type="submit"]');
        const $result = $('#page-section-result');
        const $textarea = $('#generated-section-html');
        const $preview = $('#section-preview');

        $submit.prop('disabled', true).html('<span class="dashicons dashicons-update dashicons-spin"></span> Generating...');

        $.ajax({
            url: claudeAI.ajax_url,
            method: 'POST',
            data: {
                action: 'claude_generate_page_section',
                nonce: claudeAI.nonce,
                section_type: $('#section_type').val(),
                heading: $('#section_heading').val(),
                content: $('#section_content').val()
            },
            success: function(response) {
                if (response.success) {
                    $textarea.val(response.data.html);
                    $preview.html(response.data.html);
                    $result.slideDown();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            complete: function() {
                $submit.prop('disabled', false).html('<span class="dashicons dashicons-align-left"></span> Generate Section');
            }
        });
    });

    // Copy to clipboard functionality
    function copyToClipboard(text, button) {
        navigator.clipboard.writeText(text).then(function() {
            const originalText = button.text();
            button.text('✅ Copied!');
            setTimeout(function() {
                button.text(originalText);
            }, 2000);
        });
    }

    $('#copy-html-btn').on('click', function() {
        copyToClipboard($('#generated-page-html').val(), $(this));
    });

    $('#copy-form-html-btn').on('click', function() {
        copyToClipboard($('#generated-form-html').val(), $(this));
    });

    $('#copy-section-html-btn').on('click', function() {
        copyToClipboard($('#generated-section-html').val(), $(this));
    });

    // Preview page (opens in new tab)
    $('#preview-page-btn').on('click', function() {
        const html = $('#generated-page-html').val();
        const win = window.open('', '_blank');
        win.document.write(html);
        win.document.close();
    });

    // Create WordPress page
    $('#create-page-btn').on('click', function() {
        const html = $('#generated-page-html').val();
        const title = $('#page_purpose').val();

        // Create page via WordPress
        const $button = $(this);
        $button.prop('disabled', true).text('Creating...');

        $.ajax({
            url: claudeAI.ajax_url,
            method: 'POST',
            data: {
                action: 'wp_ajax_inline_save',
                post_title: title,
                post_content: html,
                post_type: 'page',
                post_status: 'draft'
            },
            success: function(response) {
                alert('Page created successfully! Check your Pages list.');
            },
            complete: function() {
                $button.prop('disabled', false).text('✅ Create Page');
            }
        });
    });
});
</script>
