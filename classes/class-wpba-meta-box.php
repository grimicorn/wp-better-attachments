<?php
/**
 * WP Better Attachments MEta Box
 */
class WPBA_Meta_Box extends WP_Better_Attachments
{

	/**
	 * Constructor
	 */
	public function __construct( $config = array() ) {
		parent::__construct();
		$this->init_hooks();
	} // __construct


	/**
	 * Initialization Hooks
	 */
	public function init_hooks() {
		add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ) );
	} // init_hooks()


	/**
	 * Adds the meta box container
	 */
	public function add_meta_box() {
		$post_types = get_post_types();
		unset( $post_types["attachment"] );
		unset( $post_types["revision"] );
		unset( $post_types["nav_menu_item"] );
		unset( $post_types["deprecated_log"] );
		global $wpba_wp_settings_api;
		$disabled_post_types = $wpba_wp_settings_api->get_option( 'wpba-disable-post-types', 'wpba_settings', array() );

		foreach ( $post_types as $post_type ) {
			if ( !in_array( $post_type, $disabled_post_types ) ) {
				add_meta_box(
					'wpba_meta_box',
					__( 'WP Better Attachments', WPBA_LANG ),
					array( &$this, 'render_meta_box_content' ),
					$post_type,
					'advanced',
					'high'
				);
			} // if()
		} // foreach()
	} // add_meta_box()


	/**
	 * Render Meta Box content
	 */
	public function render_meta_box_content() {
		global $post; ?>
		<div id="wpba-post-<?php echo $post->ID; ?>" data-postid="<?php echo $post->ID; ?>" class="clearfix wpba">
			<input type="hidden" name="wpba_nonce" id="wpba_nonce" value="<?php echo wp_create_nonce(basename(__FILE__)) ;?>" />
			<div class="uploader pull-left">
			<?php global $wp_version;
			if ( floatval( $wp_version ) >= 3.5 ) { ?>
				<a class="button wpba-attachments-button" id="wpba_attachments_button" href="#">Add Attachments</a>
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
	*/
	public function output_title_input( $args = array() )
	{
		extract( $args );
		$post = get_post( $attachment->post_parent );
		$post_type_obj = get_post_type_object( $post->post_type );
		global $wpba_wp_settings_api;
		$disabled_post_types = $wpba_wp_settings_api->get_option( "wpba-{$post_type_obj->name}-settings", 'wpba_settings', array() );

		// Make sure user has not disabled title editing
		if ( isset( $disabled_post_types['title'] ) )
			return '';

		// Build title form
		$html = '';
		$nl = "\n";
		$html .= '<label class="wpba-label" for="attachment_'.$attachment->ID.'_title">Title</label>';
		$html .= '<input ' . $nl;
		$html .= 'class="pull-left wpba-attachment-title widefat" ' . $nl;
		$html .= 'id="attachment_'.$attachment->ID.'_title" ' . $nl;
		$html .= 'name="attachment_'.$attachment->ID.'_title" ' . $nl;
		$html .= 'value="'.$attachment->post_title.'" ' . $nl;
		$html .= 'placeholder="Title">' . $nl;
		return $html;
	} // output_title_input()


	/**
	* Output Caption Form Input
	*/
	public function output_caption_input( $args = array() )
	{
		extract( $args );
		$post = get_post( $attachment->post_parent );
		$post_type_obj = get_post_type_object( $post->post_type );
		global $wpba_wp_settings_api;
		$disabled_post_types = $wpba_wp_settings_api->get_option( "wpba-{$post_type_obj->name}-settings", 'wpba_settings', array() );

		// Make sure user has not disabled caption editing
		if ( isset( $disabled_post_types['caption'] ) )
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
	* Output WYSWIG Form Input
	*/
	public function output_wyswig_input( $args = array() )
	{
		// Not Available before 3.3
		global $wp_version;
		if ( floatval($wp_version) >= 3.3 )
			return false;

		extract( $args );
		$html = '';
		$nl = "\n";
		return $html;
	} // output_wyswig_input()

	/**
	 * Output Post Attachments
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
	*/
	public function build_image_attachment_li( $attachments, $args = array() ) {
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

			$html .= '<li class="wpba-attachment-item ui-state-default" id="attachment_'.$attachment_id.'" data-id="'.$attachment_id.'">' . $nl;
				$html .= '<div class="inner">' . $nl;
				$html .= '<div class="wpba-drag-handle"><span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span></div>' . $nl;
				$html .= '<div class="wpba-preview pull-left" data-id="'.$attachment_id.'">' . $nl;
					$html .= '<ul class="unstyled pull-left wpba-edit-attachment hide-if-no-js" data-id="'.$attachment_id.'">' . $nl;
						$html .= '<li class="pull-left"><a href="#" class="wpba-unattach">Un-attach</a> | </li>' . $nl;
						$html .= '<li class="pull-left"><a href="'.$attachment_edit_link.'" class="wpba-edit">Edit</a> | </li>' . $nl;
						$html .= '<li class="pull-left"><a href="#" class="wpba-delete">Delete</a></li>' . $nl;
					$html .= '</ul>' . $nl;
					if ( !$this->is_image( $mime_type ) ) {
						$attachment_url = wp_get_attachment_url( $attachment->ID );
						$file_name = pathinfo( $attachment_url, PATHINFO_FILENAME );
						$file_type = pathinfo( $attachment_url, PATHINFO_EXTENSION );
						$attachment_title = "<span class='wpba-filename'>{$file_name}</span>.{$file_type}";
						$html .= '<span class="wpba-attachment-name">'.$attachment_title.'</span>';
					} // if()
					$html .= $placeholder_img;
				$html .= '</div>' . $nl;
				$html .= '<div class="wpba-form-wrap pull-left" data-id="'.$attachment_id.'">' . $nl;
					$html .= '<div class="wpba-attachment-id-output">Attachment ID: '.$attachment->ID.'</div>'  . $nl;
					$html .= '<div>' . $this->output_title_input( array( 'attachment' => $attachment) )  . '</div>' . $nl;
					$html .= '<div>' . $this->output_caption_input( array( 'attachment' => $attachment) ) . '</div>'  . $nl;
					// $html .= '<div>' . $this->output_wyswig_input( array( 'attachment' => $attachment) ) . '</div>'  . $nl;
				$html .= '</div>' . $nl;
				$html .= '<div class="clear"></div>' . $nl;
				$html .= '</div>' . $nl;
			$html .= '</li>'  . $nl;
		} // foreach();

		return $html;
	} // build_image_attachment_li()


	/**
	* Edit Modal
	*/
	protected function edit_modal()
	{
		// Modal does not exist pre 3.5
		global $wp_version;
		if ( floatval( $wp_version ) <= 3.4 )
			return '';

		$html = '';
		$nl = "\n";
		$html .= '<div tabindex="0" id="wpba_edit_screen" class="supports-drag-drop" style="display: none;">' . $nl;
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

} // END Class WP_Better_Attachments

/**
 * Instantiate class and create return method for easier use later
 */
global $wpbamb;
$wpbamb = new WPBA_Meta_Box();

function call_WPBA_Meta_Box() {
	return new WPBA_Meta_Box();
} // call_WPBA_Meta_Box()
if ( is_admin() )
	add_action( 'load-post.php', 'call_WPBA_Meta_Box' );
