<?php
/**
 * WP Better Attachments Base class.
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
if ( ! class_exists( 'WP_Better_Attachments' ) ) {
	class WP_Better_Attachments {
		/**
		 * The settings option key.
		 *
		 * @since  2.0.0
		 *
		 * @var    slug
		 */
		public $option_key = 'wpba_settings';



		/**
		 * Default Disabled Post Types.
		 *
		 * @since  2.0.0
		 *
		 * @var    array
		 */
		public $default_disabled_post_types = array(
			'attachment'    => 'attachment',
			'revision'      => 'revision',
			'nav_menu_item' => 'nav_menu_item',
		);



		/**
		 * Class constructor
		 *
		 * @since   2.0.0
		 */
		function __construct() {
		} // __construct()
	} // WP_Better_Attachments
} // if()