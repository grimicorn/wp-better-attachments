<?php
/**
 * WP Better Attachments Settings
 * http://kovshenin.com/2012/the-wordpress-settings-api/
 */
if ( !class_exists( 'WPBA_Settings' ) ) :
	class WPBA_Settings extends WP_Better_Attachments {

		/**
		 * @var WP_Settings_API_Bootstrap
		 */
		private $wp_settings_api;

		/**
		 * Constructor
		 */
		function __construct() {
			parent::__construct();
			$this->wp_settings_api = new WP_Settings_API_Bootstrap();

			add_action( 'admin_init', array( $this, 'admin_init') );
			add_action( 'admin_menu', array( $this, 'admin_menu') );
		} // __construct()

		/**
		 * Initialize the settings on admin_init hook
		 */
		function admin_init() {

			//set the settings
			$this->wp_settings_api->set_sections( $this->get_settings_sections() );
			$this->wp_settings_api->set_fields( $this->get_settings_fields() );

			//initialize settings
			$this->wp_settings_api->admin_init();
		} // admin_init()

		/**
		 * Add the menu on admin_menu hook
		 */
		function admin_menu() {
			add_options_page(
				'WP Better Attachments Settings',
				'WPBA Settings',
				'delete_posts',
				'wpba-settings',
				array($this, 'plugin_page')
			);
		} // admin_menu(


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
		 * Returns all the settings fields
		 *
		 * @return array settings fields
		 */
		function get_settings_fields() {
			// Get Post Types and remove the ones we don't need
			$post_types = get_post_types();
			unset( $post_types["attachment"] );
			unset( $post_types["revision"] );
			unset( $post_types["nav_menu_item"] );
			unset( $post_types["deprecated_log"] );

			// Cleanup post types
			$post_type_options = array();
			foreach ( $post_types as $key => $post_type ) {
				// Setup Post Type Disables
				$post_type_obj = get_post_type_object( $post_type );
				$post_types[$key] = $post_type_obj->labels->name;
				// Setup Post Type Settings
				$post_type_options[] = $this->build_post_type_array( $post_type_obj );
			} // foreach()

			// Post Type Disable Settings
			$post_type_disable = array(
				array(
					'name'      => 'wpba-disable-post-types',
					'label'     => __( 'Disable Post Types', 'wpba' ),
					'desc'      => __( '', 'wpba' ),
					'type'      => 'multicheck',
					'options'   => $post_types
				)
			);

			$wpba_settings = array_merge( $post_type_disable, $post_type_options );
			// Settings
			$settings_fields = array(
				'wpba_settings' => $wpba_settings
			);

			return $settings_fields;
		} // get_settings_fields()

		/**
		 * Display the admin page
		 */
		function plugin_page() {
			echo '<div class="wrap">';
			echo '<div id="icon-options-general" class="icon32"></div>';
			echo '<h2>WP Better Attachments Settings</h2>';
			settings_errors( 'wpba-disable-post-types', false, true );

			$this->wp_settings_api->show_navigation();
			$this->wp_settings_api->show_forms();

			echo '</div>';
		} // plugin_page()

		/**
		 * Get all the pages
		 *
		 * @return array page names with key value pairs
		 */
		function get_pages() {
			$pages = get_pages();
			$pages_options = array();
			if ( $pages ) {
					foreach ($pages as $page) {
							$pages_options[$page->ID] = $page->post_title;
					}
			}

			return $pages_options;
		} // get_pages()

		/**
		 * Build post type options array
		 *
		 * @return array page names with key value pairs
		 */
		public function build_post_type_array( $post_type_obj )
		{
			// Post Type Options
			$post_type_options = array(
				'title' 		=>	'Disable Title Editor',
				'caption'		=>	'Disable Caption Editor',
				'thumbnail'	=>	'Do Not Include Thumbnail'
			);

			// Settings Array
			return array(
				'name'      => "wpba-{$post_type_obj->name}-settings",
				'label'     => __( "{$post_type_obj->labels->name} Settings", 'wpba' ),
				'desc'      => __( '', 'wpba' ),
				'type'      => 'multicheck',
				'options'   => $post_type_options
			);
		} // build_post_type_array()
	} // class
endif; // if class_exists

// initiate the class
$settings = new WPBA_Settings();