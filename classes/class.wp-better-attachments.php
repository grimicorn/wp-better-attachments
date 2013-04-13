<?php
/**
 * WP Better Attachments
 */
class WP_Better_Attachments
{

	/**
	 * Constructor
	 */
	public function __construct( $config = array() ) {
		$this->init_hooks();
	} // __construct


	/**
	 * Initialization Hooks
	 */
	public function init_hooks() {
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_scripts' ) );
		add_filter( 'media_row_actions',  array( &$this, 'unattach_media_row_action' ), 10, 2 );
	} // init_hooks()


	/**
	 * Add unattach link in media editor
	 * TODO: Added reattach link as well
	 */
	function unattach_media_row_action( $actions, $post ) {

		if ( $post->post_parent ) {
			$actions['unattach'] = '<a href="#" title="' . __( "Un-attach this media item." ) . '" class="wpba-unattach-library" data-id="'.$post->ID.'">' . __( 'Un-attach' ) . '</a>';
			$actions['reattach'] = '<a class="hide-if-no-js wpba-reattach-library" title="' . __( "Re-attach this media item." ) . '" onclick="findPosts.open( '."'media[]','".$post->ID."'". '); return false;" href="#the-list">' . __( 'Re-attach' ) . '</a>';
		} // if()

		return $actions;
	} //unattach_media_row_action()


