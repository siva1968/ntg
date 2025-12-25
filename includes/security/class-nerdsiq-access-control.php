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
     * Initialize access control hooks
     *
     * @since 1.0.0
     */
    public static function init() {
        add_action( 'template_redirect', array( __CLASS__, 'maybe_redirect_to_login' ) );
    }

    /**
     * Redirect non-logged-in users to login page if required
     *
     * @since 1.0.0
     */
    public static function maybe_redirect_to_login() {
        // Only redirect if login is required
        $require_login = get_option( 'nerdsiq_require_login', false );
        if ( ! $require_login ) {
            return;
        }

        // Skip if user is already logged in
        if ( is_user_logged_in() ) {
            return;
        }

        // Skip admin pages and login page
        if ( is_admin() || $GLOBALS['pagenow'] === 'wp-login.php' ) {
            return;
        }

        // Check if chatbot should display on this page
        if ( ! self::should_display_on_page() ) {
            return;
        }

        // Redirect to login page with return URL
        $redirect_url = wp_login_url( get_permalink() );
        wp_safe_redirect( $redirect_url );
        exit;
    }

    /**
     * Check if current user has access to chatbot
     *
     * @since  1.0.0
     * @return bool True if user has access, false otherwise.
     */
    public static function user_has_access() {
        // Check if login is required
        $require_login = get_option( 'nerdsiq_require_login', true );
        if ( $require_login && ! is_user_logged_in() ) {
            return false;
        }

        // Check if plugin is enabled
        if ( ! get_option( 'nerdsiq_enabled', true ) ) {
            return false;
        }

        // For guests (when login not required), allow access
        if ( ! is_user_logged_in() ) {
            return true;
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
        // Skip page check for AJAX requests (the check was done when page loaded)
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return true;
        }

        $display_mode = get_option( 'nerdsiq_display_mode', 'all' );
        $selected_pages = get_option( 'nerdsiq_selected_pages', array() );

        // If 'all' is selected, display everywhere
        if ( $display_mode === 'all' ) {
            return true;
        }

        // Check if current page is in the selected pages
        $is_selected = self::is_current_page_selected( $selected_pages );

        // For 'selected' mode, show only on selected pages
        if ( $display_mode === 'selected' ) {
            return $is_selected;
        }

        // For 'exclude' mode, show on all pages except selected
        if ( $display_mode === 'exclude' ) {
            return ! $is_selected;
        }

        return true;
    }

    /**
     * Check if current page is in selected pages list
     *
     * @since  1.0.0
     * @param  array $selected_pages Array of selected page IDs and special identifiers.
     * @return bool True if current page is selected, false otherwise.
     */
    private static function is_current_page_selected( $selected_pages ) {
        if ( empty( $selected_pages ) ) {
            return false;
        }

        // Ensure it's an array
        $selected_pages = (array) $selected_pages;

        // Get current post ID
        global $post;
        $current_post_id = $post ? $post->ID : 0;

        // Check if current post/page ID is in selected pages
        if ( $current_post_id && in_array( $current_post_id, $selected_pages ) ) {
            return true;
        }

        // Check special page identifiers
        if ( in_array( 'home', $selected_pages, true ) && ( is_front_page() || is_home() ) ) {
            return true;
        }

        if ( in_array( 'blog', $selected_pages, true ) && is_home() && ! is_front_page() ) {
            return true;
        }

        if ( in_array( 'archive', $selected_pages, true ) && ( is_archive() || is_category() || is_tag() || is_author() || is_date() ) ) {
            return true;
        }

        if ( in_array( 'search', $selected_pages, true ) && is_search() ) {
            return true;
        }

        return false;
    }

    /**
     * Check if URL matches pattern (legacy support)
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

        // Check if login is required
        $require_login = get_option( 'nerdsiq_require_login', true );
        if ( $require_login && ! is_user_logged_in() ) {
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

        // Check user access (only for logged in users)
        if ( is_user_logged_in() && ! self::user_has_access() ) {
            $result['reason'] = __( 'You do not have permission to use the chatbot.', 'nerdsiq-ai-assistant' );
            return $result;
        }

        $result['allowed'] = true;
        return $result;
    }
}
