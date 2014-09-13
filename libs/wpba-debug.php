<?php
/**
 * WP Better Attachments debugging.
 *
 * @version      1.4.0
 *
 * @package      WordPress
 * @subpackage   WPBA_DEBUG
 *
 * @author       Dan Holloran          <dholloran@matchboxdesigngroup.com>
 *
 * @copyright    2013 - Present         Dan Holloran
 */


/**
 * Checks if the current host is localhost, a VirtualBox VM, or sitename.dev vHost.
 *
 * <code>
 * if ( wpba_debug_is_localhost() ) {
 * 	do_something_related_to_localhost();
 * } // if()
 * </code>
 *
 * @since  1.4.0
 *
 * @return boolean If the current host is localhost.
 */
function wpba_debug_is_localhost() {
	$localhost = array(
		'localhost',
		'127.0.0.1',
		'10.0.2.2',
	);
	$host      = ( isset( $_SERVER['HTTP_HOST'] ) ) ? $_SERVER['HTTP_HOST'] : '';
	$is_vhost  = strpos( $host, '.dev' );

	if ( in_array( $host, $localhost ) or $is_vhost ) {
		return true;
	} // if()

	return false;
} // wpba_debug_is_localhost()



if ( ! function_exists( 'pp' ) ) {
	/**
	 * Pretty Print Debug Function
	 *
	 * <code>pp( $something_to_pretty_print );</code>
	 *
	 * @uses   wpba_debug_is_localhost()
	 *
	 * @since  1.4.0
	 *
	 * @param mixed   $value Any value to pretty print.
	 *
	 * @return Void
	 */
	function pp( $value ) {
		global $mdg_generic;

		if ( ! wpba_debug_is_localhost() ) {
			return;
		} // if()

		echo '<pre>';
		if ( $value and $value !== true ) {
			print_r( $value );
		} else {
			var_dump( $value );
		} // if/else()
		echo '</pre>';
	} // pp()
} // if()

if ( ! function_exists( 'wpba_debug_print_setting' ) ) {
	/**
	 * Pretty Print setting Debug Function
	 *
	 * <code>wpba_debug_print_setting( $setting );</code>
	 *
	 * @todo   This is really bad I know...some day I will setup unit tests...
	 *
	 * @uses   wpba_debug_is_localhost()
	 *
	 * @since  1.4.0
	 *
	 * @param  mixed    $setting       Any value to pretty print.
	 * @param  string  $setting_name  Optional, the setting name to display, defaults to empty string.
	 *
	 * @return Void
	 */
	function wpba_debug_print_setting( $setting, $setting_name = '' ) {
		if ( ! isset( $_GET['wpba_print_settings'] ) ) {
			return;
		} // if()

		global $wpba_print_settings_value;
		$wpba_print_settings_value = ( isset( $wpba_print_settings_value ) ) ? $wpba_print_settings_value : array();

		// Add the settings value
		// We do not want duplicates so this will resolve that...
		if ( $setting_name == '' ) {
			$wpba_print_settings_value[] = array(
				'setting'      => $setting,
				'setting_name' => $setting_name,
			);
		} else {
			$wpba_print_settings_value[$setting_name] = array(
				'setting'      => $setting,
				'setting_name' => $setting_name,
			);
		} // if()
	} // wpba_debug_print_setting()
} // if()



} // if()