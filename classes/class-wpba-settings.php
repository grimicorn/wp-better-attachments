<?php
/**
 * This class controls the settings.
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
		/**
		 * The settings page slug.
		 *
		 * @since  1.4.0
		 *
		 * @var    string
		 */
		public $page_slug = 'wpba';


		public $option_group;



		/**
		 * The current options.
		 *
		 * @since  1.4.0
		 *
		 * @var    array
		 */
		public $options = array();



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

			$this->options = get_option( $this->option_group, array() );

			$this->_add_wpba_settings_actions_filters();
		} // __construct()



		/**
		 * Retrieve a specified setting.
		 *
		 * <code>$setting = $this->get_setting( 'my_setting' );</code>
		 *
		 * @since   1.4.0
		 *
		 * @param   string   $key           The setting to retrieve.
		 * @param   string   $default       Optional, the default value to return if setting does not exist, default empty string.
		 * @param   boolean  $force_update  Optional, forces update check of options, default false.
		 *
		 * @return  mixed                   The setting value.
		 */
		public function get_setting( $key, $default = '', $force_update = false ) {
			$options = ( $force_update ) ? get_option( $this->option_group, array() ) : $this->options;

			if ( isset( $options[$key] ) ) {
				return $options[$key];
			} // if()

			return $default;
		} // get_setting()



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
			$options        = get_option( $this->option_group, array() );

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
			$post_types      = get_post_types();

			unset( $post_types['revision'] );
			unset( $post_types['nav_menu_item'] );
			unset( $post_types['attachment'] );
			unset( $post_types['deprecated_log'] );

			// == Disable Post Types ======================================================
			$disable_post_type_setting_labels = array();
			foreach ( $post_types as $type ) {
				$type_object = get_post_type_object( $type );

				if ( ! is_null( $type_object ) ) {
					$disable_post_type_setting_labels[] = $type_object->labels->singular_name;
				} // if()
			} // foreach()

			$settings_fields[] = array(
				'id'      => "{$this->page_slug}_disable_post_types",
				'label'   => 'Disable Post Types',
				'type'    => 'multi_checkbox',
				'options' => $this->setup_multi_input_options( $disable_post_type_setting_labels, 'disable_post_type' ),
			);

			// == Global Settings ======================================================
			$global_setting_labels = array(
				'Do Not Include Thumbnail',
				'Disable Shortcodes',
				'Disable WPBA Image Crop Editor',
				'Show All Image Sizes WPBA Image Crop Editor',
			);

			$settings_fields[] = array(
				'id'      => "{$this->page_slug}_global_settings",
				'label'   => 'Global Settings',
				'type'    => 'multi_checkbox',
				'options' => $this->setup_multi_input_options( $global_setting_labels, 'global_settings' ),
			);

			// == Crop Editor Settings ======================================================
			$default_crop_msg  = 'Below are all the available attachment sizes that will be cropped from the original image the other sizes will be scaled to fit.  Drag the dashed box to select the portion of the image that you would like to be used for the cropped image.';
			$settings_fields[] = array(
				'id'    => "{$this->page_slug}_crop_editor_message",
				'label' => 'Crop Editor Message',
				'type'  => 'textarea',
				'value' => ( isset( $options['crop_editor_message'] ) and $options['crop_editor_message'] != '' ) ? $options['crop_editor_message'] : $default_crop_msg,
				'attrs'  => array(
					'cols' => '55',
					'rows' => '5',
					'name' => "{$this->option_group}[crop_editor_message]",
				),
			);

			// == Global Upload Button Label ======================================================
			$global_meta_box_title_key = 'global_upload_button_content';
			$settings_fields[]  = array(
				'id'    => "{$this->page_slug}_{$global_meta_box_title_key}",
				'label' => 'Global Upload Button Label',
				'type'  => 'text',
				'value' => ( isset( $options[$global_meta_box_title_key] ) and $options[$global_meta_box_title_key] != '' ) ? $options[$global_meta_box_title_key] : 'Add Attachment(s)',
				'attrs'  => array(
					'size' => '40',
					'name' => "{$this->option_group}[$global_meta_box_title_key]",
				),
			);

			// == Media Table Settings ======================================================
			$media_table_setting_labels = array(
				'Disable Re-attach Link (hover menu)',
				'Edit (column)',
				'Disable Un-attach Link (column)',
				'Disable Re-attach Link (column)',
			);
			$settings_fields[] = array(
				'id'      => "{$this->page_slug}_media_table_settings",
				'label'   => 'Media Table Settings',
				'type'    => 'multi_checkbox',
				'options' => $this->setup_multi_input_options( $media_table_setting_labels, 'media_table_setting' ),
			);

			// == Global Meta Box Title ======================================================
			$global_meta_box_title_key = 'global_meta_box_title_setting';
			$settings_fields[]  = array(
				'id'    => "{$this->page_slug}_{$global_meta_box_title_key}",
				'label' => 'Global Meta Box Title',
				'type'  => 'text',
				'value' => ( isset( $options[$global_meta_box_title_key] ) and $options[$global_meta_box_title_key] != '' ) ? $options[$global_meta_box_title_key] : 'WP Better Attachments',
				'attrs'  => array(
					'size' => '40',
					'name' => "{$this->option_group}[$global_meta_box_title_key]",
				),
			);

			// == Global Meta Box Settings ======================================================
			$media_table_setting_labels = array(
				'Disable Title Editor',
				'Disable Caption Editor',
				'Disable Attachment ID',
				'Disable Un-attach Link',
				'Disable Edit Link',
				'Disable Delete Link',
			);
			$settings_fields[] = array(
				'id'      => "{$this->page_slug}_meta_box_settings",
				'label'   => 'Global Meta Box Settings',
				'type'    => 'multi_checkbox',
				'options' => $this->setup_multi_input_options( $media_table_setting_labels, 'meta_box_setting' ),
			);

			// == File Types ======================================================
			$file_type_setting_labels = array(
				'Disable Image Files',
				'Disable Video Files',
				'Disable Audio Files',
				'Disable Documents',
			);

			$settings_fields[] = array(
				'id'      => "{$this->page_slug}_file_type_setting",
				'label'   => 'File Types',
				'type'    => 'multi_checkbox',
				'options' => $this->setup_multi_input_options( $file_type_setting_labels, 'file_type_setting' ),
			);

			// == Global Edit Modal Settings ======================================================
			$global_edit_modal_setting_labels = array(
				'Disable Caption',
				'Disable Alternative Text',
				'Disable Description',
			);

			$settings_fields[] = array(
				'id'      => "{$this->page_slug}_global_edit_modal_setting",
				'label'   => 'Global Edit Modal Settings',
				'type'    => 'multi_checkbox',
				'options' => $this->setup_multi_input_options( $global_edit_modal_setting_labels, 'global_edit_modal_setting' ),
			);

			// ====================================================================================
			// Post Type Specific Settings
			// ====================================================================================
			foreach ( $post_types as $type ) {
				$type_object = get_post_type_object( $type );
				$type_title  = "{$type_object->labels->singular_name}";

				// == {POSTTYPE} Meta Box Title ======================================================
				$meta_box_title_key = "{$this->page_slug}_{$type}_meta_box_title_setting";
				$settings_fields[]  = array(
					'id'    => "{$this->page_slug}_meta_box_title_{$type}_setting",
					'label' => "{$type_title}  Meta Box Title",
					'type'  => 'text',
					'value' => ( isset( $options[$meta_box_title_key] ) and $options[$meta_box_title_key] != '' ) ? $options[$meta_box_title_key] : 'WP Better Attachments',
					'attrs'  => array(
						'size' => '40',
						'name' => "{$this->option_group}[$meta_box_title_key]",
					),
				);

				// == {POSTTYPE} Settings ======================================================
				$post_type_setting_labels = array(
					'Disable Title Editor (meta box)',
					'Disable Caption Editor (meta box)',
					'Disable Attachment ID (meta box)',
					'Disable Un-attach Link (meta box)',
					'Disable Edit Link (meta box)',
					'Disable Delete Link (meta box)',
					'Disable Caption (edit modal)',
					'Disable Alternative Text (edit modal)',
					'Disable Description (edit modal)',
					'Do Not Include Thumbnail',
				);

				$settings_fields[] = array(
					'id'      => "{$this->page_slug}_{$type}_settings",
					'label'   => "{$type_title} Settings",
					'type'    => 'multi_checkbox',
					'options' => $this->setup_multi_input_options( $post_type_setting_labels, 'global_edit_modal_setting' ),
				);

				// == {POSTTYPE} File Types ======================================================
				$post_type_file_type_setting_labels = array(
					'Disable Image Files',
					'Disable Video Files',
					'Disable Audio Files',
					'Disable Documents',
				);
				$settings_fields[] = array(
					'id'      => "{$this->page_slug}_{$type}_global_edit_modal_setting",
					'label'   => "{$type_title} File Types",
					'type'    => 'multi_checkbox',
					'options' => $this->setup_multi_input_options( $post_type_file_type_setting_labels, 'global_edit_modal_setting' ),
				);

				// == Enable meta box only on these {POSTTYPE} pages ======================================================
				$enable_on_page_key = "enable_only_on_{$type}_pages_setting";
				$settings_fields[]  = array(
					'id'    => "{$this->page_slug}_{$type}_enable_only_on_pages_setting",
					'label' => "Enable meta box only on these {$type_title} pages",
					'type'  => 'text',
					'value' => ( isset( $options[$enable_on_page_key] ) and $options[$enable_on_page_key] != '' ) ? $options[$enable_on_page_key] : '',
					'attrs'  => array(
						'size' => '40',
						'name' => "{$this->option_group}[$enable_on_page_key]",
					),
					'args'  => array(
						'desc'  => 'Comma separated list of page slugs and/or IDs ex: page-title,247',
					),
				);
			} // foreach()
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
			$options = get_option( $this->option_group, array() );
			$options = ( gettype( $options ) == 'array' ) ? $options : array();
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
			// pp( $options );
			// die();
			return $options;
		} // validate_options()
	} // WPBA_Settings()

	// Instantiate Class
	global $wpba_settings;
	$wpba_settings = new WPBA_Settings();
} // if()