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
		protected $meta_box_id = 'wpba_meta_box';


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
			wp_enqueue_style( 'wpba_admin_css', WPBA_URL . '/assets/css/dist/wpba-admin.0478.min.css', array(), null, 'all' );
			wp_register_script( 'wpba_admin_js', WPBA_URL . '/assets/js/dist/wpba-admin.fade.min.js', array(), null, true );
			wp_enqueue_script( 'wpba_admin_js' );
		} // enqueue_admin_assets()
	} // WP_Better_Attachments()

	// Instantiate Class
	global $wp_better_attachments;
	$wp_better_attachments = new WP_Better_Attachments();
} // if()