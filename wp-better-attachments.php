<?php
/**
 * WP Better Attachments.
 *
 * @version      2.0.0
 *
 * @package      WordPress
 * @subpackage   WP_Better_Attachments
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
Version:      2.0.0
Author URI:   http://danholloran.ghost.io/
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

define( 'WPBA_VERSION', '2.0.0' );
define( 'WPBA_LANG', 'wpba' );
define( 'WPBA_PATH', '/' . trim( plugin_dir_path(__FILE__), '/' ) );

$directories = array();

// Includes
$directories['includes']  = array(
	'wpba-debug',
);

// Base classes
$directories['classes']  = array(
	'class-wp-better-attachments',
	'class-wpba-utilities',
	'class-wpba-migrate-settings',
	'class-wpba-filter-settings',
	'class-wpba-form-fields',
	'class-wpba-setting-fields',
	'class-wpba-settings',
);

// Require all the things!!!
foreach ( $directories as $directory => $files ) {
	if ( empty( $files ) ) continue;

	foreach ( $files as $file ) {
		$has_php  = ( strpos( $file, '.php' ) !== false );
		$file     = ( $has_php ) ? $file : "{$file}.php";
		$template = WPBA_PATH . "/{$directory}/{$file}";

		if ( $template == '' ) continue;
		require_once $template;
	} // foreach()
} // foreach()