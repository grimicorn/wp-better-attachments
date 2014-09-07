<?php
/**
 * This class handles all of the form types used.
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
if ( ! class_exists( 'WPBA_Form_Fields' ) ) {
	class WPBA_Form_Fields extends WPBA_Helpers {
		/**
		 * WPBA_Form_Fields class constructor.
		 *
		 * @since  1.4.0
		 *
		 * @param  array  $config  Class configuration.
		 */
		public function __construct( $config = array() ) {
			parent::__construct();
		} // __construct()



		/**
		 * Creates a label for a form input field.
		 *
		 * <code>$this->label( $label, $id );</code>
		 *
		 * @since   1.4.0
		 *
		 * @param   string  $id     The input fields ID value.
		 * @param   string  $label  The text to be displayed in the label.
		 *
		 * @return  string          The label for the form input field.
		 */
		public function label( $id, $label ) {
			if ( $label == '' ) {
				return '';
			} // if()

			return "<label for='{$id}' class='pull-left'>{$label}</label>";
		} // label()



		/**
		 * Creates a description for a form input field.
		 *
		 * <code>$this->description( $description, $id );</code>
		 *
		 * @since   1.4.0
		 *
		 * @param   string  $description  The text to be displayed in the description.
		 *
		 * @return  string          The description for the form input field.
		 */
		public function description( $description ) {
			if ( $description == '' ) {
				return '';
			} // if()

			return "<p class='description'>{$description}</p>";
		} // description()



		/**
		 * Creates a wp_editor instance.
		 *
		 * <code>
		 * $label = ( isset( $label ) ) ? $label : '';
		 * $value = ( isset( $value ) ) ? $value : '';
		 * $type  = ( isset( $type ) ) ? $type : 'text';
		 * $name  = ( isset( $name ) ) ? $name : $id;
		 *
		 * $input_html  = '';
		 * $input_html .= $this->wp_editor( $id, $label, $value, $name );
		 * </code>
		 *
		 * @since   1.4.0
		 *
		 * @param   integer  $id     The ID & name attribute to identify the form field.
		 * @param   string   $label  Optional, the text to be displayed in the label.
		 * @param   string   $value  Optional, the value & placeholder of the form field.
		 * @param   string   $name   Optional, The name assigned to the generated textarea and passed parameter when the form is submitted. (may include [] to pass data as array).
		 * @param   array    $args   Optional, Any extra arguments to be passed, default none.
		 *
		 * @return  string           The input field.
		 */
		public function wp_editor( $id, $label = '', $value = '', $name = null, $args = array()  ) {
			// Clean up $args
			$desc = ( isset( $args['desc'] ) ) ? $args['desc'] : '';

			// Adds a AJAX loading class so the editor can be initialized
			$is_add_attachments = ( isset( $_POST['action'] ) and $_POST['action'] = 'wpba_add_attachments' );
			$ajax_class         = ( $is_add_attachments ) ? ' ajax' : '';

			// Build the input
			$wrap_class    = str_replace( '_', '-', $id );
			$textarea_name = ( is_null( $name ) ) ? $id : $name;
			$input_html    = '';
			$input_html   .= "<div class='{$wrap_class}-input-wrap wpba-wyswig-input-wrap clearfix clear{$ajax_class}'>";
			$input_html   .= $this->label( $id, $label );

			$wp_editor_settings = array(
				'editor_id'     => $id,
				'media_buttons' => false,
				'textarea_rows' => 2,
				'textarea_name' => $textarea_name,
				'teeny'         => true,
				'editor_class'  => 'wpba-wyswig pull-left',
				'quicktags'     => false,
			);
			ob_start();
			wp_editor( html_entity_decode( $value ), "{$id}_editor", $wp_editor_settings );
			$input_html .= ob_get_clean();

			$input_html .= $this->description( $desc );

			$input_html .= '</div>';

			return $input_html;
		} // wp_editor()



		/**
		 * Creates a multiple check box field field.
		 *
		 * <code>
		 * $label = ( isset( $label ) ) ? $label : '';
		 * $value = ( isset( $value ) ) ? $value : '';
		 * $attrs = ( isset( $attrs ) ) ? $attrs : array();
		 * $options = array(
		 * 	array(
		 * 		'label' => 'Option Name',
		 * 		'value' => 'Option Value'
		 * 	),
		 * );
		 *
		 * $multi_checkbox_html  = '';
		 * $multi_checkbox_html .= $this->multi_checkbox( $id, $options, $label, $value, $attrs );
		 * </code>
		 *
		 * @since   1.4.0
		 *
		 * @param   integer  $id       The ID & name attribute to identify the form field.
		 * @param   array    $options  The check boxes to be created.
		 * @param   string   $label    Optional, the text to be displayed in the label.
		 * @param   array    $attrs    Optional, attributes to add to the multi checkbox field.
		 * @param   array    $args   Optional, Any extra arguments to be passed, default none.
		 *
		 * @return  string           The multi checkbox field.
		 */
		public function multi_checkbox( $id, $options = array(), $label = '', $value = '', $attrs = array(), $args = array() ) {
			if ( empty( $options ) ) {
				return '';
			} // if()

			// Clean up $args
			$desc = ( isset( $args['desc'] ) ) ? $args['desc'] : '';

			$i           = 1;
			$input_html  = '';
			$wrap_class  = str_replace( '_', '-', $id );
			$input_html .= "<div class='{$wrap_class}-multi-checkbox-wrap wpba-multi-checkbox-wrap clearfix clear'>";
			foreach ( $options as $option ) {
				$value = ( isset( $option['value'] ) ) ? $option['value'] : '';
				$label = ( isset( $option['label'] ) ) ? $option['label'] : '';
				$name  = ( isset( $option['name'] ) ) ? $option['name'] : "{$id}_{$i}";

				$defaults = array(
					'class' => 'pull-left',
				);

				// Handle the checked attribute
				if ( $value != '' ) {
					$defaults['checked'] = 'checked';
				} // if()

				// Merge the attributes
				$multi_checkbox_attrs = $this->merge_element_attributes( $defaults, $attrs );

				// Build the multi_checkbox
				$input_html .= "<input id='{$id}_checkbox_{$i}' name='{$name}' type='checkbox' value='on' {$multi_checkbox_attrs}/>";
				$input_html .= $this->label( "{$id}_checkbox_{$i}", $label ) . '<br>';
				$i = $i + 1;
			} // foreach()

			$input_html .= $this->description( $desc );

			$input_html .= '</div>';

			return $input_html;
		} // multi_checkbox()



		/**
		 * Creates a <textarea>.
		 *
		 * <code>
		 * $label = ( isset( $label ) ) ? $label : '';
		 * $value = ( isset( $value ) ) ? $value : '';
		 * $type  = ( isset( $type ) ) ? $type : 'text';
		 * $attrs = ( isset( $attrs ) ) ? $attrs : array();
		 *
		 * $input_html  = '';
		 * $input_html .= $this->textarea( $id, $label, $value, $attrs );
		 * </code>
		 *
		 * @since   1.4.0
		 *
		 * @param   integer  $id     The ID & name attribute to identify the form field.
		 * @param   string   $label  Optional, the text to be displayed in the label.
		 * @param   string   $value  Optional, the value & placeholder of the form field.
		 * @param   array    $attrs  Optional, attributes to add to the input field.
		 * @param   array    $args   Optional, Any extra arguments to be passed, default none.
		 *
		 * @return  string           The input field.
		 */
		public function textarea( $id, $label = '', $value = '', $attrs = array(), $args = array() ) {
			// Clean up $args
			$desc = ( isset( $args['desc'] ) ) ? $args['desc'] : '';

			$defaults = array(
				'class' => 'pull-left',
				'name'  => $id,
			);
			$input_attrs = $this->merge_element_attributes( $defaults, $attrs );

			// Build the input
			$wrap_class  = str_replace( '_', '-', $id );
			$input_html  = '';
			$input_html .= "<div class='{$wrap_class}-input-wrap wpba-textarea-input-wrap clearfix clear'>";
			$input_html .= $this->label( $id, $label );
			$input_html .= "<textarea id='{$id}_textarea' {$input_attrs}>{$value}</textarea>";
			$input_html .= $this->description( $desc );
			$input_html .= '</div>';

			return $input_html;
		} // textarea()


		/**
		 * Creates an <input> field.
		 *
		 * <code>
		 * $label = ( isset( $label ) ) ? $label : '';
		 * $value = ( isset( $value ) ) ? $value : '';
		 * $type  = ( isset( $type ) ) ? $type : 'text';
		 * $attrs = ( isset( $attrs ) ) ? $attrs : array();
		 *
		 * $input_html  = '';
		 * $input_html .= $this->input( $id, $label, $value, $type, $attrs );
		 * </code>
		 *
		 * @since   1.4.0
		 *
		 * @param   integer  $id     The ID & name attribute to identify the form field.
		 * @param   string   $label  Optional, the text to be displayed in the label.
		 * @param   string   $value  Optional, the value & placeholder of the form field.
		 * @param   string   $type   Optional, the type of input to create, defaults to text.
		 * @param   array    $attrs  Optional, attributes to add to the input field.
		 * @param   array    $args   Optional, Any extra arguments to be passed, default none.
		 *
		 * @return  string           The input field.
		 */
		public function input( $id, $label = '', $value = '', $type = 'text', $attrs = array(), $args = array() ) {
			// Clean up $args
			$desc = ( isset( $args['desc'] ) ) ? $args['desc'] : '';

			$defaults = array(
				'class' => 'pull-left',
				'name'  => $id,
			);

			// Merge the attributes
			$input_attrs = $this->merge_element_attributes( $defaults, $attrs );

			// Build the input
			$wrap_class  = str_replace( '_', '-', $id );
			$input_html  = '';
			$input_html .= "<div class='{$wrap_class}-input-wrap wpba-{$type}-input-wrap clearfix clear'>";
			$input_html .= $this->label( $id, $label );
			$input_html .= "<input id='{$id}_{$type}' type='{$type}' value='{$value}' placeholder='{$value}' {$input_attrs}>";
			$input_html .= $this->description( $desc );
			$input_html .= '</div>';

			return $input_html;
		} // input()



		/**
		 * Sets up multi input options.
		 *
		 * @param   array   $labels  The options to be created.
		 * @param   string  $prefix  The setting prefix.
		 *
		 * @return  array                         The multi input options.
		 */
		public function setup_multi_input_options( $labels, $prefix ) {
			$ret_options = array();
			$options     = get_option( $this->option_group, array() );

			foreach ( $labels as $label ) {
				$option_key    = trim( "{$prefix}_" . str_replace( '-', '_', sanitize_title( $label ) ), '_' );
				$ret_options[] = array(
					'label' => $label,
					'value' => ( isset( $options[$option_key] ) ) ? $options[$option_key] : '',
					'name' => "{$this->option_group}[$option_key]",
				);
			} // foreach()

			return $ret_options;
		} // setup_multi_input_options()


		/**
		 * Builds all of the inputs.
		 *
		 * <code>
		 * $attachment_fields = '';
		 * $input_fields      = array();
		 *
		 * // Attachment title
		 * $input_fields['post_title'] = array(
		 * 	'id'    => 'post_title',
		 *  'label' => 'Title',
		 *  'value' => $attachment->post_title,
		 *  'type'  => 'text',
		 *  'attrs' => array(),
		 * );
		 * $attachment_fields .= $wpba_form_fields->build_inputs( $input_fields );
		 *
		 * @since   1.4.0
		 *
		 * @todo    Document input types.
		 *
		 * @param   array    $inputs  The input(s) information (id,label,value,type,attrs).
		 * @param   boolean  $echo    Optional, if the inputs should be echoed out once built.
		 *
		 * @return  string           The input(s) HTML.
		 */
		public function build_inputs( $inputs = array(), $echo = false ) {
			$input_html   = '';
			$allowed_html = $this->get_form_kses_allowed_html();

			foreach ( $inputs as $input ) {
				extract( $input );

				$id      = ( isset( $id ) ) ? $id : '';
				$label   = ( isset( $label ) ) ? $label : '';
				$value   = ( isset( $value ) ) ? $value : '';
				$type    = ( isset( $type ) ) ? $type : 'text';
				$attrs   = ( isset( $attrs ) ) ? $attrs : array();
				$options = ( isset( $options ) ) ? $options : array();
				$name    = ( isset( $attrs['name'] ) ) ? $attrs['name'] : $id;
				$args    = ( isset( $args ) ) ? $args : array();

				switch ( $type ) {
					case 'editor':
						$input_html .= $this->wp_editor( $id, $label, $value, $name, $args );
						break;

					case 'textarea':
						$input_html .= $this->textarea( $id, $label, $value, $attrs, $args );
						break;

					case 'multi_checkbox':
						$input_html .= $this->multi_checkbox( $id, $options, $label, $value, $attrs, $args );
						break;

					default:
						$input_html .= $this->input( $id, $label, $value, $type, $attrs, $args );
						break;
				} // switch()
			} // foreach()

			if ( $echo ) {
				echo wp_kses( $input_html, $allowed_html );
			} // if()

			return $input_html;
		} // build_inputs()
	} // WPBA_Form_Fields()

	// Instantiate Class
	global $wpba_form_fields;
	$wpba_form_fields = new WPBA_Form_Fields();
} // if()