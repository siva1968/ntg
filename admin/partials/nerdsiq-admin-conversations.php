<?php
/**
 * Admin Conversations Page Template
 */
if ( ! defined( 'WPINC' ) ) { die; }

global $wpdb;

$per_page = 30;
$current_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
$offset = ( $current_page - 1 ) * $per_page;

$total_items = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}nerdsiq_conversations" );
$total_pages = ceil( $total_items / $per_page );

$conversations = $wpdb->get_results( $wpdb->prepare(
    "SELECT c.*, u.display_name, u.user_email FROM {$wpdb->prefix}nerdsiq_conversations c LEFT JOIN {$wpdb->users} u ON c.user_id = u.ID ORDER BY c.last_message_at DESC LIMIT %d OFFSET %d",
    $per_page, $offset
) );

$view_id = isset( $_GET['view'] ) ? intval( $_GET['view'] ) : 0;
$messages = array();
if ( $view_id ) {
    $messages = $wpdb->get_results( $wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}nerdsiq_messages WHERE conversation_id = %d ORDER BY created_at ASC",
        $view_id
    ) );
}

$total_convos = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}nerdsiq_conversations" );
$total_messages = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}nerdsiq_messages" );
$avg_messages = $total_convos > 0 ? round( $total_messages / $total_convos, 1 ) : 0;
?>
<div class="wrap nerdsiq-admin-wrap">
    <h1><?php esc_html_e( 'Conversations', 'nerdsiq-ai-assistant' ); ?></h1>
    
    <div style="display:flex;gap:20px;margin:20px 0;">
        <div style="background:#fff;padding:20px;border:1px solid #ccd0d4;border-radius:4px;flex:1;text-align:center;"><div style="font-size:32px;font-weight:bold;color:#0047AC;"><?php echo number_format($total_convos); ?></div><div style="color:#666;">Total Conversations</div></div>
        <div style="background:#fff;padding:20px;border:1px solid #ccd0d4;border-radius:4px;flex:1;text-align:center;"><div style="font-size:32px;font-weight:bold;color:#0047AC;"><?php echo number_format($total_messages); ?></div><div style="color:#666;">Total Messages</div></div>
        <div style="background:#fff;padding:20px;border:1px solid #ccd0d4;border-radius:4px;flex:1;text-align:center;"><div style="font-size:32px;font-weight:bold;color:#0047AC;"><?php echo $avg_messages; ?></div><div style="color:#666;">Avg Messages/Convo</div></div>
    </div>

    <?php if ( $view_id && ! empty( $messages ) ) : ?>
    <div style="background:#fff;padding:20px;border:1px solid #ccd0d4;border-radius:4px;margin-bottom:20px;">
        <h2 style="margin-top:0;">Conversation #<?php echo $view_id; ?> <a href="<?php echo admin_url('admin.php?page=nerdsiq-conversations'); ?>" class="button" style="margin-left:10px;">← Back</a></h2>
        <div style="max-height:500px;overflow-y:auto;border:1px solid #eee;padding:15px;background:#f9f9f9;">
        <?php foreach ( $messages as $msg ) : ?>
            <div style="margin-bottom:15px;padding:10px;border-radius:8px;<?php echo $msg->message_type === 'user' ? 'background:#0047AC;color:#fff;margin-left:20%;' : 'background:#fff;border:1px solid #ddd;margin-right:20%;'; ?>">
                <div style="font-size:11px;opacity:0.7;margin-bottom:5px;"><?php echo $msg->message_type === 'user' ? 'User' : 'Assistant'; ?> • <?php echo date_i18n('M j, g:i a', strtotime($msg->created_at)); ?></div>
                <div><?php echo nl2br(esc_html($msg->message_content)); ?></div>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <table class="wp-list-table widefat fixed striped"><thead><tr><th style="width:50px;">ID</th><th style="width:180px;">Started</th><th style="width:180px;">Last Message</th><th style="width:150px;">User</th><th style="width:80px;">Messages</th><th style="width:100px;">Status</th><th style="width:100px;">Actions</th></tr></thead><tbody>
    <?php if(empty($conversations)): ?><tr><td colspan="7" style="text-align:center;padding:40px;">No conversations found.</td></tr>
    <?php else: foreach($conversations as $c): ?>
    <tr><td><?php echo $c->id; ?></td><td><?php echo date_i18n('M j, Y g:i a', strtotime($c->started_at)); ?></td><td><?php echo date_i18n('M j, Y g:i a', strtotime($c->last_message_at)); ?></td><td><?php echo $c->display_name ?: ($c->user_id ? 'User #'.$c->user_id : '<em>Guest</em>'); ?></td><td style="text-align:center;"><strong><?php echo $c->message_count; ?></strong></td><td><span style="background:<?php echo $c->status==='active'?'#c8e6c9':'#e0e0e0'; ?>;padding:3px 8px;border-radius:3px;font-size:12px;"><?php echo ucfirst($c->status); ?></span></td><td><a href="<?php echo admin_url('admin.php?page=nerdsiq-conversations&view='.$c->id); ?>" class="button button-small">View</a></td></tr>
    <?php endforeach; endif; ?>
    </tbody></table>
    <?php if($total_pages > 1): ?><div class="tablenav bottom"><div class="tablenav-pages"><?php echo paginate_links(array('base'=>add_query_arg('paged','%#%'),'total'=>$total_pages,'current'=>$current_page)); ?></div></div><?php endif; ?>
</div>
