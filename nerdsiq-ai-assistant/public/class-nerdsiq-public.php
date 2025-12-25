<?php
/**
 * The public-facing functionality of the plugin
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * The public-facing functionality class.
 */
class NerdsIQ_Public {

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
     * @param string $plugin_name The name of the plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side.
     *
     * @since 1.0.0
     */
    public function enqueue_styles() {
        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';

        // Only load if user has access
        if ( ! NerdsIQ_Access_Control::user_has_access() ) {
            return;
        }

        // Only load on allowed pages
        if ( ! NerdsIQ_Access_Control::should_display_on_page() ) {
            return;
        }

        wp_enqueue_style(
            $this->plugin_name,
            NERDSIQ_PLUGIN_URL . 'public/css/nerdsiq-public.css',
            array(),
            $this->version,
            'all'
        );

        // Add custom CSS
        $custom_css = $this->generate_custom_css();
        if ( ! empty( $custom_css ) ) {
            wp_add_inline_style( $this->plugin_name, $custom_css );
        }
    }

    /**
     * Register the JavaScript for the public-facing side.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts() {
        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';

        // Only load if user has access
        if ( ! NerdsIQ_Access_Control::user_has_access() ) {
            return;
        }

        // Only load on allowed pages
        if ( ! NerdsIQ_Access_Control::should_display_on_page() ) {
            return;
        }

        $lazy_load = get_option( 'nerdsiq_lazy_load', true );

        wp_enqueue_script(
            $this->plugin_name,
            NERDSIQ_PLUGIN_URL . 'public/js/nerdsiq-public.js',
            array( 'jquery' ),
            $this->version,
            true
        );

        // Localize script with configuration
        wp_localize_script(
            $this->plugin_name,
            'nerdsiq_config',
            array(
                'ajax_url'                => admin_url( 'admin-ajax.php' ),
                'nonce'                   => wp_create_nonce( 'nerdsiq_public_nonce' ),
                'user_id'                 => get_current_user_id(),
                'position'                => get_option( 'nerdsiq_widget_position', 'bottom-right' ),
                'button_text'             => get_option( 'nerdsiq_button_text', 'Ask NerdsIQ' ),
                'welcome_message'         => get_option( 'nerdsiq_welcome_message', 'Hi! How can I help you today?' ),
                'suggested_questions'     => get_option( 'nerdsiq_suggested_questions', array() ),
                'show_welcome_returning'  => get_option( 'nerdsiq_show_welcome_returning', false ),
                'enable_history'          => get_option( 'nerdsiq_enable_history', true ),
                'conversation_timeout'    => get_option( 'nerdsiq_conversation_timeout', 30 ),
                'max_messages'            => get_option( 'nerdsiq_max_messages', 50 ),
                'show_typing_indicator'   => get_option( 'nerdsiq_show_typing_indicator', true ),
                'typing_duration'         => get_option( 'nerdsiq_typing_duration', 2 ),
                'show_citations'          => get_option( 'nerdsiq_show_citations', true ),
                'citation_format'         => get_option( 'nerdsiq_citation_format', 'inline' ),
                'enable_suggestions'      => get_option( 'nerdsiq_enable_suggestions', true ),
                'rate_limit_message'      => get_option( 'nerdsiq_rate_limit_message', '' ),
                'strings'                 => array(
                    'error'                 => __( 'An error occurred. Please try again.', 'nerdsiq-ai-assistant' ),
                    'send'                  => __( 'Send', 'nerdsiq-ai-assistant' ),
                    'typing'                => __( 'NerdsIQ is typing...', 'nerdsiq-ai-assistant' ),
                    'placeholder'           => __( 'Type your message...', 'nerdsiq-ai-assistant' ),
                    'new_conversation'      => __( 'New Conversation', 'nerdsiq-ai-assistant' ),
                    'close'                 => __( 'Close', 'nerdsiq-ai-assistant' ),
                    'minimize'              => __( 'Minimize', 'nerdsiq-ai-assistant' ),
                    'sources'               => __( 'Sources', 'nerdsiq-ai-assistant' ),
                    'copy'                  => __( 'Copy', 'nerdsiq-ai-assistant' ),
                    'copied'                => __( 'Copied!', 'nerdsiq-ai-assistant' ),
                ),
            )
        );

        // Add custom JavaScript
        $custom_js = get_option( 'nerdsiq_custom_js', '' );
        if ( ! empty( $custom_js ) ) {
            wp_add_inline_script( $this->plugin_name, $custom_js );
        }
    }

    /**
     * Render the chat widget.
     *
     * @since 1.0.0
     */
    public function render_chat_widget() {
        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';

        // Only render if user has access
        if ( ! NerdsIQ_Access_Control::user_has_access() ) {
            return;
        }

        // Only render on allowed pages
        if ( ! NerdsIQ_Access_Control::should_display_on_page() ) {
            return;
        }

        require_once NERDSIQ_PLUGIN_DIR . 'public/partials/nerdsiq-chat-widget.php';
    }

