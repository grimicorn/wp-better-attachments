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
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		add_filter( 'media_row_actions', array( &$this, 'unattach_media_row_action' ), 10, 2 );
		add_action('media_buttons_context', array( &$this, 'add_form_button' ) );
	} // init_hooks()


	/**
	* Add Attachments button above post editor
	*/
	function add_form_button($context){
		// Make sure the user has not disabled this post type
		global $wpba_wp_settings_api;
		global $post;
		$disabled_post_types = $wpba_wp_settings_api->get_option( 'wpba-disable-post-types', 'wpba_settings', array());
		if ( isset( $post ) AND !empty( $disabled_post_types[$post->post_type] ) ) {
			return $context;
		} // if()

		// Check if the button has been added
		$button_added = ( strpos( $context, 'wpba_form_attachments_button' ) === false ) ? false : true;
		if ( $button_added ) {
			return $context;
		} // if()

		// Add the button
		$out = '<a class="button wpba-attachments-button wpba-form-attachments-button" id="wpba_form_attachments_button" href="#"><span class="wpba-media-buttons-icon"></span> Add Attachments</a>';
		return $context . $out;
	} // add_form_button()


	/**
	* Add unattach link in media editor
	*/
	function unattach_media_row_action( $actions, $post )
	{
		if ( $post->post_parent ) {
			$actions['unattach'] = '<a href="#" title="' . __( "Un-attach this media item." ) . '" class="wpba-unattach-library" data-id="'.$post->ID.'">' . __( 'Un-attach' ) . '</a>';
			$actions['reattach'] = '<a class="hide-if-no-js wpba-reattach-library" title="' . __( "Re-attach this media item." ) . '" onclick="findPosts.open( '."'media[]','".$post->ID."'". '); return false;" href="#the-list">' . __( 'Re-attach' ) . '</a>';
		} // if()

		return $actions;
	} //unattach_media_row_action()


	/**
	* Enqueue Administrator Scripts and Styles
	*/
	public function enqueue_admin_scripts()
	{

		// Make sure the user has not disabled this post type
		global $wpba_wp_settings_api;
		global $post;
		$disabled_post_types = $wpba_wp_settings_api->get_option( 'wpba-disable-post-types', 'wpba_settings', array());

		if ( isset( $post ) AND !empty( $disabled_post_types[$post->post_type] ) )
			return false;


		$current_screen = get_current_screen();
		$base = $current_screen->base;
		if ( $base == 'edit' or $base == 'upload' or $base == 'post' OR $base = 'settings_page_wpba-settings' ) {
			wp_enqueue_style( 'wpba-admin-css', plugins_url( 'assets/css/wpba-admin.css' , dirname( __FILE__ ) ), null, WPBA_VERSION );

			// global $wp_version;
			$deps = array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-widget',
				'jquery-ui-mouse',
				'jquery-ui-sortable'
			);
			// if ( floatval($wp_version) >= 3.5 ) {
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
			// } else {
			// 	wp_enqueue_script( 'media-upload' );
		//  		add_thickbox();
			// 	$deps[] = 'media-upload';
			// 	$deps[] = 'thickbox';
			// 	wp_register_script(
			// 		'wpba-media-handler',
			// 		plugins_url( 'assets/js/wpba-media-handler-old.min.js' , dirname( __FILE__ ) ),
			// 		$deps,
			// 		WPBA_VERSION,
			// 		true
			// 	);
			// } // if/else()

			wp_enqueue_script( 'wpba-media-handler' );
		} // if()
	} // enqueue_admin_scripts()


	/**
	* Enqueue Frontend Scripts and Styles
	*
	* @since 1.3.1
	*/
	function enqueue_scripts()
	{
		global $post;
		$src = plugins_url( 'assets/css/wpba-frontend.css' , dirname( __FILE__ ) );
		wp_register_style( 'wpba_front_end_styles', $src, array(), WPBA_VERSION );
		wp_enqueue_style( 'wpba_front_end_styles' );
	} // enqueue_scripts()


	/**
	* Get attachment ID from src url
	*/
	public function get_attachment_id_from_src( $image_src )
	{
		global $wpdb;
		$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
		$id = $wpdb->get_var( $query );
		return $id;
	} // get_attachment_id_from_src()


	/**
	* Get Post Attachments
	*/
	public function get_post_attachments( $args = array() )
	{
		global  $wpba_wp_settings_api;
		extract( $args );

		// Make sure we have a post to work with
		if ( !isset( $post ) )
			global $post;

		$post_type_obj = get_post_type_object( $post->post_type );
		$settings = $wpba_wp_settings_api->get_option( "wpba-{$post_type_obj->name}-settings", 'wpba_settings', false);
		extract( $args );

		$show_post_thumbnail = ( isset( $show_post_thumbnail ) ) ? $show_post_thumbnail : false;
		$get_posts_args = array(
			'post_type'				=>	'attachment',
			'posts_per_page'	=>	-1,
			'post_parent'			=>	$post->ID,
			'order'						=>	'ASC',
			'orderby'					=>	'menu_order'
		);
		// Should we exclude the thumb?
		if ( isset( $settings['thumbnail'] ) OR !$show_post_thumbnail ) {
			$get_posts_args['exclude'] = get_post_thumbnail_id($post->ID);
			$get_posts_args['meta_query'] = array(
				array(
					'key' => '_thumbnail_id',
					'compare' => 'NOT EXISTS'
				)
			);
		} // if()

		// Get the attachments
		$attachments = get_posts( $get_posts_args );

		return $attachments;
	} // get_post_attachments()


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
	protected function is_image( $mime_type )
	{
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
	protected function is_document( $mime_type )
	{
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
	protected function is_audio( $mime_type )
	{
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
	protected function is_video( $mime_type )
	{
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
	* Check Allowed Files
	*
	* @since 1.3.1
	*/
	function check_allowed_file_extensions( $attachments, $allowed_extensions )
	{

		foreach ( $attachments as $key => $attachment ) {
			$attachment_url = wp_get_attachment_url( $attachment->ID );
			if ( $attachment_url ) {
				$filetype = wp_check_filetype( $attachment_url );
				$is_allowed_extension = in_array( $filetype['ext'], $allowed_extensions );
				if ( !$is_allowed_extension ) {
					unset( $attachments[$key] );
				} // if()
			} // if()
		} // foreach()

		return $attachments;
	} // check_allowed_file_extensions()


	/**
	* Check Allowed File Type Categories
	*
	* @since 1.3.1
	*/
	function check_allowed_file_type_categories( $attachments, $allowed_categories )
	{
		// is_image
		// is_document
		// is_audio
		// is_video
		$allowed = array();
		foreach ( $attachments as $key => $attachment ) {
			foreach ( $allowed_categories as $allowed_category ) {
				switch ( $allowed_category ) {
					case 'image':
						if ( $this->is_image( $attachment->post_mime_type ) ) {
							$allowed[] = $attachment;
						} // if()
						break;

					case 'file':
						if ( $this->is_document( $attachment->post_mime_type ) ) {
							$allowed[] = $attachment;
						} // if()
						break;

					case 'audio':
						if ( $this->is_audio( $attachment->post_mime_type ) ) {
							$allowed[] = $attachment;
						} // if()
						break;

					case 'video':
						if ( $this->is_video( $attachment->post_mime_type ) ) {
							$allowed[] = $attachment;
						} // if()
						break;

					default:
						break;
				} // switch
			} // foreach
		} // foreach()

		return $allowed;
	} // check_allowed_file_type_categories()


	/**
	* Get Extensions From WP Allowed Mime Types
	*
	* @since 1.3.1
	*/
	function get_allowed_extensions()
	{
		$allowed_mime_types = get_allowed_mime_types();
		$allowed_extensions = array();

		// Extract extensions from mime types
		foreach ($allowed_mime_types as $key => $value) {
			$extensions = explode( '|', $key );
			$allowed_extensions = array_merge( $allowed_extensions, $extensions );
		} // foreach()

		return $allowed_extensions;
	} // get_allowed_extensions()

	/**
	* Attach
	*/
	public function attach( $args = array() )
	{
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
	public function unattach( $args = array() )
	{
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
	public function insert_attachment( $url )
	{
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
