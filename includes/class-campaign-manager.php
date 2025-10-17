<?php
/**
 * Campaign Manager Class
 * 
 * Handles multi-campaign management for the Campus Ambassador Plugin
 *
 * @package Campus_Ambassador_Manager
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Campaign_Manager {

    /**
     * Campaign post type
     */
    const CAMPAIGN_POST_TYPE = 'campaign_type';

    /**
     * Application post type
     */
    const APPLICATION_POST_TYPE = 'ambassador_application';

    /**
     * Initialize the campaign manager
     */
    public function __construct() {
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'register_taxonomies'));
    }

    /**
     * Register custom post types
     */
    public function register_post_types() {
        // Register Campaign Type
        register_post_type(self::CAMPAIGN_POST_TYPE, array(
            'labels' => array(
                'name' => __('Campaigns', 'campus-ambassador-manager'),
                'singular_name' => __('Campaign', 'campus-ambassador-manager'),
                'add_new' => __('Add New Campaign', 'campus-ambassador-manager'),
                'add_new_item' => __('Add New Campaign', 'campus-ambassador-manager'),
                'edit_item' => __('Edit Campaign', 'campus-ambassador-manager'),
                'view_item' => __('View Campaign', 'campus-ambassador-manager'),
                'all_items' => __('All Campaigns', 'campus-ambassador-manager'),
            ),
            'public' => true,
            'hierarchical' => true,
            'supports' => array('title', 'thumbnail', 'custom-fields', 'editor'),
            'has_archive' => true,
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-megaphone',
            'menu_position' => 20,
        ));

        // Register Application Type
        register_post_type(self::APPLICATION_POST_TYPE, array(
            'labels' => array(
                'name' => __('Applications', 'campus-ambassador-manager'),
                'singular_name' => __('Application', 'campus-ambassador-manager'),
                'add_new' => __('Add New Application', 'campus-ambassador-manager'),
                'edit_item' => __('Edit Application', 'campus-ambassador-manager'),
                'view_item' => __('View Application', 'campus-ambassador-manager'),
                'all_items' => __('All Applications', 'campus-ambassador-manager'),
            ),
            'public' => false,
            'show_ui' => true,
            'supports' => array('title', 'custom-fields'),
            'capability_type' => 'post',
            'capabilities' => array(
                'create_posts' => 'do_not_allow',
            ),
            'map_meta_cap' => true,
            'show_in_menu' => 'edit.php?post_type=' . self::CAMPAIGN_POST_TYPE,
        ));
    }

    /**
     * Register taxonomies
     */
    public function register_taxonomies() {
        register_taxonomy('campaign_status', self::APPLICATION_POST_TYPE, array(
            'labels' => array(
                'name' => __('Status', 'campus-ambassador-manager'),
                'singular_name' => __('Status', 'campus-ambassador-manager'),
            ),
            'public' => true,
            'hierarchical' => false,
            'show_admin_column' => true,
        ));
    }

    /**
     * Create a new campaign
     */
    public function create_campaign($data) {
        $campaign_data = array(
            'post_title' => sanitize_text_field($data['name']),
            'post_type' => self::CAMPAIGN_POST_TYPE,
            'post_status' => isset($data['status']) ? $data['status'] : 'active',
            'meta_input' => array(
                'campaign_type' => sanitize_text_field($data['type']),
                'template_frames' => maybe_serialize($data['frames']),
                'form_fields' => maybe_serialize($data['fields']),
                'card_style' => sanitize_text_field($data['card_style']),
            ),
        );

        $campaign_id = wp_insert_post($campaign_data);

        if (!is_wp_error($campaign_id)) {
            do_action('campaign_created', $campaign_id, $data);
            return $campaign_id;
        }

        return false;
    }

    /**
     * Get all campaigns
     */
    public function get_campaigns($args = array()) {
        $defaults = array(
            'post_type' => self::CAMPAIGN_POST_TYPE,
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        $args = wp_parse_args($args, $defaults);
        return get_posts($args);
    }

    /**
     * Get campaign by ID
     */
    public function get_campaign($campaign_id) {
        return get_post($campaign_id);
    }

    /**
     * Get campaign frames
     */
    public function get_campaign_frames($campaign_id) {
        $frames = get_post_meta($campaign_id, 'template_frames', true);
        return maybe_unserialize($frames);
    }

    /**
     * Duplicate campaign
     */
    public function duplicate_campaign($campaign_id) {
        $original = get_post($campaign_id);
        
        if (!$original) {
            return false;
        }

        $new_campaign = array(
            'post_title' => $original->post_title . ' (Copy)',
            'post_type' => self::CAMPAIGN_POST_TYPE,
            'post_status' => 'draft',
            'post_content' => $original->post_content,
        );

        $new_id = wp_insert_post($new_campaign);

        if (!is_wp_error($new_id)) {
            // Copy meta data
            $meta_data = get_post_meta($campaign_id);
            foreach ($meta_data as $key => $values) {
                foreach ($values as $value) {
                    add_post_meta($new_id, $key, maybe_unserialize($value));
                }
            }
            return $new_id;
        }

        return false;
    }

    /**
     * Bulk actions for applications
     */
    public function bulk_actions($application_ids, $action) {
        $results = array();
        
        foreach ($application_ids as $app_id) {
            switch ($action) {
                case 'approve':
                    $results[$app_id] = $this->approve_application($app_id);
                    break;
                case 'reject':
                    $results[$app_id] = $this->reject_application($app_id);
                    break;
                case 'delete':
                    $results[$app_id] = wp_delete_post($app_id, true);
                    break;
            }
        }

        return $results;
    }

    /**
     * Approve application
     */
    private function approve_application($application_id) {
        update_post_meta($application_id, 'application_status', 'approved');
        update_post_meta($application_id, 'approved_at', current_time('mysql'));
        update_post_meta($application_id, 'approved_by', get_current_user_id());
        
        do_action('application_approved', $application_id);
        
        return true;
    }

    /**
     * Reject application
     */
    private function reject_application($application_id) {
        update_post_meta($application_id, 'application_status', 'rejected');
        update_post_meta($application_id, 'rejected_at', current_time('mysql'));
        
        do_action('application_rejected', $application_id);
        
        return true;
    }

    /**
     * Get applications for a campaign
     */
    public function get_applications($campaign_id, $status = 'all') {
        $args = array(
            'post_type' => self::APPLICATION_POST_TYPE,
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'campaign_id',
                    'value' => $campaign_id,
                ),
            ),
        );

        if ($status !== 'all') {
            $args['meta_query'][] = array(
                'key' => 'application_status',
                'value' => $status,
            );
        }

        return get_posts($args);
    }
}
