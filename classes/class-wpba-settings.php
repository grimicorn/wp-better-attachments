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
if ( ! class_exists( 'WPBA_Settings' ) ) {
	class WPBA_Settings extends WPBA_Form_Fields {
		public $page_slug = 'wpba';
		public $option_group;

		/**
		 * WPBA_Settings class constructor.
		 *
		 * @since  1.4.0
		 *
		 * @param  array  $config  Class configuration.
		 */
		public function __construct( $config = array() ) {
			parent::__construct();

			$this->option_group = $this->option_prefix;

			$this->_add_wpba_settings_actions_filters();
		} // __construct()



		/**
		 * Retrieves the settings fields for a specific settings group.
		 *
		 * <code$settings_fields = $this->get_settings_fields( $settings_group );</code>
		 *
		 * @since   1.4.0
		 *
		 * @param   array  $settings_group  The settings group to retrieve fields for.
		 *
		 * @return  array                   The setting fields for the specified group.
		 */
		public function get_settings_fields( $settings_group ) {
			$settings_fields = array();
			$options        = get_option( $this->option_group );

			// Main settings
			$settings_fields["{$this->page_slug}_main"] = $this->_get_main_settings_fields();

			return $settings_fields[$settings_group];
		} // get_settings_fields()



		/**
		 * Settings fields for the main settings group.
		 *
		 * <code>$settings_fields["{$this->page_slug}_main"] = $this->_get_main_settings_fields();</code>
		 *
		 * @since   1.4.0
		 *
		 *
		 * @return  array  The setting fields for the main option group.
		 */
		private function _get_main_settings_fields() {
			$settings_fields = array();
			$options         = get_option( $this->option_group );

			// Text String
			$settings_fields[] = array(
				'id'    => "{$this->page_slug}_text_string",
				'label' => 'Text String',
				'value' => $options['text_string'],
				'type'  => 'text',
				'attrs' => array(
					'name' => "$this->option_group[text_string]",
					'size' => '40',
				),
			);

			return $settings_fields;
		} // _get_main_settings_fields()



		/**
		 * Handles adding all of the WPBA meta actions and filters.
		 *
		 * <code>$this->_add_wpba_settings_actions_filters();</code>
		 *
		 * @internal
		 *
		 * @since   1.4.0
		 *
		 * @return  void
		 */
		private function _add_wpba_settings_actions_filters() {
			add_action( 'admin_menu', array( &$this, 'admin_add_page' ) );
		} // _add_wpba_settings_actions_filters()



		/**
		 * Adds the administrator settings page to the menu.
		 *
		 * <code>add_action( 'admin_menu', array( &$this, 'admin_add_page' ) );</code>
		 *
		 * @since   1.4.0
		 *
		 * @return  void
		 */
		function admin_add_page() {
			add_options_page( 'WPBA Settings', 'WPBA Settings', 'manage_options', "{$this->page_slug}-settings", array( &$this, 'options_page' ) );
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
		} // admin_add_page()



		/**
		 * Adds the option page content.
		 *
		 * <code>add_options_page( 'WPBA Settings', 'WPBA Settings', 'manage_options', $this->page_slug, array( &$this, 'options_page' ) );</code>
		 *
		 * @since   1.4.0
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
		 * @since   1.4.0
		 *
		 * @return  void
		 */
		function admin_init() {
			$main_settings_group = "{$this->page_slug}_main";

			register_setting( $this->option_group, $this->option_group, array( &$this, 'validate_options' ) );
			add_settings_section( $main_settings_group, 'Main Settings', array( &$this, 'main_section_text' ), $this->page_slug );

			$this->add_settings_fields( $main_settings_group );
		} // admin_init()



		/**
		 * Main settings section text.
		 *
		 * @since 1.4.0
		 *
		 * <code>add_settings_section( $main_settings_group, 'Main Settings', array( &$this, 'main_section_text' ), $this->page_slug );</code>
		 *
		 * @return  void
		 */
		function main_section_text() {
		} // main_section_text()



		/**
		 * Handles adding the settings fields for a specific group.
		 *
		 * <code>$this->add_settings_fields( $main_settings_group );</code>
		 *
		 * @since  1.4.0
		 *
		 * @param   string  $settings_group  The settings group the fields should be associated with.
		 *
		 * @return  void
		 */
		public function add_settings_fields( $settings_group ) {
			$settings_fields = $this->get_settings_fields( $settings_group );

			foreach ( $settings_fields as $field ) {
				$args = array(
					'field' => $field,
				);

				$id    = ( isset( $field['id'] ) ) ? $field['id'] : '';
				$label = ( isset( $field['label'] ) ) ? $field['label'] : '';
				add_settings_field( $id, $label, array( &$this, 'output_setting_field' ), $this->page_slug, $settings_group, $args );
			} // foreach()
		} // add_settings_fields()



		/**
		 * Text string setting.
		 *
		 * <code>add_settings_field( $id, $label, array( &$this, 'output_setting_field' ), $this->page_slug, $settings_group, $args );</code>
		 *
		 * @since   1.4.0
		 *
		 * @return  void
		 */
		function output_setting_field( $args ) {
			$field = ( isset( $args['field'] ) ) ? $args['field'] : false;

			if ( ! $field ) {
				return;
			} // if()

			// We do not need a label
			$field['label'] = '';

			// We do not need to add the type to the ID like meta
			$field['add_type_to_id'] = false;

			$this->build_inputs( array( $field ), true );
		} // output_setting_field()



		/**
		 * Handles validating options.
		 *
		 * <code>register_setting( $this->option_group, $this->option_group, array( &$this, 'validate_options' ) );</code>
		 *
		 * @since   1.4.0
		 *
		 * @param   array  $input  Submitted settings fields.
		 *
		 * @return  array          The validate settings fields.
		 */
		function validate_options( $input ) {
			$options = get_option( $this->option_group );

			$options['text_string'] = trim( $input['text_string'] );

			return $options;
		} // validate_options()
	} // WPBA_Settings()

	// Instantiate Class
	global $wpba_settings;
	$wpba_settings = new WPBA_Settings();
} // if()