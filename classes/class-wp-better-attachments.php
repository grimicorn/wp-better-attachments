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
		 * The prefix for all options.
		 *
		 * @todo  allow for multiple meta boxes on a page.
		 *
		 * @since 1.4.0
		 *
		 * @var   string
		 */
		public $option_prefix = 'wpba_options';



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

			$this->attachment_multi_meta_key            = "{$this->meta_box_id}_multi_attach";
			$this->attachment_multi_menu_order_meta_key = "{$this->meta_box_id}_multi_attach_menu_order";
		} // __construct()





		/**
		 * Retrieves the enabled post types.
		 *
		 * @todo    Add setting to restrict post types.
		 *
		 * @since   1.4.0
		 *
		 * @return  array  The enabled post types.
		 */
		public function get_post_types() {
			$post_types = get_post_types();

			// Remove post types that can not have attachments
			unset( $post_types['attachment'] );
			unset( $post_types['revision'] );
			unset( $post_types['nav_menu_item'] );
			unset( $post_types['deprecated_log'] );

			/**
			 * Allows filtering of the allowed post types.
			 *
			 * <code>
			 * function myprefix_wpba_post_types( $post_types ) {
			 * 	unset( $post_types['page'] ); // Removes the "page" post type.
			 * }
			 * add_filter( 'wpba_meta_box_post_types', 'myprefix_wpba_post_types' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 * @todo  Allow for multiple meta boxes.
			 *
			 * @var   array
			 */
			$post_types = apply_filters( "{$this->meta_box_id}_post_types", $post_types );

			return $post_types;
		} // get_post_types()



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
			$wpba_enqueue_screens = array(
				'settings_page_wpba-settings'
			);
			$wpba_enqueue_screens = array_merge( $wpba_enqueue_screens, $this->get_post_types() );
			$current_screen = get_current_screen();
			if ( ! in_array( $current_screen->id, $wpba_enqueue_screens ) ) {
				return;
			} // if()

			// Make sure media scripts are added
			if ( ! did_action( 'wp_enqueue_media' ) ){
				wp_enqueue_media();
			} // if()

			wp_enqueue_style( 'wpba_admin_css', WPBA_URL . '/assets/css/dist/wpba-admin.8f34.min.css', array( 'wp-color-picker' ), null, 'all' );
			wp_register_script( 'wpba_admin_js', WPBA_URL . '/assets/js/dist/wpba-admin.cdb1.min.js', array( 'jquery', 'jquery-ui-datepicker', 'wp-color-picker', 'jquery-ui-sortable' ), null, true );
			wp_enqueue_script( 'wpba_admin_js' );
		} // enqueue_admin_assets()
	} // WP_Better_Attachments()

	// Instantiate Class
	global $wp_better_attachments;
	$wp_better_attachments = new WP_Better_Attachments();
} // if()