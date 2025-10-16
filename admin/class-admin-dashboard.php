<?php
/**
 * Admin Dashboard Class
 * 
 * Handles the admin dashboard functionality for Campus Ambassador Manager
 * 
 * @package Campus_Ambassador_Manager
 * @subpackage Admin
 */

class CAM_Admin_Dashboard {
    
    /**
     * Initialize the dashboard
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_dashboard_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_dashboard_scripts'));
    }
    
    /**
     * Add dashboard menu to WordPress admin
     */
    public function add_dashboard_menu() {
        add_menu_page(
            'Campus Ambassador Manager',
            'Ambassador Manager',
            'manage_options',
            'cam-dashboard',
            array($this, 'render_dashboard'),
            'dashicons-groups',
            30
        );
    }
    
    /**
     * Enqueue dashboard scripts and styles
     */
    public function enqueue_dashboard_scripts($hook) {
        if ('toplevel_page_cam-dashboard' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'cam-admin-styles',
            plugin_dir_url(__FILE__) . 'css/admin-styles.css',
            array(),
            '1.0.0'
        );
    }
    
    /**
     * Render the dashboard page
     */
    public function render_dashboard() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        include_once plugin_dir_path(__FILE__) . 'partials/dashboard-display.php';
    }
    
    /**
     * Get dashboard statistics
     * 
     * @return array Dashboard statistics
     */
    public function get_dashboard_stats() {
        global $wpdb;
        
        $stats = array(
            'total_ambassadors' => 0,
            'pending_applications' => 0,
            'active_campaigns' => 0,
            'total_events' => 0
        );
        
        // TODO: Implement database queries to fetch actual statistics
        
        return $stats;
    }
}

// Initialize the dashboard
new CAM_Admin_Dashboard();
