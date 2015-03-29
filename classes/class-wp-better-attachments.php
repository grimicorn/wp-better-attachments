<?php
/**
* WP Better Attachments
*
* @package WP_Better_Attachments
*
* @since 1.0.0
*
* @author Dan Holloran dan@danholloran.com
*/
class WP_Better_Attachments
{
	/** Global settings @var array */
	public $global_settings;
	/** Disabled post types @var array */
	public $disabled_post_types;
	/** Media table settings @var array */
	public $media_table_settings;
	/** Meta box settings @var array */
	public $meta_box_settings;
	/** Edit modal settings @var array */
	public $edit_modal_settings;
	/** Disabled file types @var array */
	public $disabled_file_types;
	/** Current post type meta box title @var string */
	public $current_post_type_meta_box_title;
	/** Current post type settings @var array */
	public $current_post_type_settings;
	/** Current post type disabled files @var array */
	public $current_post_type_disabled_file_types;
	/** Current WordPress global $post object @var array */
	public $current_post_obj;
	/** Current post type @var array */
	public $current_post_type;
	/** Current post type object @var array @uses get_post_type_object() */
	public $current_post_type_obj;



	/**
	* Constructor
	*
	* @param array $config Class configuration
	*
	* @since 1.0.0
	*/
	public function __construct( $config = array() )
	{
		// Setup
		$this->init_global_settings();
		$this->init_hooks();
	} // __construct



	/**
	* Initialize Global Properties
	*
	* @since 1.3.5
	*
	* @return Void
	*/
	public function init_global_properties()
	{
		global $post;
		global $wpba_wp_settings_api;
		$this->current_post_obj = new stdClass();
		$this->current_post_type = '';
		$this->current_post_type_obj = new stdClass();
		$this->current_post_type_settings = array();
		$this->current_post_type_disabled_file_types = array();
		if ( !is_null( $post ) ) {
			$this->current_post_obj = $post;
			$this->current_post_type = $post->post_type;
			$this->current_post_type_obj = get_post_type_object( $post->post_type );
			$this->current_post_type_meta_box_title = $wpba_wp_settings_api->get_option( "wpba-{$post->post_type}-meta-box-title", 'wpba_settings', 'WP Better Attachments' );
			$this->current_post_type_settings = $wpba_wp_settings_api->get_option( "wpba-{$post->post_type}-settings", 'wpba_settings', array() );
			$this->current_post_type_disabled_file_types = $wpba_wp_settings_api->get_option( "wpba-{$post->post_type}-disable-attachment-types", 'wpba_settings', array() );
		} // if()
	} // init_global_settings()



	/**
	* Initialize Global Settings
	*
	* @since 1.3.5
	*
	* @return Void
	*/
	public function init_global_settings()
	{
		global $wpba_wp_settings_api;
		$this->global_settings = $wpba_wp_settings_api->get_option( 'wpba-global-settings', 'wpba_settings', array() );
		$this->disabled_post_types = $wpba_wp_settings_api->get_option( 'wpba-disable-post-types', 'wpba_settings', array() );
		$this->media_table_settings = $wpba_wp_settings_api->get_option( 'wpba-media-table-settings', 'wpba_settings', array() );
		$this->meta_box_settings = $wpba_wp_settings_api->get_option( 'wpba-meta-box-settings', 'wpba_settings', array() );
		$this->edit_modal_settings = $wpba_wp_settings_api->get_option( 'wpba-edit-modal-settings', 'wpba_settings', array() );
		$this->disabled_file_types = $wpba_wp_settings_api->get_option( 'wpba-disable-attachment-types', 'wpba_settings', array() );
	} // init_global_settings()



	/**
	* Initialization Hooks
	*
	* @since 1.0.0
	*
	* @return Void
	*/
	public function init_hooks()
	{
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
		add_action('media_buttons_context', array( &$this, 'add_form_button' ) );

		// Properties
		add_action('wp_head', array( &$this, 'init_global_properties' ) );
		add_action('admin_head', array( &$this, 'init_global_properties' ) );
	} // init_hooks()



