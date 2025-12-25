<?php
/**
 * Admin Usage Logs Page Template
 *
 * @package NerdsIQ_AI_Assistant
 * @since   1.0.0
 */

if ( ! defined( 'WPINC' ) ) { die; }

global $wpdb;

$per_page = 50;
$current_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
$offset = ( $current_page - 1 ) * $per_page;

$action_filter = isset( $_GET['action_filter'] ) ? sanitize_text_field( $_GET['action_filter'] ) : '';
$date_from = isset( $_GET['date_from'] ) ? sanitize_text_field( $_GET['date_from'] ) : '';
$date_to = isset( $_GET['date_to'] ) ? sanitize_text_field( $_GET['date_to'] ) : '';

$where = '1=1';
$params = array();

if ( ! empty( $action_filter ) ) {
    $where .= ' AND action = %s';
    $params[] = $action_filter;
}
if ( ! empty( $date_from ) ) {
    $where .= ' AND created_at >= %s';
    $params[] = $date_from . ' 00:00:00';
}
if ( ! empty( $date_to ) ) {
    $where .= ' AND created_at <= %s';
    $params[] = $date_to . ' 23:59:59';
}

$count_query = "SELECT COUNT(*) FROM {$wpdb->prefix}nerdsiq_usage_logs WHERE {$where}";
if ( ! empty( $params ) ) { $count_query = $wpdb->prepare( $count_query, $params ); }
$total_items = (int) $wpdb->get_var( $count_query );
$total_pages = ceil( $total_items / $per_page );

$query = "SELECT l.*, u.display_name, u.user_email FROM {$wpdb->prefix}nerdsiq_usage_logs l LEFT JOIN {$wpdb->users} u ON l.user_id = u.ID WHERE {$where} ORDER BY l.created_at DESC LIMIT %d OFFSET %d";
$query_params = array_merge( $params, array( $per_page, $offset ) );
$logs = $wpdb->get_results( $wpdb->prepare( $query, $query_params ) );

$actions = $wpdb->get_col( "SELECT DISTINCT action FROM {$wpdb->prefix}nerdsiq_usage_logs ORDER BY action" );
$unique_users = (int) $wpdb->get_var( "SELECT COUNT(DISTINCT user_id) FROM {$wpdb->prefix}nerdsiq_usage_logs" );
$today_count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}nerdsiq_usage_logs WHERE DATE(created_at) = %s", current_time( 'Y-m-d' ) ) );
?>
<div class="wrap nerdsiq-admin-wrap">
    <h1><?php esc_html_e( 'Usage Logs', 'nerdsiq-ai-assistant' ); ?></h1>
    <div style="background:#fff;padding:15px;margin:20px 0;border:1px solid #ccd0d4;border-radius:4px;">
        <form method="get"><input type="hidden" name="page" value="nerdsiq-usage-logs" />
            <label><strong>Action:</strong> <select name="action_filter"><option value="">All</option><?php foreach($actions as $a): ?><option value="<?php echo esc_attr($a); ?>" <?php selected($action_filter,$a); ?>><?php echo esc_html(ucwords(str_replace('_',' ',$a))); ?></option><?php endforeach; ?></select></label>
            <label style="margin-left:10px;"><strong>From:</strong> <input type="date" name="date_from" value="<?php echo esc_attr($date_from); ?>" /></label>
            <label style="margin-left:10px;"><strong>To:</strong> <input type="date" name="date_to" value="<?php echo esc_attr($date_to); ?>" /></label>
            <button type="submit" class="button" style="margin-left:10px;">Filter</button>
            <a href="<?php echo admin_url('admin.php?page=nerdsiq-usage-logs'); ?>" class="button">Reset</a>
        </form>
    </div>
    <div style="display:flex;gap:20px;margin-bottom:20px;">
        <div style="background:#fff;padding:20px;border:1px solid #ccd0d4;border-radius:4px;flex:1;text-align:center;"><div style="font-size:32px;font-weight:bold;color:#0047AC;"><?php echo number_format($total_items); ?></div><div style="color:#666;">Total Entries</div></div>
        <div style="background:#fff;padding:20px;border:1px solid #ccd0d4;border-radius:4px;flex:1;text-align:center;"><div style="font-size:32px;font-weight:bold;color:#0047AC;"><?php echo number_format($unique_users); ?></div><div style="color:#666;">Unique Users</div></div>
        <div style="background:#fff;padding:20px;border:1px solid #ccd0d4;border-radius:4px;flex:1;text-align:center;"><div style="font-size:32px;font-weight:bold;color:#0047AC;"><?php echo number_format($today_count); ?></div><div style="color:#666;">Today</div></div>
    </div>
    <table class="wp-list-table widefat fixed striped"><thead><tr><th style="width:50px;">ID</th><th style="width:150px;">Date/Time</th><th style="width:150px;">User</th><th style="width:120px;">Action</th><th>Page URL</th><th style="width:120px;">IP</th></tr></thead><tbody>
    <?php if(empty($logs)): ?><tr><td colspan="6" style="text-align:center;padding:40px;">No logs found.</td></tr>
    <?php else: foreach($logs as $log): ?>
    <tr><td><?php echo $log->id; ?></td><td><strong><?php echo date_i18n('M j, Y', strtotime($log->created_at)); ?></strong><br><small><?php echo date_i18n('g:i a', strtotime($log->created_at)); ?></small></td><td><?php echo $log->display_name ?: ($log->user_id ? 'User #'.$log->user_id : '<em>Guest</em>'); ?></td><td><span style="background:#e3f2fd;color:#0047AC;padding:3px 8px;border-radius:3px;font-size:12px;"><?php echo ucwords(str_replace('_',' ',$log->action)); ?></span></td><td style="word-break:break-all;"><?php echo $log->page_url ? '<a href="'.esc_url($log->page_url).'" target="_blank">'.esc_html(wp_parse_url($log->page_url,PHP_URL_PATH)?:$log->page_url).'</a>' : '—'; ?></td><td><code style="font-size:11px;"><?php echo $log->ip_address ?: '—'; ?></code></td></tr>
    <?php endforeach; endif; ?>
    </tbody></table>
    <?php if($total_pages > 1): ?><div class="tablenav bottom"><div class="tablenav-pages"><?php echo paginate_links(array('base'=>add_query_arg('paged','%#%'),'total'=>$total_pages,'current'=>$current_page)); ?></div></div><?php endif; ?>
</div>
