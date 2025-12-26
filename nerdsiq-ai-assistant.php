<?php
/**
 * Plugin Name:       NerdsIQ AI Assistant
 * Plugin URI:        https://nerdstogo.com/nerdsiq-ai-assistant
 * Description:       Secure WordPress integration with AWS Q Business chatbot for NerdsToGo team members. Provides instant access to company knowledge through an AI-powered assistant.
 * Version:           1.2.1
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            NerdsToGo
 * Author URI:        https://nerdstogo.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       nerdsiq-ai-assistant
 * Domain Path:       /languages
 *
 * @package NerdsIQ_AI_Assistant
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 */
define( 'NERDSIQ_VERSION', '1.2.1' );

/**
 * Plugin directory path
 */
define( 'NERDSIQ_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL
 */
define( 'NERDSIQ_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load Composer autoloader for AWS SDK
 */
if ( file_exists( NERDSIQ_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
    require_once NERDSIQ_PLUGIN_DIR . 'vendor/autoload.php';
}

/**
 * Plugin basename
 */
define( 'NERDSIQ_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Minimum WordPress version required
 */
define( 'NERDSIQ_MIN_WP_VERSION', '5.8' );

/**
 * Minimum PHP version required
 */
define( 'NERDSIQ_MIN_PHP_VERSION', '7.4' );

/**
 * The code that runs during plugin activation.
 */
function activate_nerdsiq_ai_assistant() {
    require_once NERDSIQ_PLUGIN_DIR . 'includes/class-nerdsiq-activator.php';
    NerdsIQ_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_nerdsiq_ai_assistant() {
    require_once NERDSIQ_PLUGIN_DIR . 'includes/class-nerdsiq-deactivator.php';
    NerdsIQ_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_nerdsiq_ai_assistant' );
register_deactivation_hook( __FILE__, 'deactivate_nerdsiq_ai_assistant' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require NERDSIQ_PLUGIN_DIR . 'includes/class-nerdsiq-ai-assistant.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_nerdsiq_ai_assistant() {
    $plugin = new NerdsIQ_AI_Assistant();
    $plugin->run();
}

// Check WordPress version
if ( version_compare( get_bloginfo( 'version' ), NERDSIQ_MIN_WP_VERSION, '<' ) ) {
    add_action( 'admin_notices', 'nerdsiq_wp_version_notice' );
    return;
}

// Check PHP version
if ( version_compare( PHP_VERSION, NERDSIQ_MIN_PHP_VERSION, '<' ) ) {
    add_action( 'admin_notices', 'nerdsiq_php_version_notice' );
    return;
}

// Check required PHP extensions
$required_extensions = array( 'curl', 'json', 'openssl', 'mbstring' );
$missing_extensions = array();

foreach ( $required_extensions as $extension ) {
    if ( ! extension_loaded( $extension ) ) {
        $missing_extensions[] = $extension;
    }
}

if ( ! empty( $missing_extensions ) ) {
    add_action( 'admin_notices', function() use ( $missing_extensions ) {
        nerdsiq_extension_notice( $missing_extensions );
    } );
    return;
}

// All checks passed, run the plugin
run_nerdsiq_ai_assistant();

/**
 * Display WordPress version notice
 */
function nerdsiq_wp_version_notice() {
    ?>
    <div class="notice notice-error">
        <p>
            <?php
            echo wp_kses_post(
                sprintf(
                    /* translators: 1: Required WordPress version, 2: Current WordPress version */
                    __( '<strong>NerdsIQ AI Assistant</strong> requires WordPress version %1$s or higher. You are running version %2$s. Please upgrade WordPress.', 'nerdsiq-ai-assistant' ),
                    NERDSIQ_MIN_WP_VERSION,
                    get_bloginfo( 'version' )
                )
            );
            ?>
        </p>
    </div>
    <?php
}

/**
 * Display PHP version notice
 */
function nerdsiq_php_version_notice() {
    ?>
    <div class="notice notice-error">
        <p>
            <?php
            echo wp_kses_post(
                sprintf(
                    /* translators: 1: Required PHP version, 2: Current PHP version */
                    __( '<strong>NerdsIQ AI Assistant</strong> requires PHP version %1$s or higher. You are running version %2$s. Please upgrade PHP.', 'nerdsiq-ai-assistant' ),
                    NERDSIQ_MIN_PHP_VERSION,
                    PHP_VERSION
                )
            );
            ?>
        </p>
    </div>
    <?php
}

/**
 * Display missing extension notice
 *
 * @param array $extensions Missing extensions.
 */
function nerdsiq_extension_notice( $extensions ) {
    ?>
    <div class="notice notice-error">
        <p>
            <?php
            echo wp_kses_post(
                sprintf(
                    /* translators: %s: Comma-separated list of missing PHP extensions */
                    __( '<strong>NerdsIQ AI Assistant</strong> requires the following PHP extensions: %s. Please enable them in your PHP configuration.', 'nerdsiq-ai-assistant' ),
                    implode( ', ', $extensions )
                )
            );
            ?>
        </p>
    </div>
    <?php
}
