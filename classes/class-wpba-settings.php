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

			// General settings
			$settings_fields["{$this->page_slug}_general"] = $this->_get_general_settings_fields();

			return $settings_fields[$settings_group];
		} // get_settings_fields()



		/**
		 * Settings fields for the general settings group.
		 *
		 * <code>$settings_fields["{$this->page_slug}_general"] = $this->_get_general_settings_fields();</code>
		 *
		 * @since   1.4.0
		 *
		 *
		 * @return  array  The setting fields for the general option group.
		 */
		private function _get_general_settings_fields() {
			$settings_fields = array();
			$options         = get_option( $this->option_group );

			// == Disable Post Types ======================================================
			$post_types        = $this->get_post_types();
			$post_type_options = array();
			foreach ( $post_types as $type ) {
				$type_object = get_post_type_object( $type );

				if ( is_null( $type_object ) ) {
					continue;
				} // if()

				$post_type_options[] = array(
					'label' => $type_object->labels->singular_name,
					'value' => ( isset( $options["disable_post_type_{$type}"] ) ) ? $options["disable_post_type_{$type}"] : '',
					'name' => "$this->option_group[disable_post_type_{$type}]",
				);
			} // foreach()

			$settings_fields[] = array(
				'id'      => "{$this->page_slug}_disable_post_types",
				'label'   => 'Disable Post Types',
				'type'    => 'multi_checkbox',
				'options' => $post_type_options,
			);

			// == Global Settings ======================================================
			$settings_fields[] = array(
				'id'      => "{$this->page_slug}_global_settings",
				'label'   => 'Global Settings',
				'type'    => 'multi_checkbox',
				'options' => array(
					array(
						'label' => 'Do Not Include Thumbnail',
						'value' => ( isset( $options['global_setting_do_not_include_thumbnail'] ) ) ? $options['global_setting_do_not_include_thumbnail'] : '',
						'name' => "$this->option_group[global_setting_do_not_include_thumbnail]",
					),
					array(
						'label' => 'Disable Shortcodes',
						'value' => ( isset( $options['global_setting_disable_shortcodes'] ) ) ? $options['global_setting_disable_shortcodes'] : '',
						'name' => "$this->option_group[global_setting_disable_shortcodes]",
					),
					array(
						'label' => 'Disable WPBA Image Crop Editor',
						'value' => ( isset( $options['global_setting_disable_wpba_image_crop_editor'] ) ) ? $options['global_setting_disable_wpba_image_crop_editor'] : '',
						'name' => "$this->option_group[global_setting_disable_wpba_image_crop_editor]",
					),
					array(
						'label' => 'Show All Image Sizes WPBA Image Crop Editor',
						'value' => ( isset( $options['global_setting_show_all_image_sizes_wpba_image_crop_editor'] ) ) ? $options['global_setting_show_all_image_sizes_wpba_image_crop_editor'] : '',
						'name' => "$this->option_group[global_setting_show_all_image_sizes_wpba_image_crop_editor]",
					),
				),
			);

			// == Crop Editor Settings ======================================================
			$settings_fields[] = array(
				'id'    => "{$this->page_slug}_crop_editor_message",
				'label' => 'Crop Editor Message',
				'type'  => 'textarea',
				'attrs'  => array(
					'cols' => '55',
					'rows' => '5',
				),
			);

			// == Media Table Settings ======================================================
			$settings_fields[] = array(
				'id'      => "{$this->page_slug}_media_table_settings",
				'label'   => 'Media Table Settings',
				'type'    => 'multi_checkbox',
				'options' => array(
					array(
						'label' => 'Disable Re-attach Link (hover menu)',
						'value' => ( isset( $options['media_table_setting_disable_re_attach_link_hover_menu'] ) ) ? $options['media_table_setting_disable_re_attach_link_hover_menu'] : '',
						'name' => "$this->option_group[media_table_setting_disable_re_attach_link_hover_menu]",
					),
					array(
						'label' => 'Edit (column)',
						'value' => ( isset( $options['media_table_setting_edit_column'] ) ) ? $options['media_table_setting_edit_column'] : '',
						'name' => "$this->option_group[media_table_setting_edit_column]",
					),
					array(
						'label' => 'Disable Un-attach Link (column)',
						'value' => ( isset( $options['media_table_setting_disable_un_attach_link_column'] ) ) ? $options['media_table_setting_disable_un_attach_link_column'] : '',
						'name' => "$this->option_group[media_table_setting_disable_un_attach_link_column]",
					),
					array(
						'label' => 'Disable Re-attach Link (column)',
						'value' => ( isset( $options['media_table_setting_disable_re_attach_link_column'] ) ) ? $options['media_table_setting_disable_re_attach_link_column'] : '',
						'name' => "$this->option_group[media_table_setting_disable_re_attach_link_column]",
					),
				),
			);

			// == Global Meta Box Settings ======================================================
			$settings_fields[] = array(
				'id'      => "{$this->page_slug}_meta_box_settings",
				'label'   => 'Media Table Settings',
				'type'    => 'multi_checkbox',
				'options' => array(
					array(
						'label' => 'Disable Title Editor',
						'value' => ( isset( $options['meta_box_setting_disable_title_editor'] ) ) ? $options['meta_box_setting_disable_title_editor'] : '',
						'name' => "$this->option_group[meta_box_setting_disable_title_editor]",
					),
					array(
						'label' => 'Disable Caption Editor',
						'value' => ( isset( $options['meta_box_setting_disable_caption_editor'] ) ) ? $options['meta_box_setting_disable_caption_editor'] : '',
						'name' => "$this->option_group[meta_box_setting_disable_caption_editor]",
					),
					array(
						'label' => 'Disable Attachment ID',
						'value' => ( isset( $options['meta_box_setting_disable_attachment_id'] ) ) ? $options['meta_box_setting_disable_attachment_id'] : '',
						'name' => "$this->option_group[meta_box_setting_disable_attachment_id]",
					),
					array(
						'label' => 'Disable Un-attach Link',
						'value' => ( isset( $options['meta_box_setting_disable_un_attach_link'] ) ) ? $options['meta_box_setting_disable_un_attach_link'] : '',
						'name' => "$this->option_group[meta_box_setting_disable_un_attach_link]",
					),
					array(
						'label' => 'Disable Edit Link',
						'value' => ( isset( $options['meta_box_setting_disable_edit_link'] ) ) ? $options['meta_box_setting_disable_edit_link'] : '',
						'name' => "$this->option_group[meta_box_setting_disable_edit_link]",
					),
					array(
						'label' => 'Disable Delete Link',
						'value' => ( isset( $options['meta_box_setting_disable_delete_link'] ) ) ? $options['meta_box_setting_disable_delete_link'] : '',
						'name' => "$this->option_group[meta_box_setting_disable_delete_link]",
					),
				),
			);

			// File Types
			// 	Disable Image Files
			// 	Disable Video Files
			// 	Disable Audio Files
			// 	Disable Documents

			// Global Edit Modal Settings
			// 	Disable Caption
			// 	Disable Alternative Text
			// 	Disable Description

			// {POSTTYPE} Meta Box Title
			// 	text

			// {POSTTYPE} Settings
			// 	Disable Title Editor (meta box)
			// 	Disable Caption Editor (meta box)
			// 	Disable Attachment ID (meta box)
			// 	Disable Un-attach Link (meta box)
			// 	Disable Edit Link (meta box)
			// 	Disable Delete Link (meta box)
			// 	Disable Caption (edit modal)
			// 	Disable Alternative Text (edit modal)
			// 	Disable Description (edit modal)
			// 	Do Not Include Thumbnail

			// {POSTTYPE} File Types
			// 	Disable Image Files
			// 	Disable Video Files
			// 	Disable Audio Files
			// 	Disable Documents

			// Enable meta box only on these {POSTTYPE} pages
			// 	text
			// 		Comma separated list of page slugs ex: slug1,slug2,slug-3

			return $settings_fields;
		} // _get_general_settings_fields()



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
			$general_settings_group = "{$this->page_slug}_general";

			register_setting( $this->option_group, $this->option_group, array( &$this, 'validate_options' ) );

			// General Settings Group
			add_settings_section( $general_settings_group, 'General Settings', array( &$this, 'general_section_text' ), $this->page_slug );
			$this->add_settings_fields( $general_settings_group );
		} // admin_init()



		/**
		 * General settings section text.
		 *
		 * @since 1.4.0
		 *
		 * <code>add_settings_section( $general_settings_group, 'General Settings', array( &$this, 'general_section_text' ), $this->page_slug );</code>
		 *
		 * @return  void
		 */
		function general_section_text() {
		} // general_section_text()



		/**
		 * Handles adding the settings fields for a specific group.
		 *
		 * <code>$this->add_settings_fields( $general_settings_group );</code>
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

			$this->build_inputs( array( $field ), true );
		} // output_setting_field()



		/**
		 * Handles merging the new input options with the current options.
		 *
		 * @param   array  $inputs  The new submitted options.
		 *
		 * @return  array           The merged options.
		 */
		public function merge_options( $inputs ) {
			$options = get_option( $this->option_group );
			$options = array_merge( $options, $inputs );

			// Remove empty options
			foreach ( $options as $key => $option ) {
				if ( ! isset( $inputs[$key] ) ) {
					unset( $options[$key] );
				} // if()
			} // foreach()

			return $options;
		} // merge_options()



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
			$options = $this->merge_options( $input );

			return $options;
		} // validate_options()
	} // WPBA_Settings()

	// Instantiate Class
	global $wpba_settings;
	$wpba_settings = new WPBA_Settings();
} // if()