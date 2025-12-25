/**
 * NerdsIQ AI Assistant - Admin JavaScript
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize color pickers
        if ($.fn.wpColorPicker) {
            $('.nerdsiq-color-picker').wpColorPicker();
        }

        // Tab switching
        $('.nerdsiq-tab').on('click', function() {
            const tabId = $(this).data('tab');

            // Update active tab
            $('.nerdsiq-tab').removeClass('active');
            $(this).addClass('active');

            // Show corresponding content
            $('.nerdsiq-tab-content').removeClass('active');
            $('#tab-' + tabId).addClass('active');
        });

        // Test AWS connection
        $('#nerdsiq-test-connection').on('click', function() {
            const $btn = $(this);
            const $status = $('#nerdsiq-connection-status');

            $btn.prop('disabled', true).text(nerdsiq_admin.strings.testing);
            $status.removeClass('success error').addClass('loading').text('Testing...');

            $.ajax({
                url: nerdsiq_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'nerdsiq_test_connection',
                    nonce: nerdsiq_admin.nonce
                },
                success: function(response) {
                    $btn.prop('disabled', false).text('Test Connection');

                    if (response.success) {
                        $status.removeClass('loading error').addClass('success').text(response.data.message);
                    } else {
                        $status.removeClass('loading success').addClass('error').text(response.data.message);
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Test Connection');
                    $status.removeClass('loading success').addClass('error').text('Connection test failed');
                }
            });
        });

        // Clear logs confirmation
        $('.nerdsiq-clear-logs').on('click', function(e) {
            if (!confirm(nerdsiq_admin.strings.confirm_clear)) {
                e.preventDefault();
            }
        });

        // Auto-save form (debounced)
        let saveTimeout;
        $('.nerdsiq-form input, .nerdsiq-form select, .nerdsiq-form textarea').on('change', function() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(function() {
                // Optional: Show saving indicator
            }, 1000);
        });
    });

})(jQuery);