	/**
	* Add Attachments button above post editor
	*
	* @since 1.0.0
	*
	* @param  string $context HTML above post editor
	*
	* @return string Add attachment button HTML
	*/
	function add_form_button( $context )
	{
		// Make sure the user has not disabled this post type
		global $post;
		if ( isset( $post ) AND !empty( $this->disabled_post_types[$post->post_type] ) )
			return $context;


		// Check if the button has been added
		$button_added = ( strpos( $context, 'wpba_form_attachments_button' ) === false ) ? false : true;
		if ( $button_added )
			return $context;

		// Add the button
		$out = '<a class="button wpba-attachments-button wpba-form-attachments-button" id="wpba_form_attachments_button" href="#"><span class="wpba-media-buttons-icon"></span> Add Attachments</a>';

		return $context . $out;
	} // add_form_button()



	/**
	* Check if disabled in settings
	*
	* @since 1.3.5
	*
	* @param string  $option_type Option value
	* @param integer $post_ID Post ID for the post to check if disabled
	*
	* @return boolean
	*/
	public function setting_disabled( $option_type, $post_ID = null )
	{
		if ( $this->current_post_type_disabled( $post_ID ) )
			return true;

		switch ( $option_type ) {
			case 'thumbnail':
				if ( isset( $this->global_settings['thumbnail'] ) )
					return true;
				if ( isset( $this->current_post_type_settings['mb_thumbnail'] ) )
					return true;
				break;
			case 'shortcodes':
				if ( isset( $this->global_settings['no_shortcodes'] ) )
					return true;
				break;
			case 'crop-editor':
				if ( isset( $this->global_settings['no_crop_editor'] ) )
					return true;
				break;
			case 'crop-editor-all-image-sizes':
				if ( isset( $this->global_settings['no_crop_editor'] ) )
					return true;
				if ( isset( $this->global_settings['all_crop_sizes'] ) )
					return true;
				break;
			case 'media-table-edit-col':
				if ( isset( $this->media_table_settings['col_edit_link'] ) )
					return true;
				break;
			case 'media-table-unattach-link':
				if ( isset( $this->media_table_settings['unattach_link'] ) )
					return true;
				break;
			case 'media-table-unattach-col':
				if ( isset( $this->media_table_settings['col_unattach_link'] ) )
					return true;
				break;
			case 'media-table-reattach-link':
				if ( isset( $this->media_table_settings['reattach_link'] ) )
					return true;
				break;
			case 'media-table-reattach-col':
				if ( isset( $this->media_table_settings['col_reattach_link'] ) )
					return true;
				break;
			case 'meta-box-title-editor':
				if ( isset( $this->meta_box_settings['gmb_title'] ) )
					return true;
				if ( isset( $this->current_post_type_settings['title'] ) )
					return true;
				break;
			case 'meta-box-caption':
				if ( isset( $this->meta_box_settings['gmb_caption'] ) )
					return true;
				if ( isset( $this->current_post_type_settings['caption'] ) )
					return true;
				break;
			case 'meta-box-attachment-id':
				if ( isset( $this->meta_box_settings['gmb_show_attachment_id'] ) )
					return true;
				if ( isset( $this->current_post_type_settings['mb_show_attachment_id'] ) )
					return true;
				break;
			case 'meta-box-unattach':
				if ( isset( $this->meta_box_settings['gmb_unattach_link'] ) )
					return true;
				if ( isset( $this->current_post_type_settings['mb_unattach_link'] ) )
					return true;
				break;
			case 'meta-box-edit':
				if ( isset( $this->meta_box_settings['gmb_edit_link'] ) )
					return true;
				if ( isset( $this->current_post_type_settings['mb_edit_link'] ) )
					return true;
				break;
			case 'meta-box-delete':
				if ( isset( $this->meta_box_settings['gmb_delete_link'] ) )
					return true;
				if ( isset( $this->current_post_type_settings['mb_delete_link'] ) )
					return true;
				break;
			case 'image-file-type':
				if ( isset( $this->disabled_file_types['disable_image'] ) )
					return true;
				if ( isset( $this->current_post_type_disabled_file_types['pt_disable_image'] ) )
					return true;
				break;
			case 'video-file-type':
				if ( isset( $this->disabled_file_types['disable_video'] ) )
					return true;
				if ( isset( $this->current_post_type_disabled_file_types['pt_disable_video'] ) )
					return true;
				break;
			case 'audio-file-type':
				if ( isset( $this->disabled_file_types['disable_audio'] ) )
					return true;
				if ( isset( $this->current_post_type_disabled_file_types['pt_disable_audio'] ) )
					return true;
				break;
			case 'documents-file-type':
				if ( isset( $this->disabled_file_types['disable_document'] ) )
					return true;
				if ( isset( $this->current_post_type_disabled_file_types['pt_disable_document'] ) )
					return true;
				break;
			case 'edit-modal-caption':
				if ( isset( $this->edit_modal_settings['gem_caption'] ) )
					return true;
				if ( isset( $this->current_post_type_settings['em_caption'] ) )
					return true;
				break;
			case 'edit-modal-alternative':
				if ( isset( $this->edit_modal_settings['gem_alternative_text'] ) )
					return true;
				if ( isset( $this->current_post_type_settings['em_alternative_text'] ) )
					return true;
				break;
			case 'edit-modal-description':
				if ( isset( $this->edit_modal_settings['gem_description'] ) )
					return true;
				if ( isset( $this->current_post_type_settings['em_description'] ) )
					return true;
				break;
			default:
				return false;
				break;
		}

		return false;
	} // setting_disabled()



