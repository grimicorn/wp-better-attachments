<?php
/**
 * WP Better Attachments Settings
 * http://kovshenin.com/2012/the-wordpress-settings-api/
 */
class WPBA_Settings extends WP_Better_Attachments
{

	/**
	* @var WP_Settings_API_Bootstrap
	*/
	private $wp_settings_api;

	/**
	* Constructor
	*/
	function __construct()
	{
		parent::__construct();
		$this->wp_settings_api = new WP_Settings_API_Bootstrap();
		add_action( 'admin_init', array( $this, 'admin_init') );
		add_action( 'admin_menu', array( $this, 'admin_menu') );
	} // __construct()

	/**
	* Initialize the settings on admin_init hook
	*/
	function admin_init()
	{

		//set the settings
		$this->wp_settings_api->set_sections( $this->get_settings_sections() );
		$this->wp_settings_api->set_fields( $this->get_settings_fields() );

		//initialize settings
		$this->wp_settings_api->admin_init();
	} // admin_init()


	/**
	* Set up all of the Main settings sections
	*
	* @return array
	*/
	function get_settings_sections() {
		$sections = array(
			array(
				'id' => 'wpba_settings',
				'title' => __( 'Settings', 'wpba' )
			)
		);
		return $sections;
	}

	/**
	* Add the menu on admin_menu hook
	*/
	function admin_menu()
	{
		add_options_page(
			'WP Better Attachments Settings',
			'WP Better Attachments',
			'activate_plugins',
			'wpba-settings',
			array($this, 'plugin_page')
		);
	} // admin_menu(


	/**
	* Returns all the settings fields
	*
	* @return array settings fields
	*/
	function get_settings_fields()
	{
		global $wpba_settings_fields;
		$wpba_settings = array();
		$wpba_settings[] = $wpba_settings_fields->get_post_type_disable_settings();
		$wpba_settings[] = $wpba_settings_fields->get_global_settings();
		$wpba_settings[] = $wpba_settings_fields->get_media_table_settings();
		$wpba_settings[] = $wpba_settings_fields->get_metabox_settings();
		$wpba_settings[] = $wpba_settings_fields->get_attachment_types();
		$wpba_settings[] = $wpba_settings_fields->get_edit_modal_settings();
		$wpba_settings = array_merge( $wpba_settings, $wpba_settings_fields->get_post_type_settings() );

		// Settings
		$settings_fields = array(
			'wpba_settings' => $wpba_settings
		);

		return $settings_fields;
	} // get_settings_fields()


	/**
	* Display the admin page
	*/
	function plugin_page()
	{
		echo '<div class="wrap">';
		echo '<div id="icon-options-general" class="icon32"></div>';
		echo '<h2>WP Better Attachments Settings</h2>';
		settings_errors( 'wpba-disable-post-types', false, true );

		// $this->wp_settings_api->show_navigation();
		$this->wp_settings_api->show_forms();

		echo '</div>';
	} // plugin_page()

	/**
	* Get all the pages
	*
	* @return array page names with key value pairs
	*/
	function get_pages()
	{
		$pages = get_pages();
		$pages_options = array();
		if ( $pages ) {
				foreach ($pages as $page) {
						$pages_options[$page->ID] = $page->post_title;
				}
		}

		return $pages_options;
	} // get_pages()
} // class

// initiate the class
global $wpba_settings;
$wpba_settings = new WPBA_Settings();
