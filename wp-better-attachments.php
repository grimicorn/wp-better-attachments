<?php
/**
* @package WP_Better_Attachments
* @version 2.0.0
*/
/*
Plugin Name: WP Better Attachments
Plugin URI: http://dholloran.github.io/wp-better-attachments
Description: Better Wordpress Attachments
Author: Dan Holloran
Version: 2.0.0
Author URI: http://danholloran.ghost.io/
*/

define( 'WPBA_VERSION', '2.0.0' );
define( 'WPBA_LANG', 'wpba' );
define( 'WPBA_PATH', '/' . trim( plugin_dir_path(__FILE__), '/' ) );

$directories = array();

// Includes
$directories['includes']  = array(
	'debug',
);

// Base classes
$directories['classes']  = array(
	'class-wp-better-attachments',
	'class-wpba-filter-settings',
	'class-wpba-migrate-settings',
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