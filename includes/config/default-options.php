<?php
/**
 * Default Plugin Options
 * 
 * These are loaded on plugin activation if options don't exist.
 * Sensitive credentials (AWS keys) should be configured in admin.
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.9
 */

if ( ! defined( 'WPINC' ) ) { die; }

return array(
    // AWS Configuration (credentials must be set in admin)
    'nerdsiq_aws_region'           => 'us-east-1',
    'nerdsiq_qbusiness_app_id'     => 'c87d7dd0-d624-41aa-954e-420c178af202',
    
    // Branding - NerdsToGo Theme
    'nerdsiq_primary_color'        => '#0047AC',
    'nerdsiq_secondary_color'      => '#FFD301',
    'nerdsiq_user_message_color'   => '#0047AC',
    'nerdsiq_ai_message_color'     => '#f5f5f5',
    'nerdsiq_background_color'     => '#ffffff',
    'nerdsiq_text_color'           => '#333333',
    'nerdsiq_link_color'           => '#0047AC',
    
    // Logo and Header
    'nerdsiq_logo_url'             => 'https://nerdsiq-qbusiness-ntgtheme.s3.us-east-1.amazonaws.com/NTG-Stacked.png',
    'nerdsiq_header_title'         => 'NerdsIQ AI Assistant',
    'nerdsiq_button_text'          => 'NerdsIQ',
    
    // Messages
    'nerdsiq_welcome_message'      => 'Ask me anything about how we do things at NerdsToGo. I will find it fast! :)',
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
    'nerdsiq_require_login'        => '1',
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
