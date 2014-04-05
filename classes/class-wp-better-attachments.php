<?php
/**
* WP Better Attachments Base Class
*/

/**
 * This class should be extended by every other WPBA class.
 * All of the setup logic should be contained here.
 *
 * @version      1.4.0
 *
 * @package      WordPress
 * @subpackage   WPBA
 *
 * @since        1.4.0
 *
 * @author       Dan Holloran          <dholloran@matchboxdesigngroup.com>
 *
 * @copyright    2013 - Present         Dan Holloran
 */
if ( ! class_exists( 'WP_Better_Attachments' ) ) {
	class WP_Better_Attachments {

		/**
		 * WP_Better_Attachments class constructor.
		 *
		 * @param  array  $config  Class configuration.
		 */
		function __construct( $config = array() ) {
			add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_public_assets' ) );
			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_assets' ) );
		} // __construct()



		/**
		 * Enqueues scripts and styles for use on the public front end.
		 *
		 * @return  void
		 */
		public function enqueue_public_assets() {
			wp_enqueue_style( 'wpba_public_css', WPBA_URL . '/assets/css/dist/wpba.min.css', array(), null, 'all' );
			wp_register_script( 'wpba_public_js', WPBA_URL . '/assets/js/dist/wpba.min.js', array(), null, true );
			wp_enqueue_script( 'wpba_public_js' );
		} // enqueue_public_assets()



		/**
		 * Enqueues scripts and styles for use in wp-admin.
		 *
		 * @return  void
		 */
		public function enqueue_admin_assets() {
			wp_enqueue_style( 'wpba_admin_css', WPBA_URL . '/assets/css/dist/wpba-admin.min.css', array(), null, 'all' );
			wp_register_script( 'wpba_admin_js', WPBA_URL . '/assets/js/dist/wpba-admin.min.js', array(), null, true );
			wp_enqueue_script( 'wpba_admin_js' );
		} // enqueue_admin_assets()
	} // WP_Better_Attachments()
} // if()