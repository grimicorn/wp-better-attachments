<?php
/**
 * @package WP_Better_Attachments
 * @version 1.0.0
 */
/*
Plugin Name: WP Better Attachments
Plugin URI:
Description: Better Wordpress Attachments
Author: Dan Holloran
Version: 1.0.0
Author URI: http://danholloran.com/
*/

define( 'WPBA_VERSION', '1.0.0' );
define( 'WPBA_LANG', 'wpba' );

function wp_test(){
	global $post;
// pp($post);
}
add_action('admin_enqueue_scripts', 'wp_test');

/**
* Handles Activation/Deactivation/Install
*/
require_once "classes/class.wpba-init.php";
register_activation_hook( __FILE__, array( 'WPBA_Init', 'on_activate' ) );
register_deactivation_hook( __FILE__, array( 'WPBA_Init', 'on_deactivate' ) );
register_uninstall_hook( __FILE__, array( 'WPBA_Init', 'on_uninstall' ) );


/**
* Required Classes
*/
require_once "classes/class.wp-better-attachments.php";
require_once "classes/class.wpba-ajax.php";

/**
* Required Libs
*/

/**
* Add Attachments button above post editor
*/
global $wp_version;
if ( floatval($wp_version) >= 3.5 ) {
	add_action('media_buttons_context', 'add_form_button');
}
function add_form_button($context){
		$is_post_edit_page = in_array(RG_CURRENT_PAGE, array('post.php', 'page.php', 'page-new.php', 'post-new.php'));
		if(!$is_post_edit_page)
				return $context;

		$image_btn = GFCommon::get_base_url() . "/images/form-button.png";
		$out = '<a class="button wpba-attachments-button wpba-form-attachments-button" id="wpba_form_attachments_button" href="#">Add Attachments</a>';
		return $context . $out;
}