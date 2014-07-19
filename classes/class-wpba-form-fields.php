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
		 *
		 * @return  string           The input field.
		 */
		public function wp_editor( $id, $label = '', $value = '', $name = null  ) {
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
		 *
		 * @return  string           The multi checkbox field.
		 */
		public function multi_checkbox( $id, $options = array(), $label = '', $value = '', $attrs = array() ) {
			if ( empty( $options ) ) {
				return '';
			} // if()

			$i                    = 1;
			$multi_checkbox_html  = '';
			$wrap_class           = str_replace( '_', '-', $id );
			$multi_checkbox_html .= "<div class='{$wrap_class}-multi-checkbox-wrap wpba-multi-checkbox-wrap clearfix clear'>";
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
				$multi_checkbox_html .= "<input id='{$id}_checkbox_{$i}' name='{$name}' type='checkbox' value='on' {$multi_checkbox_attrs}/>";
				$multi_checkbox_html .= $this->label( "{$id}_checkbox_{$i}", $label ) . '<br>';
				$i = $i + 1;
			} // foreach()
			$multi_checkbox_html .= '</div>';

			return $multi_checkbox_html;
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
		 *
		 * @return  string           The input field.
		 */
		public function textarea( $id, $label = '', $value = '', $attrs = array() ) {
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
		 *
		 * @return  string           The input field.
		 */
		public function input( $id, $label = '', $value = '', $type = 'text', $attrs = array() ) {
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
			$input_html .= '</div>';

			return $input_html;
		} // input()



		/**
		 * Builds all of the inputs for the meta box.
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

				switch ( $type ) {
					case 'editor':
						$input_html .= $this->wp_editor( $id, $label, $value, $name );
						break;

					case 'textarea':
						$input_html .= $this->textarea( $id, $label, $value, $attrs );
						break;

					case 'multi_checkbox':
						$input_html .= $this->multi_checkbox( $id, $options, $label, $value, $attrs );
						break;

					default:
						$input_html .= $this->input( $id, $label, $value, $type, $attrs );
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