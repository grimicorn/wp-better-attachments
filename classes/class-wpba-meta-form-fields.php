<?php
/**
 * This class handles all of the form types used in the meta box.
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
if ( ! class_exists( 'WPBA_Meta_Form_Fields' ) ) {
	class WPBA_Meta_Form_Fields extends WP_Better_Attachments {
		/**
		 * WPBA_Meta_Form_Fields class constructor.
		 *
		 * @since  1.4.0
		 *
		 * @param  array  $config  Class configuration.
		 */
		public function __construct( $config = array() ) {
			parent::__construct();
		} // __construct()



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
		 * $attachment_fields .= $wpba_meta_form_fields->build_inputs( $input_fields );
		 *
		 * @since   1.4.0
		 *
		 * @param   array   $inputs  The input(s) information (id,label,value,type,attrs).
		 *
		 * @return  string           The input(s) HTML.
		 */
		public function build_inputs( $inputs = array() ) {
			$input_html = '';

			foreach ( $inputs as $input ) {
				extract( $input );

				$label = ( isset( $label ) ) ? $label : '';
				$value = ( isset( $value ) ) ? $value : '';
				$type  = ( isset( $type ) ) ? $type : 'text';
				$attrs = ( isset( $attrs ) ) ? $attrs : array();

				switch ( $type ) {
					case 'wp_editor':
						$input_html .= $this->wp_editor( $id, $label, $value );
						break;

					case 'textarea':
						$input_html .= $this->textarea( $id, $label, $value, $attrs );
						break;

					default:
						$input_html .= $this->input( $id, $label, $value, $type, $attrs );
						break;
				} // switch()
			} // foreach()

			return $input_html;
		} // build_inputs()



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
		 *
		 * $input_html  = '';
		 * $input_html .= $this->wp_editor( $id, $label, $value );
		 * </code>
		 *
		 * @since   1.4.0
		 *
		 * @param   integer  $id     The ID & name attribute to identify the form field.
		 * @param   string   $label  Optional, the text to be displayed in the label.
		 * @param   string   $value  Optional, the value & placeholder of the form field.
		 * @param   string   $type   Optional, the type of input to create, defaults to text.
		 *
		 * @return  string           The input field.
		 */
		public function wp_editor( $id, $label = '', $value = '' ) {
			// Build the input
			$wrap_class  = str_replace( '_', '-', $id );
			$input_html  = '';
			$input_html .= "<div class='{$wrap_class}-input-wrap wpba-textarea-input-wrap clearfix clear'>";
			$input_html .= $this->label( $id, $label );

			$wp_editor_settings = array(
				'media_buttons' => false,
				'textarea_rows' => 2,
				'teeny'         => true,
				'editor_class'  => 'wpba-wyswig pull-left',
				'quicktags'     => false,
			);
			ob_start();
			wp_editor( 'test', $id, $wp_editor_settings );
			$input_html .= ob_get_clean();

			$input_html .= '</div>';

			return $input_html;
		} // wp_editor()



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
		 * @param   string   $type   Optional, the type of input to create, defaults to text.
		 * @param   array    $attrs  Optional, attributes to add to the input field.
		 *
		 * @return  string           The input field.
		 */
		public function textarea( $id, $label = '', $value = '', $attrs = array() ) {
			$default_attrs = array(
				'class' => 'pull-left',
			);
			$input_attrs = $this->merge_element_attributes( $default_attrs, $attrs );



			// Build the input
			$wrap_class  = str_replace( '_', '-', $id );
			$input_html  = '';
			$input_html .= "<div class='{$wrap_class}-input-wrap wpba-textarea-input-wrap clearfix clear'>";
			$input_html .= $this->label( $id, $label );
			$input_html .= "<textarea id='{$id}' name='{$id}_textarea' {$input_attrs}>{$value}</textarea>";
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
			$default_attrs = array(
			'class' => 'pull-left',
			);
			$input_attrs = $this->merge_element_attributes( $default_attrs, $attrs );

			// Build the input
			$wrap_class  = str_replace( '_', '-', $id );
			$input_html  = '';
			$input_html .= "<div class='{$wrap_class}-input-wrap wpba-{$type}-input-wrap clearfix clear'>";
			$input_html .= $this->label( $id, $label );
			$input_html .= "<input id='{$id}' type='{$type}' name='{$id}_{$type}' value='{$value}' placeholder='{$value}' {$input_attrs}>";
			$input_html .= '</div>';

			return $input_html;
		} // input()


		/**
		 * Merges two sets of attributes together and combines them for a HTML element.
		 *
		 * @param   array   $defaults  The default attributes.
		 * @param   array   $attrs     The supplied attributes
		 *
		 * @return  string             The merged HTML element attributes.
		 */
		public function merge_element_attributes( $defaults, $attrs ) {
			// Merge the attributes together
			foreach ( $defaults as $key => $value ) {
				if ( isset( $attrs[$key] ) ) {
					$attrs[$key] = "{$attrs[$key]} {$value}";
				} else {
					$attrs[$key] = $value;
				} // if/else()
			} // foreach()

			// Flatten the attributes
			$input_attrs = array();
			foreach ( $attrs as $attr => $attr_value ) {
				$attr_value    = ( $attr == 'class' ) ? "{$attr_value} pull-left" : $attr_value;
				$input_attrs[] = "{$attr}='{$attr_value}'";
			} // foreach()
			$input_attrs = trim( implode( ' ', $input_attrs ) );

			return $input_attrs;
		} // merge_element_attributes()
	} // WPBA_Meta_Form_Fields()

	// Instantiate Class
	global $wpba_meta_form_fields;
	$wpba_meta_form_fields = new WPBA_Meta_Form_Fields();
} // if()