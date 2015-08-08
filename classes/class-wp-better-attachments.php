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
		public $option_group = 'wpba_settings';



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
		 * The ID for the meta box.
		 *
		 * @todo  allow for multiple meta boxes on a page.
		 *
		 * @since 1.4.0
		 *
		 * @var   string
		 */
		public $meta_box_id = 'wpba_meta_box';



		/**
		 * The meta key for allowing attachments to be added to multiple posts.
		 *
		 * @todo  allow for multiple meta boxes on a page.
		 *
		 * @since 1.4.0
		 *
		 * @var   string
		 */
		public $attachment_multi_meta_key;



		/**
		 * The meta key for attachments menu order when added to multiple posts.
		 *
		 * @todo  allow for multiple meta boxes on a page.
		 *
		 * @since 1.4.0
		 *
		 * @var   string
		 */
		public $attachment_multi_menu_order_meta_key;



		/**
		 * Class constructor
		 *
		 * @since   2.0.0
		 */
		function __construct() {
		} // __construct()
	} // WP_Better_Attachments
} // if()