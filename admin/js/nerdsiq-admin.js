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

        // Save AWS Credentials via AJAX
        $('#nerdsiq-save-credentials').on('click', function() {
            const $btn = $(this);
            const $status = $('#nerdsiq-connection-status');
            const originalText = $btn.text();

            const accessKey = $('#nerdsiq_aws_access_key').val();
            const secretKey = $('#nerdsiq_aws_secret_key').val();
            const region = $('#nerdsiq_aws_region').val();
            const appId = $('#nerdsiq_qbusiness_app_id').val();

            $btn.prop('disabled', true).text('Saving...');
            $status.removeClass('success error').addClass('loading').text('Saving credentials...');

            $.ajax({
                url: nerdsiq_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'nerdsiq_save_aws_credentials',
                    nonce: nerdsiq_admin.nonce,
                    access_key: accessKey,
                    secret_key: secretKey,
                    region: region,
                    app_id: appId
                },
                success: function(response) {
                    $btn.prop('disabled', false).text(originalText);

                    if (response.success) {
                        $status.removeClass('loading error').addClass('success').text(response.data.message);
                        // Clear the fields to show asterisks
                        $('#nerdsiq_aws_access_key').val('********************');
                        $('#nerdsiq_aws_secret_key').val('****************************************');
                    } else {
                        $status.removeClass('loading success').addClass('error').text(response.data.message);
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text(originalText);
                    $status.removeClass('loading success').addClass('error').text('Failed to save credentials');
                }
            });
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
                timeout: 30000,
                data: {
                    action: 'nerdsiq_test_connection',
                    nonce: nerdsiq_admin.nonce
                },
                success: function(response) {
                    $btn.prop('disabled', false).text('Test Connection');

                    if (response.success) {
                        $status.removeClass('loading error').addClass('success').text(response.data.message);
                    } else {
                        $status.removeClass('loading success').addClass('error').text(response.data.message || 'Connection failed');
                    }
                },
                error: function(xhr, status, error) {
                    $btn.prop('disabled', false).text('Test Connection');
                    var errorMsg = 'Connection test failed';
                    if (status === 'timeout') {
                        errorMsg = 'Request timed out. Check your AWS credentials and network.';
                    } else if (xhr.responseText) {
                        // Try to extract error from response
                        try {
                            var resp = JSON.parse(xhr.responseText);
                            if (resp.data && resp.data.message) {
                                errorMsg = resp.data.message;
                            }
                        } catch(e) {
                            // Check for PHP errors in response
                            if (xhr.responseText.indexOf('Fatal error') !== -1) {
                                errorMsg = 'PHP Error. Check server logs.';
                            } else if (xhr.status === 0) {
                                errorMsg = 'Network error. Check your connection.';
                            }
                        }
                    }
                    $status.removeClass('loading success').addClass('error').text(errorMsg);
                    console.log('AJAX Error:', status, error, xhr.responseText);
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
