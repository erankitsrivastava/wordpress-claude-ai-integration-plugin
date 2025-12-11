<?php
/**
 * Content Generator functionality
 */
class Claude_AI_Content_Generator {

    private $api;

    public function __construct() {
        $this->api = new Claude_AI_API();
    }

    /**
     * Add meta boxes to post and page editor
     */
    public function add_meta_boxes() {
        $post_types = array('post', 'page');

        foreach ($post_types as $post_type) {
            add_meta_box(
                'claude_ai_generator',
                __('Claude AI Assistant', 'claude-ai-integration'),
                array($this, 'render_meta_box'),
                $post_type,
                'side',
                'high'
            );
        }
    }

    /**
     * Render the meta box
     */
    public function render_meta_box($post) {
        wp_nonce_field('claude_ai_meta_box', 'claude_ai_meta_box_nonce');
        ?>
        <div class="claude-ai-meta-box">
            <h4><?php esc_html_e('Quick Actions', 'claude-ai-integration'); ?></h4>
            <p>
                <button type="button" class="button button-secondary claude-generate-title" data-post-id="<?php echo esc_attr($post->ID); ?>">
                    <?php esc_html_e('✨ Generate Title', 'claude-ai-integration'); ?>
                </button>
            </p>
            <p>
                <button type="button" class="button button-secondary claude-generate-excerpt" data-post-id="<?php echo esc_attr($post->ID); ?>">
                    <?php esc_html_e('📝 Generate Excerpt', 'claude-ai-integration'); ?>
                </button>
            </p>
            <p>
                <button type="button" class="button button-secondary claude-generate-meta-description" data-post-id="<?php echo esc_attr($post->ID); ?>">
                    <?php esc_html_e('🔍 Generate Meta Description', 'claude-ai-integration'); ?>
                </button>
            </p>
            <?php if (get_option('claude_ai_auto_images', false)): ?>
            <p>
                <button type="button" class="button button-secondary claude-add-featured-image" data-post-id="<?php echo esc_attr($post->ID); ?>">
                    <?php esc_html_e('🖼️ Add Featured Image', 'claude-ai-integration'); ?>
                </button>
            </p>
            <?php endif; ?>

            <hr>

            <h4><?php esc_html_e('Content Modification', 'claude-ai-integration'); ?></h4>
            <p>
                <label for="claude-modify-instruction">
                    <?php esc_html_e('Instruction:', 'claude-ai-integration'); ?>
                </label>
                <textarea id="claude-modify-instruction" class="widefat" rows="3" placeholder="<?php esc_attr_e('E.g., Make it more engaging, add bullet points, optimize for SEO, etc.', 'claude-ai-integration'); ?>"></textarea>
            </p>
            <p>
                <button type="button" class="button button-primary claude-modify-content" data-post-id="<?php echo esc_attr($post->ID); ?>">
                    <?php esc_html_e('✏️ Modify Content', 'claude-ai-integration'); ?>
                </button>
            </p>

            <hr>

            <h4><?php esc_html_e('Advanced Features', 'claude-ai-integration'); ?></h4>
            <p>
                <button type="button" class="button button-secondary claude-humanize-content" data-post-id="<?php echo esc_attr($post->ID); ?>">
                    <?php esc_html_e('👤 Humanize Content', 'claude-ai-integration'); ?>
                </button>
            </p>
            <p class="description"><?php esc_html_e('Rewrite content to sound more natural and less AI-generated.', 'claude-ai-integration'); ?></p>

            <p>
                <button type="button" class="button button-secondary claude-analyze-readability" data-post-id="<?php echo esc_attr($post->ID); ?>">
                    <?php esc_html_e('📊 Analyze Readability', 'claude-ai-integration'); ?>
                </button>
            </p>
            <p class="description"><?php esc_html_e('Get suggestions to improve content readability.', 'claude-ai-integration'); ?></p>

            <div class="claude-ai-status" style="margin-top: 15px; padding: 10px; background: #f0f0f1; border-radius: 4px; display: none;"></div>
        </div>
        <style>
            .claude-ai-meta-box h4 {
                margin: 10px 0 5px;
                font-size: 13px;
                font-weight: 600;
            }
            .claude-ai-meta-box .button {
                width: 100%;
                margin-bottom: 5px;
            }
        </style>
        <?php
    }

    /**
     * AJAX handler for generating content
     */
    public function ajax_generate_content() {
        check_ajax_referer('claude_ai_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'claude-ai-integration')));
        }

        $topic = sanitize_text_field($_POST['topic'] ?? '');
        $content_type = sanitize_text_field($_POST['content_type'] ?? 'post');
        $length = sanitize_text_field($_POST['length'] ?? 'medium');
        $tone = sanitize_text_field($_POST['tone'] ?? 'professional');
        $custom_prompt = isset($_POST['custom_prompt']) ? sanitize_textarea_field($_POST['custom_prompt']) : '';

        if (empty($topic) && empty($custom_prompt)) {
            wp_send_json_error(array('message' => __('Topic or custom prompt is required.', 'claude-ai-integration')));
        }

        // Build options array
        $options = array(
            'add_images' => get_option('claude_ai_auto_images', false),
            'human_style' => get_option('claude_ai_human_style', true),
            'custom_prompt' => $custom_prompt
        );

        $result = $this->api->generate_content($content_type, $topic, $length, $tone, $options);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }

