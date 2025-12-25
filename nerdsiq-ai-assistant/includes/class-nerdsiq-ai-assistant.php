<?php
/**
 * The core plugin class
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * The core plugin class.
 */
class NerdsIQ_AI_Assistant {

    /**
     * The loader that's responsible for maintaining and registering all hooks.
     *
     * @var NerdsIQ_Loader
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @var string
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @var string
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->plugin_name = 'nerdsiq-ai-assistant';
        $this->version = NERDSIQ_VERSION;

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since 1.0.0
     */
    private function load_dependencies() {
        /**
         * The class responsible for orchestrating the actions and filters
         */
        require_once NERDSIQ_PLUGIN_DIR . 'includes/class-nerdsiq-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         */
        require_once NERDSIQ_PLUGIN_DIR . 'includes/class-nerdsiq-i18n.php';

        /**
         * Database management class
         */
        require_once NERDSIQ_PLUGIN_DIR . 'includes/database/class-nerdsiq-database.php';

        /**
         * Security and encryption class
         */
        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-security.php';

        /**
         * Logging class
         */
        require_once NERDSIQ_PLUGIN_DIR . 'includes/logging/class-nerdsiq-logger.php';

        /**
         * AWS API client class
         */
        require_once NERDSIQ_PLUGIN_DIR . 'includes/api/class-nerdsiq-aws-client.php';

        /**
         * Access control class
         */
        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-access-control.php';

        /**
         * Rate limiting class
         */
        require_once NERDSIQ_PLUGIN_DIR . 'includes/security/class-nerdsiq-rate-limiter.php';

        /**
         * The class responsible for defining all actions in the admin area
         */
        require_once NERDSIQ_PLUGIN_DIR . 'admin/class-nerdsiq-admin.php';

        /**
         * The class responsible for defining all actions for the public-facing side
         */
        require_once NERDSIQ_PLUGIN_DIR . 'public/class-nerdsiq-public.php';

        $this->loader = new NerdsIQ_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * @since 1.0.0
     */
    private function set_locale() {
        $plugin_i18n = new NerdsIQ_i18n();

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     *
     * @since 1.0.0
     */
    private function define_admin_hooks() {
        $plugin_admin = new NerdsIQ_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );

        // AJAX handlers for admin
        $this->loader->add_action( 'wp_ajax_nerdsiq_test_connection', $plugin_admin, 'ajax_test_connection' );
        $this->loader->add_action( 'wp_ajax_nerdsiq_get_analytics', $plugin_admin, 'ajax_get_analytics' );
        $this->loader->add_action( 'wp_ajax_nerdsiq_export_data', $plugin_admin, 'ajax_export_data' );
        $this->loader->add_action( 'wp_ajax_nerdsiq_clear_logs', $plugin_admin, 'ajax_clear_logs' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     *
     * @since 1.0.0
     */
    private function define_public_hooks() {
        $plugin_public = new NerdsIQ_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action( 'wp_footer', $plugin_public, 'render_chat_widget' );

        // AJAX handlers for public
        $this->loader->add_action( 'wp_ajax_nerdsiq_send_message', $plugin_public, 'ajax_send_message' );
        $this->loader->add_action( 'wp_ajax_nerdsiq_get_history', $plugin_public, 'ajax_get_history' );
        $this->loader->add_action( 'wp_ajax_nerdsiq_reset_conversation', $plugin_public, 'ajax_reset_conversation' );
        $this->loader->add_action( 'wp_ajax_nerdsiq_log_event', $plugin_public, 'ajax_log_event' );
    }

    /**
     * Run the loader to execute all of the hooks.
     *
     * @since 1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin.
     *
     * @since  1.0.0
     * @return string
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks.
     *
     * @since  1.0.0
     * @return NerdsIQ_Loader
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since  1.0.0
     * @return string
     */
    public function get_version() {
        return $this->version;
    }
}
