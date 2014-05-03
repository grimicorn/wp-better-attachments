<?php
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
		 * WP_Better_Attachments class constructor.
		 *
		 * @since  1.4.0
		 *
		 * @todo   Only add on required pages.
		 *
		 * @param  array  $config  Class configuration.
		 */
		public function __construct( $config = array() ) {
			// Enqueue Scripts
			add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_public_assets' ) );
			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_assets' ) );

			// Add Global PHP -> JS
			add_action( 'wp_head', array( &$this, 'add_global_public_js' ) );
			add_action( 'admin_head', array( &$this, 'add_global_admin_js' ) );
		} // __construct()



		/**
		 * Adds global public JS object.
		 *
		 * <code>add_action( 'wp_head', array( &$this, 'add_global_public_js' ) );</code>
		 *
		 * @since  1.4.0
		 *
		 * @todo   Only add on required pages.
		 *
		 * @return void
		 */
		public function add_global_public_js() {
			global $wpba_global_js_added;
			if ( isset( $wpba_global_js_added ) and $wpba_global_js_added ) {
				return;
			} // if()
			$wpba_global_js_added = true;

			// Add Global PHP -> JS
			$mdg_globals = array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			);
			$mdg_globals = json_encode( $mdg_globals ); ?>
			<script>var WPBA_PUBLIC_JS = <?php echo wp_kses( $mdg_globals, 'data' ); ?>;</script>
			<?php
		} // add_global_public_js()



		/**
		 * Adds global wp-admin JS object.
		 * <code>add_action( 'admin_head', array( &$this, 'add_global_admin_js' ) );</code>
		 *
		 * @since  1.4.0
		 *
		 * @todo   Only add on required pages.
		 *
		 * @return void
		 */
		public function add_global_admin_js() {
			global $wpba_global_js_added;
			if ( isset( $wpba_global_js_added ) and $wpba_global_js_added ) {
				return;
			} // if()
			$wpba_global_js_added = true;

			// Add Global PHP -> JS
			$mdg_globals = array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			);
			$mdg_globals = json_encode( $mdg_globals ); ?>
			<script>var WPBA_ADMIN_JS = <?php echo wp_kses( $mdg_globals, 'data' ); ?>;</script>
			<?php
		} // add_global_admin_js()



		/**
		 * Enqueues scripts and styles for use on the public front end.
		 *
		 * <code>add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_public_assets' ) );</code>
		 *
		 * @since  1.4.0
		 *
		 * @todo   Only add on required pages.
		 *
		 * @return  void
		 */
		public function enqueue_public_assets() {
			wp_enqueue_style( 'wpba_public_css', WPBA_URL . '/assets/css/dist/wpba.da39.min.css', array(), null, 'all' );
			wp_register_script( 'wpba_public_js', WPBA_URL . '/assets/js/dist/wpba.min.js', array(), null, true );
			wp_enqueue_script( 'wpba_public_js' );
		} // enqueue_public_assets()



		/**
		 * Enqueues scripts and styles for use in wp-admin.
		 *
		 * <code>add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_assets' ) );</code>
		 *
		 * @since  1.4.0
		 *
		 * @return  void
		 */
		public function enqueue_admin_assets() {
			// Make sure media scripts are added
			if ( ! did_action( 'wp_enqueue_media' ) ){
				wp_enqueue_media();
			} // if()

			wp_enqueue_style( 'wpba_admin_css', WPBA_URL . '/assets/css/dist/wpba-admin.8f34.min.css', array(), null, 'all' );
			wp_register_script( 'wpba_admin_js', WPBA_URL . '/assets/js/dist/wpba-admin.3ae5.min.js', array( 'jquery-ui-sortable' ), null, true );
			wp_enqueue_script( 'wpba_admin_js' );
		} // enqueue_admin_assets()



		/**
		* Handles attaching attachments.
		*
		* <code>$attach = $this->attach_attachment( $attachment_id );</code>
		*
		* @since   1.4.0
		*
		* @param   integer  $attachment_id  The attachment post id to attach.
		* @param   integer  $post_id        The post id to attach the attachment to.
		*
		* @return  boolean                  True on success false on failure.
		*/
		public function attach_attachment( $attachment_id, $post_id ) {
			$post_args = array(
				'ID'          => $attachment_id,
				'post_parent' => $post_id,
			);
			$update = wp_update_post( $post_args, true );

			if ( is_wp_error( $update ) ) {
				return false;
			} // if()

			/**
			 * Runs when WP Better Attachments attaches an attachment.
			 *
			 * @since 1.4.0
			 */
			do_action( 'wpba_attachment_attached' );

			return true;
		} // attach_attachment()


		/**
		* Handles un-attaching an attachment.
		*
		* <code>$unattach = $this->unattach_attachment( $attachment_id );</code>
		*
		* @since   1.4.0
		*
		* @param   integer  $attachment_id  The attachment post id to unattach.
		*
		* @return  boolean                  True on success false on failure.
		*/
		public function unattach_attachment( $attachment_id ) {
			$post_args = array(
				'ID'          => $attachment_id,
				'post_parent' => 0,
			);
			$update = wp_update_post( $post_args, true );

			if ( is_wp_error( $update ) ) {
				return false;
			} // if()

			/**
			 * Runs when WP Better Attachments un-attaches an attachment.
			 *
			 * @since 1.4.0
			 */
			do_action( 'wpba_attachment_unattached' );

			return true;
		} // unattach_attachment()



		/**
		 * Handles deleting an attachment.
		 *
		 * <code>$delete = $this->delete_attachment( $attachment_id );</code>
		 *
		 * @param   integer  $attachment_id  The attachment post id to delete.
		 *
		 * @return  boolean                  True on success and false on failure.
		 */
		public function delete_attachment( $attachment_id ) {
			$deleted = wp_delete_attachment( $attachment_id, true );
			if ( false === $deleted ) {
				return false;
			} // if()

			/**
			 * Runs when WP Better Attachments deletes an attachment.
			 *
			 * @since 1.4.0
			 */
			do_action( 'wpba_attachment_deleted' );

			return true;
		} // delete_attachment()
	} // WP_Better_Attachments()

	// Instantiate Class
	global $wp_better_attachments;
	$wp_better_attachments = new WP_Better_Attachments();
} // if()