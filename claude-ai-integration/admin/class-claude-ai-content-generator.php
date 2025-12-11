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
            <p>
                <button type="button" class="button button-secondary claude-generate-title" data-post-id="<?php echo esc_attr($post->ID); ?>">
                    <?php esc_html_e('Generate Title', 'claude-ai-integration'); ?>
                </button>
            </p>
            <p>
                <button type="button" class="button button-secondary claude-generate-excerpt" data-post-id="<?php echo esc_attr($post->ID); ?>">
                    <?php esc_html_e('Generate Excerpt', 'claude-ai-integration'); ?>
                </button>
            </p>
            <hr>
            <p>
                <label for="claude-modify-instruction">
                    <?php esc_html_e('Modify Content:', 'claude-ai-integration'); ?>
                </label>
                <textarea id="claude-modify-instruction" class="widefat" rows="3" placeholder="<?php esc_attr_e('E.g., Make it more engaging, add bullet points, etc.', 'claude-ai-integration'); ?>"></textarea>
            </p>
            <p>
                <button type="button" class="button button-primary claude-modify-content" data-post-id="<?php echo esc_attr($post->ID); ?>">
                    <?php esc_html_e('Modify Content', 'claude-ai-integration'); ?>
                </button>
            </p>
            <div class="claude-ai-status"></div>
        </div>
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

        if (empty($topic)) {
            wp_send_json_error(array('message' => __('Topic is required.', 'claude-ai-integration')));
        }

        $result = $this->api->generate_content($content_type, $topic, $length, $tone);

        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
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
}
