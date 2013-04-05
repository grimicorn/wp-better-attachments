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

if ( !defined( 'WPBA_VERSION' ) ) {
  define( 'WPBA_VERSION', '1.0.0' );
}


/**
* Handles Activation/Deactivation/Install
*/
require_once "classes/class.wpba-init.php";
register_activation_hook( __FILE__, array( 'WPBA_Init', 'on_activate' ) );
register_deactivation_hook( __FILE__, array( 'WPBA_Init', 'on_deactivate' ) );
register_uninstall_hook( __FILE__, array( 'WPBA_Init', 'on_uninstall' ) );

  // $wp_filetype = wp_check_filetype(basename($filename), null );
  // $wp_upload_dir = wp_upload_dir();
  // $attachment = array(
  //    'guid' => $wp_upload_dir['url'] . '/' . basename( $filename ),
  //    'post_mime_type' => $wp_filetype['type'],
  //    'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
  //    'post_content' => '',
  //    'post_status' => 'inherit'
  // );
  // $attach_id = wp_insert_attachment( $attachment, $filename, 37 );
  // // you must first include the image.php file
  // // for the function wp_generate_attachment_metadata() to work
  // require_once(ABSPATH . 'wp-admin/includes/image.php');
  // $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
  // wp_update_attachment_metadata( $attach_id, $attach_data );