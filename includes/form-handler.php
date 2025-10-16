<?php
/**
 * Form Handler for Campus Ambassador Manager
 * Handles form submissions, validations, and database operations
 * 
 * @package Campus_Ambassador_Manager
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Campus Ambassador Form Handler Class
 */
class CAM_Form_Handler {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('wp_ajax_cam_submit_application', array($this, 'handle_ajax_submission'));
        add_action('wp_ajax_nopriv_cam_submit_application', array($this, 'handle_ajax_submission'));
        add_action('init', array($this, 'handle_verification'));
    }
    
    /**
     * Handle AJAX form submission
     */
    public function handle_ajax_submission() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'cam_form_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed', 'campus-ambassador-manager')));
        }
        
        // Sanitize input data
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $university = sanitize_text_field($_POST['university']);
        $major = sanitize_text_field($_POST['major']);
        $year = sanitize_text_field($_POST['year']);
        $motivation = sanitize_textarea_field($_POST['motivation']);
        
        // Validate required fields
        $validation = $this->validate_submission($name, $email, $university);
        if (is_wp_error($validation)) {
            wp_send_json_error(array('message' => $validation->get_error_message()));
        }
        
        // Insert into database
        $result = $this->insert_application(array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'university' => $university,
            'major' => $major,
            'year' => $year,
            'motivation' => $motivation
        ));
        
        if (is_wp_error($result)) {
            wp_send_json_error(array('message' => $result->get_error_message()));
        }
        
        wp_send_json_success(array(
            'message' => __('Application submitted successfully! Please check your email for verification.', 'campus-ambassador-manager')
        ));
    }
    
    /**
     * Validate form submission
     */
    private function validate_submission($name, $email, $university) {
        if (empty($name)) {
            return new WP_Error('invalid_name', __('Name is required', 'campus-ambassador-manager'));
        }
        
        if (!is_email($email)) {
            return new WP_Error('invalid_email', __('Please provide a valid email address', 'campus-ambassador-manager'));
        }
        
        if (empty($university)) {
            return new WP_Error('invalid_university', __('University is required', 'campus-ambassador-manager'));
        }
        
        // Check if email already exists
        global $wpdb;
        $table_name = $wpdb->prefix . 'campus_ambassadors';
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE email = %s",
            $email
        ));
        
        if ($exists > 0) {
            return new WP_Error('duplicate_email', __('This email has already been registered', 'campus-ambassador-manager'));
        }
        
        return true;
    }
    
    /**
     * Insert application into database
     */
    private function insert_application($data) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'campus_ambassadors';
        
        // Generate verification code
        $verification_code = wp_generate_password(32, false);
        
        $insert_data = array(
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'university' => $data['university'],
            'major' => $data['major'],
            'year' => $data['year'],
            'motivation' => $data['motivation'],
            'status' => 'pending',
            'verification_code' => $verification_code,
            'verified' => 0,
            'created_at' => current_time('mysql')
        );
        
        $result = $wpdb->insert(
            $table_name,
            $insert_data,
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s')
        );
        
        if ($result === false) {
            return new WP_Error('database_error', __('Failed to save application', 'campus-ambassador-manager'));
        }
        
        // Send verification email
        $this->send_verification_email($data['email'], $data['name'], $verification_code);
        
        return true;
    }
    
    /**
     * Send verification email
     */
    private function send_verification_email($email, $name, $code) {
        $verification_url = add_query_arg(array(
            'cam_verify' => '1',
            'code' => $code,
            'email' => urlencode($email)
        ), home_url('/'));
        
        ob_start();
        include CAM_PLUGIN_DIR . 'templates/email-templates/verification-email.php';
        $message = ob_get_clean();
        
        $subject = __('Verify Your Campus Ambassador Application', 'campus-ambassador-manager');
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        wp_mail($email, $subject, $message, $headers);
    }
    
    /**
     * Handle email verification
     */
    public function handle_verification() {
        if (!isset($_GET['cam_verify']) || !isset($_GET['code']) || !isset($_GET['email'])) {
            return;
        }
        
        $code = sanitize_text_field($_GET['code']);
        $email = sanitize_email($_GET['email']);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'campus_ambassadors';
        
        // Verify the code
        $ambassador = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE email = %s AND verification_code = %s",
            $email,
            $code
        ));
        
        if (!$ambassador) {
            wp_die(__('Invalid verification link', 'campus-ambassador-manager'));
        }
        
        if ($ambassador->verified == 1) {
            wp_die(__('This email has already been verified', 'campus-ambassador-manager'));
        }
        
        // Update verification status
        $wpdb->update(
            $table_name,
            array('verified' => 1, 'status' => 'verified'),
            array('email' => $email),
            array('%d', '%s'),
            array('%s')
        );
        
        // Redirect with success message
        wp_redirect(add_query_arg('cam_verified', '1', home_url('/')));
        exit;
    }
    
    /**
     * Get all applications
     */
    public static function get_applications($status = null, $limit = 100, $offset = 0) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'campus_ambassadors';
        
        if ($status) {
            $query = $wpdb->prepare(
                "SELECT * FROM $table_name WHERE status = %s ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $status,
                $limit,
                $offset
            );
        } else {
            $query = $wpdb->prepare(
                "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
                $limit,
                $offset
            );
        }
        
        return $wpdb->get_results($query);
    }
    
    /**
     * Update application status
     */
    public static function update_status($id, $status) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'campus_ambassadors';
        
        return $wpdb->update(
            $table_name,
            array('status' => $status),
            array('id' => $id),
            array('%s'),
            array('%d')
        );
    }
    
    /**
     * Delete application
     */
    public static function delete_application($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'campus_ambassadors';
        
        return $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );
    }
}

// Initialize the form handler
new CAM_Form_Handler();
