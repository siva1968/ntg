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
            register_setting(
                'nerdsiq_settings_group',
                $option_name,
                array( $this, 'sanitize_setting' )
            );
        }
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
     * AJAX handler for testing AWS connection.
     *
     * @since 1.0.0
     */
    public function ajax_test_connection() {
        check_ajax_referer( 'nerdsiq_admin_nonce', 'nonce' );

        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';

        if ( ! NerdsIQ_Access_Control::can_manage_settings() ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'nerdsiq-ai-assistant' ) ) );
        }

        require_once NERDSIQ_PLUGIN_DIR . 'includes/api/class-nerdsiq-aws-client.php';

        $client = new NerdsIQ_AWS_Client();
        $result = $client->test_connection();

        if ( $result['success'] ) {
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
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
