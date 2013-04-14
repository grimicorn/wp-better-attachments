<?php
/**
 * WP Better Attachments Settings
 * http://kovshenin.com/2012/the-wordpress-settings-api/
 */
class WPBA_Settings extends WP_Better_Attachments
{

	/**
	 * Constructor
	 */
	public function __construct( $config = array() ) {
		$this->init_hooks();
	} // __construct


	/**
	 * Initialization Hooks
	 */
	public function init_hooks() {
		add_action('admin_menu', array( &$this, 'settings_page_menu' ) );
	} // init_hooks()


	/**
	* Settings Page Menu
	*/
	function settings_page_menu() {
		add_options_page(
			'WP Better Attachments Options',
			'WPBA Settings',
			'manage_options',
			'wp-better-attachments.php',
			array( &$this, 'settings_page' )
		);
	} // settings_page_menu()


	/**
	* Settings Page
	*/
	function settings_page()
	{

	} // settings_page()

} // END Class WPBA_Settings

/**
 * Instantiate class and create return method for easier use later
 */
global $wpba;
$wpba = new WPBA_Settings();

function call_WPBA_Settings() {
	return new WPBA_Settings();
} // call_WPBA_Settings()
if ( is_admin() )
	add_action( 'load-post.php', 'call_WPBA_Settings' );
