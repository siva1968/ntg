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
        // Load defaults from config file if it exists
        $config_file = NERDSIQ_PLUGIN_DIR . 'includes/config/default-options.php';
        if ( file_exists( $config_file ) ) {
            $default_options = include $config_file;
        } else {
            // Fallback defaults
            $default_options = array(
                // AWS Configuration
                'nerdsiq_aws_region'           => 'us-east-1',
                'nerdsiq_qbusiness_app_id'     => '',
                
                // Branding - NerdsToGo Theme
                'nerdsiq_primary_color'        => '#0047AC',
                'nerdsiq_secondary_color'      => '#FFD301',
                'nerdsiq_user_message_color'   => '#0047AC',
                'nerdsiq_ai_message_color'     => '#f5f5f5',
                'nerdsiq_background_color'     => '#ffffff',
                'nerdsiq_text_color'           => '#333333',
                'nerdsiq_link_color'           => '#0047AC',
                
                // Logo and Header
                'nerdsiq_logo_url'             => '',
                'nerdsiq_header_title'         => 'NerdsIQ AI Assistant',
                'nerdsiq_button_text'          => 'Ask NerdsIQ',
                
                // Messages
                'nerdsiq_welcome_message'      => 'Hi! How can I help you today?',
                'nerdsiq_input_placeholder'    => 'Type your message...',
                
                // Widget Settings
                'nerdsiq_widget_position'      => 'bottom-right',
                'nerdsiq_widget_width'         => '400',
                'nerdsiq_widget_height'        => '600',
                'nerdsiq_border_radius'        => '10',
                'nerdsiq_shadow_depth'         => 'medium',
                
                // Typography
                'nerdsiq_font_family'          => 'system',
                'nerdsiq_font_size'            => '14',
                'nerdsiq_line_height'          => '1.5',
                
                // Access Control
                'nerdsiq_enabled'              => '1',
                'nerdsiq_require_login'        => '0',
                'nerdsiq_display_mode'         => 'all',
                'nerdsiq_allowed_roles'        => array( 'administrator', 'editor' ),
                
                // Features
                'nerdsiq_show_citations'       => '1',
                'nerdsiq_enable_history'       => '1',
                'nerdsiq_show_typing_indicator'=> '1',
                
                // Rate Limiting
                'nerdsiq_rate_limit_hourly'    => '50',
                'nerdsiq_rate_limit_daily'     => '250',
            );
        }

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
