<?php
/**
* WP Better Attachments Meta Box
*
* @package WP_Better_Attachments
*
* @since 1.0.0
*
* @author Dan Holloran dan@danholloran.com
*/
class WPBA_Meta_Box extends WP_Better_Attachments
{
	/**
	* Constructor
	*
	* @param array $config Class configuration
	*
	* @since 1.0.0
	*/
	public function __construct( $config = array() ) {
		parent::__construct();
		$this->init_hooks();
	} // __construct



	/**
	* Initialization Hooks
	*
	* @since 1.0.0
	*
	* @return Void
	*/
	public function init_hooks() {
		add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ) );

		// Properties
		add_action('wp_head', array( &$this, 'init_global_properties' ) );
		add_action('admin_head', array( &$this, 'init_global_properties' ) );
	} // init_hooks()



	/**
	* Adds the meta box container
	*
	* @since 1.0.0
	*
	* @return Void
	*/
	public function add_meta_box() {
		$post_types = get_post_types();
		unset( $post_types["attachment"] );
		unset( $post_types["revision"] );
		unset( $post_types["nav_menu_item"] );
		unset( $post_types["deprecated_log"] );

		global $wpba_wp_settings_api;
		global $post;

		$disabled_post_types = $this->disabled_post_types;

		foreach ( $post_types as $post_type ) {

			if ( $this->meta_box_is_enabled( $post_type ) ) {
				$meta_box_title = $wpba_wp_settings_api->get_option( "wpba-{$post->post_type}-meta-box-title", 'wpba_settings', 'WP Better Attachments' );
				add_meta_box(
					'wpba_meta_box',
					__( $meta_box_title, WPBA_LANG ),
					array( &$this, 'render_meta_box_content' ),
					$post_type,
					'advanced',
					'high'
				);
			} // if()
		} // foreach()
	} // add_meta_box()



	/**
	 * Checks if the meta box is enabled
	 * This will validate if the current page and post type have been enabled in settings
	 *
	 * @param  string $validation_post_type Post type to validate against
	 *
	 * @return boolean                      Returns true if the meta box is enabled
	 */
	public function meta_box_is_enabled( $validation_post_type )
	{
		global $wpba_wp_settings_api;
		global $post;

		// Post Type Check
		$disabled_post_types = $this->disabled_post_types;
		$post_type_enabled = ( ! in_array( $validation_post_type, $disabled_post_types ) );

		// Page Check
		$enabled_page_slugs = $wpba_wp_settings_api->get_option( "wpba-{$post->post_type}-enabled-pages", 'wpba_settings', '' );
		$page_enabled = true;
		if ( $enabled_page_slugs !== '' ) {
			$enabled_pages = explode(',', $enabled_page_slugs);

			// Whitespace insensitive
			foreach ( $enabled_pages as $key => $enabled_page )
				$enabled_pages[$key] = trim( $enabled_page );

			$page_enabled = ( in_array( $post->post_name, $enabled_pages ) );
		} // if()

		// Validate both page and post type
		if ( $post_type_enabled && $page_enabled )
			return true;

		return false;
	} // meta_box_is_enabled()



	/**
	* Render Meta Box content
	*
	* @since 1.0.0
	*
	* @return Void
	*/
	public function render_meta_box_content() {
		global $post; ?>
		<div id="wpba-post-<?php echo $post->ID; ?>" data-postid="<?php echo $post->ID; ?>" class="clearfix wpba<?php echo $this->display_class(); ?>">
			<input type="hidden" name="wpba_nonce" id="wpba_nonce" value="<?php echo wp_create_nonce(basename(__FILE__)) ;?>" />
			<div class="uploader pull-left">
			<?php global $wp_version;
			if ( floatval( $wp_version ) >= 3.5 ) { ?>
				<a class="button wpba-attachments-button" id="wpba_attachments_button" href="#"><span class="wpba-media-buttons-icon"></span> Add Attachments</a>
			<?php } ?>
			</div>
			<div class="pull-left wpba-saving hide">
				<span>Saving Attachments </span>
				<img src="<?php echo admin_url( 'images/wpspin_light.gif' ); ?>">
			</div>
			<div class="clear"></div>
			<?php echo $this->output_post_attachments(); ?>
			<div class="clear"></div>
		</div>
		<?php
		echo $this->edit_modal();
	} // render_meta_box_content()



	/**
	* Output Title Form Input
	*
	* @since 1.2.0
	*
	* @return string Title meta box input field HTML
	*/
	public function output_title_input( $args = array() )
	{
		extract( $args );
		$post = get_post( $attachment->post_parent );
		$post_type_obj = get_post_type_object( $post->post_type );
		global $wpba_wp_settings_api;
		$disabled_post_types = $wpba_wp_settings_api->get_option( "wpba-{$post_type_obj->name}-settings", 'wpba_settings', array() );

		// Make sure user has not disabled title editing
		if ( $this->setting_disabled( 'meta-box-title-editor' ) )
			return '';

		// Build title form
		$html = '';
		$nl = "\n";
		$html .= '<label class="wpba-label" for="attachment_'.$attachment->ID.'_title">Title</label>';
		$html .= '<input ';
		$html .= 'type="text" ';
		$html .= 'class="pull-left wpba-attachment-title widefat" ';
		$html .= 'id="attachment_'.$attachment->ID.'_title" ';
		$html .= 'name="attachment_'.$attachment->ID.'_title" ';
		$html .= 'value="'.$attachment->post_title.'" ';
		$html .= 'placeholder="Title">' . $nl;
		return $html;
	} // output_title_input()



	/**
	* Output Caption Form Input
	*
	* @since 1.2.0
	*
	* @return string Caption meta box input field HTML
	*/
	public function output_caption_input( $args = array() )
	{
		extract( $args );
		$post = get_post( $attachment->post_parent );
		$post_type_obj = get_post_type_object( $post->post_type );
		global $wpba_wp_settings_api;
		$disabled_post_types = $wpba_wp_settings_api->get_option( "wpba-{$post_type_obj->name}-settings", 'wpba_settings', array() );

		// Make sure user has not disabled caption editing
		if ( $this->setting_disabled( 'meta-box-caption' ) )
			return '';

		// Build caption form
		$html = '';
		$nl = "\n";
		$html .= '<label class="wpba-label" for="attachment_'.$attachment->ID.'_caption">Caption</label>';
		$html .= '<textarea ' . $nl;
		$html .= 'class="pull-left clear wpba-attachment-caption widefat" ' . $nl;
		$html .= 'id="attachment_'.$attachment->ID.'_caption" ' . $nl;
		$html .= 'name="attachment_'.$attachment->ID.'_caption" ' . $nl;
		$html .= 'placeholder="Caption" >'.$attachment->post_excerpt.'</textarea>' . $nl;
		return $html;
	} // output_caption_input()



	/**
	* Editors Display Class
	*
	* @since 1.3.5
	*
	* @return string Class to control how to display the meta box either collapsed with no text boxes or normal
	*/
	public function display_class() {
		global $post;
		$post_type_obj = get_post_type_object( $post->post_type );

		if ( $this->setting_disabled( 'meta-box-title-editor' ) AND $this->setting_disabled( 'meta-box-caption' ) )
			return ' wpba-editor-collapsed';

		return '';
	} // display_class()


	/**
	* Output Post Attachments
 	*
	* @since 1.0.0
	*
	* @return string Attachments HTML
	*/
	public function output_post_attachments( $args = array() )
	{
		extract( $args );

		$html = '';
		$nl = "\n";
		$attachments = $this->get_post_attachments( $args );
		$html .= '<ul id="wpba_image_sortable" class="unstyled wpba-image-attachments">';
		// Build Attachments Output
		if ( !empty( $attachments ) ) {
			$html .= $this->build_image_attachment_li( $attachments );
		} // if (!empty($attachments))
		$html .= '</ul>';

		return $html;
	} // output_post_attachments()



	/**
	* Build Attachment List
	*
	* @since 1.0.0
	*
	* @return string Attachment image list item(s) HTML
	*/
	public function build_image_attachment_li( $attachments, $args = array() ) {
		extract( $args );
		$html = '';
		$nl = "\n";


		foreach ( $attachments as $attachment ) {
			$attachment_id = ( isset( $a_array ) and $a_array ) ? $attachment['id'] : $attachment->ID;
			$attachment = get_post( $attachment_id );
			$mime_type = get_post_mime_type( $attachment_id );



			$html .= '<li class="wpba-attachment-item ui-state-default" id="attachment_'.$attachment_id.'" data-id="'.$attachment_id.'">' . $nl;
				$html .= '<div class="inner">' . $nl;
				$html .= '<div class="wpba-drag-handle"><span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span></div>' . $nl;
				$html .= '<div class="wpba-preview pull-left" data-id="'.$attachment_id.'">' . $nl;
					$html .= $this->output_menu_id_title( $attachment, $mime_type );
					$html .= $this->output_placeholder_image( $attachment, $mime_type );
				$html .= '</div>' . $nl;
				$html .= '<div class="wpba-form-wrap pull-left" data-id="'.$attachment_id.'">' . $nl;
					$html .= '<div>' . $this->output_title_input( array( 'attachment' => $attachment) )  . '</div>' . $nl;
					$html .= '<div>' . $this->output_caption_input( array( 'attachment' => $attachment) ) . '</div>'  . $nl;
				$html .= '</div>' . $nl;
				$html .= '<div class="clear"></div>' . $nl;
				$html .= '</div>' . $nl;
			$html .= '</li>'  . $nl;
		} // foreach();

		return $html;
	} // build_image_attachment_li()



	/**
	* Output Edit Modal
	* @since 1.3.6
	*
	* @return string Edit menu HTML
	*/
	public function output_edit_menu( $attachment )
	{
		if ( $this->setting_disabled( 'meta-box-unattach' ) AND $this->setting_disabled( 'meta-box-delete' ) AND $this->setting_disabled( 'meta-box-edit' ) )
			return '';

		$menu = '';
		$nl = "\n";
		$attachment_edit_link = admin_url( "post.php?post={$attachment->ID}&action=edit" );
		$menu .= '<ul class="unstyled pull-left wpba-edit-attachment hide-if-no-js" data-id="'.$attachment->ID.'">' . $nl;
			if ( !$this->setting_disabled( 'meta-box-unattach' ) )
				$menu .= '<li class="pull-left"><a href="#" class="wpba-unattach">Un-attach</a></li>' . $nl;
			if ( !$this->setting_disabled( 'meta-box-edit' ) )
				$menu .= '<li class="pull-left"> | <a href="'.$attachment_edit_link.'" class="wpba-edit">Edit</a></li>' . $nl;
			if ( !$this->setting_disabled( 'meta-box-delete' ) )
				$menu .= '<li class="pull-left"> | <a href="#" class="wpba-delete">Delete</a></li>' . $nl;
		$menu .= '</ul>' . $nl;

		return $menu;
	} // output_edit_menu()



	/**
	* Output attachment menu, title, and id
	*
	* @since 1.3.6
	*
	* @return string Title with attachment information HTML
	*/
	public function output_menu_id_title( $attachment, $mime_type )
	{
		$menu_id_title = '';
		$menu = $this->output_edit_menu( $attachment );
		$id_class = ( $menu == '' ) ? ' no-menu' : '';
		$menu_id_title .= $menu;

		if ( !$this->is_image( $mime_type ) ) {
			$attachment_url = wp_get_attachment_url( $attachment->ID );
			$file_name = pathinfo( $attachment_url, PATHINFO_FILENAME );
			$file_type = pathinfo( $attachment_url, PATHINFO_EXTENSION );
			$attachment_title = "<span class='wpba-filename'>{$file_name}</span>.{$file_type}";
			$menu_id_title .= '<span class="wpba-attachment-name">'.$attachment_title.'</span>';
			$id_class .= ' with-title';
		} // if()

		// Attachment ID
		if ( !$this->setting_disabled( 'meta-box-attachment-id' ) )
				$menu_id_title .= "<span class='wpba-attachment-id-output{$id_class}'>Attachment ID: {$attachment->ID}</span>";

		return $menu_id_title;
	} // output_menu_id_title()




	/**
	* Output Placeholder Image
	*
	* @since 1.3.6
	*
	* @return string Placeholder image HTML for attachment types other than image
	*/
	function output_placeholder_image( $attachment, $mime_type )
	{
		$placeholder_img = '';
		$img_class = ( $this->setting_disabled( 'meta-box-attachment-id' ) ) ? 'wpba-preview-img attachment-disabled' : 'wpba-preview-img';

		if ( $this->is_image( $mime_type ) ) {
			$attachment_src = wp_get_attachment_image_src( $attachment->ID, 'thumbnail' );
			if ( !empty( $attachment_src ) )
				$src = $attachment_src[0];
				$width = $attachment_src[1];
				$height = $attachment_src[2];
				$id = $attachment->ID;
				$placeholder_img .= "<img src='{$src}' width='{$width}' height='{$height}' class='attid-{$id} {$img_class}' data-url='{$src}'>";
		} else {
			$placeholder_img_file = $this->placeholder_image( $mime_type );
			$img_src = site_url().'/wp-includes/images/crystal/'.$placeholder_img_file;
			$placeholder_img .= '<div class="icon-wrap"><img src="'.$img_src.'" class="icon" draggable="false"></div>';
		} // if/else()

		return $placeholder_img;
	} // placeholder_image()



	/**
	* Edit Modal
	*
	* @since 1.1.0
	*
	* @return string Edit modal HTML
	*/
	public function edit_modal()
	{
		// Modal does not exist pre 3.5
		global $wp_version;
		if ( floatval( $wp_version ) <= 3.4 )
			return '';

		$modal_settings = json_encode(array(
			'caption'			=> $this->setting_disabled( 'edit-modal-caption' ),
			'alt'					=> $this->setting_disabled( 'edit-modal-alternative' ),
			'description'	=> $this->setting_disabled( 'edit-modal-description' )
		));

		$html = '';
		$nl = "\n";
		$html .= "<div tabindex='0' id='wpba_edit_screen' class='supports-drag-drop' style='display: none;' data-settings='{$modal_settings}'>" . $nl;
		$html .= '<div class="media-modal wp-core-ui">' . $nl;
		$html .= '<a id="wpba_edit_screen_close" class="media-modal-close" href="#" title="Close"><span class="media-modal-icon"></span></a>' . $nl;
		$html .= '<div class="media-modal-content">' . $nl;
		$html .= '<iframe seamless="seamless" src=""></iframe>' . $nl;
		$html .= '</div>' . $nl;
		$html .= '</div>' . $nl;
		$html .= '<div class="media-modal-backdrop"></div>' . $nl;
		$html .= '</div>' . $nl;

		return $html;
	} // edit_modal()
} // END Class WP_Better_Attachments()

/**
 * Instantiate class and create return method for easier use later
 */
global $wpba_meta_box;
$wpba_meta_box = new WPBA_Meta_Box();

function call_WPBA_Meta_Box() {
	return new WPBA_Meta_Box();
} // call_WPBA_Meta_Box()
if ( is_admin() )
	add_action( 'load-post.php', 'call_WPBA_Meta_Box' );
