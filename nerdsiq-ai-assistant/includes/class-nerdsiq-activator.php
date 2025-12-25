<?php
/**
 * Fired during plugin activation
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 */
class NerdsIQ_Activator {

    /**
     * Activate the plugin.
     *
     * Creates database tables, sets default options, and creates custom capabilities.
     *
     * @since 1.0.0
     */
    public static function activate() {
        // Require database class
        require_once NERDSIQ_PLUGIN_DIR . 'includes/database/class-nerdsiq-database.php';

        // Create database tables
        NerdsIQ_Database::create_tables();

        // Set default options
        self::set_default_options();

        // Add custom capabilities
        self::add_capabilities();

        // Set activation timestamp
        update_option( 'nerdsiq_activated_at', current_time( 'mysql' ) );

        // Set plugin version
        update_option( 'nerdsiq_version', NERDSIQ_VERSION );

        // Flush rewrite rules (if needed in future versions)
        flush_rewrite_rules();
    }

    /**
     * Set default plugin options
     *
     * @since 1.0.0
     */
    private static function set_default_options() {
        $default_options = array(
            // General Settings
            'nerdsiq_enabled' => true,
            'nerdsiq_aws_access_key' => '',
            'nerdsiq_aws_secret_key' => '',
            'nerdsiq_aws_region' => 'us-east-1',
            'nerdsiq_qbusiness_app_id' => '',
            'nerdsiq_qbusiness_index_id' => '',

            // Display & Access Control
            'nerdsiq_display_pages' => array( 'all' ),
            'nerdsiq_allowed_roles' => array( 'administrator', 'editor' ),
            'nerdsiq_user_whitelist' => array(),
            'nerdsiq_user_blacklist' => array(),

            // Appearance & Branding
            'nerdsiq_widget_position' => 'bottom-right',
            'nerdsiq_button_text' => 'Ask NerdsIQ',
            'nerdsiq_primary_color' => '#0073aa',
            'nerdsiq_secondary_color' => '#005177',
            'nerdsiq_user_message_color' => '#0073aa',
            'nerdsiq_ai_message_color' => '#f0f0f0',
            'nerdsiq_background_color' => '#ffffff',
            'nerdsiq_text_color' => '#333333',
            'nerdsiq_link_color' => '#0073aa',
            'nerdsiq_widget_width' => 400,
            'nerdsiq_widget_height' => 600,
            'nerdsiq_border_radius' => 10,
            'nerdsiq_shadow_depth' => 'medium',
            'nerdsiq_font_family' => 'system',
            'nerdsiq_font_size' => 14,
            'nerdsiq_line_height' => 1.5,
            'nerdsiq_welcome_message' => 'Hi! I\'m your NerdsIQ AI Assistant. How can I help you today?',
            'nerdsiq_suggested_questions' => array(
                'How do I reset a customer\'s password?',
                'What are our service pricing tiers?',
                'How do I troubleshoot network connectivity?',
            ),
            'nerdsiq_show_welcome_returning' => false,

            // Behavior & Features
            'nerdsiq_enable_history' => true,
            'nerdsiq_conversation_timeout' => 30,
            'nerdsiq_max_messages' => 50,
            'nerdsiq_show_typing_indicator' => true,
            'nerdsiq_typing_duration' => 2,
            'nerdsiq_show_citations' => true,
            'nerdsiq_citation_format' => 'inline',
            'nerdsiq_enable_suggestions' => true,

            // Rate Limiting
            'nerdsiq_rate_limit_hourly' => 50,
            'nerdsiq_rate_limit_daily' => 250,
            'nerdsiq_rate_limit_message' => 'You\'ve reached your message limit. Please try again later.',

            // Advanced Settings
            'nerdsiq_custom_css' => '',
            'nerdsiq_custom_js' => '',
            'nerdsiq_enable_cache' => true,
            'nerdsiq_cache_duration' => 15,
            'nerdsiq_lazy_load' => true,
            'nerdsiq_preload_history' => false,
            'nerdsiq_debug_mode' => false,
            'nerdsiq_log_level' => 'errors',
            'nerdsiq_log_retention' => 30,
            'nerdsiq_api_timeout' => 30,
            'nerdsiq_api_retry_attempts' => 2,

            // Analytics
            'nerdsiq_enable_analytics' => true,
            'nerdsiq_track_anonymous' => true,
        );

        foreach ( $default_options as $option_name => $option_value ) {
            if ( false === get_option( $option_name ) ) {
                add_option( $option_name, $option_value );
            }
        }
    }

    /**
     * Add custom capabilities
     *
     * @since 1.0.0
     */
    private static function add_capabilities() {
        // Get administrator role
        $admin_role = get_role( 'administrator' );

        if ( $admin_role ) {
            // Add capability to manage NerdsIQ settings
            $admin_role->add_cap( 'manage_nerdsiq_settings' );
            $admin_role->add_cap( 'use_nerdsiq_chatbot' );
        }

        // Get editor role
        $editor_role = get_role( 'editor' );

        if ( $editor_role ) {
            // Add capability to use chatbot
            $editor_role->add_cap( 'use_nerdsiq_chatbot' );
        }
    }
}
