<?php
/**
 * Admin System Status Page Template
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>

<div class="wrap nerdsiq-admin-wrap">
    <h1><?php esc_html_e( 'System Status', 'nerdsiq-ai-assistant' ); ?></h1>

    <p class="description">
        <?php esc_html_e( 'Check system health and configuration status.', 'nerdsiq-ai-assistant' ); ?>
    </p>

    <div class="nerdsiq-system-status">
        <h2><?php esc_html_e( 'Environment Information', 'nerdsiq-ai-assistant' ); ?></h2>
        <table class="widefat">
            <tbody>
                <tr>
                    <td><strong><?php esc_html_e( 'WordPress Version', 'nerdsiq-ai-assistant' ); ?></strong></td>
                    <td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'PHP Version', 'nerdsiq-ai-assistant' ); ?></strong></td>
                    <td><?php echo esc_html( PHP_VERSION ); ?></td>
                </tr>
                <tr>
                    <td><strong><?php esc_html_e( 'Plugin Version', 'nerdsiq-ai-assistant' ); ?></strong></td>
                    <td><?php echo esc_html( NERDSIQ_VERSION ); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
