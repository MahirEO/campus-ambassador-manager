<?php
/**
 * Plugin Name: Campus Ambassador Manager
 * Plugin URI: https://github.com/MahirEO/campus-ambassador-manager
 * Description: A comprehensive plugin to manage campus ambassador applications, verification, and profiles with an interactive dashboard.
 * Version: 1.0.0
 * Author: MahirEO
 * Author URI: https://github.com/MahirEO
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: campus-ambassador-manager
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CAM_VERSION', '1.0.0');
define('CAM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CAM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CAM_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Campus Ambassador Manager Class
 */
class Campus_Ambassador_Manager {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_assets'));
    }
    
    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        require_once CAM_PLUGIN_DIR . 'includes/form-handler.php';
        require_once CAM_PLUGIN_DIR . 'admin/dashboard.php';
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'campus_ambassadors';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            phone varchar(20) DEFAULT NULL,
            university varchar(255) NOT NULL,
            major varchar(255) DEFAULT NULL,
            year varchar(50) DEFAULT NULL,
            motivation text DEFAULT NULL,
            status varchar(50) DEFAULT 'pending',
            verification_code varchar(100) DEFAULT NULL,
            verified tinyint(1) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY email (email)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Set default options
        add_option('cam_email_notifications', '1');
        add_option('cam_require_verification', '1');
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Cleanup tasks if needed
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Register custom post types, taxonomies, etc.
        load_plugin_textdomain('campus-ambassador-manager', false, dirname(CAM_PLUGIN_BASENAME) . '/languages');
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Campus Ambassadors', 'campus-ambassador-manager'),
            __('Ambassadors', 'campus-ambassador-manager'),
            'manage_options',
            'campus-ambassadors',
            array($this, 'render_admin_dashboard'),
            'dashicons-groups',
            30
        );
    }
    
    /**
     * Render admin dashboard
     */
    public function render_admin_dashboard() {
        include CAM_PLUGIN_DIR . 'admin/dashboard.php';
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ('toplevel_page_campus-ambassadors' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'cam-admin-style',
            CAM_PLUGIN_URL . 'public/css/style.css',
            array(),
            CAM_VERSION
        );
    }
    
    /**
     * Enqueue public assets
     */
    public function enqueue_public_assets() {
        wp_enqueue_style(
            'cam-public-style',
            CAM_PLUGIN_URL . 'public/css/style.css',
            array(),
            CAM_VERSION
        );
        
        wp_enqueue_script(
            'cam-form-script',
            CAM_PLUGIN_URL . 'public/js/form.js',
            array('jquery'),
            CAM_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('cam-form-script', 'camAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('cam_form_nonce')
        ));
    }
}

// Initialize the plugin
function campus_ambassador_manager_init() {
    return Campus_Ambassador_Manager::get_instance();
}
add_action('plugins_loaded', 'campus_ambassador_manager_init');

// Shortcode to display application form
function cam_application_form_shortcode($atts) {
    ob_start();
    include CAM_PLUGIN_DIR . 'templates/ambassador-card-template.php';
    return ob_get_clean();
}
add_shortcode('campus_ambassador_form', 'cam_application_form_shortcode');
