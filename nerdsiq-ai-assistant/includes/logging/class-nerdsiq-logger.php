<?php
/**
 * Logger
 *
 * Handles logging of events, errors, and usage statistics.
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Logger Class
 */
class NerdsIQ_Logger {

    /**
     * Log levels
     */
    const LEVEL_DEBUG = 'debug';
    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';

    /**
     * Log an event
     *
     * @since 1.0.0
     * @param string $message Log message.
     * @param string $level   Log level.
     * @param array  $context Additional context.
     */
    public static function log( $message, $level = self::LEVEL_INFO, $context = array() ) {
        // Check if logging is enabled
        if ( ! self::should_log( $level ) ) {
            return;
        }

        // Add to WordPress debug log if debug mode is enabled
        if ( get_option( 'nerdsiq_debug_mode', false ) && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( sprintf( '[NerdsIQ %s] %s', strtoupper( $level ), $message ) );

            if ( ! empty( $context ) ) {
                error_log( '[NerdsIQ Context] ' . wp_json_encode( $context ) );
            }
        }
    }

    /**
     * Check if message should be logged based on level
     *
     * @since  1.0.0
     * @param  string $level Log level.
     * @return bool True if should log, false otherwise.
     */
    private static function should_log( $level ) {
        $log_level = get_option( 'nerdsiq_log_level', 'errors' );

        $levels = array(
            'errors'   => array( self::LEVEL_ERROR ),
            'warnings' => array( self::LEVEL_ERROR, self::LEVEL_WARNING ),
            'info'     => array( self::LEVEL_ERROR, self::LEVEL_WARNING, self::LEVEL_INFO ),
            'debug'    => array( self::LEVEL_ERROR, self::LEVEL_WARNING, self::LEVEL_INFO, self::LEVEL_DEBUG ),
        );

        $allowed_levels = isset( $levels[ $log_level ] ) ? $levels[ $log_level ] : $levels['errors'];

        return in_array( $level, $allowed_levels, true );
    }

    /**
     * Log an error
     *
     * @since 1.0.0
     * @param string      $error_type    Error type.
     * @param string      $error_message Error message.
     * @param string|null $stack_trace   Stack trace.
     * @param array       $context       Additional context.
     * @param int|null    $user_id       User ID.
     * @return int|false Insert ID or false on failure.
     */
    public static function log_error( $error_type, $error_message, $stack_trace = null, $context = array(), $user_id = null ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'nerdsiq_errors';

        // Get user ID if not provided
        if ( null === $user_id && is_user_logged_in() ) {
            $user_id = get_current_user_id();
        }

        $data = array(
            'user_id'       => $user_id,
            'error_type'    => $error_type,
            'error_message' => $error_message,
            'stack_trace'   => $stack_trace,
            'context'       => wp_json_encode( $context ),
            'created_at'    => current_time( 'mysql' ),
            'resolved'      => 0,
        );

        $result = $wpdb->insert( $table_name, $data );

        // Also log to standard log
        self::log( $error_message, self::LEVEL_ERROR, $context );

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Log usage event
     *
     * @since 1.0.0
     * @param string $action   Action performed.
     * @param array  $metadata Additional metadata.
     * @param int|null $user_id User ID.
     * @return int|false Insert ID or false on failure.
     */
    public static function log_usage( $action, $metadata = array(), $user_id = null ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'nerdsiq_usage_logs';

        // Get user ID if not provided
        if ( null === $user_id && is_user_logged_in() ) {
            $user_id = get_current_user_id();
        }

        // Get current page URL
        $page_url = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( $_SERVER['REQUEST_URI'] ) : '';

        // Get IP address
        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-security.php';
        $ip_address = NerdsIQ_Security::get_user_ip();

        $data = array(
            'user_id'    => $user_id,
            'action'     => $action,
            'page_url'   => $page_url,
            'metadata'   => wp_json_encode( $metadata ),
            'created_at' => current_time( 'mysql' ),
            'ip_address' => $ip_address,
        );

        $result = $wpdb->insert( $table_name, $data );

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Log conversation
     *
     * @since 1.0.0
     * @param string $conversation_id AWS conversation ID.
     * @param int    $user_id         User ID.
     * @param array  $metadata        Additional metadata.
     * @return int|false Insert ID or false on failure.
     */
    public static function log_conversation( $conversation_id, $user_id, $metadata = array() ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'nerdsiq_conversations';

        $data = array(
            'conversation_id' => $conversation_id,
            'user_id'         => $user_id,
            'started_at'      => current_time( 'mysql' ),
            'last_message_at' => current_time( 'mysql' ),
            'message_count'   => 0,
            'status'          => 'active',
            'metadata'        => wp_json_encode( $metadata ),
        );

        $result = $wpdb->insert( $table_name, $data );

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Log message
     *
     * @since 1.0.0
     * @param int    $conversation_id Database conversation ID.
     * @param int    $user_id         User ID.
     * @param string $message_type    Message type ('user' or 'assistant').
     * @param string $message_content Message content.
     * @param array  $sources         Source citations.
     * @param int    $response_time   Response time in milliseconds.
     * @return int|false Insert ID or false on failure.
     */
    public static function log_message( $conversation_id, $user_id, $message_type, $message_content, $sources = array(), $response_time = null ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'nerdsiq_messages';

        $data = array(
            'conversation_id' => $conversation_id,
            'user_id'         => $user_id,
            'message_type'    => $message_type,
            'message_content' => $message_content,
            'sources'         => wp_json_encode( $sources ),
            'created_at'      => current_time( 'mysql' ),
            'response_time'   => $response_time,
        );

        $result = $wpdb->insert( $table_name, $data );

        // Update conversation
        if ( $result ) {
            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE {$wpdb->prefix}nerdsiq_conversations
                     SET last_message_at = %s,
                         message_count = message_count + 1
                     WHERE id = %d",
                    current_time( 'mysql' ),
                    $conversation_id
                )
            );
        }

        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Get conversation by AWS conversation ID
     *
     * @since  1.0.0
     * @param  string $conversation_id AWS conversation ID.
     * @return object|null Conversation object or null.
     */
    public static function get_conversation( $conversation_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'nerdsiq_conversations';

        return $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE conversation_id = %s",
                $conversation_id
            )
        );
    }

    /**
     * Clean up old logs
     *
     * @since 1.0.0
     */
    public static function cleanup_old_logs() {
        global $wpdb;

        $retention_days = get_option( 'nerdsiq_log_retention', 30 );
        $threshold = gmdate( 'Y-m-d H:i:s', strtotime( "-{$retention_days} days" ) );

        // Clean up usage logs
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->prefix}nerdsiq_usage_logs WHERE created_at < %s",
                $threshold
            )
        );

        // Clean up error logs (only resolved ones)
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->prefix}nerdsiq_errors WHERE created_at < %s AND resolved = 1",
                $threshold
            )
        );

        // Archive old conversations (don't delete, just mark as archived)
        $wpdb->query(
            $wpdb->prepare(
                "UPDATE {$wpdb->prefix}nerdsiq_conversations SET status = 'archived' WHERE last_message_at < %s AND status = 'active'",
                $threshold
            )
        );

        self::log( 'Cleaned up old logs', self::LEVEL_INFO, array( 'retention_days' => $retention_days ) );
    }
}
