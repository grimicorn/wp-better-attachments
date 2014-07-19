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
		 * @internal
		 *
		 * @since   1.4.0
		 *
		 * @return  void
		 */
		private function _add_wpba_meta_actions_filters() {
			// Add meta box
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

			// Save meta box input
			add_action( 'save_post', array( $this, 'save' ) );

			// Adds an attachments button above the post editor
			add_action( 'media_buttons_context', array( &$this, 'add_editor_form_button' ), 11 );
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
			$post_types     = $this->get_post_types();
			$meta_box_title = $this->meta_box_title;

			/**
			 * Allows filtering of the meta box title for all post types.
			 *
			 * <code>
			 * function myprefix_meta_box_title( $input_fields ) {
			 * 	return 'Attachments';
			 * }
			 * add_filter( 'wpba_meta_box_meta_box_title', 'myprefix_meta_box_title' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 *
			 * @var   string
			 */
			$meta_box_title = apply_filters( "{$this->meta_box_id}_meta_box_title", $meta_box_title );

			/**
			 * Allows filtering of the meta box title for a specific post type.
			 *
			 * <code>
			 * function myprefix_post_type_meta_box_title( $input_fields ) {
			 * 	return 'Slides';
			 * }
			 * add_filter( 'wpba_meta_box_post_type_meta_box_title', 'myprefix_post_type_meta_box_title' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 * @todo  Figure out why passing $post_type from apply_filters() is not working.
			 *
			 * @var   string
			 */
			$meta_box_title = apply_filters( "{$this->meta_box_id}_{$post_type}_meta_box_title", $meta_box_title, $post_type );

			if ( in_array( $post_type, $post_types ) ) {
				add_meta_box(
					$this->meta_box_id,
					__( $meta_box_title, 'wpba' ),
					array( $this, 'render_meta_box_content' ),
					$post_type,
					'advanced',
					'high'
				);
			} // if()

			$this->meta_box_title = $meta_box_title;
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
			if ( ! isset( $_POST["{$this->meta_box_id}_nonce"] ) ) {
				return $post_id;
			} // if()

			$nonce = $_POST["{$this->meta_box_id}_nonce"];

			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $nonce, '{$this->meta_box_id}_save_fields' ) ){
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

			// Group the user input
			$fields = $this->_group_meta_fields( $_POST );

			// Sanitize the user input.
			$fields = $this->_sanitize_fields( $fields );

			// Update the attachment meta.
			$this->_update_attachment_meta( $fields );
		} // save



		/**
		 * Handles grouping of the submitted meta fields.
		 *
		 * <code>
		 * $fields = array( 'input_id_text' => 'value' );
		 * $fields = $this->_group_meta_fields( $fields );
		 * </code>
		 *
		 * @internal
		 *
		 * @since   1.4.0
		 *
		 * @param   array  $fields  The submitted meta fields.
		 *
		 * @return  array           The grouped meta fields.
		 */
		private function _group_meta_fields( $fields ) {
			$attachment_id_base = "{$this->meta_box_id}_attachment_";

			// Strip the id base
			$stripped_id_fields = array();
			foreach ( $fields as $key => $value ) {
				// We only want to group WPBA meta fields.
				if ( strpos( $key, $attachment_id_base ) === false ) {
					continue;
				} // if()

				$stripped_key                      = str_replace( $attachment_id_base, '', $key );
				$stripped_id_fields[$stripped_key] = $value;
			} // foreach()

			// Sort the fields.
			$sorted_fields = array();
			foreach ( $stripped_id_fields as $key => $value ) {
				// Attachment ID
				$first_underscore = strpos( $key, '_' );
				$attachment_id    = substr( $key, 0, $first_underscore );
				$key              = substr_replace( $key, '', 0, $first_underscore + 1 );

				// Type
				$type = strrchr( $key, '_' );
				$key  = str_replace( $type, '', $key );
				$type = trim( $type, '_' );

				// Add the sorted values
				$sorted_fields[] = array(
					'value'     => $value,
					'type'      => $type,
					'meta_name' => $key,
					'post_id'   => $attachment_id,
				);
			} // foreach()

			return $sorted_fields;
		} // _group_meta_fields()



		/**
		 * Handles sanitizing of the meta box fields.
		 *
		 * <code>
		 * $fields = array( 'input_id_text' => 'value' );
		 * $fields = $this->_group_meta_fields( $fields );
		 * $fields = $this->_sanitize_fields( $fields );
		 * </code>
		 *
		 * @internal
		 *
		 * @since   1.4.0
		 *
		 * @param   array   $fields  The fields to sanitize.
		 *
		 * @return  array            The sanitized fields.
		 */
		private function _sanitize_fields( $fields ) {
			foreach ( $fields as $key => $field ) {
				extract( $field );

				$sanitized_value = $this->sanitize_input_fields( $field_type, $value );

				$fields[$key]['value'] = $sanitized_value;
				unset( $fields[$key]['type'] );
			} // foreach()

			return $fields;
		} // _sanitize_fields()



		/**
		 * Retrieves the possible post keys when using get_post().
		 *
		 * <code>$post_updateable_keys = $this->_possible_post_keys();</code>
		 *
		 * @internal
		 *
		 * @since   1.4.0
		 *
		 * @return  array  The possible post keys.
		 */
		private function _possible_post_keys() {
			return array(
				'ID',
				'post_author',
				'post_date',
				'post_date_gmt',
				'post_content',
				'post_title',
				'post_excerpt',
				'post_status',
				'comment_status',
				'ping_status',
				'post_password',
				'post_name',
				'to_ping',
				'pinged',
				'post_modified',
				'post_modified_gmt',
				'post_content_filtered',
				'post_parent',
				'guid',
				'menu_order',
				'post_type',
				'post_mime_type',
				'comment_count',
				'filter',
			);
		} // _possible_post_keys()



		/**
		 * Updates the attachment meta.
		 *
		 * <code>
		 * $fields = array( 'input_id_text' => 'value' );
		 * $fields = $this->_group_meta_fields( $fields );
		 * $fields = $this->_sanitize_fields( $fields );
		 * $this->_update_attachment_meta( $fields );
		 * </code>
		 *
		 * @internal
		 *
		 * @todo    Add code example.
		 * @todo    Build out method.
		 *
		 * @since   1.4.0
		 *
		 * @param   array   $fields  The fields to update.
		 *
		 * @return  void
		 */
		private function _update_attachment_meta( $fields ) {
			global $post;
			$parent_post_id = $post->ID;

			// Add all the values into posts
			$posts_to_update = array();
			foreach ( $fields as $key => $field ) {
				extract( $field );
				if ( ! isset( $posts_to_update[$post_id] ) ) {
					$posts_to_update[$post_id] = array();
				} // if()
				$posts_to_update[$post_id][$meta_name] = $value;
			} // foreach

			// For multiple posts add menu order to custom menu order
			foreach ( $posts_to_update as $post_key => $post_data ) {
				$post_id = $post_data['ID'];
				$attachment_multi_parents_meta = get_post_meta( $post_id, $this->attachment_multi_meta_key, true );
				$attachment_multi_parents      = explode( ',', $attachment_multi_parents_meta );
				if ( in_array( $parent_post_id, $attachment_multi_parents ) ) {
					$prev_value                  = get_post_meta( $post_id, $this->attachment_multi_menu_order_meta_key, true );
					$prev_value                  = ( $prev_value == '' ) ? array() : $prev_value;
					$meta_value                  = $prev_value;
					$meta_value[$parent_post_id] = $post_data['menu_order'];

					update_post_meta( $post_id, $this->attachment_multi_menu_order_meta_key, $meta_value, $prev_value );

					unset( $posts_to_update[$post_key]['menu_order'] );
				} // if()
			} // foreach()

			// Update the post data using wp_update_post()
			$post_updateable_keys = $this->_possible_post_keys();
			foreach ( $posts_to_update as $post_to_update_key => $post_to_update ) {
				// Only add what can be updated through wp_update_post() we will take care of custom meta later.
				$post_args = array();
				foreach ( $post_to_update as $post_key => $post_value ) {
					if ( in_array( $post_key, $post_updateable_keys ) ) {
						$post_args[$post_key] = $post_value;

						if ( $post_key != 'ID' ) {
							unset( $posts_to_update[$post_to_update_key][$post_key] );
						} // id()
					} // if()
				} // foreach()

				$update_post = wp_update_post( $post_to_update, true );
			} // foreach()

			// Update custom meta
			foreach ( $posts_to_update as $post_key => $post_data ) {
				$post_id = $post_data['ID'];
				unset( $post_data['ID'] ); // Don't need to update the ID

				if ( ! empty( $post_data ) ) {
					foreach ( $post_data as $meta_key => $meta_value ) {
						$prev_value = get_post_meta( $post_id, $meta_key, true );
						update_post_meta( $post_id, $meta_key, $meta_value, $prev_value );
					} // foreach()
				} // if()
			} // foreach()
		} // _update_attachment_meta()



		/**
		 * Render Meta Box content.
		 *
		 * <code>
		 * add_meta_box(
		 * 	$this->meta_box_id,
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
		 * @todo    Add setting to disable file types.
		 * @todo    Add toggles to disable/enable file types visibility.
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

			// Get all of the current attachment IDs, used when adding attachments so we do not have duplicates.
			$attachment_ids = array();
			foreach ( $attachments as $attachment ) {
				$attachment_ids[] = $attachment->ID;
			} // foreach()
			$attachment_ids = implode( ',', $attachment_ids );

			// Add an nonce field so we can check for it later.
			wp_nonce_field( '{$this->meta_box_id}_save_fields', "{$this->meta_box_id}_nonce" ); ?>

			<div class="wpba-wrap wpba-utils wpba-meta-box-wrap clearfix">
				<?php echo wp_kses( $this->add_attachment_button( $post ), 'post' ); ?>

				<div class="wpba-before-meta-box-list clear clearfix">
					<?php
					/**
					 * Fires directly before the WPBA attachment list.
					 *
					 * @since  1.4.0
					 *
					 * @todo   Create example documentation.
					 *
					 * @param  object  The current post.
					 * @param  array   All of the attachments for the current post.
					 */
					do_action( 'wpba_before_meta_box_list', $post, $attachments ); ?>
				</div> <!-- /.wpba-before-meta-box-list -->

				<?php // Attachment List ?>
				<ul id="wpba_sortable" class="clear wpba-attachment-form-fields list-inline clearfix" data-postid="<?php echo esc_attr( $post->ID ); ?>" data-attachmentids="<?php echo esc_attr( $attachment_ids ); ?>">
					<?php $this->build_attachment_items( $attachments ); ?>
				</ul>

				<div class="wpba-after-meta-box-list clear clearfix">
					<?php
					/**
					 * Fires directly after the WPBA attachment list.
					 *
					 * @since  1.4.0
					 *
					 * @todo   Create example documentation.
					 *
					 * @param  object  The current post.
					 * @param  array   All of the attachments for the current post.
					 */
					do_action( 'wpba_after_meta_box_list', $post, $attachments ); ?>
				</div> <!-- /.wpba-after-meta-box-list -->

			</div> <!-- /.wpba-wrap -->
			<?php
		} // render_meta_box_content()



		/**
		 * Handles building of the attachment items from the supplied attachments.
		 *
		 * @since   1.4.0
		 *
		 * @param   array    $attachments  The attachments to build the items for.
		 * @param   boolean  $echo         Optional, output the attachments, default true.
		 *
		 * @return  string                 The attachment items HTML.
		 */
		public function build_attachment_items( $attachments, $echo = true ) {
			$meta = array();
			foreach ($attachments as $attachment ) {
				$meta[] = get_post_meta( $attachment->ID, $this->attachment_multi_meta_key, true );
			} // foreach()

			$attachment_items = '';
			$allowed_html     = $this->get_form_kses_allowed_html();

			foreach ( $attachments as $attachment ) {
				$attachment_items .= "<li id='wpba_attachment_{$attachment->ID}' class='clear ui-state-default wpba-sortable-item clearfix pull-left attachment-item'>";
				$attachment_items .= '<i class="dashicons dashicons-menu wpba-sort-handle"></i>';
				$attachment_items .= $this->build_attachment_thumbnail( $attachment );
				$attachment_items .= $this->build_attachment_fields( $attachment );
				$attachment_items .= '</li>';
			} // foreach()

			if ( $echo ) {
				echo wp_kses( $attachment_items, $allowed_html );
			} // if()

			return $attachment_items;
		} // build_attachment_items()



		/**
		 * Adds a button for uploading attachments.
		 *
		 * @since   1.4.0
		 *
		 * @param   object  $post  WordPress post object to add the attachment(s) to.
		 *
		 * @return  string         The upload button HTML
		 */
		public function add_attachment_button( $post ) {
			$upload_button_content = 'Add Attachment(s)';
			$post_type             = get_post_type( $post );

			/**
			 * Allows for changing of the upload button content for all post types.
			 *
			 * <code>
			 * function myprefix_wpba_upload_button_content( $upload_button_content ) {
			 * 	return false;
			 * }
			 * add_filter( 'wpba_meta_box_upload_button_content', 'myprefix_wpba_upload_button_content' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 *
			 * @var   string
			 */
			$upload_button_content = apply_filters( "{$this->meta_box_id}_upload_button_content", $upload_button_content );



			/**
			 * Allows for changing of the upload button content for specific post type.
			 *
			 * <code>
			 * function myprefix_wpba_post_type_upload_button_content( $upload_button_content ) {
			 * 	return false;
			 * }
			 * add_filter( 'wpba_meta_box_post_type_upload_button_content', 'myprefix_wpba_post_type_upload_button_content' );
			 * </code>
			 *
			 * @since  1.4.0
			 *
			 * @todo   Create example documentation.
			 * @todo   Figure out why passing $post_type from apply_filters() is not working.
			 *
			 * @var    string
			 */
			$upload_button_content = apply_filters( "{$this->meta_box_id}_{$post_type}_upload_button_content", $upload_button_content, $post_type );



			$upload_button  = '';
			$upload_button .= "<a href='#' id='wpba_add_{$post->ID}' class='wpba-add-link button add_media' title='{$upload_button_content}'>";
			$upload_button .= '<span class="wp-media-buttons-icon"></span>';
			$upload_button .= "<span class='wpba-add-link-title'>&nbsp;{$upload_button_content}</span>";
			$upload_button .= '</a>';

			return $upload_button;
		} // add_attachment_button()



		/**
		* Add Attachments button above post editor
		*
		* @since 1.4.0
		*
		* @param   string  $context HTML above post editor.
		*
		* @return  string  The add attachment button added to the HTML above the post editor.
		*/
		public function add_editor_form_button( $context ) {
			global $post;

			$post_type                  = get_post_type();
			$display_editor_form_button = true;

			/**
			 * Allows filtering of the meta box title for all post types.
			 *
			 * <code>
			 * function myprefix_display_editor_form_button( $input_fields ) {
			 * 	return false;
			 * }
			 * add_filter( 'wpba_meta_box_display_editor_form_button', 'myprefix_display_editor_form_button' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 *
			 * @var   string
			 */
			$display_editor_form_button = apply_filters( "{$this->meta_box_id}_display_editor_form_button", $display_editor_form_button );

			/**
			 * Allows filtering of the meta box title for a specific post type.
			 *
			 * <code>
			 * function myprefix_post_type_display_editor_form_button( $input_fields ) {
			 * 	return false;
			 * }
			 * add_filter( 'wpba_meta_box_post_type_display_editor_form_button', 'myprefix_post_type_display_editor_form_button' );
			 * </code>
			 *
			 * @since  1.4.0
			 *
			 * @todo   Create example documentation.
			 * @todo   Figure out why passing $post_type from apply_filters() is not working.
			 *
			 * @var    string
			 */
			$display_editor_form_button = apply_filters( "{$this->meta_box_id}_{$post_type}_display_editor_form_button", $display_editor_form_button, $post_type );

			// Make sure the user has not disabled displaying the button.
			if ( ! $display_editor_form_button ) {
				return $context;
			} // if()

			// Make sure the user has not disabled this post type
			if ( ! in_array( $post_type, $this->get_post_types() ) ) {
				return $context;
			} // if()


			// Check if the button has been added
			$button_added = ( strpos( $context, 'wpba-add-link-title' ) === false ) ? false : true;
			if ( $button_added ){
				return $context;
			} // if()

			return $context .= $this->add_attachment_button( $post );
		} // add_editor_form_button()




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
			$post_type             = get_post_type();
			$edit_link             = admin_url( "post.php?post={$attachment->ID}&action=edit" );
			$display_unattach_link = true;
			$display_edit_link     = true;
			$display_delete_link   = true;



			/**
			 * Allows enabling/disabling the unattach link for all post types.
			 *
			 * <code>
			 * function myprefix_wpba_display_unattach_link( $display_unattach_link ) {
			 * 	return false;
			 * }
			 * add_filter( 'wpba_meta_box_display_unattach_link', 'myprefix_wpba_display_unattach_link' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 *
			 * @var   string
			 */
			$display_unattach_link = apply_filters( "{$this->meta_box_id}_display_unattach_link", $display_unattach_link );



			/**
			 * Allows enabling/disabling the unattach link for specific post type.
			 *
			 * <code>
			 * function myprefix_wpba_post_type_display_unattach_link( $display_unattach_link ) {
			 * 	return false;
			 * }
			 * add_filter( 'wpba_meta_box_post_type_display_unattach_link', 'myprefix_wpba_post_type_display_unattach_link' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 *
			 * @var   string
			 */
			$display_unattach_link = apply_filters( "{$this->meta_box_id}_{$post_type}_display_unattach_link", $display_unattach_link, $post_type );



			/**
			 * Allows enabling/disabling the delete link for all post types.
			 *
			 * <code>
			 * function myprefix_wpba_display_delete_link( $display_delete_link ) {
			 * 	return false;
			 * }
			 * add_filter( 'wpba_meta_box_display_delete_link', 'myprefix_wpba_display_delete_link' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 *
			 * @var   string
			 */
			$display_delete_link = apply_filters( "{$this->meta_box_id}_display_delete_link", $display_delete_link );



			/**
			 * Allows enabling/disabling the delete link for specific post type.
			 *
			 * <code>
			 * function myprefix_wpba_post_type_display_delete_link( $display_delete_link ) {
			 * 	return false;
			 * }
			 * add_filter( 'wpba_meta_box_post_type_display_delete_link', 'myprefix_wpba_post_type_display_delete_link' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 *
			 * @var   string
			 */
			$display_delete_link = apply_filters( "{$this->meta_box_id}_{$post_type}_display_delete_link", $display_delete_link, $post_type );



			/**
			 * Allows enabling/disabling the edit link for all post types.
			 *
			 * <code>
			 * function myprefix_wpba_display_edit_link( $display_edit_link ) {
			 * 	return false;
			 * }
			 * add_filter( 'wpba_meta_box_display_edit_link', 'myprefix_wpba_display_edit_link' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 *
			 * @var   string
			 */
			$display_edit_link = apply_filters( "{$this->meta_box_id}_display_edit_link", $display_edit_link );



			/**
			 * Allows enabling/disabling the edit link for specific post type.
			 *
			 * <code>
			 * function myprefix_wpba_post_type_display_edit_link( $display_edit_link ) {
			 * 	return false;
			 * }
			 * add_filter( 'wpba_meta_box_post_type_display_edit_link', 'myprefix_wpba_post_type_display_edit_link' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 *
			 * @var   string
			 */
			$display_edit_link = apply_filters( "{$this->meta_box_id}_{$post_type}_display_edit_link", $display_edit_link, $post_type );


			// No point in going any further since if all links have been disabled.
			if ( ! $display_unattach_link and ! $display_delete_link and ! $display_edit_link ) {
				return '';
			} // if()

			// Build the menu
			$menu  = '';
			$menu .= "<ul class='list-unstyled pull-left wpba-attachment-menu hide-if-no-js' data-id='{$attachment->ID}'>";

			// Unattach Link
			if ( (boolean) $display_unattach_link ) {
				$menu .= '<li class="pull-left text-center">';
				$menu .= "<a href='#' id='wpba_unattach_{$attachment->ID}' class='wpba-unattach-link'>Un-attach</a>";
				$menu .= '</li>';
			} // if()

			// Edit Link
			if ( (boolean) $display_edit_link ) {
				$menu .= '<li class="pull-left text-center">';
				$menu .= "<a href='{$edit_link}' class='wpba-edit-link' target='_blank'>Edit</a>";
				$menu .= '</li>';
			} // if()

			// Delete Link
			if ( (boolean) $display_delete_link ) {
				$menu .= '<li class="pull-left text-center">';
				$menu .= "<a href='#' id='wpba_delete_{$attachment->ID}' class='wpba-delete-link'>Delete</a>";
				$menu .= '</li>';
			} // if()

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
		 * @todo    Add filter for image size?
		 *
		 * @param   object  $attachment  The attachment post.
		 *
		 * @return  string               The attachment thumbnail HTML.
		 */
		public function build_attachment_thumbnail( $attachment ) {
			$display_attachment_id = true;
			$post_type             = get_post_type();

			/**
			 * Allows enable/disable displaying of the attachment ID for all post types.
			 *
			 * <code>
			 * function myprefix_wpba_display_unattach_link( $input_fields ) {
			 * 	return false;
			 * }
			 * add_filter( 'wpba_meta_box_display_unattach_link', 'myprefix_wpba_display_unattach_link' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 *
			 * @var   string
			 */
			$display_attachment_id = apply_filters( "{$this->meta_box_id}_display_attachment_id", $display_attachment_id, $post_type );



			/**
			 * Allows enable/disable displaying of the attachment ID for specific post type.
			 *
			 * <code>
			 * function myprefix_wpba_post_type_display_attachment_id( $input_fields ) {
			 * 	return false;
			 * }
			 * add_filter( 'wpba_meta_box_post_type_display_attachment_id', 'myprefix_wpba_post_type_display_attachment_id' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 *
			 * @var   string
			 */
			$display_attachment_id = apply_filters( "{$this->meta_box_id}_{$post_type}_display_attachment_id", $display_attachment_id );


			$attachment_thumbnail  = '';
			$attachment_thumbnail .= '<div class="wpba-attachment-image-wrap pull-left">';
			if ( (boolean)$display_attachment_id ) {
				$attachment_thumbnail .= "<strong class='wpba-attachment-id'>Attachment ID: {$attachment->ID}</strong>";
			} // if()
			$attachment_thumbnail .= '<div class="inner">';
			$attachment_thumbnail .= $this->attachment_menu( $attachment );
			$attachment_thumbnail .= '</div>';
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

			$attachment_id_base = "{$this->meta_box_id}_attachment_{$attachment->ID}";
			$atttachment_fields = '';
			$input_fields       = $this->_get_attachment_fields( $attachment );

			// Adds a prefix so we know to grab them and save them.
			foreach ( $input_fields as $key => $value ) {
				$input_fields[$key]['id'] = "{$attachment_id_base}_{$value['id']}";
			} // foreach()

			$attachment_fields  = '';
			$attachment_fields .= '<div class="wpba-attachment-fields-wrap pull-left">';
			$attachment_fields .= $wpba_meta_form_fields->build_inputs( $input_fields );
			$attachment_fields .= '</div>';

			return $attachment_fields;
		} // build_attachment_fields()



		/**
		 * Retrieves the attachment input fields.
		 *
		 * <code>$input_fields = $this->_get_attachment_fields( $attachment );</code>
		 *
		 * @internal
		 *
		 * @param   object  $attachment  The attachment post.
		 *
		 * @return  array                The attachment input fields.
		 */
		private function _get_attachment_fields( $attachment ) {
			$post_type    = get_post_type();
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
				'type'  => 'editor',
				'attrs' => array(),
			);


			/**
			 * Allows filtering of the input fields for all post types, add/remove fields.
			 *
			 * <code>
			 * function myprefix_wpba_input_fields( $input_fields ) {
			 * 	unset( $input_fields['alt_text'] ); // Removes the Alt text input.
			 * 	return $input_fields;
			 * }
			 * add_filter( 'wpba_meta_box_input_fields', 'myprefix_wpba_input_fields' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 *
			 * @var   array
			 */
			$input_fields = apply_filters( "{$this->meta_box_id}_input_fields", $input_fields );



			/**
			 * Allows filtering of the input fields for specific post type, add/remove fields.
			 *
			 * <code>
			 * function myprefix_wpba_post_type_input_fields( $input_fields ) {
			 * 	unset( $input_fields['alt_text'] ); // Removes the Alt text input.
			 * 	return $input_fields;
			 * }
			 * add_filter( 'wpba_meta_box_post_type_input_fields', 'myprefix_wpba_post_type_input_fields' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 *
			 * @var   array
			 */
			$input_fields = apply_filters( "{$this->meta_box_id}_{$post_type}_input_fields", $input_fields );

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
				'attrs' => array( 'class' => 'menu-order-input', ),
			);

			return $input_fields;
		} // _get_attachment_fields()
	} // WPBA_Meta()

	// Instantiate Class
	global $wpba_meta;
	$wpba_meta = new WPBA_Meta();
} // if()