<?php
/**
 * Shortcodes Class
 *
 * @package Campus_Ambassador_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Campus_Ambassador_Shortcodes {

	public function __construct() {
		add_shortcode( 'campus_ambassador_form', array( $this, 'render_form_shortcode' ) );
	}

	public function render_form_shortcode( $atts ) {
		// Render shortcode output
		return '';
	}
}
