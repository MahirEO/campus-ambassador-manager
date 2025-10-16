<?php
/**
 * Admin Dashboard for Campus Ambassador Manager
 * Displays all applications with filtering and management options
 * 
 * @package Campus_Ambassador_Manager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check user capabilities
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.', 'campus-ambassador-manager'));
}

// Handle status update
if (isset($_POST['cam_update_status']) && isset($_POST['application_id']) && isset($_POST['new_status'])) {
    check_admin_referer('cam_update_status');
    $id = intval($_POST['application_id']);
    $status = sanitize_text_field($_POST['new_status']);
    CAM_Form_Handler::update_status($id, $status);
    echo '<div class="notice notice-success"><p>' . __('Status updated successfully!', 'campus-ambassador-manager') . '</p></div>';
}

// Handle delete
if (isset($_POST['cam_delete_application']) && isset($_POST['application_id'])) {
    check_admin_referer('cam_delete_application');
    $id = intval($_POST['application_id']);
    CAM_Form_Handler::delete_application($id);
    echo '<div class="notice notice-success"><p>' . __('Application deleted successfully!', 'campus-ambassador-manager') . '</p></div>';
}

// Get filter status
$filter_status = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : null;

// Get applications
$applications = CAM_Form_Handler::get_applications($filter_status);

// Count by status
global $wpdb;
$table_name = $wpdb->prefix . 'campus_ambassadors';
$status_counts = $wpdb->get_results(
    "SELECT status, COUNT(*) as count FROM $table_name GROUP BY status",
    OBJECT_K
);

$total_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
?>

<div class="wrap cam-dashboard">
    <h1><?php _e('Campus Ambassador Applications', 'campus-ambassador-manager'); ?></h1>
    
    <!-- Status Filter Tabs -->
    <ul class="subsubsub">
        <li>
            <a href="<?php echo admin_url('admin.php?page=campus-ambassadors'); ?>" 
               class="<?php echo !$filter_status ? 'current' : ''; ?>">
                <?php _e('All', 'campus-ambassador-manager'); ?>
                <span class="count">(<?php echo $total_count; ?>)</span>
            </a> |
        </li>
        <li>
            <a href="<?php echo admin_url('admin.php?page=campus-ambassadors&status=pending'); ?>"
               class="<?php echo $filter_status === 'pending' ? 'current' : ''; ?>">
                <?php _e('Pending', 'campus-ambassador-manager'); ?>
                <span class="count">(<?php echo isset($status_counts['pending']) ? $status_counts['pending']->count : 0; ?>)</span>
            </a> |
        </li>
        <li>
            <a href="<?php echo admin_url('admin.php?page=campus-ambassadors&status=verified'); ?>"
               class="<?php echo $filter_status === 'verified' ? 'current' : ''; ?>">
                <?php _e('Verified', 'campus-ambassador-manager'); ?>
                <span class="count">(<?php echo isset($status_counts['verified']) ? $status_counts['verified']->count : 0; ?>)</span>
            </a> |
        </li>
        <li>
            <a href="<?php echo admin_url('admin.php?page=campus-ambassadors&status=approved'); ?>"
               class="<?php echo $filter_status === 'approved' ? 'current' : ''; ?>">
                <?php _e('Approved', 'campus-ambassador-manager'); ?>
                <span class="count">(<?php echo isset($status_counts['approved']) ? $status_counts['approved']->count : 0; ?>)</span>
            </a> |
        </li>
        <li>
            <a href="<?php echo admin_url('admin.php?page=campus-ambassadors&status=rejected'); ?>"
               class="<?php echo $filter_status === 'rejected' ? 'current' : ''; ?>">
                <?php _e('Rejected', 'campus-ambassador-manager'); ?>
                <span class="count">(<?php echo isset($status_counts['rejected']) ? $status_counts['rejected']->count : 0; ?>)</span>
            </a>
        </li>
    </ul>
    
    <br class="clear">
    
    <!-- Applications Table -->
    <?php if (empty($applications)) : ?>
        <p><?php _e('No applications found.', 'campus-ambassador-manager'); ?></p>
    <?php else : ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('ID', 'campus-ambassador-manager'); ?></th>
                    <th><?php _e('Name', 'campus-ambassador-manager'); ?></th>
                    <th><?php _e('Email', 'campus-ambassador-manager'); ?></th>
                    <th><?php _e('Phone', 'campus-ambassador-manager'); ?></th>
                    <th><?php _e('University', 'campus-ambassador-manager'); ?></th>
                    <th><?php _e('Major', 'campus-ambassador-manager'); ?></th>
                    <th><?php _e('Year', 'campus-ambassador-manager'); ?></th>
                    <th><?php _e('Status', 'campus-ambassador-manager'); ?></th>
                    <th><?php _e('Verified', 'campus-ambassador-manager'); ?></th>
                    <th><?php _e('Date', 'campus-ambassador-manager'); ?></th>
                    <th><?php _e('Actions', 'campus-ambassador-manager'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $app) : ?>
                    <tr>
                        <td><?php echo esc_html($app->id); ?></td>
                        <td><?php echo esc_html($app->name); ?></td>
                        <td><?php echo esc_html($app->email); ?></td>
                        <td><?php echo esc_html($app->phone); ?></td>
                        <td><?php echo esc_html($app->university); ?></td>
                        <td><?php echo esc_html($app->major); ?></td>
                        <td><?php echo esc_html($app->year); ?></td>
                        <td>
                            <span class="cam-status cam-status-<?php echo esc_attr($app->status); ?>">
                                <?php echo esc_html(ucfirst($app->status)); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($app->verified) : ?>
                                <span class="dashicons dashicons-yes-alt" style="color: green;"></span>
                            <?php else : ?>
                                <span class="dashicons dashicons-dismiss" style="color: red;"></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html(date_i18n(get_option('date_format'), strtotime($app->created_at))); ?></td>
                        <td>
                            <!-- Status Update Form -->
                            <form method="post" style="display: inline-block;">
                                <?php wp_nonce_field('cam_update_status'); ?>
                                <input type="hidden" name="application_id" value="<?php echo esc_attr($app->id); ?>">
                                <select name="new_status" onchange="this.form.submit()" style="font-size: 12px;">
                                    <option value=""><?php _e('Change Status', 'campus-ambassador-manager'); ?></option>
                                    <option value="pending" <?php selected($app->status, 'pending'); ?>><?php _e('Pending', 'campus-ambassador-manager'); ?></option>
                                    <option value="verified" <?php selected($app->status, 'verified'); ?>><?php _e('Verified', 'campus-ambassador-manager'); ?></option>
                                    <option value="approved" <?php selected($app->status, 'approved'); ?>><?php _e('Approved', 'campus-ambassador-manager'); ?></option>
                                    <option value="rejected" <?php selected($app->status, 'rejected'); ?>><?php _e('Rejected', 'campus-ambassador-manager'); ?></option>
                                </select>
                                <input type="hidden" name="cam_update_status" value="1">
                            </form>
                            
                            <!-- View Details Button -->
                            <button type="button" class="button button-small" 
                                    onclick="jQuery('#details-<?php echo $app->id; ?>').toggle();">
                                <?php _e('View Details', 'campus-ambassador-manager'); ?>
                            </button>
                            
                            <!-- Delete Form -->
                            <form method="post" style="display: inline-block;" 
                                  onsubmit="return confirm('<?php _e('Are you sure you want to delete this application?', 'campus-ambassador-manager'); ?>');">
                                <?php wp_nonce_field('cam_delete_application'); ?>
                                <input type="hidden" name="application_id" value="<?php echo esc_attr($app->id); ?>">
                                <input type="hidden" name="cam_delete_application" value="1">
                                <button type="submit" class="button button-small button-link-delete">
                                    <?php _e('Delete', 'campus-ambassador-manager'); ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    
                    <!-- Hidden Details Row -->
                    <tr id="details-<?php echo $app->id; ?>" style="display: none;">
                        <td colspan="11" style="background: #f9f9f9; padding: 20px;">
                            <h3><?php _e('Application Details', 'campus-ambassador-manager'); ?></h3>
                            <p><strong><?php _e('Name:', 'campus-ambassador-manager'); ?></strong> <?php echo esc_html($app->name); ?></p>
                            <p><strong><?php _e('Email:', 'campus-ambassador-manager'); ?></strong> <?php echo esc_html($app->email); ?></p>
                            <p><strong><?php _e('Phone:', 'campus-ambassador-manager'); ?></strong> <?php echo esc_html($app->phone); ?></p>
                            <p><strong><?php _e('University:', 'campus-ambassador-manager'); ?></strong> <?php echo esc_html($app->university); ?></p>
                            <p><strong><?php _e('Major:', 'campus-ambassador-manager'); ?></strong> <?php echo esc_html($app->major); ?></p>
                            <p><strong><?php _e('Year:', 'campus-ambassador-manager'); ?></strong> <?php echo esc_html($app->year); ?></p>
                            <p><strong><?php _e('Motivation:', 'campus-ambassador-manager'); ?></strong></p>
                            <p><?php echo nl2br(esc_html($app->motivation)); ?></p>
                            <p><strong><?php _e('Status:', 'campus-ambassador-manager'); ?></strong> <?php echo esc_html(ucfirst($app->status)); ?></p>
                            <p><strong><?php _e('Verified:', 'campus-ambassador-manager'); ?></strong> <?php echo $app->verified ? __('Yes', 'campus-ambassador-manager') : __('No', 'campus-ambassador-manager'); ?></p>
                            <p><strong><?php _e('Applied on:', 'campus-ambassador-manager'); ?></strong> <?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($app->created_at))); ?></p>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<style>
.cam-dashboard {
    margin: 20px;
}

.cam-status {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
}

.cam-status-pending {
    background: #fef8e7;
    color: #b7791f;
}

.cam-status-verified {
    background: #e8f5e9;
    color: #2e7d32;
}

.cam-status-approved {
    background: #e3f2fd;
    color: #1565c0;
}

.cam-status-rejected {
    background: #ffebee;
    color: #c62828;
}
</style>
