<?php
/**
 * Admin Analytics Page Template
 */
if ( ! defined( 'WPINC' ) ) { die; }

global $wpdb;

$period = isset( $_GET['period'] ) ? sanitize_text_field( $_GET['period'] ) : '30';
$date_from = date( 'Y-m-d', strtotime( "-{$period} days" ) );

// Get stats
$total_conversations = (int) $wpdb->get_var( $wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}nerdsiq_conversations WHERE started_at >= %s", $date_from
) );
$total_messages = (int) $wpdb->get_var( $wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}nerdsiq_messages WHERE created_at >= %s", $date_from
) );
$unique_users = (int) $wpdb->get_var( $wpdb->prepare(
    "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->prefix}nerdsiq_conversations WHERE started_at >= %s", $date_from
) );
$avg_response_time = (float) $wpdb->get_var( $wpdb->prepare(
    "SELECT AVG(response_time) FROM {$wpdb->prefix}nerdsiq_messages WHERE message_type = 'assistant' AND created_at >= %s", $date_from
) );
$widget_opens = (int) $wpdb->get_var( $wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}nerdsiq_usage_logs WHERE action = 'widget_opened' AND created_at >= %s", $date_from
) );

// Daily stats for chart
$daily_stats = $wpdb->get_results( $wpdb->prepare(
    "SELECT DATE(created_at) as date, COUNT(*) as count FROM {$wpdb->prefix}nerdsiq_messages WHERE created_at >= %s GROUP BY DATE(created_at) ORDER BY date",
    $date_from
) );

// Top actions
$top_actions = $wpdb->get_results( $wpdb->prepare(
    "SELECT action, COUNT(*) as count FROM {$wpdb->prefix}nerdsiq_usage_logs WHERE created_at >= %s GROUP BY action ORDER BY count DESC LIMIT 10",
    $date_from
) );
?>
<div class="wrap nerdsiq-admin-wrap">
    <h1><?php esc_html_e( 'Analytics & Statistics', 'nerdsiq-ai-assistant' ); ?></h1>
    
    <!-- Period Filter -->
    <div style="background:#fff;padding:15px;margin:20px 0;border:1px solid #ccd0d4;border-radius:4px;">
        <form method="get"><input type="hidden" name="page" value="nerdsiq-analytics" />
            <strong>Time Period:</strong>
            <select name="period" onchange="this.form.submit()">
                <option value="7" <?php selected($period,'7'); ?>>Last 7 days</option>
                <option value="30" <?php selected($period,'30'); ?>>Last 30 days</option>
                <option value="90" <?php selected($period,'90'); ?>>Last 90 days</option>
                <option value="365" <?php selected($period,'365'); ?>>Last year</option>
            </select>
        </form>
    </div>

    <!-- Stats Cards -->
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:20px;margin-bottom:30px;">
        <div style="background:#fff;padding:25px;border:1px solid #ccd0d4;border-radius:8px;text-align:center;">
            <div style="font-size:36px;font-weight:bold;color:#0047AC;"><?php echo number_format($total_conversations); ?></div>
            <div style="color:#666;margin-top:5px;">Conversations</div>
        </div>
        <div style="background:#fff;padding:25px;border:1px solid #ccd0d4;border-radius:8px;text-align:center;">
            <div style="font-size:36px;font-weight:bold;color:#0047AC;"><?php echo number_format($total_messages); ?></div>
            <div style="color:#666;margin-top:5px;">Messages</div>
        </div>
        <div style="background:#fff;padding:25px;border:1px solid #ccd0d4;border-radius:8px;text-align:center;">
            <div style="font-size:36px;font-weight:bold;color:#0047AC;"><?php echo number_format($unique_users); ?></div>
            <div style="color:#666;margin-top:5px;">Active Users</div>
        </div>
        <div style="background:#fff;padding:25px;border:1px solid #ccd0d4;border-radius:8px;text-align:center;">
            <div style="font-size:36px;font-weight:bold;color:#0047AC;"><?php echo number_format($widget_opens); ?></div>
            <div style="color:#666;margin-top:5px;">Widget Opens</div>
        </div>
        <div style="background:#fff;padding:25px;border:1px solid #ccd0d4;border-radius:8px;text-align:center;">
            <div style="font-size:36px;font-weight:bold;color:#0047AC;"><?php echo $avg_response_time ? round($avg_response_time/1000, 2).'s' : '—'; ?></div>
            <div style="color:#666;margin-top:5px;">Avg Response</div>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">
        <!-- Daily Messages Chart -->
        <div style="background:#fff;padding:20px;border:1px solid #ccd0d4;border-radius:8px;">
            <h3 style="margin-top:0;">Messages Over Time</h3>
            <div style="height:250px;display:flex;align-items:flex-end;gap:2px;padding:10px 0;">
                <?php 
                $max_count = max( array_column( $daily_stats, 'count' ) ) ?: 1;
                foreach ( $daily_stats as $day ) :
                    $height = ( $day->count / $max_count ) * 200;
                ?>
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;">
                    <div style="background:#0047AC;width:100%;height:<?php echo max(2, $height); ?>px;border-radius:2px 2px 0 0;" title="<?php echo $day->date . ': ' . $day->count; ?>"></div>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align:center;color:#666;font-size:12px;">Daily message volume</div>
        </div>

        <!-- Top Actions -->
        <div style="background:#fff;padding:20px;border:1px solid #ccd0d4;border-radius:8px;">
            <h3 style="margin-top:0;">Top Actions</h3>
            <?php if ( empty( $top_actions ) ) : ?>
                <p style="color:#666;">No activity data yet.</p>
            <?php else : ?>
                <table style="width:100%;">
                    <?php foreach ( $top_actions as $action ) : ?>
                    <tr>
                        <td style="padding:8px 0;"><span style="background:#e3f2fd;color:#0047AC;padding:3px 8px;border-radius:3px;font-size:12px;"><?php echo ucwords(str_replace('_',' ',$action->action)); ?></span></td>
                        <td style="text-align:right;font-weight:bold;"><?php echo number_format($action->count); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Export Section -->
    <div style="background:#fff;padding:20px;border:1px solid #ccd0d4;border-radius:8px;margin-top:20px;">
        <h3 style="margin-top:0;">Export Data</h3>
        <p>Download your data for further analysis.</p>
        <button class="button button-primary" onclick="alert('Export feature - implement AJAX call to nerdsiq_export_data');">Export Conversations (CSV)</button>
        <button class="button" onclick="alert('Export feature - implement AJAX call to nerdsiq_export_data');" style="margin-left:10px;">Export Usage Logs (CSV)</button>
    </div>
</div>
