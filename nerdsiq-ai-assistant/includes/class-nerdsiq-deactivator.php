<?php
/**
 * Fired during plugin deactivation
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 */
class NerdsIQ_Deactivator {

    /**
     * Deactivate the plugin.
     *
     * Clears scheduled tasks and temporary data.
     * Does NOT delete database tables or settings (use uninstall.php for that).
     *
     * @since 1.0.0
     */
    public static function deactivate() {
        // Clear any scheduled cron jobs (if we add them in future)
        wp_clear_scheduled_hook( 'nerdsiq_cleanup_logs' );
        wp_clear_scheduled_hook( 'nerdsiq_sync_data' );

        // Clear transients/caches
        self::clear_caches();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Set deactivation timestamp
        update_option( 'nerdsiq_deactivated_at', current_time( 'mysql' ) );
    }

    /**
     * Clear plugin caches
     *
     * @since 1.0.0
     */
    private static function clear_caches() {
        global $wpdb;

        // Delete all transients starting with 'nerdsiq_'
        $wpdb->query(
            "DELETE FROM {$wpdb->options}
             WHERE option_name LIKE '_transient_nerdsiq_%'
             OR option_name LIKE '_transient_timeout_nerdsiq_%'"
        );
    }

    /**
     * Remove custom capabilities (called from uninstall, not deactivation)
     *
     * @since 1.0.0
     */
    public static function remove_capabilities() {
        // Get all roles
        $roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' );

        foreach ( $roles as $role_name ) {
            $role = get_role( $role_name );

            if ( $role ) {
                $role->remove_cap( 'manage_nerdsiq_settings' );
                $role->remove_cap( 'use_nerdsiq_chatbot' );
            }
        }
    }
}
