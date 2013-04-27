<?php
/**
 * @package WP_Better_Attachments
 * @version 1.2.2
 */
/*
Plugin Name: WP Better Attachments
Plugin URI: http://dholloran.github.io/wp-better-attachments
Description: Better Wordpress Attachments
Author: Dan Holloran
Version: 1.2.2
Author URI: http://danholloran.com/
*/

define( 'WPBA_VERSION', '1.2.2' );
define( 'WPBA_LANG', 'wpba' );
define( 'WPBA_PATH', plugin_dir_path(__FILE__) );

/**
* Handles Activation/Deactivation/Install
*/
require_once "classes/class-wpba-init.php";
register_activation_hook( __FILE__, array( 'WPBA_Init', 'on_activate' ) );
register_deactivation_hook( __FILE__, array( 'WPBA_Init', 'on_deactivate' ) );
register_uninstall_hook( __FILE__, array( 'WPBA_Init', 'on_uninstall' ) );


/**
* Required Classes
*/
require_once "classes/class-wp-better-attachments.php";
require_once 'libs/wp-settings-api-bootstrap/class.wp-settings-api-bootstrap.php';
require_once "classes/class-wpba-meta-box.php";
require_once "classes/class-wpba-crop-resize.php";
require_once "classes/class-wpba-ajax.php";
require_once "classes/class-wpba-settings.php";


/**
* Easy Attachment Function
*/
function wpba_get_attachments( $post_id = 0 )
{
	global $wpba;
	if ( $post_id != 0 ) {
		$post = get_post( $post_id );
	} else {
		global $post;
	} // if/else()
	return $wpba->get_post_attachments( $args = array( 'post' => $post ) );
}


/**
* Add Attachments button above post editor
*/
add_action('media_buttons_context', 'add_form_button');
function add_form_button($context){
	$out = '<a class="button wpba-attachments-button wpba-form-attachments-button" id="wpba_form_attachments_button" href="#">Add Attachments</a>';
	return $context . $out;
}