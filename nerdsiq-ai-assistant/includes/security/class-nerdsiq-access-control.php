<?php
/**
 * Access Control Handler
 *
 * Manages role-based access control and page restrictions.
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Access Control Class
 */
class NerdsIQ_Access_Control {

    /**
     * Check if current user has access to chatbot
     *
     * @since  1.0.0
     * @return bool True if user has access, false otherwise.
     */
    public static function user_has_access() {
        // Must be logged in
        if ( ! is_user_logged_in() ) {
            return false;
        }

        // Check if plugin is enabled
        if ( ! get_option( 'nerdsiq_enabled', true ) ) {
            return false;
        }

        $user = wp_get_current_user();

        // Check blacklist first
        if ( self::is_user_blacklisted( $user ) ) {
            return false;
        }

        // Check whitelist (if whitelist is enabled, only whitelisted users have access)
        $whitelist = get_option( 'nerdsiq_user_whitelist', array() );
        if ( ! empty( $whitelist ) ) {
            return self::is_user_whitelisted( $user );
        }

        // Check role-based access
        return self::user_role_has_access( $user );
    }

    /**
     * Check if user is whitelisted
     *
     * @since  1.0.0
     * @param  WP_User $user User object.
     * @return bool True if whitelisted, false otherwise.
     */
    private static function is_user_whitelisted( $user ) {
        $whitelist = get_option( 'nerdsiq_user_whitelist', array() );

        if ( empty( $whitelist ) ) {
            return true; // No whitelist means all are allowed
        }

        return in_array( $user->user_email, $whitelist, true ) ||
               in_array( $user->user_login, $whitelist, true ) ||
               in_array( $user->ID, $whitelist, true );
    }

    /**
     * Check if user is blacklisted
     *
     * @since  1.0.0
     * @param  WP_User $user User object.
     * @return bool True if blacklisted, false otherwise.
     */
    private static function is_user_blacklisted( $user ) {
        $blacklist = get_option( 'nerdsiq_user_blacklist', array() );

        if ( empty( $blacklist ) ) {
            return false;
        }

        return in_array( $user->user_email, $blacklist, true ) ||
               in_array( $user->user_login, $blacklist, true ) ||
               in_array( $user->ID, $blacklist, true );
    }

    /**
     * Check if user's role has access
     *
     * @since  1.0.0
     * @param  WP_User $user User object.
     * @return bool True if role has access, false otherwise.
     */
    private static function user_role_has_access( $user ) {
        // Get allowed roles
        $allowed_roles = get_option( 'nerdsiq_allowed_roles', array( 'administrator', 'editor' ) );

        if ( empty( $allowed_roles ) ) {
            return false;
        }

        // Check if user has any of the allowed roles
        foreach ( $allowed_roles as $role ) {
            if ( in_array( $role, $user->roles, true ) ) {
                return true;
            }
        }

        // Check for custom capability
        if ( $user->has_cap( 'use_nerdsiq_chatbot' ) ) {
            return true;
        }

        return false;
    }

    /**
     * Check if chatbot should be displayed on current page
     *
     * @since  1.0.0
     * @return bool True if should display, false otherwise.
     */
    public static function should_display_on_page() {
        $display_pages = get_option( 'nerdsiq_display_pages', array( 'all' ) );

        // If 'all' is selected, display everywhere
        if ( in_array( 'all', $display_pages, true ) ) {
            return true;
        }

        if ( empty( $display_pages ) ) {
            return false;
        }

        // Get current page info
        global $post;

        // Check if current post/page ID is in allowed pages
        if ( $post && in_array( $post->ID, $display_pages, true ) ) {
            return true;
        }

        // Check current URL patterns
        $current_url = $_SERVER['REQUEST_URI'];

        foreach ( $display_pages as $pattern ) {
            // If it's a URL pattern (contains /)
            if ( strpos( $pattern, '/' ) === 0 ) {
                // Check if current URL matches pattern
                if ( self::url_matches_pattern( $current_url, $pattern ) ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check if URL matches pattern
     *
     * @since  1.0.0
     * @param  string $url     Current URL.
     * @param  string $pattern Pattern to match.
     * @return bool True if matches, false otherwise.
     */
    private static function url_matches_pattern( $url, $pattern ) {
        // Exact match
        if ( $url === $pattern ) {
            return true;
        }

        // Wildcard pattern (e.g., /internal/*)
        if ( strpos( $pattern, '*' ) !== false ) {
            $regex = str_replace( '*', '.*', preg_quote( $pattern, '/' ) );
            return preg_match( '/^' . $regex . '$/', $url );
        }

        // Prefix match
        if ( strpos( $url, rtrim( $pattern, '/' ) ) === 0 ) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can manage plugin settings
     *
     * @since  1.0.0
     * @return bool True if user can manage, false otherwise.
     */
    public static function can_manage_settings() {
        return current_user_can( 'manage_nerdsiq_settings' ) ||
               current_user_can( 'manage_options' );
    }

    /**
     * Check if access is allowed and return detailed response
     *
     * @since  1.0.0
     * @return array Array with 'allowed' boolean and optional 'reason' string.
     */
    public static function check_access() {
        $result = array(
            'allowed' => false,
            'reason'  => '',
        );

        // Check if logged in
        if ( ! is_user_logged_in() ) {
            $result['reason'] = __( 'You must be logged in to use the chatbot.', 'nerdsiq-ai-assistant' );
            return $result;
        }

        // Check if plugin is enabled
        if ( ! get_option( 'nerdsiq_enabled', true ) ) {
            $result['reason'] = __( 'The chatbot is currently disabled.', 'nerdsiq-ai-assistant' );
            return $result;
        }

        // Check if page is allowed
        if ( ! self::should_display_on_page() ) {
            $result['reason'] = __( 'The chatbot is not available on this page.', 'nerdsiq-ai-assistant' );
            return $result;
        }

        // Check user access
        if ( ! self::user_has_access() ) {
            $result['reason'] = __( 'You do not have permission to use the chatbot.', 'nerdsiq-ai-assistant' );
            return $result;
        }

        $result['allowed'] = true;
        return $result;
    }
}
