<?php
/**
* @package WP_Better_Attachments
* @version 1.3.10
*/
/*
Plugin Name: WP Better Attachments
Plugin URI: http://dholloran.github.io/wp-better-attachments
Description: Better Wordpress Attachments
Author: Dan Holloran
Version: 1.3.10
Author URI: http://danholloran.ghost.io/
*/

define( 'WPBA_VERSION', '1.3.10' );
define( 'WPBA_LANG', 'wpba' );
define( 'WPBA_PATH', plugin_dir_path(__FILE__) );

/*
* Pretty Print Debug Function
*
* Only on localhost
*/
if ( !function_exists( 'pp' ) AND $_SERVER['HTTP_HOST'] == 'localhost' ) {
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
* Required Classes
*/
require_once "libs/wp-settings-api-bootstrap/class.wp-settings-api-bootstrap.php";
require_once "classes/class-wp-better-attachments.php";
require_once "classes/class-wpba-media-library.php";
require_once "classes/class-wpba-meta-box.php";
require_once "classes/class-wpba-crop-resize.php";
require_once "classes/class-wpba-ajax.php";
require_once "classes/class-wpba-settings.php";
require_once "classes/class-wpba-settings-fields.php";
require_once "classes/class-wpba-frontend.php";
require_once "libs/survey-notification.php";


/**
* Includes
*/
require_once "inc/shortcodes.inc.php";