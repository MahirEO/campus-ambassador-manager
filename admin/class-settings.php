<?php
/**
 * Settings Class
 * 
 * Handles plugin settings and configuration for Campus Ambassador Manager
 * 
 * @package Campus_Ambassador_Manager
 * @subpackage Admin
 */

class CAM_Settings {
    
    /**
     * Settings option name
     */
    private $option_name = 'cam_settings';
    
    /**
     * Initialize the settings
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }
    
    /**
     * Add settings submenu to admin menu
     */
    public function add_settings_menu() {
        add_submenu_page(
            'cam-dashboard',
            'Settings',
            'Settings',
            'manage_options',
            'cam-settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting(
            'cam_settings_group',
            $this->option_name,
            array($this, 'sanitize_settings')
        );
        
        // General Settings Section
        add_settings_section(
            'cam_general_settings',
            'General Settings',
            array($this, 'general_settings_callback'),
            'cam-settings'
        );
        
        // Email Settings Section
        add_settings_section(
            'cam_email_settings',
            'Email Settings',
            array($this, 'email_settings_callback'),
            'cam-settings'
        );
        
        // Add individual settings fields
        $this->add_settings_fields();
    }
    
    /**
     * Add individual settings fields
     */
    private function add_settings_fields() {
        // General Settings Fields
        add_settings_field(
            'enable_registration',
            'Enable Registration',
            array($this, 'checkbox_field_callback'),
            'cam-settings',
            'cam_general_settings',
            array('field_id' => 'enable_registration')
        );
        
        add_settings_field(
            'items_per_page',
            'Items Per Page',
            array($this, 'number_field_callback'),
            'cam-settings',
            'cam_general_settings',
            array('field_id' => 'items_per_page', 'default' => 10)
        );
        
        // Email Settings Fields
        add_settings_field(
            'admin_email',
            'Admin Email',
            array($this, 'email_field_callback'),
            'cam-settings',
            'cam_email_settings',
            array('field_id' => 'admin_email')
        );
        
        add_settings_field(
            'email_notifications',
            'Enable Email Notifications',
            array($this, 'checkbox_field_callback'),
            'cam-settings',
            'cam_email_settings',
            array('field_id' => 'email_notifications')
        );
    }
    
    /**
     * General settings section callback
     */
    public function general_settings_callback() {
        echo '<p>Configure general plugin settings.</p>';
    }
    
    /**
     * Email settings section callback
     */
    public function email_settings_callback() {
        echo '<p>Configure email notification settings.</p>';
    }
    
    /**
     * Checkbox field callback
     */
    public function checkbox_field_callback($args) {
        $options = get_option($this->option_name);
        $field_id = $args['field_id'];
        $checked = isset($options[$field_id]) ? checked($options[$field_id], 1, false) : '';
        
        echo '<input type="checkbox" id="' . esc_attr($field_id) . '" name="' . esc_attr($this->option_name) . '[' . esc_attr($field_id) . ']" value="1" ' . $checked . ' />';
    }
    
    /**
     * Number field callback
     */
    public function number_field_callback($args) {
        $options = get_option($this->option_name);
        $field_id = $args['field_id'];
        $default = isset($args['default']) ? $args['default'] : '';
        $value = isset($options[$field_id]) ? $options[$field_id] : $default;
        
        echo '<input type="number" id="' . esc_attr($field_id) . '" name="' . esc_attr($this->option_name) . '[' . esc_attr($field_id) . ']" value="' . esc_attr($value) . '" min="1" />';
    }
    
    /**
     * Email field callback
     */
    public function email_field_callback($args) {
        $options = get_option($this->option_name);
        $field_id = $args['field_id'];
        $value = isset($options[$field_id]) ? $options[$field_id] : '';
        
        echo '<input type="email" id="' . esc_attr($field_id) . '" name="' . esc_attr($this->option_name) . '[' . esc_attr($field_id) . ']" value="' . esc_attr($value) . '" class="regular-text" />';
    }
    
    /**
     * Sanitize settings before saving
     */
    public function sanitize_settings($input) {
        $sanitized = array();
        
        if (isset($input['enable_registration'])) {
            $sanitized['enable_registration'] = 1;
        }
        
        if (isset($input['items_per_page'])) {
            $sanitized['items_per_page'] = absint($input['items_per_page']);
        }
        
        if (isset($input['admin_email'])) {
            $sanitized['admin_email'] = sanitize_email($input['admin_email']);
        }
        
        if (isset($input['email_notifications'])) {
            $sanitized['email_notifications'] = 1;
        }
        
        return $sanitized;
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        include_once plugin_dir_path(__FILE__) . 'partials/settings-display.php';
    }
    
    /**
     * Get a setting value
     * 
     * @param string $key Setting key
     * @param mixed $default Default value
     * @return mixed Setting value
     */
    public function get_setting($key, $default = null) {
        $options = get_option($this->option_name);
        return isset($options[$key]) ? $options[$key] : $default;
    }
}

// Initialize the settings
new CAM_Settings();
