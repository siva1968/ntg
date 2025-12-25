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
                            <button type="button" id="nerdsiq-save-credentials" class="button button-primary">
                                <?php esc_html_e( 'Save AWS Credentials', 'nerdsiq-ai-assistant' ); ?>
                            </button>
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
                        <th scope="row"><?php esc_html_e( 'Display Mode', 'nerdsiq-ai-assistant' ); ?></th>
                        <td>
                            <?php $display_mode = get_option( 'nerdsiq_display_mode', 'all' ); ?>
                            <label>
                                <input type="radio" name="nerdsiq_display_mode" value="all" <?php checked( $display_mode, 'all' ); ?> />
                                <?php esc_html_e( 'All pages', 'nerdsiq-ai-assistant' ); ?>
                            </label>
                            <br>
                            <label>
                                <input type="radio" name="nerdsiq_display_mode" value="selected" <?php checked( $display_mode, 'selected' ); ?> />
                                <?php esc_html_e( 'Selected pages only', 'nerdsiq-ai-assistant' ); ?>
                            </label>
                            <br>
                            <label>
                                <input type="radio" name="nerdsiq_display_mode" value="exclude" <?php checked( $display_mode, 'exclude' ); ?> />
                                <?php esc_html_e( 'All pages except selected', 'nerdsiq-ai-assistant' ); ?>
                            </label>
                            <p class="description"><?php esc_html_e( 'Choose where the chatbot should be displayed', 'nerdsiq-ai-assistant' ); ?></p>
                        </td>
                    </tr>

                    <tr id="nerdsiq-page-selection-row" style="<?php echo $display_mode === 'all' ? 'display:none;' : ''; ?>">
                        <th scope="row"><?php esc_html_e( 'Select Pages', 'nerdsiq-ai-assistant' ); ?></th>
                        <td>
                            <?php
                            $selected_pages = get_option( 'nerdsiq_selected_pages', array() );
                            $pages = get_pages( array( 'sort_column' => 'post_title', 'sort_order' => 'ASC' ) );
                            
                            if ( ! empty( $pages ) ) :
                                echo '<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff;">';
                                foreach ( $pages as $page ) :
                                    ?>
                                    <label style="display: block; margin-bottom: 5px;">
                                        <input type="checkbox" name="nerdsiq_selected_pages[]" value="<?php echo esc_attr( $page->ID ); ?>" <?php checked( in_array( $page->ID, (array) $selected_pages, false ), true ); ?> />
                                        <?php echo esc_html( $page->post_title ); ?>
                                    </label>
                                <?php 
                                endforeach;
                                echo '</div>';
                            else :
                                echo '<p>' . esc_html__( 'No pages found.', 'nerdsiq-ai-assistant' ) . '</p>';
                            endif;
                            ?>

                            <h4 style="margin-top: 15px;"><?php esc_html_e( 'Posts', 'nerdsiq-ai-assistant' ); ?></h4>
                            <?php
                            $posts = get_posts( array( 'numberposts' => 50, 'orderby' => 'title', 'order' => 'ASC' ) );
                            
                            if ( ! empty( $posts ) ) :
                                echo '<div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff;">';
                                foreach ( $posts as $post ) :
                                    ?>
                                    <label style="display: block; margin-bottom: 5px;">
                                        <input type="checkbox" name="nerdsiq_selected_pages[]" value="<?php echo esc_attr( $post->ID ); ?>" <?php checked( in_array( $post->ID, (array) $selected_pages, false ), true ); ?> />
                                        <?php echo esc_html( $post->post_title ); ?>
                                    </label>
                                <?php 
                                endforeach;
                                echo '</div>';
                            else :
                                echo '<p>' . esc_html__( 'No posts found.', 'nerdsiq-ai-assistant' ) . '</p>';
                            endif;
                            ?>

                            <h4 style="margin-top: 15px;"><?php esc_html_e( 'Special Pages', 'nerdsiq-ai-assistant' ); ?></h4>
                            <label style="display: block; margin-bottom: 5px;">
                                <input type="checkbox" name="nerdsiq_selected_pages[]" value="home" <?php checked( in_array( 'home', (array) $selected_pages, true ), true ); ?> />
                                <?php esc_html_e( 'Homepage / Front Page', 'nerdsiq-ai-assistant' ); ?>
                            </label>
                            <label style="display: block; margin-bottom: 5px;">
                                <input type="checkbox" name="nerdsiq_selected_pages[]" value="blog" <?php checked( in_array( 'blog', (array) $selected_pages, true ), true ); ?> />
                                <?php esc_html_e( 'Blog / Posts Page', 'nerdsiq-ai-assistant' ); ?>
                            </label>
                            <label style="display: block; margin-bottom: 5px;">
                                <input type="checkbox" name="nerdsiq_selected_pages[]" value="archive" <?php checked( in_array( 'archive', (array) $selected_pages, true ), true ); ?> />
                                <?php esc_html_e( 'Archive Pages', 'nerdsiq-ai-assistant' ); ?>
                            </label>
                            <label style="display: block; margin-bottom: 5px;">
                                <input type="checkbox" name="nerdsiq_selected_pages[]" value="search" <?php checked( in_array( 'search', (array) $selected_pages, true ), true ); ?> />
                                <?php esc_html_e( 'Search Results', 'nerdsiq-ai-assistant' ); ?>
                            </label>

                            <p class="description"><?php esc_html_e( 'Select which pages should display (or not display) the chatbot based on the mode selected above.', 'nerdsiq-ai-assistant' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e( 'Require Login', 'nerdsiq-ai-assistant' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="nerdsiq_require_login" value="1" <?php checked( get_option( 'nerdsiq_require_login', '0' ), '1' ); ?> />
                                <?php esc_html_e( 'Only show chatbot to logged-in users', 'nerdsiq-ai-assistant' ); ?>
                            </label>
                            <p class="description"><?php esc_html_e( 'When unchecked, guests can also use the chatbot.', 'nerdsiq-ai-assistant' ); ?></p>
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
                            <label for="nerdsiq_logo_url"><?php esc_html_e( 'Logo URL', 'nerdsiq-ai-assistant' ); ?></label>
                        </th>
                        <td>
                            <input type="url" id="nerdsiq_logo_url" name="nerdsiq_logo_url" value="<?php echo esc_attr( get_option( 'nerdsiq_logo_url', '' ) ); ?>" class="large-text" placeholder="https://example.com/logo.png" />
                            <p class="description"><?php esc_html_e( 'URL to the logo image displayed in the chat header and button. Leave empty to use default icon.', 'nerdsiq-ai-assistant' ); ?></p>
                            <?php if ( $logo_url = get_option( 'nerdsiq_logo_url', '' ) ) : ?>
                                <p><img src="<?php echo esc_url( $logo_url ); ?>" alt="Logo Preview" style="max-width: 100px; max-height: 50px; margin-top: 10px;" /></p>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="nerdsiq_header_title"><?php esc_html_e( 'Header Title', 'nerdsiq-ai-assistant' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="nerdsiq_header_title" name="nerdsiq_header_title" value="<?php echo esc_attr( get_option( 'nerdsiq_header_title', 'NerdsIQ AI Assistant' ) ); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e( 'Title displayed in the chat window header.', 'nerdsiq-ai-assistant' ); ?></p>
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

                    <tr>
                        <th scope="row">
                            <label for="nerdsiq_input_placeholder"><?php esc_html_e( 'Input Placeholder', 'nerdsiq-ai-assistant' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="nerdsiq_input_placeholder" name="nerdsiq_input_placeholder" value="<?php echo esc_attr( get_option( 'nerdsiq_input_placeholder', 'Type your message...' ) ); ?>" class="regular-text" />
                            <p class="description"><?php esc_html_e( 'Placeholder text shown in the message input field', 'nerdsiq-ai-assistant' ); ?></p>
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

<script>
jQuery(document).ready(function($) {
    // Show/hide page selection based on display mode
    function togglePageSelection() {
        var mode = $('input[name="nerdsiq_display_mode"]:checked').val();
        if (mode === 'all') {
            $('#nerdsiq-page-selection-row').hide();
        } else {
            $('#nerdsiq-page-selection-row').show();
        }
    }

    // Initial state
    togglePageSelection();

    // On change
    $('input[name="nerdsiq_display_mode"]').on('change', togglePageSelection);
});
</script>