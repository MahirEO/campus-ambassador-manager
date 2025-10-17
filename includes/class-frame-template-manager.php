<?php
/**
 * Frame Template Manager Class
 * 
 * Manages frame templates for different campaigns
 *
 * @package Campus_Ambassador_Manager
 * @version 2.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Frame_Template_Manager {

    /**
     * Frame post type
     */
    const FRAME_POST_TYPE = 'frame_template';

    /**
     * Initialize the frame manager
     */
    public function __construct() {
        add_action('init', array($this, 'register_frame_post_type'));
    }

    /**
     * Register frame template post type
     */
    public function register_frame_post_type() {
        register_post_type(self::FRAME_POST_TYPE, array(
            'labels' => array(
                'name' => __('Frame Templates', 'campus-ambassador-manager'),
                'singular_name' => __('Frame Template', 'campus-ambassador-manager'),
                'add_new' => __('Add New Frame', 'campus-ambassador-manager'),
                'add_new_item' => __('Add New Frame Template', 'campus-ambassador-manager'),
                'edit_item' => __('Edit Frame Template', 'campus-ambassador-manager'),
                'view_item' => __('View Frame Template', 'campus-ambassador-manager'),
                'all_items' => __('All Frames', 'campus-ambassador-manager'),
            ),
            'public' => false,
            'show_ui' => true,
            'supports' => array('title', 'thumbnail'),
            'has_archive' => false,
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-images-alt2',
            'show_in_menu' => 'edit.php?post_type=campaign_type',
        ));
    }

    /**
     * Register a new frame template
     */
    public function register_frame($campaign_id, $frame_data) {
        $frame = array(
            'post_title' => sanitize_text_field($frame_data['name']),
            'post_type' => self::FRAME_POST_TYPE,
            'post_status' => 'publish',
            'meta_input' => array(
                'frame_id' => sanitize_text_field($frame_data['id']),
                'campaign_id' => intval($campaign_id),
                'template_url' => esc_url($frame_data['template_url']),
                'zones' => maybe_serialize($frame_data['zones']),
                'usage_count' => 0,
            ),
        );

        $frame_id = wp_insert_post($frame);

        if (!is_wp_error($frame_id)) {
            // Set thumbnail if provided
            if (!empty($frame_data['thumbnail'])) {
                $attachment_id = $this->upload_frame_thumbnail($frame_data['thumbnail']);
                if ($attachment_id) {
                    set_post_thumbnail($frame_id, $attachment_id);
                }
            }

            do_action('frame_registered', $frame_id, $campaign_id);
            return $frame_id;
        }

        return false;
    }

    /**
     * Get frames for a specific campaign
     */
    public function get_campaign_frames($campaign_id) {
        $args = array(
            'post_type' => self::FRAME_POST_TYPE,
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'campaign_id',
                    'value' => $campaign_id,
                ),
            ),
        );

        $frames = get_posts($args);
        $frame_data = array();

        foreach ($frames as $frame) {
            $frame_data[] = array(
                'id' => get_post_meta($frame->ID, 'frame_id', true),
                'name' => $frame->post_title,
                'template_url' => get_post_meta($frame->ID, 'template_url', true),
                'thumbnail' => get_the_post_thumbnail_url($frame->ID, 'medium'),
                'zones' => maybe_unserialize(get_post_meta($frame->ID, 'zones', true)),
                'usage_count' => get_post_meta($frame->ID, 'usage_count', true),
            );
        }

        return $frame_data;
    }

    /**
     * Get all frames
     */
    public function get_all_frames() {
        $args = array(
            'post_type' => self::FRAME_POST_TYPE,
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        return get_posts($args);
    }

    /**
     * Render frame selector interface
     */
    public function render_frame_selector($campaign_id, $selected_frame = null) {
        $frames = $this->get_campaign_frames($campaign_id);

        if (empty($frames)) {
            echo '<p>' . __('No frames available for this campaign.', 'campus-ambassador-manager') . '</p>';
            return;
        }

        ob_start();
        ?>
        <div class="frame-selector-container">
            <h3><?php _e('Choose Your Frame', 'campus-ambassador-manager'); ?></h3>
            <div class="frame-grid">
                <?php foreach ($frames as $frame) : ?>
                    <div class="frame-option <?php echo ($selected_frame === $frame['id']) ? 'selected' : ''; ?>" 
                         data-frame-id="<?php echo esc_attr($frame['id']); ?>">
                        <?php if ($frame['thumbnail']) : ?>
                            <img src="<?php echo esc_url($frame['thumbnail']); ?>" 
                                 alt="<?php echo esc_attr($frame['name']); ?>" />
                        <?php endif; ?>
                        <span class="frame-name"><?php echo esc_html($frame['name']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="frame-preview">
                <p><?php _e('Click a frame to preview', 'campus-ambassador-manager'); ?></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Upload frame thumbnail
     */
    private function upload_frame_thumbnail($file) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        $attachment_id = media_handle_upload($file, 0);

        if (is_wp_error($attachment_id)) {
            return false;
        }

        return $attachment_id;
    }

    /**
     * Update frame usage count
     */
    public function increment_usage_count($frame_id) {
        $current_count = get_post_meta($frame_id, 'usage_count', true);
        $new_count = intval($current_count) + 1;
        update_post_meta($frame_id, 'usage_count', $new_count);
    }

    /**
     * Get frame zones configuration
     */
    public function get_frame_zones($frame_id) {
        $zones = get_post_meta($frame_id, 'zones', true);
        return maybe_unserialize($zones);
    }

    /**
     * Update frame zones
     */
    public function update_frame_zones($frame_id, $zones) {
        return update_post_meta($frame_id, 'zones', maybe_serialize($zones));
    }

    /**
     * Delete frame
     */
    public function delete_frame($frame_id) {
        return wp_delete_post($frame_id, true);
    }

    /**
     * Get frame by ID
     */
    public function get_frame($frame_id) {
        $frame = get_post($frame_id);
        
        if (!$frame || $frame->post_type !== self::FRAME_POST_TYPE) {
            return false;
        }

        return array(
            'id' => get_post_meta($frame->ID, 'frame_id', true),
            'name' => $frame->post_title,
            'template_url' => get_post_meta($frame->ID, 'template_url', true),
            'thumbnail' => get_the_post_thumbnail_url($frame->ID, 'full'),
            'zones' => maybe_unserialize(get_post_meta($frame->ID, 'zones', true)),
            'campaign_id' => get_post_meta($frame->ID, 'campaign_id', true),
            'usage_count' => get_post_meta($frame->ID, 'usage_count', true),
        );
    }
}