	/**
	 * Enqueue Administrator Scripts and Styles
	 */
	public function enqueue_admin_scripts() {
		$current_screen = get_current_screen();
		if ( $current_screen->base == 'edit' or $current_screen->base == 'upload' or $current_screen->base == 'post' ) {
			wp_enqueue_style( 'wpba-admin-css', plugins_url( 'assets/css/wpba-admin.css' , dirname( __FILE__ ) ), null, WPBA_VERSION );

			global $wp_version;
			$deps = array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-widget',
				'jquery-ui-mouse',
				'jquery-ui-sortable'
			);
			if ( floatval($wp_version) >= 3.5 ) {
				// Make sure to enqueue media
				if ( ! did_action( 'wp_enqueue_media' ) )
			    wp_enqueue_media();

				wp_register_script(
					'wpba-media-handler',
					plugins_url( 'assets/js/wpba-media-handler-new.min.js' , dirname( __FILE__ ) ),
					$deps,
					WPBA_VERSION,
					true
				);
			} else {
				wp_enqueue_script( 'media-upload' );
	   		add_thickbox();
				$deps[] = 'media-upload';
				$deps[] = 'thickbox';
				wp_register_script(
					'wpba-media-handler',
					plugins_url( 'assets/js/wpba-media-handler-old.min.js' , dirname( __FILE__ ) ),
					$deps,
					WPBA_VERSION,
					true
				);
			} // if/else()

			wp_enqueue_script( 'wpba-media-handler' );
		} // if()
	} // enqueue_admin_scripts()


	/**
	 * Get attachment ID from src url
	 */
	public function get_attachment_id_from_src( $image_src ) {
		global $wpdb;
		$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
		$id = $wpdb->get_var( $query );
		return $id;
	} // get_attachment_id_from_src()


	/**
	 * Get Post Attachments
	 */
	protected function get_post_attachments( $args = array() ) {
		extract( $args );

		if ( !isset( $post ) )
			global $post;

		$show_thumbnail = ( isset( $show_thumbnail ) ) ? $show_thumbnail : true;
		// Should we exclude the thumb?
		if ( !$show_thumbnail ) {
			$get_posts_args['meta_query'] = array(
				array(
					'key' => '_thumbnail_id',
					'compare' => 'NOT EXISTS'
				)
			);
		}

		$get_posts_args = array(
			'post_type'     => 'attachment',
			'posts_per_page'  =>  -1,
			'post_parent'    => $post->ID,
			'order'       => 'ASC',
			'orderby'      => 'menu_order'
		);

		// Get the attachments
		$attachments = get_posts( $get_posts_args );
		// $image_attachments = array();
		// foreach ( $attachments as $attachment ) {
		// 	if ( $this->is_image( $attachment->post_mime_type ) ) {
		// 		$image_attachments[] = $attachment;
		// 	} // if(is_image())
		// } // foreach();


		return $attachments;
	} // get_post_attachments()


	/**
	 * Build Attachment List
	 */
	protected function build_image_attachment_li( $attachments, $args = array() ) {
		extract( $args );
		$html = '';
		$nl = "\n";

		foreach ( $attachments as $attachment ) {
			$attachment_id = ( isset( $a_array ) and $a_array ) ? $attachment['id'] : $attachment->ID;
			$attachment = get_post( $attachment_id );
			$mime_type = get_post_mime_type( $attachment_id );
			$attachment_edit_link = admin_url( "post.php?post={$attachment_id}&action=edit" );
			$placeholder_img = '';

			if ( $this->is_image( $mime_type ) ) {
				$attachment_src = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
				if ( !empty( $attachment_src ) )
					$src = $attachment_src[0];
					$width = $attachment_src[1];
					$height = $attachment_src[2];
					$id = $attachment->ID;
					$placeholder_img .= '<img src="'.$src.'" width="'.$width.'" height="'.$height.'" class="attid-'.$id.'" data-url="'.$src.'">';
			} else {
				$placeholder_img_file = $this->placeholder_image( $mime_type );
				$img_src = site_url().'/wp-includes/images/crystal/'.$placeholder_img_file;
				$placeholder_img .= '<div class="icon-wrap"><img src="'.$img_src.'" class="icon" draggable="false"></div>';
			} // if/else()

			$html .= '<li class="wpba-preview pull-left ui-state-default" data-id="'.$attachment_id.'">';
			$html .= '<ul class="unstyled wpba-edit-attachment hide-if-no-js" data-id="'.$attachment_id.'">';
			$html .= '<li class="pull-left"><a href="#" class="wpba-unattach">Un-attach</a> | </li>';
			$html .= '<li class="pull-left"><a href="'.$attachment_edit_link.'" class="wpba-edit">Edit</a> | </li>';
			$html .= '<li class="pull-left"><a href="#" class="wpba-delete">Delete</a></li>';
			$html .= '</ul>';
			if ( !$this->is_image( $mime_type ) ) {
				$attachment_url = wp_get_attachment_url( $attachment->ID );
				$file_name = pathinfo( $attachment_url, PATHINFO_FILENAME );
				$file_type = pathinfo( $attachment_url, PATHINFO_EXTENSION );
				$attachment_title = "{$file_name}.{$file_type}";
				$html .= '<span class="wpba-attachment-name">'.$attachment_title.'</span>';
			} // if()
			$html .= $placeholder_img;
			$html .= '</li>';
		} // foreach();

		return $html;
	} // build_image_attachment_li()


	/**
	* Attachment placeholder image name
	*/
	protected function placeholder_image( $mime_type )
	{
		if ( $this->is_document( $mime_type ) ) {
			return 'document.png';
		} elseif ( $this->is_audio( $mime_type ) ) {
			return 'audio.png';
		} elseif ( $this->is_video( $mime_type ) ) {
			return 'video.png';
		} //if/elseif

		return '';
	} // placeholder_image()

	/**
	 * Attachment is an image
	 */
	protected function is_image( $mime_type ) {
		$image_mime_types = array(
			'image/jpeg',
			'image/gif',
			'image/png'
		);
		if ( in_array( $mime_type, $image_mime_types ) )
			return true;

		return false;
	} // is_image()


		/**
	 * Attachment is a document
	 */
	protected function is_document( $mime_type ) {
		$document_mime_types = array(
			'application/pdf',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/msword',
			'application/vnd.ms-powerpoint',
			'application/vnd.ms-powerpoint',
			'application/vnd.oasis.opendocument.text',
			'application/vnd.ms-excel',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
		);
		if ( in_array( $mime_type, $document_mime_types ) )
			return true;

		return false;
	} // is_document()


		/**
	 * Attachment is a audio
	 */
	protected function is_audio( $mime_type ) {
		$audio_mime_types = array(
			'audio/mpeg',
			'audio/mpeg',
			'audio/ogg',
			'audio/wav'
		);
		if ( in_array( $mime_type, $audio_mime_types ) )
			return true;

		return false;
	} // is_audio()


	/**
	 * Attachment is a video
	 */
	protected function is_video( $mime_type ) {
		$video_mime_types = array(
			'video/mp4',
			'video/mp4',
			'video/quicktime',
			'video/asf.avi',
			'video/mpeg',
			'video/ogg'
		);
		if ( in_array( $mime_type, $video_mime_types ) )
			return true;

		return false;
	} // is_video()


	/**
	 * Attach
	 */
	public function attach( $args = array() ) {
		extract( $args );
		global $wpdb;

		if ( !$parent_id )
			return;

		$parent = get_post( $parent_id );

		$attach = array();
		foreach ( (array) $media as $att_id ) {
			$att_id = (int) $att_id;

			$attach[] = $att_id;
		}

		if ( ! empty( $attach ) ) {
			$attach_string = implode( ',', $attach );
			$attached = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_parent = %d WHERE post_type = 'attachment' AND ID IN ( $attach_string )", $parent_id ) );
			foreach ( $attach as $att_id ) {
				clean_attachment_cache( $att_id );
			}
		}

		if ( isset( $attached ) ) {
			return true;
		}

		return false;
	} //attach()


	/**
	 * Un-attach
	 */
	public function unattach( $args = array() ) {
		extract( $args );

		// Can not do anything with out the attachment id
		if ( empty( $attachment_id ) )
			return false;

		global $wpdb;
		$wpdb->update( $wpdb->posts, array( 'post_parent'=>0 ),
			array( 'id' => (int)$attachment_id, 'post_type' => 'attachment' ) );

		return true;
	} // unattach()


	/**
	 * Insert Attachment
	 */
	public function insert_attachment( $url ) {
		$wp_upload_dir = wp_upload_dir();
		$filename = str_replace( $wp_upload_dir['url'] . '/', '', $url );
		$wp_filetype = wp_check_filetype( basename( $filename ), null );

		$attachment = array(
			'guid' => $url,
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment( $attachment, $url );
		// you must first include the image.php file
		// for the function wp_generate_attachment_metadata() to work
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	} // insert_attachemt()


} // END Class WP_Better_Attachments

/**
 * Instantiate class and create return method for easier use later
 */
global $wpba;
$wpba = new WP_Better_Attachments();

function call_WP_Better_Attachments() {
	return new WP_Better_Attachments();
} // call_WP_Better_Attachments()
if ( is_admin() )
	add_action( 'load-post.php', 'call_WP_Better_Attachments' );