    /**
     * Generate custom CSS from settings.
     *
     * @since  1.0.0
     * @return string Custom CSS.
     */
    private function generate_custom_css() {
        $css = '';

        // Get color settings
        $primary_color = get_option( 'nerdsiq_primary_color', '#0073aa' );
        $secondary_color = get_option( 'nerdsiq_secondary_color', '#005177' );
        $user_message_color = get_option( 'nerdsiq_user_message_color', '#0073aa' );
        $ai_message_color = get_option( 'nerdsiq_ai_message_color', '#f0f0f0' );
        $background_color = get_option( 'nerdsiq_background_color', '#ffffff' );
        $text_color = get_option( 'nerdsiq_text_color', '#333333' );
        $link_color = get_option( 'nerdsiq_link_color', '#0073aa' );

        // Get dimension settings
        $widget_width = get_option( 'nerdsiq_widget_width', 400 );
        $widget_height = get_option( 'nerdsiq_widget_height', 600 );
        $border_radius = get_option( 'nerdsiq_border_radius', 10 );

        // Get typography settings
        $font_family = get_option( 'nerdsiq_font_family', 'system' );
        $font_size = get_option( 'nerdsiq_font_size', 14 );
        $line_height = get_option( 'nerdsiq_line_height', 1.5 );

        // Get shadow settings
        $shadow_depth = get_option( 'nerdsiq_shadow_depth', 'medium' );
        $shadow_map = array(
            'none'   => 'none',
            'subtle' => '0 2px 8px rgba(0,0,0,0.1)',
            'medium' => '0 4px 16px rgba(0,0,0,0.15)',
            'strong' => '0 8px 32px rgba(0,0,0,0.2)',
        );
        $box_shadow = isset( $shadow_map[ $shadow_depth ] ) ? $shadow_map[ $shadow_depth ] : $shadow_map['medium'];

        // Build CSS
        $css .= ":root {\n";
        $css .= "  --nerdsiq-primary-color: {$primary_color};\n";
        $css .= "  --nerdsiq-secondary-color: {$secondary_color};\n";
        $css .= "  --nerdsiq-user-message-color: {$user_message_color};\n";
        $css .= "  --nerdsiq-ai-message-color: {$ai_message_color};\n";
        $css .= "  --nerdsiq-background-color: {$background_color};\n";
        $css .= "  --nerdsiq-text-color: {$text_color};\n";
        $css .= "  --nerdsiq-link-color: {$link_color};\n";
        $css .= "  --nerdsiq-widget-width: {$widget_width}px;\n";
        $css .= "  --nerdsiq-widget-height: {$widget_height}px;\n";
        $css .= "  --nerdsiq-border-radius: {$border_radius}px;\n";
        $css .= "  --nerdsiq-box-shadow: {$box_shadow};\n";
        $css .= "  --nerdsiq-font-size: {$font_size}px;\n";
        $css .= "  --nerdsiq-line-height: {$line_height};\n";

        if ( 'system' === $font_family ) {
            $css .= "  --nerdsiq-font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;\n";
        } else {
            $css .= "  --nerdsiq-font-family: '{$font_family}', sans-serif;\n";
        }

        $css .= "}\n\n";

        // Add user's custom CSS
        $custom_css = get_option( 'nerdsiq_custom_css', '' );
        if ( ! empty( $custom_css ) ) {
            $css .= "\n/* Custom CSS */\n";
            $css .= wp_strip_all_tags( $custom_css );
        }

        return $css;
    }

