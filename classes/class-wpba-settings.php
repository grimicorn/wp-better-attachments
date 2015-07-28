<?php
/**
 * This class controls the settings.
 *
 * @version      2.0.0
 *
 * @package      WordPress
 * @subpackage   WP_Better_Attachments
 *
 * @since        2.0.0
 *
 * @author       Dan Holloran          <dholloran@matchboxdesigngroup.com>
 *
 * @copyright    2013 - Present         Dan Holloran
 */

if ( ! class_exists( 'WPBA_Settings' ) ) {

	/**
	 * WPBA Settings Fields
	 */
	class WPBA_Settings extends WPBA_Setting_Fields {
		/**
		 * The current options.
		 *
		 * @since  2.0.0
		 *
		 * @var    array
		 */
		public $options = array();


		/**
		 * Settings page title.
		 *
		 * @var  string
		 */
		private $_page_title = 'WPBA Settings';


		/**
		 * Settings page menu title.
		 *
		 * @var  string
		 */
		private $_menu_title = 'WPBA Settings';



		/**
		 * Settings page menu slug.
		 *
		 * @var  string
		 */
		private $_menu_slug  = 'wpba-settings';



		/**
		 * WPBA_Settings class constructor.
		 *
		 * @since  2.0.0
		 *
		 * @param  array $config  Class configuration.
		 */
		public function __construct( $config = array() ) {
			parent::__construct();

			// Set options.
			$this->options = $this->get_options();

			// Add filters.
			$this->_add_wpba_settings_actions_filters();
		} // __construct()



		/**
		 * Handles adding all of the WPBA meta actions and filters.
		 *
		 * <code>$this->_add_wpba_settings_actions_filters();</code>
		 *
		 * @internal
		 *
		 * @since   2.0.0
		 *
		 * @return  void
		 */
		private function _add_wpba_settings_actions_filters() {
			// Add the settings page.
			add_action( 'admin_menu', array( &$this, 'add_options_page' ) );

			// Initialize the settings.
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
		} // _add_wpba_settings_actions_filters()



		/**
		 * Adds the administrator settings page to the menu.
		 *
		 * <code>add_action( 'admin_menu', array( &$this, 'add_options_page' ) );</code>
		 *
		 * @since   2.0.0
		 *
		 * @return  void
		 */
		function add_options_page() {
			add_options_page(
				$this->_page_title,
				$this->_menu_title,
				'manage_options',
				$this->_menu_slug,
				array( &$this, 'options_page' )
			);
		} // add_options_page()



		/**
		 * Adds the option page content.
		 *
		 * <code>add_options_page( 'WPBA Settings', 'WPBA Settings', 'manage_options', $this->page_slug, array( &$this, 'options_page' ) );</code>
		 *
		 * @since   2.0.0
		 *
		 * @return  void
		 */
		function options_page() {
			$this->get_template_part( 'admin/settings-page' );
		} // options_page()



		/**
		 * Adds the administrator settings.
		 *
		 * <code>add_action( 'admin_init', array( &$this, 'admin_init' ) );</code>
		 *
		 * @since   2.0.0
		 *
		 * @return  void
		 */
		function admin_init() {
			// General Settings.
			$options_names = array(
				'post_types'               => 'Enabled Post Types',
				'disable_attachment_types' => 'Disabled Attachment Types',
				'general'                  => 'General Settings',
				'crop_editor'              => 'Crop Editor Settings',
				'media'                    => 'Media Settings',
				'meta_box'                 => 'Meta Box Settings',
				'edit_modal'               => 'Edit Modal Settings',
			);

			// Post Type Specific Settings.
			$post_types = $this->get_post_types();
			foreach ( $post_types as $post_type ) {
				// Get post type object.
				$obj = get_post_type_object( $post_type );

				// Make sure we got something back.
				if ( is_null( $obj ) ) { continue; }

				$options_names[ $post_type ] = "{$obj->labels->singular_name} Settings";
			} // foreach()

			foreach ( $options_names as $option_name => $option_title ) {
				// Register the setting.
				register_setting(
					$this->option_group,
					$option_name,
					array( &$this, 'validate_options' )
				);

				// Settings Section.
				add_settings_section(
					$option_name,
					$option_title,
					array( &$this, 'section_text' ),
					$this->page_slug
				);

				// Add setting fields.
				$this->add_settings_fields( $option_name );
			} // foreach()
		} // admin_init()



		/**
		 * Settings section text.
		 *
		 * @since 2.0.0
		 *
		 * <code>add_settings_section( $settings_group, 'Settings Title', array( &$this, 'section_text' ), $this->page_slug );</code>
		 *
		 * @return  void
		 */
		function section_text() {
		} // section_text()



		/**
		 * Handles merging the new input options with the current options.
		 *
		 * @param   array $inputs  The new submitted options.
		 *
		 * @return  array           The merged options.
		 */
		public function merge_options( $inputs ) {
			$options = $this->options;
			$options = ( 'array' === gettype( $options ) ) ? $options : array();
			$options = array_merge( $options, $inputs );

			// Remove empty options.
			foreach ( $options as $key => $option ) {
				if ( ! isset( $inputs[ $key ] ) ) {
					unset( $options[ $key ] );
				} // if()
			} // foreach()

			return $options;
		} // merge_options()



		/**
		 * Handles validating options.
		 *
		 * <code>register_setting( $this->option_group, $this->option_group, array( &$this, 'validate_options' ) );</code>
		 *
		 * @since   2.0.0
		 *
		 * @param   array $input  Submitted settings fields.
		 *
		 * @return  array          The validate settings fields.
		 */
		function validate_options( $input ) {
			$options = $this->merge_options( $input );

			return $options;
		} // validate_options()
	} // WPBA_Settings()

	// Instantiate Class.
	global $wpba_settings;
	$wpba_settings = new WPBA_Settings();
} // if()
