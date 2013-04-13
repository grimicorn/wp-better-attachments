<?php
/**
 * @package WP_Better_Attachments
 * @version 1.2.0
 */
/*
Plugin Name: WP Better Attachments
Plugin URI: http://dholloran.github.io/wp-better-attachments
Description: Better Wordpress Attachments
Author: Dan Holloran
Version: 1.2.0
Author URI: http://danholloran.com/
*/

define( 'WPBA_VERSION', '1.2.0' );
define( 'WPBA_LANG', 'wpba' );

function wp_test(){
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
require_once "classes/class.wpba-meta-box.php";
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
	$out = '<a class="button wpba-attachments-button wpba-form-attachments-button" id="wpba_form_attachments_button" href="#">Add Attachments</a>';
	return $context . $out;
}