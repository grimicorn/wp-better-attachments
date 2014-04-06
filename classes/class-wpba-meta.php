<?php
/**
 * This class contains anything to do with the Meta box or meta CRUD.
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
if ( ! class_exists( 'WPBA_Meta' ) ) {
	class WPBA_Meta extends WPBA_Helpers {
		/**
		 * The title for the meta box.
		 *
		 * @since  1.4.0
		 *
		 * @todo  Add setting to alter the meta box title.
		 *
		 * @var   string
		 */
		public $meta_box_title = 'WP Better Attachments';


		/**
		 * The ID for the meta box.
		 *
		 * @todo  allow for multiple meta boxes on a page.
		 *
		 * @since 1.4.0
		 *
		 * @var   string
		 */
		private $_meta_box_id = 'wpba_meta_box';



		/**
		 * WPBA_Meta class constructor.
		 *
		 * @since  1.4.0
		 *
		 * @param  array  $config  Class configuration.
		 */
		public function __construct( $config = array() ) {
			parent::__construct();

			$this->_add_wpba_meta_actions_filters();
		} // __construct()


		/**
		 * Handles adding all of the WPBA meta actions and filters.
		 *
		 * <code>$this->_add_wpba_meta_actions_filters();</code>
		 *
		 * @since   1.4.0
		 *
		 * @return  void
		 */
		private function _add_wpba_meta_actions_filters() {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			add_action( 'save_post', array( $this, 'save' ) );
		} // _add_wpba_meta_actions_filters()



		/**
		 * Adds the meta box container.
		 *
		 * <code>add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );</code>
		 *
		 * @todo    setting to limit adding to post type.
		 *
		 * @since   1.4.0
		 *
		 * @param   string  $post_type  The current post type.
		 *
		 * @return  void
		 */
		public function add_meta_box( $post_type ) {
			$post_types = $this->get_post_types();

			if ( in_array( $post_type, $post_types ) ) {
				add_meta_box(
					$this->_meta_box_id,
					__( $this->meta_box_title, 'wpba' ),
					array( $this, 'render_meta_box_content' ),
					$post_type,
					'advanced',
					'high'
				);
			} // if()
		} // add_meta_box()



		/**
		 * Saves the meta when the post is saved.
		 *
		 * <code>add_action( 'save_post', array( $this, 'save' ) );</code>
		 *
		 * @since   1.4.0
		 *
		 * @param   integer  $post_id The ID of the post being saved.
		 *
		 * @return  void
		 */
		public function save( $post_id ) {
			// Check if our nonce is set.
			if ( ! isset( $_POST["{$this->_meta_box_id}_nonce"] ) ) {
				return $post_id;
			} // if()

			$nonce = $_POST["{$this->_meta_box_id}_nonce"];

			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $nonce, '{$this->_meta_box_id}_save_fields' ) ){
				return $post_id;
			} // if()

			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
				return $post_id;
			} // if()

			// Check the user's permissions.
			if ( 'page' == $_POST['post_type'] ) {
				if ( ! current_user_can( 'edit_page', $post_id ) ) {
					return $post_id;
				} // if()
			} else {
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return $post_id;
				} // if()
			} // if/else()

			// Sanitize the user input.
			$fields = $_POST;
			$fields = $this->_sanitize_fields( $fields );

			// Update the attachment meta.
			$attachment_id = $post_id;
			$this->_update_attachment_meta( $attachment_id, $fields );
		} // save



		/**
		 * Handles sanitizing of the meta box fields.
		 *
		 * <code>
		 * $fields = array( 'input_id_text' => 'value' );
		 * $fields = $this->_sanitize_fields( $fields );
		 * </code>
		 *
		 * @since   1.4.0
		 *
		 * @param   array   $fields  The fields to sanitize.
		 *
		 * @return  array            The sanitized fields.
		 */
		private function _sanitize_fields( $fields ) {

			return $fields;
		} // _sanitize_fields()



		/**
		 * Updates the meta.
		 *
		 * <code></code>
		 *
		 * @todo    Add code example.
		 * @todo    Build out method.
		 *
		 * @since   1.4.0
		 *
		 * @param   integer $post_id The ID of the post being updated.
		 * @param   array   $fields  The fields to update.
		 *
		 * @return  void
		 */
		private function _update_attachment_meta( $post_id, $fields ) {} // _update_attachment_meta()



		/**
		 * Render Meta Box content.
		 *
		 * <code>
		 * add_meta_box(
		 * 	$this->_meta_box_id,
		 * 	__( $this->meta_box_title, 'wpba' ),
		 * 	array( $this, 'render_meta_box_content' ),
		 * 	$post_type,
		 * 	'advanced',
		 * 	'high'
		 * );
		 * </code>
		 *
		 * @since   1.4.0
		 *
		 * @uses    WPBA_Meta_Form_Fields
		 *
		 * @param   object  $post  The post object.
		 *
		 * @return  void
		 */
		public function render_meta_box_content( $post ) {
			global $wpba_meta_form_fields;

			$attachments  = $this->get_attachments( $post, true );
			$allowed_html = $this->get_form_kses_allowed_html();

			// Add an nonce field so we can check for it later.
			wp_nonce_field( '{$this->_meta_box_id}_save_fields', "{$this->_meta_box_id}_nonce" );

			echo '<ul id="wpba_sortable" class="wpba-attachment-form-fields list-unstyled">';
			foreach ( $attachments as $attachment ) {
				echo "<li id='attachment_{$attachment->ID}' class='ui-state-default wpba-sortable-item'>";
				echo '<i class="dashicons dashicons-menu wpba-sort-handle"></i>';
				echo wp_kses( $this->build_attachment_thumbnail( $attachment ), $allowed_html );
				echo wp_kses( $this->build_attachment_fields( $attachment ), $allowed_html );
				echo '</li>';
			} // foreach
			echo '</ul>';
		} // render_meta_box_content()



		/**
		 * Attachment menu.
		 *
		 * <code>$attachment_menu = $this->attachment_menu( $attachment );</code>
		 *
		 * @since   1.4.0
		 *
		 * @param   object  $attachment  The attachment post.
		 *
		 * @return  string               The attachment menu HTML.
		 */
		public function attachment_menu( $attachment ) {
			$edit_link = admin_url( "post.php?post={$attachment->ID}&action=edit" );

			$menu = '';
			$menu .= "<ul class='list-unstyled pull-left wpba-edit-attachment hide-if-no-js' data-id='{$attachment->ID}'>";
			$menu .= '<li class="pull-left"><a href="#" class="wpba-unattach">Un-attach</a></li>';
			$menu .= "<li class='pull-left'><a href='{$edit_link}' class='wpba-edit' target='_blank'>Edit</a></li>";
			$menu .= '<li class="pull-left"><a href="#" class="wpba-delete">Delete</a></li>';
			$menu .= '</ul>';

			return $menu;
		} // attachment_menu()



		/**
		 * Retrieves the attachment thumbnail.
		 *
		 * <code>
		 * foreach ( $attachments as $attachment ) {
		 * 	$allowed_html = $this->get_form_kses_allowed_html();
		 * 	echo wp_kses( $this->build_attachment_thumbnail( $attachment ), $allowed_html );
		 * } // foreach
		 * </code>
		 *
		 * @since   1.4.0
		 *
		 * @param   object  $attachment  The attachment post.
		 *
		 * @return  string               The attachment thumbnail HTML.
		 */
		public function build_attachment_thumbnail( $attachment ) {
			$attachment_thumbnail  = '';
			$attachment_thumbnail .= '<div class="wpba-attachment-image-wrap pull-left">';
			$attachment_thumbnail .= $this->attachment_menu( $attachment );
			$attachment_thumbnail .= wp_get_attachment_image( $attachment->ID, 'thumbnail', true, array( 'class' => 'wpba-attachment-image' ) );
			$attachment_thumbnail .= '</div>';

			return $attachment_thumbnail;
		} // build_attachment_thumbnail()



		/**
		 * Builds the attachment fields.
		 *
		 * <code>
		 * foreach ( $attachments as $attachment ) {
		 * 	$allowed_html = $this->get_form_kses_allowed_html();
		 * 	echo wp_kses( $this->build_attachment_fields( $attachment ), $allowed_html );
		 * } // foreach
		 * </code>
		 *
		 * @since   1.4.0
		 *
		 * @uses    WPBA_Meta_Form_Fields
		 *
		 * @param   object  $attachment  The attachment post.
		 *
		 * @return  string               The attachment form fields.
		 */
		public function build_attachment_fields( $attachment ) {
			global $wpba_meta_form_fields;

			$attachment_id_base = "attachment_{$attachment->ID}";
			$atttachment_fields = '';
			$input_fields = array();

			// Attachment title
			$input_fields['post_title'] = array(
				'id'    => 'post_title',
				'label' => 'Title',
				'value' => $attachment->post_title,
				'type'  => 'text',
				'attrs' => array(),
			);

			// Attachment caption
			$input_fields['post_excerpt'] = array(
				'id'    => 'post_excerpt',
				'label' => 'Caption',
				'value' => $attachment->post_excerpt,
				'type'  => 'textarea',
				'attrs' => array(),
			);

			if ( wp_attachment_is_image( $attachment->ID ) ) {
				// Attachment alt text
				$input_fields['alt_text'] = array(
					'id'    => '_wp_attachment_image_alt',
					'label' => 'Alt Text',
					'value' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
					'type'  => 'text',
					'attrs' => array(),
				);
			} // if()

			// Attachment description
			$input_fields['post_content'] = array(
				'id'    => 'post_content',
				'label' => 'Description',
				'value' => $attachment->post_content,
				'type'  => 'textarea',
				'attrs' => array(),
			);



			/**
			 * Allows filtering of the input fields, add/remove fields.
			 *
			 * <code>
			 * function myprefix_wpba_input_fields( $input_fields ) {
			 * 	unset( $input_fields['alt_text'] ); // Removes the Alt text input.
			 * }
			 * add_filter( 'wpba_meta_box_input_fields', 'filter_test' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 * @todo  Allow for multiple meta boxes.
			 *
			 * @var   array
			 */
			$input_fields = apply_filters( "{$this->_meta_box_id}_input_fields", $input_fields );



			// Attachment ID field
			$input_fields['ID'] = array(
				'id'    => 'ID',
				'label' => '',
				'value' => $attachment->ID,
				'type'  => 'hidden',
			);

			// Attachment menu order field
			$input_fields['menu_order'] = array(
				'id'    => 'menu_order',
				'label' => '',
				'value' => $attachment->menu_order,
				'type'  => 'hidden',
			);

			$attachment_fields  = '';
			$attachment_fields .= '<div class="wpba-attachment-fields-wrap pull-left">';
			$attachment_fields .= $wpba_meta_form_fields->build_inputs( $input_fields );
			$attachment_fields .= '</div>';

			return $attachment_fields;
		} // build_attachment_fields()
	} // WPBA_Meta()

	// Instantiate Class
	global $wpba_meta;
	$wpba_helpers = new WPBA_Meta();
} // if()