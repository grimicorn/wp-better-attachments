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
		 * Handles sending the data back to the JavaScript.
		 *
		 * @since   1.4.0
		 *
		 * @param   mixed   $data  Data to be sent back to JavaScript.
		 *
		 * @return  void
		 */
		public function response( $data ) {
			echo json_encode( $data );
			die();
		} // response()



		/**
		 * Handles adding all of the WPBA meta actions and filters.
		 *
		 * <code>$this->_add_wpba_meta_actions_filters();</code>
		 *
		 * @internal
		 *
		 * @since   1.4.0
		 *
		 * @return  void
		 */
		private function _add_wpba_meta_actions_filters() {
			// Add attachments callback
			add_action( 'wp_ajax_wpba_add_attachments', array( &$this, 'wpba_add_attachments_callback' ) );

			// Unattach attachment AJAX callback
			add_action( 'wp_ajax_wpba_unattach_attachment', array( &$this, 'wpba_unattach_attachment_callback' ) );

			// Delete attachment AJAX callback
			add_action( 'wp_ajax_wpba_delete_attachment', array( &$this, 'wpba_delete_attachment_callback' ) );
		} // _add_wpba_meta_actions_filters()



		/**
		 * Handles the attach AJAX callback.
		 *
		 * <code>add_action( 'wp_ajax_wpba_add_attachments', array( &$this, 'wpba_add_attachments_callback' ) );</code>
		 *
		 * @since   1.4.0
		 *
		 * @return  void
		 */
		public function wpba_add_attachments_callback() {
			// Make sure we have something to work with
			if ( ! isset( $_POST['postid'] ) or ! $_POST['postid'] or ! isset( $_POST['attachmentids'] ) or ! isset( $_POST['currentattachments'] ) ) {
				echo json_encode( false );
				die();
			} // if()

			$post_id                = $_POST['postid'];
			$attachment_ids         = $_POST['attachmentids'];
			$current_attachment_ids = $_POST['currentattachments'];

			// Make sure we do not duplicate attachments
			foreach ( $attachment_ids as $attachment_key => $attachment_id ) {
				if ( in_array( $attachment_id, $current_attachment_ids ) ) {
					unset( $attachment_ids[$attachment_key] );
				} // if()
			} // foreach()

			// If we have nothing to add then we are done, Nothing to see here folks....
			if ( empty( $attachment_ids ) ) {
				// Send the status and HTML back in JSON
				$data = array(
					'success' => false,
					'html'    => '',
				);
				$this->response( $data );
			} // if()

			// Attach attachments
			foreach ( $attachment_ids as $attachment_id ) {
				$this->attach_attachment( $attachment_id, $post_id );
			} // foreach()

			// Get the attachments
			$query_args = array(
				'post__in' => $attachment_ids,
			);
			$attachments = $this->get_attachments( $post_id, false, $query_args );

			// Get the attachment items HTML
			global $wpba_meta;
			$attachment_items = $wpba_meta->build_attachment_items( $attachments, false );

			// Send the status and HTML back in JSON
			$data = array(
				'success' => true,
				'html'    => $attachment_items,
			);
			$this->response( $data );
		} // wpba_add_attachments_callback()



		/**
		 * Handles the unattach link AJAX callback.
		 *
		 * <code>add_action( 'wp_ajax_wpba_unattach_attachment', array( &$this, 'wpba_unattach_attachment_callback' ) );</code>
		 *
		 * @since   1.4.0
		 *
		 * @return  void
		 */
		public function wpba_unattach_attachment_callback() {
			$attachment_id = $_POST['id'];
			$post_id       = $_POST['postID'];

			// Make sure we have something to work with
			if ( ! isset( $attachment_id ) or ! isset( $post_id ) ) {
				echo json_encode( false );
				die();
			} // if()

			// Unattach the attachment and send the status back as JSON.
			$this->response( $this->unattach_attachment( $attachment_id, $post_id ) );
		} // wpba_unattach_attachment_callback



		/**
		 * Handles the delete link AJAX callback.
		 *
		 * <code>add_action( 'wp_ajax_wpba_delete_attachment', array( &$this, 'wpba_delete_attachment_callback' ) );</code>
		 *
		 * @since   1.4.0
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
			$this->response( $this->delete_attachment( $attachment_id ) );
		} // wpba_delete_attachment_callback
	} // WPBA_AJAX()

	// Instantiate Class
	global $wpba_meta;
	$wpba_helpers = new WPBA_AJAX();
} // if()