<?php
/**
 * Admin Settings Page Template
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
    <h1><?php esc_html_e( 'NerdsIQ AI Assistant Settings', 'nerdsiq-ai-assistant' ); ?></h1>

    <?php settings_errors(); ?>

    <div class="nerdsiq-admin-container">
        <!-- Tabs -->
        <nav class="nerdsiq-tabs">
            <button class="nerdsiq-tab active" data-tab="general"><?php esc_html_e( 'General Settings', 'nerdsiq-ai-assistant' ); ?></button>
            <button class="nerdsiq-tab" data-tab="access"><?php esc_html_e( 'Access Control', 'nerdsiq-ai-assistant' ); ?></button>
            <button class="nerdsiq-tab" data-tab="appearance"><?php esc_html_e( 'Appearance', 'nerdsiq-ai-assistant' ); ?></button>
            <button class="nerdsiq-tab" data-tab="behavior"><?php esc_html_e( 'Behavior', 'nerdsiq-ai-assistant' ); ?></button>
            <button class="nerdsiq-tab" data-tab="advanced"><?php esc_html_e( 'Advanced', 'nerdsiq-ai-assistant' ); ?></button>
        </nav>

        <form method="post" action="options.php" class="nerdsiq-form">
            <?php settings_fields( 'nerdsiq_settings_group' ); ?>

            <!-- General Settings Tab -->
            <div id="tab-general" class="nerdsiq-tab-content active">
                <h2><?php esc_html_e( 'General Settings', 'nerdsiq-ai-assistant' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Enable Chatbot', 'nerdsiq-ai-assistant' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="nerdsiq_enabled" value="1" <?php checked( get_option( 'nerdsiq_enabled', true ), true ); ?> />
                                <?php esc_html_e( 'Enable the NerdsIQ AI Assistant chatbot', 'nerdsiq-ai-assistant' ); ?>
                            </label>
                        </td>
                    </tr>
                </table>

                <h3><?php esc_html_e( 'AWS Configuration', 'nerdsiq-ai-assistant' ); ?></h3>
                <p class="description">
                    <?php esc_html_e( 'Enter your AWS credentials and Q Business application details. These credentials are encrypted and stored securely.', 'nerdsiq-ai-assistant' ); ?>
                </p>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="nerdsiq_aws_access_key"><?php esc_html_e( 'AWS Access Key ID', 'nerdsiq-ai-assistant' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="nerdsiq_aws_access_key" name="nerdsiq_aws_access_key" value="<?php echo esc_attr( str_repeat( '*', 20 ) ); ?>" class="regular-text" autocomplete="off" />
                            <p class="description"><?php esc_html_e( 'Your AWS access key ID (encrypted when saved)', 'nerdsiq-ai-assistant' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="nerdsiq_aws_secret_key"><?php esc_html_e( 'AWS Secret Access Key', 'nerdsiq-ai-assistant' ); ?></label>
                        </th>
                        <td>
                            <input type="password" id="nerdsiq_aws_secret_key" name="nerdsiq_aws_secret_key" value="<?php echo esc_attr( str_repeat( '*', 40 ) ); ?>" class="regular-text" autocomplete="off" />
                            <p class="description"><?php esc_html_e( 'Your AWS secret access key (encrypted when saved)', 'nerdsiq-ai-assistant' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="nerdsiq_aws_region"><?php esc_html_e( 'AWS Region', 'nerdsiq-ai-assistant' ); ?></label>
                        </th>
                        <td>
                            <select id="nerdsiq_aws_region" name="nerdsiq_aws_region">
                                <option value="us-east-1" <?php selected( get_option( 'nerdsiq_aws_region', 'us-east-1' ), 'us-east-1' ); ?>>US East (N. Virginia)</option>
                                <option value="us-west-2" <?php selected( get_option( 'nerdsiq_aws_region' ), 'us-west-2' ); ?>>US West (Oregon)</option>
                                <option value="eu-west-1" <?php selected( get_option( 'nerdsiq_aws_region' ), 'eu-west-1' ); ?>>EU (Ireland)</option>
                                <option value="ap-southeast-1" <?php selected( get_option( 'nerdsiq_aws_region' ), 'ap-southeast-1' ); ?>>Asia Pacific (Singapore)</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="nerdsiq_qbusiness_app_id"><?php esc_html_e( 'Q Business Application ID', 'nerdsiq-ai-assistant' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="nerdsiq_qbusiness_app_id" name="nerdsiq_qbusiness_app_id" value="<?php echo esc_attr( get_option( 'nerdsiq_qbusiness_app_id', '' ) ); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e( 'Your AWS Q Business Application ID', 'nerdsiq-ai-assistant' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"></th>
                        <td>
                            <button type="button" id="nerdsiq-test-connection" class="button button-secondary">
                                <?php esc_html_e( 'Test Connection', 'nerdsiq-ai-assistant' ); ?>
                            </button>
                            <span id="nerdsiq-connection-status" class="nerdsiq-status"></span>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Access Control Tab -->
            <div id="tab-access" class="nerdsiq-tab-content">
                <h2><?php esc_html_e( 'Access Control', 'nerdsiq-ai-assistant' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Display on Pages', 'nerdsiq-ai-assistant' ); ?></th>
                        <td>
                            <label>
                                <input type="radio" name="nerdsiq_display_pages[]" value="all" <?php checked( in_array( 'all', get_option( 'nerdsiq_display_pages', array( 'all' ) ), true ), true ); ?> />
                                <?php esc_html_e( 'All pages', 'nerdsiq-ai-assistant' ); ?>
                            </label>
                            <p class="description"><?php esc_html_e( 'Choose where the chatbot should be displayed', 'nerdsiq-ai-assistant' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e( 'Allowed User Roles', 'nerdsiq-ai-assistant' ); ?></th>
                        <td>
                            <?php
                            global $wp_roles;
                            $roles = $wp_roles->get_names();
                            $allowed_roles = get_option( 'nerdsiq_allowed_roles', array( 'administrator', 'editor' ) );

                            foreach ( $roles as $role_key => $role_name ) :
                                ?>
                                <label style="display: block; margin-bottom: 5px;">
                                    <input type="checkbox" name="nerdsiq_allowed_roles[]" value="<?php echo esc_attr( $role_key ); ?>" <?php checked( in_array( $role_key, $allowed_roles, true ), true ); ?> />
                                    <?php echo esc_html( $role_name ); ?>
                                </label>
                            <?php endforeach; ?>
                            <p class="description"><?php esc_html_e( 'Select which user roles can access the chatbot', 'nerdsiq-ai-assistant' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Appearance Tab -->
            <div id="tab-appearance" class="nerdsiq-tab-content">
                <h2><?php esc_html_e( 'Appearance & Branding', 'nerdsiq-ai-assistant' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Widget Position', 'nerdsiq-ai-assistant' ); ?></th>
                        <td>
                            <label>
                                <input type="radio" name="nerdsiq_widget_position" value="bottom-right" <?php checked( get_option( 'nerdsiq_widget_position', 'bottom-right' ), 'bottom-right' ); ?> />
                                <?php esc_html_e( 'Bottom Right', 'nerdsiq-ai-assistant' ); ?>
                            </label>
                            <br>
                            <label>
                                <input type="radio" name="nerdsiq_widget_position" value="bottom-left" <?php checked( get_option( 'nerdsiq_widget_position' ), 'bottom-left' ); ?> />
                                <?php esc_html_e( 'Bottom Left', 'nerdsiq-ai-assistant' ); ?>
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="nerdsiq_button_text"><?php esc_html_e( 'Button Text', 'nerdsiq-ai-assistant' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="nerdsiq_button_text" name="nerdsiq_button_text" value="<?php echo esc_attr( get_option( 'nerdsiq_button_text', 'Ask NerdsIQ' ) ); ?>" class="regular-text" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="nerdsiq_primary_color"><?php esc_html_e( 'Primary Color', 'nerdsiq-ai-assistant' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="nerdsiq_primary_color" name="nerdsiq_primary_color" value="<?php echo esc_attr( get_option( 'nerdsiq_primary_color', '#0073aa' ) ); ?>" class="nerdsiq-color-picker" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="nerdsiq_welcome_message"><?php esc_html_e( 'Welcome Message', 'nerdsiq-ai-assistant' ); ?></label>
                        </th>
                        <td>
                            <textarea id="nerdsiq_welcome_message" name="nerdsiq_welcome_message" rows="3" class="large-text"><?php echo esc_textarea( get_option( 'nerdsiq_welcome_message', "Hi! I'm your NerdsIQ AI Assistant. How can I help you today?" ) ); ?></textarea>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Behavior Tab -->
            <div id="tab-behavior" class="nerdsiq-tab-content">
                <h2><?php esc_html_e( 'Behavior & Features', 'nerdsiq-ai-assistant' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Conversation Settings', 'nerdsiq-ai-assistant' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="nerdsiq_enable_history" value="1" <?php checked( get_option( 'nerdsiq_enable_history', true ), true ); ?> />
                                <?php esc_html_e( 'Enable conversation history', 'nerdsiq-ai-assistant' ); ?>
                            </label>
                            <br>
                            <label>
                                <input type="checkbox" name="nerdsiq_show_citations" value="1" <?php checked( get_option( 'nerdsiq_show_citations', true ), true ); ?> />
                                <?php esc_html_e( 'Show source citations', 'nerdsiq-ai-assistant' ); ?>
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="nerdsiq_rate_limit_hourly"><?php esc_html_e( 'Hourly Rate Limit', 'nerdsiq-ai-assistant' ); ?></label>
                        </th>
                        <td>
                            <input type="number" id="nerdsiq_rate_limit_hourly" name="nerdsiq_rate_limit_hourly" value="<?php echo esc_attr( get_option( 'nerdsiq_rate_limit_hourly', 50 ) ); ?>" min="0" class="small-text" />
                            <span class="description"><?php esc_html_e( 'messages per hour (0 = unlimited)', 'nerdsiq-ai-assistant' ); ?></span>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="nerdsiq_rate_limit_daily"><?php esc_html_e( 'Daily Rate Limit', 'nerdsiq-ai-assistant' ); ?></label>
                        </th>
                        <td>
                            <input type="number" id="nerdsiq_rate_limit_daily" name="nerdsiq_rate_limit_daily" value="<?php echo esc_attr( get_option( 'nerdsiq_rate_limit_daily', 250 ) ); ?>" min="0" class="small-text" />
                            <span class="description"><?php esc_html_e( 'messages per day (0 = unlimited)', 'nerdsiq-ai-assistant' ); ?></span>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Advanced Tab -->
            <div id="tab-advanced" class="nerdsiq-tab-content">
                <h2><?php esc_html_e( 'Advanced Settings', 'nerdsiq-ai-assistant' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Debug Mode', 'nerdsiq-ai-assistant' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="nerdsiq_debug_mode" value="1" <?php checked( get_option( 'nerdsiq_debug_mode', false ), true ); ?> />
                                <?php esc_html_e( 'Enable debug mode (logs will be written to WordPress debug.log)', 'nerdsiq-ai-assistant' ); ?>
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="nerdsiq_custom_css"><?php esc_html_e( 'Custom CSS', 'nerdsiq-ai-assistant' ); ?></label>
                        </th>
                        <td>
                            <textarea id="nerdsiq_custom_css" name="nerdsiq_custom_css" rows="10" class="large-text code"><?php echo esc_textarea( get_option( 'nerdsiq_custom_css', '' ) ); ?></textarea>
                            <p class="description"><?php esc_html_e( 'Add custom CSS to style the chat widget', 'nerdsiq-ai-assistant' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>

            <?php submit_button( __( 'Save Settings', 'nerdsiq-ai-assistant' ) ); ?>
        </form>
    </div>
</div>