	/**
	* Current Post Type Disabled
	*
	* @since 1.3.5
	*
	* @param integer $post_ID Post ID for the post to check if disabled
	*
	* @return boolean
	*/
	public function current_post_type_disabled( $post_ID = null )
	{
		$current_post_type = $this->current_post_type;

		if ( ! is_null( $post_ID ) )
			$current_post_type = get_post_type( $post_ID );

		if ( in_array( $current_post_type, $this->disabled_post_types ) )
			return true;

		return false;
	} // current_post_type_disabled()



	/**
	* Enqueue Administrator Scripts and Styles
	*
	* @since 1.0.0
	*
	* @return Void
	*/
	public function enqueue_admin_scripts()
	{
		// Make sure the user has not disabled this post type
		global $post;
		if ( isset( $post ) AND !empty( $this->disabled_post_types[$post->post_type] ) )
			return false;

		// Make sure we neeed to load WPBA files
		$current_screen = get_current_screen();
		$base = $current_screen->base;
		if ( $base == 'edit' or $base == 'upload' or $base == 'post' OR $base == 'settings_page_wpba-settings' ) {
			// WPBA Main Style File
			wp_enqueue_style(
				'wpba-admin-css',
				plugins_url( 'assets/css/wpba-admin.css' , dirname( __FILE__ ) ),
				null,
				WPBA_VERSION,
				'all'
			);

			// JS Dependencies
			$deps = array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-widget',
				'jquery-ui-mouse',
				'jquery-ui-sortable'
			);

			// Media Scripts
			if ( !did_action( 'wp_enqueue_media' ) )
				wp_enqueue_media();

			// WPBA Main Script File
			wp_register_script(
				'wpba-media-handler',
				plugins_url( 'assets/js/wpba-media-handler-new.min.js' , dirname( __FILE__ ) ),
				$deps,
				WPBA_VERSION,
				true
			);

			wp_enqueue_script( 'wpba-media-handler' );
		} // if()
	} // enqueue_admin_scripts()



	/**
	* Enqueue Frontend Scripts and Styles
	*
	* @since 1.3.2
	*
	* @return Void
	*/
	function enqueue_scripts()
	{
		if ( isset($this->global_settings['no_shortcodes']) )
			return false;

		// WPBA FrontEnd Styles
		wp_register_style(
			'wpba_front_end_styles',
			plugins_url( 'assets/css/wpba-frontend.css' , dirname( __FILE__ ) ),
			array(),
			WPBA_VERSION,
			'all'
		);
		wp_enqueue_style( 'wpba_front_end_styles' );
	} // enqueue_scripts()



	/**
	* Get attachment ID from src url
	*
	* @since 1.0.0
	*
	* @param string $attachment_url Absolute URI to an attachment
	*
	* @return integer Post ID
	*/
	public function get_attachment_id_from_src( $attachment_url )
	{
		global $wpdb;
		$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$attachment_url'";
		$id = $wpdb->get_var( $query );
		return $id;
	} // get_attachment_id_from_src()



	/**
	* Get Post Attachments
	*
	* @since 1.0.0
	*
	* @param  string[]  $args {
	* 	@type integer $post                Optional Post object used to retrieve attachments
	* 	@type boolean $show_post_thumbnail Optional To include thumbnail as attachment. Default false
	* }
	*
	* @return array Retrieved attachments
	*/
	public function get_post_attachments( $args = array() )
	{
		extract( $args );

		if ( isset( $post_id ) )
			$post = get_post( $post_id );

		// Make sure we have a post to work with
		if ( ! isset( $post ) )
			global $post;

		// Make sure that post is not null
		if ( is_null( $post ) )
			return array();

		// Specific Post settings
		global $wpba_wp_settings_api;
		$post_settings = $wpba_wp_settings_api->get_option( "wpba-{$post->post_type}-settings", 'wpba_settings', array() );


		$get_posts_args = array(
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'post_parent'    => $post->ID,
			'order'          => 'ASC',
			'orderby'        => 'menu_order'
		);

		// Should we exclude the thumb?
		$post_settings_thumb = isset( $post_settings['thumbnail'] );
		$global_settings_thumb = isset( $this->global_settings['thumbnail'] );
		$no_thumbs = false;
		if ( isset( $show_post_thumbnail ) ) {
			if ( !$show_post_thumbnail ) {
				$no_thumbs = true;
			} // if()
		} elseif( $post_settings_thumb OR $global_settings_thumb ) {
			$no_thumbs = true;
		} // if/else


		if ( $no_thumbs ) {
			// Need to more han likely check againts show_post_thumbnnail isset
			$get_posts_args['exclude'] = get_post_thumbnail_id( $post->ID );
			$get_posts_args['meta_query'] = array(array(
				'key' => '_thumbnail_id',
				'compare' => 'NOT EXISTS'
			));
		} // if()

		// Get the attachments
		$attachments = $this->validate_attachment_mime_type( get_posts( $get_posts_args ), $post->ID );

		return $attachments;
	} // get_post_attachments()



	/**
	* Validate Attachment Mime Type Settings
	*
	* @since 1.3.6
	*
	* @param array   $attachments Post attachment objects
	* @param integer $post_id     The post ID of the post to validate
	*
	* @return boolean
	*/
	function validate_attachment_mime_type( $attachments, $post_id )
	{
		$disable_image = $this->setting_disabled( 'image-file-type', $post_id );
		$disable_video = $this->setting_disabled( 'video-file-type', $post_id );
		$disable_audio = $this->setting_disabled( 'audio-file-type', $post_id );
		$disable_document = $this->setting_disabled( 'documents-file-type', $post_id );

		foreach ( $attachments as $key => $attachment ) {
			$mime_type = get_post_mime_type( $attachment->ID );
			if ( $this->is_image( $mime_type) AND $disable_image )
				unset( $attachments[$key] );
			if ( $this->is_video( $mime_type) AND $disable_video )
				unset( $attachments[$key] );
			if ( $this->is_audio( $mime_type) AND $disable_audio )
				unset( $attachments[$key] );
			if ( $this->is_document( $mime_type) AND $disable_document )
				unset( $attachments[$key] );
		} // foreach()

		return $attachments;
	} // validate_attachment_mime_type()



	/**
	* Attachment placeholder image name
	*
	* @since 1.0.0
	*
	* @param string $mime_type Mime type value
	*
	* @return string Placeholder image name
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
	*
	* @since 1.0.0
	*
	* @param string $mime_type Mime type value
	*
	* @return boolean
	*/
	protected function is_image( $mime_type )
	{
		$image_mime_types = array(
			'image/jpeg',
			'image/gif',
			'image/png'
		);

		// Filter the image mime types
		$image_mime_types = apply_filters( 'wpba_is_image', $image_mime_types );

		if ( in_array( $mime_type, $image_mime_types ) )
			return true;

		return false;
	} // is_image()



	/**
	* Attachment is a document
	*
	* @since 1.0.0
	*
	* @param string $mime_type Mime type value
	*
	* @return boolean
	*/
	protected function is_document( $mime_type )
	{
		$document_mime_types = array(
			'application/pdf',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/msword',
			'application/epub+zip',
			'application/vnd.ms-powerpoint',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'application/vnd.oasis.opendocument.text',
			'application/vnd.ms-excel',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'application/zip'
		);

		// Filter the document mime types
		$document_mime_types = apply_filters( 'wpba_is_document', $document_mime_types );

		if ( in_array( $mime_type, $document_mime_types ) )
			return true;

		return false;
	} // is_document()



	/**
	* Attachment is audio
	*
	* @since 1.0.0
	*
	* @param string $mime_type Mime type value
	*
	* @return boolean
	*/
	protected function is_audio( $mime_type )
	{
		$audio_mime_types = array(
			'audio/mpeg',
			'audio/mpeg',
			'audio/ogg',
			'audio/wav'
		);

		// Filter the audio mime types
		$audio_mime_types = apply_filters( 'wpba_is_audio', $audio_mime_types );

		if ( in_array( $mime_type, $audio_mime_types ) )
			return true;

		return false;
	} // is_audio()



	/**
	* Attachment is video
	*
	* @since 1.0.0
	*
	* @param string $mime_type Mime type value
	*
	* @return boolean
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

		// Filter the video mime types
		$video_mime_types = apply_filters( 'wpba_is_video', $video_mime_types );

		if ( in_array( $mime_type, $video_mime_types ) )
			return true;

		return false;
	} // is_video()



	/**
	* Check Allowed Files
	*
	* @since 1.3.2
	*
	* @param array $attachments Attachment post objects
	* @param array $allowed_extensions Allowed extensions
	*
	* @return array Attachments with allowed file type(s)
	*/
	function check_allowed_file_extensions( $attachments, $allowed_extensions )
	{

		foreach ( $attachments as $key => $attachment ) {
			$attachment_url = wp_get_attachment_url( $attachment->ID );
			if ( $attachment_url ) {
				$filetype = wp_check_filetype( $attachment_url );
				$is_allowed_extension = in_array( $filetype['ext'], $allowed_extensions );
				if ( !$is_allowed_extension )
					unset( $attachments[$key] );
			} // if()
		} // foreach()

		return $attachments;
	} // check_allowed_file_extensions()



	/**
	* Check Allowed File Type Categories
	*
	* @since 1.3.2
	*
	* @param array $attachments Attachment post objects
	* @param array $allowed_extensions Allowed categories
	*
	* @return array Attachments with allowed file categories
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
	* @since 1.3.2
	*
	* @return array Allowed extensions
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
	*
	* @since 1.0.0
	*
	* @param  string[]  $args {
	* 	@type integer $parent_id Required Post ID to add the attachment to
	* 	@type array   $media     Attachment IDs
	* }
	*
	* @return boolean
	*/
	public function attach( $args = array() )
	{
		extract( $args );
		global $wpdb;

		if ( ! $parent_id )
			return;

		$parent = get_post( $parent_id );

		$attach = array();
		foreach ( (array) $media as $att_id ) {
			$att_id = (int) $att_id;

			$attach[] = $att_id;
		} // if()

		if ( ! empty( $attach ) ) {
			$attach_string = implode( ',', $attach );
			$attached = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_parent = %d WHERE post_type = 'attachment' AND ID IN ( $attach_string )", $parent_id ) );
			foreach ( $attach as $att_id ) {
				clean_attachment_cache( $att_id );
			} // foreach()
		} // if()

		if ( isset( $attached ) )
			return true;

		return false;
	} //attach()



	/**
	* Un-attach
	*
	* @since 1.0.0
	*
	* @param  string[]  $args {
	* 	@type integer $attachment_id Required Post ID of the attachment to un-attach
	* }
	*
	* @return boolean
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
	*
	* @since 1.0.0
	*
	* @param  string $url URL of the attachment to add
	*
	* @return integer Post ID of the inserted attachment
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

		require_once ABSPATH . 'wp-admin/includes/image.php';
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	} // insert_attachemt()
} // END Class WP_Better_Attachments()

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
