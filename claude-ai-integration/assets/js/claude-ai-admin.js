/**
 * Claude AI Integration - Admin JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        // Content Generator Form
        $('#claude-content-generator-form').on('submit', function(e) {
            e.preventDefault();

            const $form = $(this);
            const $button = $('#generate-content-btn');
            const $status = $('#claude-generation-status');
            const $preview = $('#claude-generated-content');
            const $actions = $('#claude-content-actions');

            const data = {
                action: 'claude_generate_content',
                nonce: claudeAI.nonce,
                topic: $('#topic').val(),
                content_type: $('#content-type').val(),
                length: $('#length').val(),
                tone: $('#tone').val()
            };

            // Update UI
            $button.addClass('loading').prop('disabled', true);
            $status.removeClass('success error').addClass('loading').show();
            $status.html('<span class="claude-loading"></span>' + claudeAI.strings.generating);
            $preview.html('');
            $actions.hide();

            // Make AJAX request
            $.ajax({
                url: claudeAI.ajax_url,
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        $status.removeClass('loading').addClass('success');
                        $status.text(claudeAI.strings.success);
                        $preview.html(response.data.content);
                        $actions.show();

                        // Store generated content for saving
                        $preview.data('generated-content', response.data.content);
                        $preview.data('content-type', data.content_type);
                    } else {
                        $status.removeClass('loading').addClass('error');
                        $status.text(response.data.message || claudeAI.strings.error);
                    }
                },
                error: function() {
                    $status.removeClass('loading').addClass('error');
                    $status.text(claudeAI.strings.error);
                },
                complete: function() {
                    $button.removeClass('loading').prop('disabled', false);
                }
            });
        });

        // Save as Draft Button
        $('#save-as-draft-btn').on('click', function() {
            const $preview = $('#claude-generated-content');
            const content = $preview.data('generated-content');
            const contentType = $preview.data('content-type');

            if (!content) {
                alert('No content to save');
                return;
            }

            // Extract title from content (first h1)
            const $tempDiv = $('<div>').html(content);
            const title = $tempDiv.find('h1').first().text() || 'Untitled';

            // Create new post/page
            const newPostUrl = 'post-new.php?post_type=' + contentType;
            const url = claudeAI.ajax_url.replace('/admin-ajax.php', '/' + newPostUrl);

            // Store content in sessionStorage to retrieve it on the new page
            sessionStorage.setItem('claude_generated_content', content);
            sessionStorage.setItem('claude_generated_title', title);

            // Redirect to new post page
            window.location.href = url;
        });

        // Copy to Clipboard Button
        $('#copy-content-btn').on('click', function() {
            const $preview = $('#claude-generated-content');
            const content = $preview.data('generated-content');

            if (!content) {
                alert('No content to copy');
                return;
            }

            // Copy to clipboard
            navigator.clipboard.writeText(content).then(function() {
                const $button = $('#copy-content-btn');
                const originalText = $button.text();
                $button.text('Copied!');
                setTimeout(function() {
                    $button.text(originalText);
                }, 2000);
            }).catch(function(err) {
                console.error('Failed to copy:', err);
                alert('Failed to copy to clipboard');
            });
        });

        // Check for generated content in sessionStorage (when redirected to new post)
        if (typeof wp !== 'undefined' && wp.data) {
            const savedContent = sessionStorage.getItem('claude_generated_content');
            const savedTitle = sessionStorage.getItem('claude_generated_title');

            if (savedContent) {
                // Wait for editor to be ready
                setTimeout(function() {
                    // Set title
                    if (savedTitle && wp.data.select('core/editor')) {
                        wp.data.dispatch('core/editor').editPost({ title: savedTitle });
                    }

                    // Set content
                    if (wp.data.select('core/editor')) {
                        wp.data.dispatch('core/editor').editPost({ content: savedContent });
                    }

                    // Clear sessionStorage
                    sessionStorage.removeItem('claude_generated_content');
                    sessionStorage.removeItem('claude_generated_title');
                }, 1000);
            }
        }

        // Meta Box - Generate Title
        $('.claude-generate-title').on('click', function() {
            const $button = $(this);
            const postId = $button.data('post-id');
            const $status = $('.claude-ai-status');

            let content = '';

            // Get content from WordPress editor
            if (typeof wp !== 'undefined' && wp.data && wp.data.select('core/editor')) {
                const blocks = wp.data.select('core/editor').getBlocks();
                content = blocks.map(block => block.attributes.content || '').join(' ');
            } else if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
                content = tinymce.activeEditor.getContent();
            } else {
                content = $('#content').val();
            }

            const data = {
                action: 'claude_generate_title',
                nonce: claudeAI.nonce,
                post_id: postId,
                content: content
            };

            $button.addClass('loading').prop('disabled', true);
            $status.removeClass('success error').addClass('loading').show();
            $status.text(claudeAI.strings.generating);

            $.ajax({
                url: claudeAI.ajax_url,
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        // Set title in editor
                        if (typeof wp !== 'undefined' && wp.data) {
                            wp.data.dispatch('core/editor').editPost({ title: response.data.title });
                        } else {
                            $('#title').val(response.data.title);
                        }

                        $status.removeClass('loading').addClass('success');
                        $status.text(claudeAI.strings.success);

                        setTimeout(function() {
                            $status.fadeOut();
                        }, 3000);
                    } else {
                        $status.removeClass('loading').addClass('error');
                        $status.text(response.data.message || claudeAI.strings.error);
                    }
                },
                error: function() {
                    $status.removeClass('loading').addClass('error');
                    $status.text(claudeAI.strings.error);
                },
                complete: function() {
                    $button.removeClass('loading').prop('disabled', false);
                }
            });
        });

        // Meta Box - Generate Excerpt
        $('.claude-generate-excerpt').on('click', function() {
            const $button = $(this);
            const postId = $button.data('post-id');
            const $status = $('.claude-ai-status');

            let content = '';

            // Get content from WordPress editor
            if (typeof wp !== 'undefined' && wp.data && wp.data.select('core/editor')) {
                const blocks = wp.data.select('core/editor').getBlocks();
                content = blocks.map(block => block.attributes.content || '').join(' ');
            } else if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
                content = tinymce.activeEditor.getContent();
            } else {
                content = $('#content').val();
            }

            const data = {
                action: 'claude_generate_excerpt',
                nonce: claudeAI.nonce,
                post_id: postId,
                content: content
            };

            $button.addClass('loading').prop('disabled', true);
            $status.removeClass('success error').addClass('loading').show();
            $status.text(claudeAI.strings.generating);

            $.ajax({
                url: claudeAI.ajax_url,
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        // Set excerpt in editor
                        if (typeof wp !== 'undefined' && wp.data) {
                            wp.data.dispatch('core/editor').editPost({ excerpt: response.data.excerpt });
                        } else {
                            $('#excerpt').val(response.data.excerpt);
                        }

                        $status.removeClass('loading').addClass('success');
                        $status.text(claudeAI.strings.success);

                        setTimeout(function() {
                            $status.fadeOut();
                        }, 3000);
                    } else {
                        $status.removeClass('loading').addClass('error');
                        $status.text(response.data.message || claudeAI.strings.error);
                    }
                },
                error: function() {
                    $status.removeClass('loading').addClass('error');
                    $status.text(claudeAI.strings.error);
                },
                complete: function() {
                    $button.removeClass('loading').prop('disabled', false);
                }
            });
        });

        // Meta Box - Modify Content
        $('.claude-modify-content').on('click', function() {
            const $button = $(this);
            const postId = $button.data('post-id');
            const instruction = $('#claude-modify-instruction').val();
            const $status = $('.claude-ai-status');

            if (!instruction) {
                alert('Please enter modification instructions');
                return;
            }

            let content = '';

            // Get content from WordPress editor
            if (typeof wp !== 'undefined' && wp.data && wp.data.select('core/editor')) {
                const blocks = wp.data.select('core/editor').getBlocks();
                content = blocks.map(block => block.attributes.content || '').join(' ');
            } else if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
                content = tinymce.activeEditor.getContent();
            } else {
                content = $('#content').val();
            }

            if (!content) {
                alert('No content to modify');
                return;
            }

            if (!confirm(claudeAI.strings.confirm_replace)) {
                return;
            }

            const data = {
                action: 'claude_modify_content',
                nonce: claudeAI.nonce,
                content: content,
                instruction: instruction
            };

            $button.addClass('loading').prop('disabled', true);
            $status.removeClass('success error').addClass('loading').show();
            $status.text(claudeAI.strings.generating);

            $.ajax({
                url: claudeAI.ajax_url,
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        // Set modified content in editor
                        if (typeof wp !== 'undefined' && wp.data) {
                            wp.data.dispatch('core/editor').editPost({ content: response.data.content });
                        } else if (typeof tinymce !== 'undefined' && tinymce.activeEditor) {
                            tinymce.activeEditor.setContent(response.data.content);
                        } else {
                            $('#content').val(response.data.content);
                        }

                        $status.removeClass('loading').addClass('success');
                        $status.text(claudeAI.strings.success);

                        // Clear instruction field
                        $('#claude-modify-instruction').val('');

                        setTimeout(function() {
                            $status.fadeOut();
                        }, 3000);
                    } else {
                        $status.removeClass('loading').addClass('error');
                        $status.text(response.data.message || claudeAI.strings.error);
                    }
                },
                error: function() {
                    $status.removeClass('loading').addClass('error');
                    $status.text(claudeAI.strings.error);
                },
                complete: function() {
                    $button.removeClass('loading').prop('disabled', false);
                }
            });
        });

    });

})(jQuery);
