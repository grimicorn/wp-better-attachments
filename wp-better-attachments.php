<?php
/**
 * WP Better Attachments.
 *
 * @version      1.4.0

 * @package      WordPress
 * @subpackage   WPBA
 *
 * @author       Dan Holloran    <dtholloran@gmail.com>
 *
 * @copyright    2013 - Present  Dan Holloran
 */

/*
Plugin Name:  WP Better Attachments
Plugin URI:   http://dholloran.github.io/wp-better-attachments
Description:  Better Wordpress Attachments
Author:       Dan Holloran
Version:      1.4.0
Author URI:   http://danholloran.com/
License:      GPL2
Text Domain:  wpba
Domain Path:  lang
*/


/*
	Copyright (C) 2014  Dan Holloran dtholloran@gmail.com

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/



// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
} // if()



if ( ! defined( 'WPBA_VERSION' ) ) {
	/**
	 * WP Better Attachments version number.
	 *
	 * @var  string
	 */
	define( 'WPBA_VERSION', '1.4.0' );
} // if()



if ( ! defined( 'WPBA_URL' ) ) {
	/**
	 * WP Better Attachments plugin URL without trailing slash (http://{plugins_url}/wp-better-attachments).
	 *
	 * @var  string
	 */
	define( 'WPBA_URL', plugins_url( '/wp-better-attachments' ) );
} // if()



if ( ! defined( 'WPBA_PATH' ) ) {
	/**
	 * WP Better Attachments plugin path without trailing slash (/PATH/TO/PLUGIN/DIRECTORY/wp-better-attachments).
	 *
	 * @var  string
	 */
	define( 'WPBA_PATH', trim( plugin_dir_path( __FILE__ ), '/' ) );
} // if()

// Debugging
require_once 'libs/wpba-debug.php';



/**
* Required Classes
*/
$classes = array(
	'class-wp-better-attachments',
	'class-wpba-helpers',
	'class-wpba-meta-form-fields',
	'class-wpba-meta',
);
foreach ( $classes as $class ) {
	$has_php = ( strpos( $class, '.php' ) !== false );
	$class   = ( $has_php ) ? $class : "{$class}.php";
	require_once "classes/{$class}";
} // foreach()



/**
 * Libs
 */
$libs = array();
foreach ( $libs as $lib ) {
	$has_php = ( strpos( $lib, '.php' ) !== false );
	$lib     = ( $has_php ) ? $lib : "{$lib}.php";
	require_once "lib/{$lib}";
} // foreach()