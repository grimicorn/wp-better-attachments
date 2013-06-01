<?php
/**
 * @package WP_Better_Attachments
 * @version 1.3.1
 */
/*
Plugin Name: WP Better Attachments
Plugin URI: http://dholloran.github.io/wp-better-attachments
Description: Better Wordpress Attachments
Author: Dan Holloran
Version: 1.3.1
Author URI: http://danholloran.com/
*/

define( 'WPBA_VERSION', '1.3.1' );
define( 'WPBA_LANG', 'wpba' );
define( 'WPBA_PATH', plugin_dir_path(__FILE__) );

/*
* Pretty Print Debug Function
*
* Only on localhost
*/
if ( !function_exists( 'pp' ) ) {
	function pp( $value )
	{
		if( $_SERVER['HTTP_HOST'] != 'localhost' ) return;
		echo "<pre>";
		if ( $value ) {
			print_r( $value );
		} else {
			var_dump( $value );
		}
		echo "</pre>";
	} // pp()
} // if()



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
require_once "libs/wp-settings-api-bootstrap/class.wp-settings-api-bootstrap.php";
require_once "classes/class-wpba-meta-box.php";
require_once "classes/class-wpba-crop-resize.php";
require_once "classes/class-wpba-ajax.php";
require_once "classes/class-wpba-settings.php";
require_once "classes/class-wpba-frontend.php";


/**
* Includes
*/
require_once "inc/shortcodes.inc.php";