        // Optionally humanize further if content seems too AI-like
        $humanize = isset($_POST['humanize']) && $_POST['humanize'] === 'true';
        if ($humanize) {
            $result = $this->api->humanize_content($result);
            if (is_wp_error($result)) {
                wp_send_json_error(array('message' => $result->get_error_message()));
            }
        }

        wp_send_json_success(array('content' => $result));
    }

    /**
     * AJAX handler for modifying content
     */
    public function ajax_modify_content() {
        check_ajax_referer('claude_ai_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'claude-ai-integration')));
        }

        $content = wp_kses_post($_POST['content'] ?? '');
        $instruction = sanitize_text_field($_POST['instruction'] ?? '');

        if (empty($content)) {
            wp_send_json_error(array('message' => __('Content is required.', 'claude-ai-integration')));
        }

        if (empty($instruction)) {
            wp_send_json_error(array('message' => __('Instruction is required.', 'claude-ai-integration')));
        }

        $result = $this->api->modify_content($content, $instruction);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }

        wp_send_json_success(array('content' => $result));
    }

    /**
     * AJAX handler for generating title
     */
    public function ajax_generate_title() {
        check_ajax_referer('claude_ai_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'claude-ai-integration')));
        }

        $content = wp_kses_post($_POST['content'] ?? '');
        $post_id = intval($_POST['post_id'] ?? 0);

        if (empty($content) && $post_id > 0) {
            $post = get_post($post_id);
            if ($post) {
                $content = $post->post_content;
            }
        }

        if (empty($content)) {
            wp_send_json_error(array('message' => __('Content is required to generate a title.', 'claude-ai-integration')));
        }

        $result = $this->api->generate_title($content);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }

        wp_send_json_success(array('title' => trim($result)));
    }

    /**
     * AJAX handler for generating excerpt
     */
    public function ajax_generate_excerpt() {
        check_ajax_referer('claude_ai_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'claude-ai-integration')));
        }

        $content = wp_kses_post($_POST['content'] ?? '');
        $post_id = intval($_POST['post_id'] ?? 0);

        if (empty($content) && $post_id > 0) {
            $post = get_post($post_id);
            if ($post) {
                $content = $post->post_content;
            }
        }

        if (empty($content)) {
            wp_send_json_error(array('message' => __('Content is required to generate an excerpt.', 'claude-ai-integration')));
        }

        $result = $this->api->generate_excerpt($content);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }

        wp_send_json_success(array('excerpt' => trim($result)));
    }

    /**
     * AJAX handler for humanizing content
     */
    public function ajax_humanize_content() {
        check_ajax_referer('claude_ai_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'claude-ai-integration')));
        }

        $content = wp_kses_post($_POST['content'] ?? '');

        if (empty($content)) {
            wp_send_json_error(array('message' => __('Content is required.', 'claude-ai-integration')));
        }

        $result = $this->api->humanize_content($content);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }

        wp_send_json_success(array('content' => $result));
    }

    /**
     * AJAX handler for generating meta description
     */
    public function ajax_generate_meta_description() {
        check_ajax_referer('claude_ai_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'claude-ai-integration')));
        }

        $content = wp_kses_post($_POST['content'] ?? '');
        $title = sanitize_text_field($_POST['title'] ?? '');

        if (empty($content)) {
            wp_send_json_error(array('message' => __('Content is required.', 'claude-ai-integration')));
        }

        $input = !empty($title) ? $title . "\n\n" . $content : $content;
        $result = $this->api->generate_meta_description($input);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }

        wp_send_json_success(array('meta_description' => trim($result)));
    }

    /**
     * AJAX handler for analyzing readability
     */
    public function ajax_analyze_readability() {
        check_ajax_referer('claude_ai_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'claude-ai-integration')));
        }

        $content = wp_kses_post($_POST['content'] ?? '');

        if (empty($content)) {
            wp_send_json_error(array('message' => __('Content is required.', 'claude-ai-integration')));
        }

        $result = $this->api->analyze_readability($content);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }

        wp_send_json_success(array('analysis' => $result));
    }

    /**
     * AJAX handler for adding featured image
     */
    public function ajax_add_featured_image() {
        check_ajax_referer('claude_ai_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'claude-ai-integration')));
        }

        $post_id = intval($_POST['post_id'] ?? 0);
        $topic = sanitize_text_field($_POST['topic'] ?? '');

        if ($post_id <= 0) {
            wp_send_json_error(array('message' => __('Invalid post ID.', 'claude-ai-integration')));
        }

        if (empty($topic)) {
            $post = get_post($post_id);
            $topic = $post ? $post->post_title : '';
        }

        if (empty($topic)) {
            wp_send_json_error(array('message' => __('Topic or post title is required.', 'claude-ai-integration')));
        }

        $result = $this->api->fetch_featured_image($topic, $post_id);

        if (!$result || is_wp_error($result)) {
            wp_send_json_error(array('message' => __('Failed to fetch image. Please check your image API keys.', 'claude-ai-integration')));
        }

        wp_send_json_success(array('message' => __('Featured image added successfully!', 'claude-ai-integration')));
    }
}
