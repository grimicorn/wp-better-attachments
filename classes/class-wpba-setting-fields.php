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

if ( ! class_exists( 'WPBA_Setting_Fields' ) ) {

	/**
	 * WPBA Form Fields.
	 */
	class WPBA_Setting_Fields extends WPBA_Form_Fields {
		/**
		 * The settings page slug.
		 *
		 * @since  2.0.0
		 *
		 * @var    string
		 */
		public $page_slug = 'wpba';



		/**
		 * The field id prefix.
		 *
		 * @since  2.0.0
		 *
		 * @var  string
		 */
		private $field_prefix = 'wpba';



		/**
		 * WPBA_Setting_Fields class constructor.
		 *
		 * @since  2.0.0
		 *
		 * @param  array $config  Class configuration.
		 */
		public function __construct( $config = array() ) {
			parent::__construct();
		} // __construct()



		/**
		 * Settings fields for the post type settings group.
		 *
		 * <code>$settings_fields['post_types'] = $this->_post_type_settings_fields();</code>
		 *
		 * @since   2.0.0
		 *
		 * @return  array  The setting fields for the general option group.
		 */
		private function _post_type_settings_fields() {
			$fields     = array();
			$post_types = $this->get_post_types();
			$labels     = array();
			foreach ( $post_types as $post_type ) {
				$post_type_object = get_post_type_object( $post_type );

				if ( ! is_null( $post_type_object ) ) {
					$labels[ $post_type ] = $post_type_object->labels->singular_name;
				} // if()
			} // foreach()

			$fields[] = array(
				'id'      => "{$this->field_prefix}_post_types",
				'label'   => '',
				'type'    => 'multi_checkbox',
				'options' => $this->setup_multi_input_options( $labels, $this->options['post_types'], "{$this->option_group}[post_types]" ),
			);

			return $fields;
		} // _post_type_settings_fields()



		/**
		 * Settings fields for the media settings group.
		 *
		 * <code>$settings_fields['media'] = $this->_media_settings_fields();</code>
		 *
		 * @since   2.0.0
		 *
		 * @return  array  The setting fields for the media option group.
		 */
		private function _media_settings_fields() {
			$fields = array();
			$hover_labels = array(
				'edit'     => 'Edit Link',
				'reattach' => 'Un-attach Link',
				'unattach' => 'Re-attach Link',
			);

			$fields[] = array(
				'id'      => "{$this->field_prefix}_media_table_hover",
				'label'   => 'Hover',
				'type'    => 'multi_checkbox',
				'options' => $this->setup_multi_input_options( $hover_labels, $this->options['media']['hover'], "{$this->option_group}[media][hover]" ),
			);

			$column_labels = array(
				'edit'     => 'Edit Link',
				'reattach' => 'Un-attach Link',
				'unattach' => 'Re-attach Link',
			);

			$fields[] = array(
				'id'      => "{$this->field_prefix}_media_table_column",
				'label'   => 'Column',
				'type'    => 'multi_checkbox',
				'options' => $this->setup_multi_input_options( $column_labels, $this->options['media']['column'], "{$this->option_group}[media][column]" ),
			);

			return $fields;
		} // _media_settings_fields()



		/**
		 * Settings fields for the general settings group.
		 *
		 * <code>$settings_fields['general'] = $this->_general_settings_fields();</code>
		 *
		 * @since   2.0.0
		 *
		 * @return  array  The setting fields for the general option group.
		 */
		private function _general_settings_fields() {
			$settings_fields = array();

			$setting_labels = array(
				'disable_thumbnail'  => 'Disable Featured Image',
				'disable_shortcodes' => 'Disable Shortcodes',
			);

			$settings_fields[] = array(
				'id'      => 'general_settings',
				'label'   => '',
				'type'    => 'multi_checkbox',
				'options' => $this->setup_multi_input_options( $setting_labels, $this->options['general'], "{$this->option_group}[general]" ),
			);

			return $settings_fields;
		} // _general_settings_fields()



		/**
		 * Settings fields for the crop editor settings group.
		 *
		 * <code>$settings_fields[crop_editor] = $this->_crop_editor_settings_fields();</code>
		 *
		 * @since   2.0.0
		 *
		 * @return  array  The setting fields for the crop editor option group.
		 */
		private function _crop_editor_settings_fields() {
			$fields        = array();
			$options       = $this->options['crop_editor'];
			$group         = "{$this->option_group}[crop_editor]";
			$multi_options = $options;

			// Remove message form multi options.
			if ( isset( $multi_options['message'] ) ) { unset( $multi_options['message'] ); }

			$labels = array(
				'disable'   => 'Disable',
				'all_sizes' => 'Allow All Sizes',
			);

			$fields[] = array(
				'id'      => "{$this->field_prefix}_crop_editor",
				'label'   => '',
				'type'    => 'multi_checkbox',
				'options' => $this->setup_multi_input_options( $labels, $multi_options, "{$this->option_group}[crop_editor]" ),
			);

			// Message.
			$fields[] = array(
				'id'    => "{$this->page_slug}_crop_editor_message",
				'label' => 'Message',
				'type'  => 'textarea',
				'value' => $options['message'],
				'attrs'  => array(
					'cols' => '55',
					'rows' => '5',
					'name' => "{$group}[message]",
				),
			);

			return $fields;
		} // _crop_editor_settings_fields()



		/**
		 * Settings fields for the meta box settings group.
		 *
		 * <code>$settings_fields['meta_box'] = $this->_meta_box_settings_fields();</code>
		 *
		 * @since   2.0.0
		 *
		 * @return  array  The setting fields for the meta box option group.
		 */
		private function _meta_box_settings_fields() {
			$fields        = array();
			$options       = $this->options['meta_box'];
			$group         = "{$this->option_group}[meta_box]";
			$multi_options = $options;

			// Remove title form multi options.
			if ( isset( $multi_options['title'] ) ) { unset( $multi_options['title'] ); }

			$labels = array(
				'title_editor'   => 'Disable Title Editor',
				'caption_editor' => 'Disable Caption Editor',
				'attachment_id'  => 'Disable Attachment ID',
				'unattach'       => 'Disable Un-attach Link',
				'edit'           => 'Disable Edit Link',
				'delete'         => 'Disable Delete Link',
			);

			$fields[] = array(
				'id'      => "{$this->field_prefix}_meta_box",
				'label'   => '',
				'type'    => 'multi_checkbox',
				'options' => $this->setup_multi_input_options( $labels, $multi_options, "{$this->option_group}[meta_box]" ),
			);

			// Title.
			$fields[] = array(
				'id'    => "{$this->page_slug}_meta_box_title",
				'label' => 'Title',
				'type'  => 'text',
				'value' => $options['title'],
				'attrs'  => array(
					'name' => "{$group}[title]",
				),
			);

			return $fields;
		} // _meta_box_settings_fields()



		/**
		 * Settings fields for the disabled attachment types settings group.
		 *
		 * <code>$settings_fields['disable_attachment_types'] = $this->_disable_attachment_types_settings_fields();</code>
		 *
		 * @since   2.0.0
		 *
		 * @return  array  The setting fields for the disabled attachment types option group.
		 */
		private function _disable_attachment_types_settings_fields() {
			$fields = array();
			$labels = array(
				'image'    => 'Disable Image Files',
				'video'    => 'Disable Video Files',
				'audio'    => 'Disable Audio Files',
				'document' => 'Disable Documents',
			);

			$fields[] = array(
				'id'      => "{$this->field_prefix}_disable_attachment_types",
				'label'   => '',
				'type'    => 'multi_checkbox',
				'options' => $this->setup_multi_input_options( $labels, $this->options['disable_attachment_types'], "{$this->option_group}[disable_attachment_types]" ),
			);

			return $fields;
		} // _disable_attachment_types_settings_fields()



		/**
		 * Settings fields for the edit modal settings group.
		 *
		 * <code>$settings_fields['edit_modal'] = $this->_edit_modal_settings_fields();</code>
		 *
		 * @since   2.0.0
		 *
		 * @return  array  The setting fields for the edit modal option group.
		 */
		private function _edit_modal_settings_fields() {
			$fields = array();
			$labels = array(
				'disable_caption'          => 'Disable Caption',
				'disable_alternative_text' => 'Disable Alternative Text',
				'disable_description'      => 'Disable Description',
			);

			$fields[] = array(
				'id'      => "{$this->field_prefix}_edit_modal",
				'label'   => '',
				'type'    => 'multi_checkbox',
				'options' => $this->setup_multi_input_options( $labels, $this->options['edit_modal'], "{$this->option_group}[edit_modal]" ),
			);

			return $fields;
		} // _edit_modal_settings_fields()


		/**
		 * Retrieves post type specific settings.
		 *
		 * @param   string $post_type  The post type slug.
		 *
		 * @return  array               The setting fields for the specified post type.
		 */
		public function _get_post_type_settings_fields( $post_type ) {
			$fields = array();

			if ( ! in_array( $post_type, $this->get_post_types() ) ) { return $fields; }

			return $fields;
		} // _get_post_type_settings_fields()



		/**
		 * Retrieves the settings fields for a specific settings group.
		 *
		 * <code$settings_fields = $this->get_settings_fields( $group );</code>
		 *
		 * @since   2.0.0
		 *
		 * @param   array $group  The settings group to retrieve fields for.
		 *
		 * @return  array          The setting fields for the specified group.
		 */
		public function get_settings_fields( $group ) {
			$fields = array();

			// Settings.
			$fields['post_types']               = $this->_post_type_settings_fields();
			$fields['general']                  = $this->_general_settings_fields();
			$fields['media']                    = $this->_media_settings_fields();
			$fields['crop_editor']              = $this->_crop_editor_settings_fields();
			$fields['meta_box']                 = $this->_meta_box_settings_fields();
			$fields['disable_attachment_types'] = $this->_disable_attachment_types_settings_fields();
			$fields['edit_modal']               = $this->_edit_modal_settings_fields();

			// Handle post type settings.
			if ( in_array( $group, $this->get_post_types() ) ) {
				$fields[ $group ] = $this->_get_post_type_settings_fields( $group );
			} // if()

			return ( isset( $fields[ $group ] ) ) ? $fields[ $group ] : array();
		} // get_settings_fields()



		/**
		 * Handles adding the settings fields for a specific group.
		 *
		 * <code>$this->add_settings_fields( $general_settings_group );</code>
		 *
		 * @since  2.0.0
		 *
		 * @param   string $settings_group  The settings group the fields should be associated with.
		 */
		public function add_settings_fields( $settings_group ) {
			$settings_fields = $this->get_settings_fields( $settings_group );

			foreach ( $settings_fields as $field ) {
				$id    = ( isset( $field['id'] ) ) ? $field['id'] : '';
				$title = ( isset( $field['label'] ) ) ? $field['label'] : '';
				$args  = array(
					'field' => $field,
				);

				// Add settings field.
				add_settings_field(
					$id,
					$title,
					array( &$this, 'output_setting_field' ),
					$this->page_slug,
					$settings_group,
					$args
				);
			} // foreach()
		} // add_settings_fields()



		/**
		 * Text string setting.
		 *
		 * <code>add_settings_field( $id, $label, array( &$this, 'output_setting_field' ), $this->page_slug, $settings_group, $args );</code>
		 *
		 * @since   2.0.0
		 *
		 * @param array $args The input arguments.
		 */
		function output_setting_field( $args ) {
			$field = ( isset( $args['field'] ) ) ? $args['field'] : false;

			if ( ! $field ) {
				return;
			} // if()

			// We do not need a label.
			$field['label'] = '';

			$this->build_inputs( array( $field ), true );
		} // output_setting_field()



		/**
		 * Sets up multi input options.
		 *
		 * @param   array  $labels        The options to be created.
		 * @param   array  $options       The input options.
		 * @param   string $option_group  Optional, overwrite the default option key, Default WP_Better_Attachments::option_group.
		 *
		 * @return  array                  The multi input options.
		 */
		public function setup_multi_input_options( $labels, $options, $option_group = null ) {
			$ret_options = array();
			$option_group  = ( is_null( $option_group ) ) ? $this->option_group : $option_group;

			foreach ( $labels as $option_key => $option_label ) {
				$ret_options[] = array(
					'label' => $option_label,
					'value' => ( isset( $options[ $option_key ] ) ) ? $options[ $option_key ] : '',
					'name'  => "{$option_group}[$option_key]",
				);
			} // foreach()

			return $ret_options;
		} // setup_multi_input_options()
	} // WPBA_Setting_Fields()
} // if()
