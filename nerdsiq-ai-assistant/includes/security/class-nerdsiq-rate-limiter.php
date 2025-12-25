<?php
/**
 * Rate Limiter
 *
 * Handles rate limiting to prevent abuse.
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Rate Limiter Class
 */
class NerdsIQ_Rate_Limiter {

    /**
     * Check if user has exceeded rate limit
     *
     * @since  1.0.0
     * @param  int $user_id User ID.
     * @return array Array with 'allowed' boolean and optional 'message' string.
     */
    public static function check_rate_limit( $user_id ) {
        $result = array(
            'allowed' => true,
            'message' => '',
            'reset_in' => 0,
        );

        // Check hourly limit
        $hourly_limit = get_option( 'nerdsiq_rate_limit_hourly', 50 );
        if ( $hourly_limit > 0 ) {
            $hourly_count = self::get_message_count( $user_id, 'hour' );

            if ( $hourly_count >= $hourly_limit ) {
                $result['allowed'] = false;
                $result['message'] = sprintf(
                    /* translators: %d: Rate limit */
                    __( 'You have exceeded the hourly limit of %d messages. Please try again later.', 'nerdsiq-ai-assistant' ),
                    $hourly_limit
                );
                $result['reset_in'] = self::get_time_until_reset( 'hour' );
                return $result;
            }
        }

        // Check daily limit
        $daily_limit = get_option( 'nerdsiq_rate_limit_daily', 250 );
        if ( $daily_limit > 0 ) {
            $daily_count = self::get_message_count( $user_id, 'day' );

            if ( $daily_count >= $daily_limit ) {
                $result['allowed'] = false;
                $result['message'] = sprintf(
                    /* translators: %d: Rate limit */
                    __( 'You have exceeded the daily limit of %d messages. Please try again tomorrow.', 'nerdsiq-ai-assistant' ),
                    $daily_limit
                );
                $result['reset_in'] = self::get_time_until_reset( 'day' );
                return $result;
            }
        }

        return $result;
    }

    /**
     * Get message count for user in time period
     *
     * @since  1.0.0
     * @param  int    $user_id User ID.
     * @param  string $period  Period ('hour' or 'day').
     * @return int Message count.
     */
    private static function get_message_count( $user_id, $period = 'hour' ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'nerdsiq_messages';

        // Calculate time threshold
        if ( 'hour' === $period ) {
            $threshold = gmdate( 'Y-m-d H:i:s', strtotime( '-1 hour' ) );
        } else {
            $threshold = gmdate( 'Y-m-d H:i:s', strtotime( '-1 day' ) );
        }

        // Count messages from user in time period (only user messages, not AI responses)
        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table_name}
                 WHERE user_id = %d
                 AND message_type = 'user'
                 AND created_at >= %s",
                $user_id,
                $threshold
            )
        );

        return (int) $count;
    }

    /**
     * Get time until rate limit resets
     *
     * @since  1.0.0
     * @param  string $period Period ('hour' or 'day').
     * @return int Seconds until reset.
     */
    private static function get_time_until_reset( $period = 'hour' ) {
        $now = time();

        if ( 'hour' === $period ) {
            // Reset at the next hour
            $reset_time = strtotime( '+1 hour', $now );
            $reset_time = strtotime( gmdate( 'Y-m-d H:00:00', $reset_time ) );
        } else {
            // Reset at midnight
            $reset_time = strtotime( 'tomorrow 00:00:00' );
        }

        return $reset_time - $now;
    }

    /**
     * Record message for rate limiting
     *
     * @since 1.0.0
     * @param int $user_id User ID.
     */
    public static function record_message( $user_id ) {
        // Increment transient counter for quick checks
        $hourly_key = 'nerdsiq_rate_hour_' . $user_id;
        $daily_key = 'nerdsiq_rate_day_' . $user_id;

        $hourly_count = get_transient( $hourly_key );
        $daily_count = get_transient( $daily_key );

        set_transient( $hourly_key, (int) $hourly_count + 1, HOUR_IN_SECONDS );
        set_transient( $daily_key, (int) $daily_count + 1, DAY_IN_SECONDS );
    }

    /**
     * Check IP-based rate limiting (for DDoS protection)
     *
     * @since  1.0.0
     * @param  string $ip IP address.
     * @return bool True if allowed, false if rate limited.
     */
    public static function check_ip_rate_limit( $ip ) {
        $transient_key = 'nerdsiq_ip_rate_' . md5( $ip );
        $count = get_transient( $transient_key );

        // Allow 100 requests per minute per IP
        $ip_limit = 100;

        if ( $count && $count >= $ip_limit ) {
            return false;
        }

        set_transient( $transient_key, (int) $count + 1, MINUTE_IN_SECONDS );

        return true;
    }

    /**
     * Get rate limit status for user
     *
     * @since  1.0.0
     * @param  int $user_id User ID.
     * @return array Array with 'hourly' and 'daily' usage info.
     */
    public static function get_rate_limit_status( $user_id ) {
        $hourly_limit = get_option( 'nerdsiq_rate_limit_hourly', 50 );
        $daily_limit = get_option( 'nerdsiq_rate_limit_daily', 250 );

        $hourly_count = self::get_message_count( $user_id, 'hour' );
        $daily_count = self::get_message_count( $user_id, 'day' );

        return array(
            'hourly' => array(
                'count' => $hourly_count,
                'limit' => $hourly_limit,
                'remaining' => max( 0, $hourly_limit - $hourly_count ),
                'reset_in' => self::get_time_until_reset( 'hour' ),
            ),
            'daily'  => array(
                'count' => $daily_count,
                'limit' => $daily_limit,
                'remaining' => max( 0, $daily_limit - $daily_count ),
                'reset_in' => self::get_time_until_reset( 'day' ),
            ),
        );
    }
}
