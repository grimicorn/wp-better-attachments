<?php
/**
 * This class contains anything to do with the AJAX requests.
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
if ( ! class_exists( 'WPBA_AJAX' ) ) {
	class WPBA_AJAX extends WPBA_Helpers {
		/**
		 * WPBA_AJAX class constructor.
		 *
		 * @since  1.4.0
		 *
		 * @param  array  $config  Class configuration.
		 */
		public function __construct( $config = array() ) {
			parent::__construct();

			$this->_add_wpba_meta_actions_filters();
		} // __construct()


		/**
		 * Handles adding all of the WPBA meta actions and filters.
		 *
		 * <code>$this->_add_wpba_meta_actions_filters();</code>
		 *
		 * @since   1.4.0
		 *
		 * @return  void
		 */
		private function _add_wpba_meta_actions_filters() {
			// Unattach attachment AJAX callback
			add_action( 'wp_ajax_wpba_unattach_attachment', array( &$this, 'wpba_unattach_attachment_callback' ) );

			// Delete attachment AJAX callback
			add_action( 'wp_ajax_wpba_delete_attachment', array( &$this, 'wpba_delete_attachment_callback' ) );
		} // _add_wpba_meta_actions_filters()



		/**
		 * Handles the unattach link AJAX callback.
		 *
		 * @return  void
		 */
		public function wpba_unattach_attachment_callback() {
			$attachment_id = $_POST['id'];

			// Make sure we have something to work with
			if ( ! isset( $attachment_id ) ) {
				echo json_encode( false );
				die();
			} // if()

			// Unattach the attachment and send the status back as JSON.
			echo json_encode( $unattach = $this->unattach_attachment( $attachment_id ) );
			die();
		} // wpba_unattach_attachment_callback


		/**
		 * Handles the delete link AJAX callback.
		 *
		 * @return  void
		 */
		public function wpba_delete_attachment_callback() {
			$attachment_id = $_POST['id'];

			// Make sure we have something to work with
			if ( ! isset( $attachment_id ) ) {
				echo json_encode( false );
				die();
			} // if()

			// Delete the attachment and send the status back as JSON.
			echo json_encode( $this->delete_attachment( $attachment_id ) );
			die();
		} // wpba_delete_attachment_callback
	} // WPBA_AJAX()

	// Instantiate Class
	global $wpba_meta;
	$wpba_helpers = new WPBA_AJAX();
} // if()