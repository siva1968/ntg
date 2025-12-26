<?php
/**
 * The admin-specific functionality of the plugin
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * The admin-specific functionality class.
 */
class NerdsIQ_Admin {

    /**
     * The ID of this plugin.
     *
     * @var string
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @var string
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since 1.0.0
     */
    public function enqueue_styles() {
        $screen = get_current_screen();

        // Only load on plugin admin pages
        if ( $screen && strpos( $screen->id, 'nerdsiq' ) !== false ) {
            wp_enqueue_style(
                $this->plugin_name,
                NERDSIQ_PLUGIN_URL . 'admin/css/nerdsiq-admin.css',
                array(),
                $this->version,
                'all'
            );

            // Color picker
            wp_enqueue_style( 'wp-color-picker' );
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts() {
        $screen = get_current_screen();

        // Only load on plugin admin pages
        if ( $screen && strpos( $screen->id, 'nerdsiq' ) !== false ) {
            wp_enqueue_script(
                $this->plugin_name,
                NERDSIQ_PLUGIN_URL . 'admin/js/nerdsiq-admin.js',
                array( 'jquery', 'wp-color-picker' ),
                $this->version,
                false
            );

            // Localize script with AJAX URL and nonce
            wp_localize_script(
                $this->plugin_name,
                'nerdsiq_admin',
                array(
                    'ajax_url' => admin_url( 'admin-ajax.php' ),
                    'nonce'    => wp_create_nonce( 'nerdsiq_admin_nonce' ),
                    'strings'  => array(
                        'testing'           => __( 'Testing connection...', 'nerdsiq-ai-assistant' ),
                        'saving'            => __( 'Saving...', 'nerdsiq-ai-assistant' ),
                        'confirm_clear'     => __( 'Are you sure you want to clear all logs? This cannot be undone.', 'nerdsiq-ai-assistant' ),
                        'confirm_delete'    => __( 'Are you sure you want to delete this item?', 'nerdsiq-ai-assistant' ),
                    ),
                )
            );
        }
    }

    /**
     * Add admin menu pages.
     *
     * @since 1.0.0
     */
    public function add_admin_menu() {
        // Main menu page
        add_menu_page(
            __( 'NerdsIQ AI Assistant', 'nerdsiq-ai-assistant' ),
            __( 'NerdsIQ', 'nerdsiq-ai-assistant' ),
            'manage_nerdsiq_settings',
            'nerdsiq-settings',
            array( $this, 'display_settings_page' ),
            'dashicons-format-chat',
            30
        );

        // Settings submenu (same as main page)
        add_submenu_page(
            'nerdsiq-settings',
            __( 'Settings', 'nerdsiq-ai-assistant' ),
            __( 'Settings', 'nerdsiq-ai-assistant' ),
            'manage_nerdsiq_settings',
            'nerdsiq-settings',
            array( $this, 'display_settings_page' )
        );

        // Usage Logs
        add_submenu_page(
            'nerdsiq-settings',
            __( 'Usage Logs', 'nerdsiq-ai-assistant' ),
            __( 'Usage Logs', 'nerdsiq-ai-assistant' ),
            'manage_nerdsiq_settings',
            'nerdsiq-usage-logs',
            array( $this, 'display_usage_logs_page' )
        );

        // Conversations
        add_submenu_page(
            'nerdsiq-settings',
            __( 'Conversations', 'nerdsiq-ai-assistant' ),
            __( 'Conversations', 'nerdsiq-ai-assistant' ),
            'manage_nerdsiq_settings',
            'nerdsiq-conversations',
            array( $this, 'display_conversations_page' )
        );

        // Analytics
        add_submenu_page(
            'nerdsiq-settings',
            __( 'Analytics', 'nerdsiq-ai-assistant' ),
            __( 'Analytics', 'nerdsiq-ai-assistant' ),
            'manage_nerdsiq_settings',
            'nerdsiq-analytics',
            array( $this, 'display_analytics_page' )
        );

        // System Status
        add_submenu_page(
            'nerdsiq-settings',
            __( 'System Status', 'nerdsiq-ai-assistant' ),
            __( 'System Status', 'nerdsiq-ai-assistant' ),
            'manage_nerdsiq_settings',
            'nerdsiq-system-status',
            array( $this, 'display_system_status_page' )
        );
    }

    /**
     * Register plugin settings.
     *
     * @since 1.0.0
     */
    public function register_settings() {
        // Get all option names
        $options = $this->get_all_option_names();

        // Register each setting
        foreach ( $options as $option_name ) {
            // Use specific sanitization for AWS credentials
            if ( 'nerdsiq_aws_access_key' === $option_name || 'nerdsiq_aws_secret_key' === $option_name ) {
                register_setting(
                    'nerdsiq_settings_group',
                    $option_name,
                    array( $this, 'sanitize_aws_credential' )
                );
            } else {
                register_setting(
                    'nerdsiq_settings_group',
                    $option_name,
                    array( $this, 'sanitize_setting' )
                );
            }
        }
    }

    /**
     * Sanitize AWS credentials with encryption.
     *
     * @since  1.0.0
     * @param  mixed $value Credential value.
     * @return string Encrypted credential.
     */
    public function sanitize_aws_credential( $value ) {
        // Don't save if it's just asterisks (placeholder)
        if ( preg_match( '/^\*+$/', $value ) ) {
            // Get the current filter to determine which option
            $current_filter = current_filter();
            if ( strpos( $current_filter, 'nerdsiq_aws_access_key' ) !== false ) {
                return get_option( 'nerdsiq_aws_access_key', '' );
            } elseif ( strpos( $current_filter, 'nerdsiq_aws_secret_key' ) !== false ) {
                return get_option( 'nerdsiq_aws_secret_key', '' );
            }
            return $value;
        }

        // Encrypt the new value
        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-security.php';
        return NerdsIQ_Security::encrypt( sanitize_text_field( $value ) );
    }

    /**
     * Get all option names.
     *
     * @since  1.0.0
     * @return array Array of option names.
     */
    private function get_all_option_names() {
        return array(
            'nerdsiq_enabled',
            'nerdsiq_aws_access_key',
            'nerdsiq_aws_secret_key',
            'nerdsiq_aws_region',
            'nerdsiq_qbusiness_app_id',
            'nerdsiq_qbusiness_index_id',
            'nerdsiq_display_mode',
            'nerdsiq_selected_pages',
            'nerdsiq_display_pages',
            'nerdsiq_require_login',
            'nerdsiq_allowed_roles',
            'nerdsiq_user_whitelist',
            'nerdsiq_user_blacklist',
            'nerdsiq_widget_position',
            'nerdsiq_logo_url',
            'nerdsiq_header_title',
            'nerdsiq_button_text',
            'nerdsiq_primary_color',
            'nerdsiq_secondary_color',
            'nerdsiq_user_message_color',
            'nerdsiq_ai_message_color',
            'nerdsiq_background_color',
            'nerdsiq_text_color',
            'nerdsiq_link_color',
            'nerdsiq_widget_width',
            'nerdsiq_widget_height',
            'nerdsiq_border_radius',
            'nerdsiq_shadow_depth',
            'nerdsiq_font_family',
            'nerdsiq_font_size',
            'nerdsiq_line_height',
            'nerdsiq_welcome_message',
            'nerdsiq_input_placeholder',
            'nerdsiq_suggested_questions',
            'nerdsiq_show_welcome_returning',
            'nerdsiq_enable_history',
            'nerdsiq_conversation_timeout',
            'nerdsiq_max_messages',
            'nerdsiq_show_typing_indicator',
            'nerdsiq_typing_duration',
            'nerdsiq_show_citations',
            'nerdsiq_citation_format',
            'nerdsiq_enable_suggestions',
            'nerdsiq_rate_limit_hourly',
            'nerdsiq_rate_limit_daily',
            'nerdsiq_rate_limit_message',
            'nerdsiq_custom_css',
            'nerdsiq_custom_js',
            'nerdsiq_enable_cache',
            'nerdsiq_cache_duration',
            'nerdsiq_lazy_load',
            'nerdsiq_preload_history',
            'nerdsiq_debug_mode',
            'nerdsiq_log_level',
            'nerdsiq_log_retention',
            'nerdsiq_api_timeout',
            'nerdsiq_api_retry_attempts',
            'nerdsiq_enable_analytics',
            'nerdsiq_track_anonymous',
        );
    }

    /**
     * Sanitize setting values.
     *
     * @since  1.0.0
     * @param  mixed $value Setting value.
     * @return mixed Sanitized value.
     */
    public function sanitize_setting( $value ) {
        $option_name = isset( $_POST['option_page'] ) ? sanitize_text_field( $_POST['option_page'] ) : '';

        // Handle AWS credentials separately with encryption
        if ( 'nerdsiq_aws_access_key' === $option_name || 'nerdsiq_aws_secret_key' === $option_name ) {
            require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-security.php';
            return NerdsIQ_Security::encrypt( sanitize_text_field( $value ) );
        }

        // Handle arrays
        if ( is_array( $value ) ) {
            return array_map( 'sanitize_text_field', $value );
        }

        // Handle booleans
        if ( is_bool( $value ) || 'true' === $value || 'false' === $value ) {
            return (bool) $value;
        }

        // Handle numbers
        if ( is_numeric( $value ) ) {
            return floatval( $value );
        }

        // Handle text
        if ( in_array( $option_name, array( 'nerdsiq_custom_css', 'nerdsiq_custom_js', 'nerdsiq_welcome_message' ), true ) ) {
            return wp_kses_post( $value );
        }

        return sanitize_text_field( $value );
    }

    /**
     * Display main settings page.
     *
     * @since 1.0.0
     */
    public function display_settings_page() {
        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';

        if ( ! NerdsIQ_Access_Control::can_manage_settings() ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'nerdsiq-ai-assistant' ) );
        }

        require_once NERDSIQ_PLUGIN_DIR . 'admin/partials/nerdsiq-admin-settings.php';
    }

