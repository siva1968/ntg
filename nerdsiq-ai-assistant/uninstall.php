<?php
/**
 * Fired when the plugin is uninstalled
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.0
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

/**
 * Delete plugin data on uninstall
 *
 * This will remove all database tables, options, and capabilities
 * created by the plugin.
 */

// Define plugin directory
define( 'NERDSIQ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

// Load database class
require_once NERDSIQ_PLUGIN_DIR . 'includes/database/class-nerdsiq-database.php';

// Load deactivator class
require_once NERDSIQ_PLUGIN_DIR . 'includes/class-nerdsiq-deactivator.php';

// Drop database tables
NerdsIQ_Database::drop_tables();

// Remove capabilities
NerdsIQ_Deactivator::remove_capabilities();

// Delete all plugin options
global $wpdb;

// Get all options starting with 'nerdsiq_'
$options = $wpdb->get_col(
    "SELECT option_name FROM {$wpdb->options}
     WHERE option_name LIKE 'nerdsiq_%'"
);

// Delete each option
foreach ( $options as $option ) {
    delete_option( $option );
}

// Delete transients
$wpdb->query(
    "DELETE FROM {$wpdb->options}
     WHERE option_name LIKE '_transient_nerdsiq_%'
     OR option_name LIKE '_transient_timeout_nerdsiq_%'"
);

// Delete user meta related to the plugin
$wpdb->query(
    "DELETE FROM {$wpdb->usermeta}
     WHERE meta_key LIKE 'nerdsiq_%'"
);

// Clear any scheduled events
wp_clear_scheduled_hook( 'nerdsiq_cleanup_logs' );
wp_clear_scheduled_hook( 'nerdsiq_sync_data' );