    /**
     * AJAX handler for sending messages.
     *
     * @since 1.0.0
     */
    public function ajax_send_message() {
        check_ajax_referer( 'nerdsiq_public_nonce', 'nonce' );

        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';
        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-rate-limiter.php';
        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-security.php';
        require_once NERDSIQ_PLUGIN_DIR . 'includes/logging/class-nerdsiq-logger.php';
        require_once NERDSIQ_PLUGIN_DIR . 'includes/api/class-nerdsiq-aws-client.php';

        // Check access
        $access = NerdsIQ_Access_Control::check_access();
        if ( ! $access['allowed'] ) {
            wp_send_json_error( array( 'message' => $access['reason'] ) );
        }

        $user_id = get_current_user_id();

        // Check rate limit
        $rate_limit = NerdsIQ_Rate_Limiter::check_rate_limit( $user_id );
        if ( ! $rate_limit['allowed'] ) {
            wp_send_json_error(
                array(
                    'message'   => $rate_limit['message'],
                    'reset_in'  => $rate_limit['reset_in'],
                    'rate_limited' => true,
                )
            );
        }

        // Get and validate message
        $message = isset( $_POST['message'] ) ? wp_unslash( $_POST['message'] ) : '';
        $conversation_id = isset( $_POST['conversation_id'] ) ? sanitize_text_field( $_POST['conversation_id'] ) : null;

        // Validate message
        $validation = NerdsIQ_Security::validate_message( $message );
        if ( ! $validation['valid'] ) {
            wp_send_json_error( array( 'message' => $validation['error'] ) );
        }

        // Send message to AWS Q Business
        $aws_client = new NerdsIQ_AWS_Client();
        $result = $aws_client->send_message( $validation['message'], $conversation_id, $user_id );

        if ( ! $result['success'] ) {
            NerdsIQ_Logger::log_error(
                'message_send_failed',
                $result['message'],
                null,
                array(
                    'user_id' => $user_id,
                    'message' => $validation['message'],
                )
            );

            wp_send_json_error( $result );
        }

        // Log conversation if new
        if ( empty( $conversation_id ) ) {
            $db_conversation_id = NerdsIQ_Logger::log_conversation(
                $result['conversation_id'],
                $user_id,
                array()
            );
        } else {
            // Get existing conversation
            $existing_conv = NerdsIQ_Logger::get_conversation( $result['conversation_id'] );
            $db_conversation_id = $existing_conv ? $existing_conv->id : null;
        }

        // Log user message
        if ( $db_conversation_id ) {
            NerdsIQ_Logger::log_message(
                $db_conversation_id,
                $user_id,
                'user',
                $validation['message']
            );

            // Log AI response
            NerdsIQ_Logger::log_message(
                $db_conversation_id,
                $user_id,
                'assistant',
                $result['message'],
                $result['sources'],
                $result['response_time']
            );
        }

        // Record for rate limiting
        NerdsIQ_Rate_Limiter::record_message( $user_id );

        // Log usage
        NerdsIQ_Logger::log_usage( 'message_sent', array( 'conversation_id' => $result['conversation_id'] ) );

        wp_send_json_success( $result );
    }

    /**
     * AJAX handler for getting conversation history.
     *
     * @since 1.0.0
     */
    public function ajax_get_history() {
        check_ajax_referer( 'nerdsiq_public_nonce', 'nonce' );

        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';

        // Check access
        $access = NerdsIQ_Access_Control::check_access();
        if ( ! $access['allowed'] ) {
            wp_send_json_error( array( 'message' => $access['reason'] ) );
        }

        $conversation_id = isset( $_POST['conversation_id'] ) ? sanitize_text_field( $_POST['conversation_id'] ) : '';

        if ( empty( $conversation_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Conversation ID is required.', 'nerdsiq-ai-assistant' ) ) );
        }

        global $wpdb;

        // Get conversation
        $conversation = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}nerdsiq_conversations WHERE conversation_id = %s",
                $conversation_id
            )
        );

        if ( ! $conversation ) {
            wp_send_json_error( array( 'message' => __( 'Conversation not found.', 'nerdsiq-ai-assistant' ) ) );
        }

        // Get messages
        $messages = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}nerdsiq_messages WHERE conversation_id = %d ORDER BY created_at ASC",
                $conversation->id
            )
        );

        // Format messages
        $formatted_messages = array();
        foreach ( $messages as $message ) {
            $formatted_messages[] = array(
                'type'    => $message->message_type,
                'content' => $message->message_content,
                'sources' => json_decode( $message->sources, true ),
                'time'    => $message->created_at,
            );
        }

        wp_send_json_success(
            array(
                'messages' => $formatted_messages,
            )
        );
    }

    /**
     * AJAX handler for resetting conversation.
     *
     * @since 1.0.0
     */
    public function ajax_reset_conversation() {
        check_ajax_referer( 'nerdsiq_public_nonce', 'nonce' );

        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';

        // Check access
        $access = NerdsIQ_Access_Control::check_access();
        if ( ! $access['allowed'] ) {
            wp_send_json_error( array( 'message' => $access['reason'] ) );
        }

        require_once NERDSIQ_PLUGIN_DIR . 'includes/logging/class-nerdsiq-logger.php';
        NerdsIQ_Logger::log_usage( 'conversation_reset' );

        wp_send_json_success(
            array(
                'message' => __( 'Conversation reset successfully.', 'nerdsiq-ai-assistant' ),
            )
        );
    }

    /**
     * AJAX handler for logging events.
     *
     * @since 1.0.0
     */
    public function ajax_log_event() {
        check_ajax_referer( 'nerdsiq_public_nonce', 'nonce' );

        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';

        // Check if user has access
        if ( ! NerdsIQ_Access_Control::user_has_access() ) {
            wp_send_json_error( array( 'message' => __( 'Access denied.', 'nerdsiq-ai-assistant' ) ) );
        }

        $action = isset( $_POST['action_type'] ) ? sanitize_text_field( $_POST['action_type'] ) : '';
        $metadata = isset( $_POST['metadata'] ) ? json_decode( wp_unslash( $_POST['metadata'] ), true ) : array();

        if ( empty( $action ) ) {
            wp_send_json_error( array( 'message' => __( 'Action type is required.', 'nerdsiq-ai-assistant' ) ) );
        }

        require_once NERDSIQ_PLUGIN_DIR . 'includes/logging/class-nerdsiq-logger.php';
        NerdsIQ_Logger::log_usage( $action, $metadata );

        wp_send_json_success();
    }
}