    /**
     * Display usage logs page.
     *
     * @since 1.0.0
     */
    public function display_usage_logs_page() {
        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';

        if ( ! NerdsIQ_Access_Control::can_manage_settings() ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'nerdsiq-ai-assistant' ) );
        }

        require_once NERDSIQ_PLUGIN_DIR . 'admin/partials/nerdsiq-admin-usage-logs.php';
    }

    /**
     * Display conversations page.
     *
     * @since 1.0.0
     */
    public function display_conversations_page() {
        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';

        if ( ! NerdsIQ_Access_Control::can_manage_settings() ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'nerdsiq-ai-assistant' ) );
        }

        require_once NERDSIQ_PLUGIN_DIR . 'admin/partials/nerdsiq-admin-conversations.php';
    }

    /**
     * Display analytics page.
     *
     * @since 1.0.0
     */
    public function display_analytics_page() {
        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';

        if ( ! NerdsIQ_Access_Control::can_manage_settings() ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'nerdsiq-ai-assistant' ) );
        }

        require_once NERDSIQ_PLUGIN_DIR . 'admin/partials/nerdsiq-admin-analytics.php';
    }

    /**
     * Display system status page.
     *
     * @since 1.0.0
     */
    public function display_system_status_page() {
        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';

        if ( ! NerdsIQ_Access_Control::can_manage_settings() ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'nerdsiq-ai-assistant' ) );
        }

        require_once NERDSIQ_PLUGIN_DIR . 'admin/partials/nerdsiq-admin-system-status.php';
    }

    /**
     * AJAX handler for saving AWS credentials.
     *
     * @since 1.0.0
     */
    public function ajax_save_aws_credentials() {
        check_ajax_referer( 'nerdsiq_admin_nonce', 'nonce' );

        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';

        if ( ! NerdsIQ_Access_Control::can_manage_settings() ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'nerdsiq-ai-assistant' ) ) );
        }

        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-security.php';

        $access_key = isset( $_POST['access_key'] ) ? sanitize_text_field( $_POST['access_key'] ) : '';
        $secret_key = isset( $_POST['secret_key'] ) ? sanitize_text_field( $_POST['secret_key'] ) : '';
        $region = isset( $_POST['region'] ) ? sanitize_text_field( $_POST['region'] ) : 'us-east-1';
        $app_id = isset( $_POST['app_id'] ) ? sanitize_text_field( $_POST['app_id'] ) : '';

        // Validate access key format before saving
        if ( ! empty( $access_key ) && ! preg_match( '/^\*+$/', $access_key ) ) {
            // Check if it looks like a valid AWS Access Key
            if ( ! preg_match( '/^(AKIA|ASIA|AIDA|AROA|AIPA|ANPA|ANVA|AGPA)[A-Z0-9]{16}$/', $access_key ) ) {
                wp_send_json_error( array( 
                    'message' => sprintf(
                        __( 'Invalid Access Key format. AWS Access Key IDs start with AKIA and are 20 characters. You entered: %s...', 'nerdsiq-ai-assistant' ),
                        substr( $access_key, 0, 8 )
                    )
                ) );
                return;
            }
        }

        // Encrypt and save credentials - always delete first to ensure clean save
        if ( ! empty( $access_key ) && ! preg_match( '/^\*+$/', $access_key ) ) {
            delete_option( 'nerdsiq_aws_access_key' );
            $encrypted_access_key = NerdsIQ_Security::encrypt( $access_key );
            update_option( 'nerdsiq_aws_access_key', $encrypted_access_key, false );
        }

        if ( ! empty( $secret_key ) && ! preg_match( '/^\*+$/', $secret_key ) ) {
            delete_option( 'nerdsiq_aws_secret_key' );
            $encrypted_secret_key = NerdsIQ_Security::encrypt( $secret_key );
            update_option( 'nerdsiq_aws_secret_key', $encrypted_secret_key, false );
        }

        // Save region and app ID (not encrypted)
        update_option( 'nerdsiq_aws_region', $region );
        update_option( 'nerdsiq_qbusiness_app_id', $app_id );

        wp_send_json_success( array( 'message' => __( 'AWS credentials saved successfully.', 'nerdsiq-ai-assistant' ) ) );
    }

    /**
     * AJAX handler for testing AWS connection.
     *
     * @since 1.0.0
     */
    public function ajax_test_connection() {
        check_ajax_referer( 'nerdsiq_admin_nonce', 'nonce' );

        // Enable debug logging
        $log_file = WP_CONTENT_DIR . '/nerdsiq-debug.log';
        $log = function( $msg ) use ( $log_file ) {
            file_put_contents( $log_file, '[' . date( 'Y-m-d H:i:s' ) . '] ' . $msg . "\n", FILE_APPEND );
        };
        
        $log( '=== TEST CONNECTION START ===' );

        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';

        if ( ! NerdsIQ_Access_Control::can_manage_settings() ) {
            $log( 'Permission denied' );
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'nerdsiq-ai-assistant' ) ) );
        }

        // Get credentials from form (if provided and not asterisks)
        $access_key = isset( $_POST['access_key'] ) ? sanitize_text_field( $_POST['access_key'] ) : '';
        $secret_key = isset( $_POST['secret_key'] ) ? sanitize_text_field( $_POST['secret_key'] ) : '';
        $region = isset( $_POST['region'] ) ? sanitize_text_field( $_POST['region'] ) : 'us-east-1';
        $app_id = isset( $_POST['app_id'] ) ? sanitize_text_field( $_POST['app_id'] ) : '';

        $log( 'POST access_key received: ' . substr( $access_key, 0, 8 ) . '... (length: ' . strlen( $access_key ) . ')' );
        $log( 'POST secret_key received: ' . ( ! empty( $secret_key ) ? 'YES (length: ' . strlen( $secret_key ) . ')' : 'EMPTY' ) );
        $log( 'POST region: ' . $region );
        $log( 'POST app_id: ' . $app_id );

        // Check if form values are real (not asterisks placeholder)
        $is_asterisks_access = preg_match( '/^\*+$/', $access_key );
        $is_asterisks_secret = preg_match( '/^\*+$/', $secret_key );
        $use_form_values = ! empty( $access_key ) && ! $is_asterisks_access &&
                           ! empty( $secret_key ) && ! $is_asterisks_secret;

        $log( 'Is access_key asterisks: ' . ( $is_asterisks_access ? 'YES' : 'NO' ) );
        $log( 'Is secret_key asterisks: ' . ( $is_asterisks_secret ? 'YES' : 'NO' ) );
        $log( 'Use form values: ' . ( $use_form_values ? 'YES' : 'NO' ) );

        // If asterisks were sent, tell user to re-enter credentials
        if ( $is_asterisks_access || $is_asterisks_secret ) {
            $log( 'FAILED: Asterisks detected - user must re-enter credentials' );
            wp_send_json_error( array( 
                'message' => __( 'Please enter your AWS credentials in the fields above before testing. The asterisks (****) are just placeholders - you need to type your actual Access Key and Secret Key.', 'nerdsiq-ai-assistant' )
            ) );
            return;
        }

        // Determine which credentials to use
        $test_credentials = array();

        if ( $use_form_values ) {
            $log( 'Using form values for test' );
            
            // Validate access key format
            $valid_format = preg_match( '/^(AKIA|ASIA|AIDA|AROA|AIPA|ANPA|ANVA|AGPA)[A-Z0-9]{16}$/', $access_key );
            $log( 'Access key format valid: ' . ( $valid_format ? 'YES' : 'NO' ) );
            
            if ( ! $valid_format ) {
                $log( 'FAILED: Invalid access key format' );
                wp_send_json_error( array( 
                    'message' => sprintf(
                        __( 'Invalid Access Key format. AWS Access Key IDs start with AKIA and are 20 characters. You entered: %s...', 'nerdsiq-ai-assistant' ),
                        substr( $access_key, 0, 8 )
                    )
                ) );
                return;
            }

            // Use form values directly for testing (no database involved!)
            $test_credentials = array(
                'access_key' => $access_key,
                'secret_key' => $secret_key,
                'region'     => $region,
                'app_id'     => $app_id,
            );
            
            $log( 'test_credentials prepared - access_key starts with: ' . substr( $test_credentials['access_key'], 0, 8 ) );

            // Also save them to database for future use
            require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-security.php';

            // Clear object cache for these options
            wp_cache_delete( 'nerdsiq_aws_access_key', 'options' );
            wp_cache_delete( 'nerdsiq_aws_secret_key', 'options' );
            
            delete_option( 'nerdsiq_aws_access_key' );
            delete_option( 'nerdsiq_aws_secret_key' );
            
            $encrypted_access_key = NerdsIQ_Security::encrypt( $access_key );
            $encrypted_secret_key = NerdsIQ_Security::encrypt( $secret_key );
            
            update_option( 'nerdsiq_aws_access_key', $encrypted_access_key, false );
            update_option( 'nerdsiq_aws_secret_key', $encrypted_secret_key, false );
            
            if ( ! empty( $region ) ) {
                update_option( 'nerdsiq_aws_region', $region );
            }
            if ( ! empty( $app_id ) ) {
                update_option( 'nerdsiq_qbusiness_app_id', $app_id );
            }
            
            $log( 'Credentials saved to database' );
        } else {
            $log( 'NOT using form values - test_credentials is EMPTY' );
        }

        try {
            // Check if autoloader exists
            $autoloader = NERDSIQ_PLUGIN_DIR . 'vendor/autoload.php';
            if ( ! file_exists( $autoloader ) ) {
                $log( 'FAILED: Autoloader not found' );
                wp_send_json_error( array( 'message' => __( 'AWS SDK not found. Please reinstall the plugin.', 'nerdsiq-ai-assistant' ) ) );
            }

            require_once $autoloader;
            require_once NERDSIQ_PLUGIN_DIR . 'includes/api/class-nerdsiq-aws-client.php';

            $log( 'Creating AWS client with test_credentials: ' . ( ! empty( $test_credentials ) ? 'YES' : 'EMPTY ARRAY' ) );
            if ( ! empty( $test_credentials ) ) {
                $log( 'test_credentials access_key: ' . substr( $test_credentials['access_key'], 0, 8 ) . '...' );
            }

            // Pass credentials directly to client - bypasses database entirely for testing
            $client = new NerdsIQ_AWS_Client( $test_credentials );
            $result = $client->test_connection();

            $log( 'Test result: ' . ( $result['success'] ? 'SUCCESS' : 'FAILED' ) );
            $log( 'Message: ' . $result['message'] );

            if ( $result['success'] ) {
                wp_send_json_success( $result );
            } else {
                wp_send_json_error( $result );
            }
        } catch ( Exception $e ) {
            $log( 'Exception: ' . $e->getMessage() );
            wp_send_json_error( array( 'message' => 'Error: ' . $e->getMessage() ) );
        } catch ( Error $e ) {
            $log( 'PHP Error: ' . $e->getMessage() );
            wp_send_json_error( array( 'message' => 'PHP Error: ' . $e->getMessage() ) );
        }
    }

    /**
     * AJAX handler for getting analytics data.
     *
     * @since 1.0.0
     */
    public function ajax_get_analytics() {
        check_ajax_referer( 'nerdsiq_admin_nonce', 'nonce' );

        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';

        if ( ! NerdsIQ_Access_Control::can_manage_settings() ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'nerdsiq-ai-assistant' ) ) );
        }

        global $wpdb;

        $period = isset( $_POST['period'] ) ? sanitize_text_field( $_POST['period'] ) : '30days';

        // Calculate date range
        switch ( $period ) {
            case '7days':
                $date_from = gmdate( 'Y-m-d H:i:s', strtotime( '-7 days' ) );
                break;
            case '30days':
                $date_from = gmdate( 'Y-m-d H:i:s', strtotime( '-30 days' ) );
                break;
            case '90days':
                $date_from = gmdate( 'Y-m-d H:i:s', strtotime( '-90 days' ) );
                break;
            default:
                $date_from = gmdate( 'Y-m-d H:i:s', strtotime( '-30 days' ) );
        }

        // Get analytics data
        $total_conversations = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}nerdsiq_conversations WHERE started_at >= %s",
                $date_from
            )
        );

        $total_messages = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}nerdsiq_messages WHERE created_at >= %s",
                $date_from
            )
        );

        $unique_users = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->prefix}nerdsiq_conversations WHERE started_at >= %s",
                $date_from
            )
        );

        $avg_messages_per_conversation = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT AVG(message_count) FROM {$wpdb->prefix}nerdsiq_conversations WHERE started_at >= %s",
                $date_from
            )
        );

        $avg_response_time = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT AVG(response_time) FROM {$wpdb->prefix}nerdsiq_messages WHERE created_at >= %s AND message_type = 'assistant'",
                $date_from
            )
        );

        wp_send_json_success(
            array(
                'total_conversations'           => (int) $total_conversations,
                'total_messages'                => (int) $total_messages,
                'unique_users'                  => (int) $unique_users,
                'avg_messages_per_conversation' => round( (float) $avg_messages_per_conversation, 2 ),
                'avg_response_time'             => round( (float) $avg_response_time, 0 ),
            )
        );
    }

    /**
     * AJAX handler for exporting data.
     *
     * @since 1.0.0
     */
    public function ajax_export_data() {
        check_ajax_referer( 'nerdsiq_admin_nonce', 'nonce' );

        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';

        if ( ! NerdsIQ_Access_Control::can_manage_settings() ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'nerdsiq-ai-assistant' ) ) );
        }

        $export_type = isset( $_POST['export_type'] ) ? sanitize_text_field( $_POST['export_type'] ) : 'conversations';

        global $wpdb;

        switch ( $export_type ) {
            case 'conversations':
                $data = $wpdb->get_results(
                    "SELECT * FROM {$wpdb->prefix}nerdsiq_conversations ORDER BY started_at DESC",
                    ARRAY_A
                );
                break;
            case 'messages':
                $data = $wpdb->get_results(
                    "SELECT * FROM {$wpdb->prefix}nerdsiq_messages ORDER BY created_at DESC",
                    ARRAY_A
                );
                break;
            case 'usage':
                $data = $wpdb->get_results(
                    "SELECT * FROM {$wpdb->prefix}nerdsiq_usage_logs ORDER BY created_at DESC",
                    ARRAY_A
                );
                break;
            default:
                wp_send_json_error( array( 'message' => __( 'Invalid export type.', 'nerdsiq-ai-assistant' ) ) );
                return;
        }

        // Convert to CSV
        $csv = $this->array_to_csv( $data );

        wp_send_json_success(
            array(
                'csv' => $csv,
                'filename' => "nerdsiq-{$export_type}-" . gmdate( 'Y-m-d' ) . '.csv',
            )
        );
    }

    /**
     * AJAX handler for clearing logs.
     *
     * @since 1.0.0
     */
    public function ajax_clear_logs() {
        check_ajax_referer( 'nerdsiq_admin_nonce', 'nonce' );

        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';

        if ( ! NerdsIQ_Access_Control::can_manage_settings() ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'nerdsiq-ai-assistant' ) ) );
        }

        require_once NERDSIQ_PLUGIN_DIR . 'includes/logging/class-nerdsiq-logger.php';
        NerdsIQ_Logger::cleanup_old_logs();

        wp_send_json_success( array( 'message' => __( 'Logs cleared successfully.', 'nerdsiq-ai-assistant' ) ) );
    }

    /**
     * Convert array to CSV string.
     *
     * @since  1.0.0
     * @param  array $data Data array.
     * @return string CSV string.
     */
    private function array_to_csv( $data ) {
        if ( empty( $data ) ) {
            return '';
        }

        $output = fopen( 'php://temp', 'r+' );

        // Add headers
        fputcsv( $output, array_keys( $data[0] ) );

        // Add data rows
        foreach ( $data as $row ) {
            fputcsv( $output, $row );
        }

        rewind( $output );
        $csv = stream_get_contents( $output );
        fclose( $output );

        return $csv;
    }
}
