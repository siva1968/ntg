<?php
/**
 * Database Schema Handler
 *
 * Creates and manages database tables for the plugin.
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Database Schema Handler Class
 */
class NerdsIQ_Database {

    /**
     * Current database version
     *
     * @var string
     */
    const DB_VERSION = '1.0.0';

    /**
     * Create plugin database tables
     *
     * @since 1.0.0
     */
    public static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_prefix = $wpdb->prefix;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Table 1: Conversations
        $conversations_table = $table_prefix . 'nerdsiq_conversations';
        $sql_conversations = "CREATE TABLE IF NOT EXISTS {$conversations_table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            conversation_id varchar(255) NOT NULL,
            user_id bigint(20) unsigned NOT NULL,
            started_at datetime NOT NULL,
            last_message_at datetime NOT NULL,
            message_count int(11) unsigned NOT NULL DEFAULT 0,
            status varchar(20) NOT NULL DEFAULT 'active',
            metadata longtext,
            PRIMARY KEY  (id),
            KEY conversation_id (conversation_id),
            KEY user_id (user_id),
            KEY status (status),
            KEY started_at (started_at)
        ) $charset_collate;";

        dbDelta( $sql_conversations );

        // Table 2: Messages
        $messages_table = $table_prefix . 'nerdsiq_messages';
        $sql_messages = "CREATE TABLE IF NOT EXISTS {$messages_table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            conversation_id bigint(20) unsigned NOT NULL,
            user_id bigint(20) unsigned NOT NULL,
            message_type varchar(20) NOT NULL,
            message_content longtext NOT NULL,
            sources longtext,
            created_at datetime NOT NULL,
            response_time int(11) unsigned DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY conversation_id (conversation_id),
            KEY user_id (user_id),
            KEY message_type (message_type),
            KEY created_at (created_at)
        ) $charset_collate;";

        dbDelta( $sql_messages );

        // Table 3: Usage Logs
        $usage_logs_table = $table_prefix . 'nerdsiq_usage_logs';
        $sql_usage = "CREATE TABLE IF NOT EXISTS {$usage_logs_table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            action varchar(50) NOT NULL,
            page_url varchar(500),
            metadata longtext,
            created_at datetime NOT NULL,
            ip_address varchar(45),
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY action (action),
            KEY created_at (created_at)
        ) $charset_collate;";

        dbDelta( $sql_usage );

        // Table 4: Error Logs
        $error_logs_table = $table_prefix . 'nerdsiq_errors';
        $sql_errors = "CREATE TABLE IF NOT EXISTS {$error_logs_table} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned DEFAULT NULL,
            error_type varchar(50) NOT NULL,
            error_message text NOT NULL,
            stack_trace longtext,
            context longtext,
            created_at datetime NOT NULL,
            resolved tinyint(1) NOT NULL DEFAULT 0,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY error_type (error_type),
            KEY resolved (resolved),
            KEY created_at (created_at)
        ) $charset_collate;";

        dbDelta( $sql_errors );

        // Save database version
        update_option( 'nerdsiq_db_version', self::DB_VERSION );
    }

    /**
     * Drop plugin database tables
     *
     * @since 1.0.0
     */
    public static function drop_tables() {
        global $wpdb;

        $table_prefix = $wpdb->prefix;

        $tables = array(
            $table_prefix . 'nerdsiq_conversations',
            $table_prefix . 'nerdsiq_messages',
            $table_prefix . 'nerdsiq_usage_logs',
            $table_prefix . 'nerdsiq_errors',
        );

        foreach ( $tables as $table ) {
            $wpdb->query( "DROP TABLE IF EXISTS {$table}" );
        }

        delete_option( 'nerdsiq_db_version' );
    }

    /**
     * Check if database tables need updating
     *
     * @since 1.0.0
     * @return bool
     */
    public static function needs_update() {
        $current_version = get_option( 'nerdsiq_db_version', '0.0.0' );
        return version_compare( $current_version, self::DB_VERSION, '<' );
    }

    /**
     * Update database tables if needed
     *
     * @since 1.0.0
     */
    public static function maybe_update() {
        if ( self::needs_update() ) {
            self::create_tables();
        }
    }
}
