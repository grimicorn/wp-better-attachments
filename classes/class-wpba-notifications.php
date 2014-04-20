<?php
/**
 * This class contains the WPBA notifications system.
 * This will allow WPBA to distribute notifications in the WordPress admin.
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
if ( ! class_exists( 'WPBA_Notifications' ) ) {
	class WPBA_Notifications extends WPBA_Helpers {
		/**
		 * The meta key name for the ignore notice user meta.
		 *
		 * @since  1.4.0
		 *
		 * @var    string
		 */
		protected $ignore_meta_name = 'example_ignore_notice';



		/**
		 * WPBA_Notifications class constructor.
		 *
		 * @since  1.4.0
		 *
		 * @param array   $config Class configuration.
		 */
		public function __construct( $config = array() ) {
			parent::__construct();
			$this->_add_wpba_helpers_actions_filters();
		} // __construct()



		/**
		 * Handles adding all of the WPBA helpers actions and filters.
		 *
		 * <code>$this->_add_wpba_helpers_actions_filters();</code>
		 *
		 * @since   1.4.0
		 *
		 * @return  void
		 */
		private function _add_wpba_helpers_actions_filters() {
			// Add the update notice
			add_action( 'admin_notices', array( &$this, 'admin_notice' ) );
			add_action( 'network_admin_notices', array( &$this, 'admin_notice' ) );

			// Allows user to dismiss the notice
			add_action( 'admin_init', array( &$this, 'add_notice_ignore' ) );
		} // _add_wpba_helpers_actions_filters()



		/**
		 * Displays the WP Better Attachments admin notice.
		 *
		 * @return  void
		 */
		function admin_notice() {
			global $current_user ;
			$user_id = $current_user->ID;
			$notice  = $this->get_notifications();

			// Check that the user hasn't already clicked to ignore the message
			if ( ! get_user_meta( $user_id, $this->ignore_meta_name, true ) ) {
				$request_uri    = $_SERVER['REQUEST_URI'];
				$admin_url_path = ( strpos( $request_uri, '?' ) ) ? "{$request_uri}&wpba_notice_ignore=0" : "{$request_uri}?wpba_notice_ignore=0";
				$admin_url_path = str_replace( '/wp-admin', '', $admin_url_path );
				$admin_url      = admin_url( $admin_url_path ); ?>
				<div class="updated">
					<p>
					This is an annoying nag message.  Why do people make these?
					<br>
					<a href="<?php echo esc_url( $admin_url ); ?>">Dismiss</a>
					</p>
				</div>
			<?php } // if()
		} // admin_notice()



		/**
		 * Sets the ignore status.
		 * <code>add_action( 'admin_init', array( &$this, 'add_notice_ignore' ) );</code>
		 *
		 * @since   1.4.0
		 *
		 * @return  integer|boolean  Primary key id for success. False for failure.
		 */
		function add_notice_ignore() {
			global $current_user;
			$user_id = $current_user->ID;
			/* If user clicks to ignore the notice, add that to their user meta */
			if ( isset($_GET['wpba_notice_ignore']) && '0' == $_GET['wpba_notice_ignore'] ) {
				return add_user_meta( $user_id, $this->ignore_meta_name, 'true', true );
			} // if()

			return false;
		} // add_notice_ignore



		/**
		 * Sets the ignore status.
		 * <code>$this->reset_notice_ignore();</code>
		 *
		 *
		 * @return  boolean  False for failure. True for success.
		 */
		function reset_notice_ignore() {
			global $current_user;
			$user_id      = $current_user->ID;
			$current_meta = get_user_meta( $user_id, $this->ignore_meta_name, true );
			return delete_user_meta( $user_id, $this->ignore_meta_name, $current_meta );
		} // reset_notice_ignore



		public function get_notifications() {
			$request_url = 'http://plugin.dev/wpba-notifications/';
			$post_args   = array(
				'body' => array(
					'wpba_notification_check' => '1',
				),
			);
			$post_response = wp_remote_post( $request_url, $post_args );

			if ( is_wp_error( $post_response ) ) {
				return;
			} // if()

			$body = json_decode( wp_remote_retrieve_body( $post_response ) );
			// pp( $body );
		} // get_notifications()
	} // WPBA_Notifications()

	// Instantiate Class
	global $wpba_notifications;
	$wpba_notifications = new WPBA_Notifications();
} // if()
