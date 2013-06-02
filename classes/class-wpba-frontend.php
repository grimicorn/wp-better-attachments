<?php
/**
* WPBA Front End
*
* @since 1.3.1
*/
class WPBA_Frontend extends WP_Better_Attachments
{

	/**
	* Constructor
	*/
	public function __construct( $config = array() )
	{

	} // constructor()


	/**
	* Frontend Build Attachments List
	*
	* @since 1.3.1
	*/
	public function build_attachment_list( $args = array() )
	{
		$list = "";
		$nl = "\n";
		$plugin_url = plugins_url('wp-better-attachments');
		$atts_to_be_cleaned = array(
			'post_id'								=>	'int',
			'show_icon'							=>	'boolean',
			'file_type_categories'	=>	'array',
			'file_extensions'				=>	'array',
			'icon_size'							=>	'array',
			'use_attachment_page'		=>	'boolean',
			'open_new_window'				=>	'boolean',
			'show_post_thumbnail'		=>	'boolean'
		);
		$defaults = array(
			'post_id'								=>	NULL,
			'show_icon'							=>	true,
			'file_type_categories'	=>	array(
				'image',
				'file',
				'audio',
				'video'
			),
			'file_extensions'				=>	$this->get_allowed_extensions(),
			'image_icon'						=>	"{$plugin_url}/assets/img/icons/image-icon.png",
			'file_icon'							=>	"{$plugin_url}/assets/img/icons/file-icon.png",
			'audio_icon'						=>	"{$plugin_url}/assets/img/icons/audio-icon.png",
			'video_icon'						=>	"{$plugin_url}/assets/img/icons/video-icon.png",
			'icon_size'							=>	array( 16, 20 ),
			'use_attachment_page'		=>	false,
			'open_new_window'				=>	false,
			'show_post_thumbnail'		=>	false
		);
		$atts = shortcode_atts( $defaults, $args );
		$atts = $this->clean_shortcode_atts( $atts, $atts_to_be_cleaned );
		extract( $atts );

		// Set the post variable
		if ( !is_null( $post_id ) ) {
			$post = get_post( $post_id );
		} else {
			global $post;
		} // if/else()

		// Get the attachments
		$attachments = $this->get_post_attachments(array(
			'post' => $post,
			'show_post_thumbnail' => $show_post_thumbnail
		));

		// Make sure we have attachments
		if ( is_null( $attachments ) ) {
			return '';
		} // if()

		// Go through the restrictions
		$attachments = $this->check_allowed_file_type_categories( $attachments, $file_type_categories );
		$attachments = $this->check_allowed_file_extensions( $attachments, $file_extensions );

		// Build the list
		$list .= '<div class="wpba">' . $nl;
		$list .= '<ul class="wpba-attachment-list unstyled">';
		foreach ( $attachments as $attachment ) {
			$title = $attachment->post_title;
			$link = ( $use_attachment_page ) ? get_attachment_link( $attachment->ID ) : wp_get_attachment_url( $attachment->ID );
			$target = ( $open_new_window ) ? 'target="_blank"' : 'target="_self"';
			$list .= "<li>";
			if ( $show_icon ) $list .= $this->icon( $attachment, shortcode_atts( $defaults, $args ) );
			$list .= "<a href='{$link}' title='{$title}' class='pull-left' {$target}>{$title}</a>";
			$list .= "<li>" . $nl;
		} // foreach()
		$list .= "</ul>";
		$list .= '</div>' . $nl;

		return $list;
	} // wpba_build_attachment_list()


	/**
	* Frontend Build FlexSlider
	*
	* @since 1.3.1
	*/
	public function build_flexslider( $args = array() )
	{
		$defaults = array(
			'post_id'							=>	NULL,
			'show_post_thumbnail'	=>	false,
			'width'								=>	'600px',
			'height'							=>	'auto',
			'control_nav'					=>	true,
			'direction_nav'				=>	true
		);
		$atts = shortcode_atts( $defaults, $args );

		$atts_to_be_cleaned = array(
			'post_id'								=>	'int',
			'show_post_thumbnail'		=>	'boolean',
			'control_nav'						=>	'boolean',
			'direction_nav'					=>	'boolean'
		);
		$atts = $this->clean_shortcode_atts( $atts, $atts_to_be_cleaned );
		extract( $atts );
		pp($show_post_thumbnail);
		// Set the post variable
		if ( !is_null( $post_id ) ) {
			$post = get_post( $post_id );
		} else {
			global $post;
		} // if/else()

		// Get the attachments
		$attachments = $this->get_post_attachments(array(
			'post' => $post,
			'show_post_thumbnail' => $show_post_thumbnail
		));
		$slider_properties = json_encode(array(
			'controlNav'		=>	$control_nav,
			'directionNav'	=>	$direction_nav
		));
		$slider = '';
		$slider .= "<div class='wpba-flexslider flexslider' style='width:{$width};height:{$height};' data-sliderproperties='{$slider_properties}'>";
		$slider .= '<ul class="slides">';
		foreach ( $attachments as $attachment ) {
			if ( $this->is_image( $attachment->post_mime_type ) ) {
				$attachment_src = wp_get_attachment_image_src( $attachment->ID, 'full' );
				$src = $attachment_src[0];
				$width = $attachment_src[1];
				$height = $attachment_src[2];
				$slider .= "<li><img src='{$src}' width='{$width}' height='{$height}'/></li>";
			} // if()
		} // foreach()
		$slider .= '</ul>';
		$slider .= '</div>';

		return $slider;
	} // build_flexslider()


	/**
	* Attachment placeholder image name
	*
	* @since 1.3.1
	*/
	public function icon( $attachment, $args )
	{
		$img_src = '';
		$plugin_url = WPBA_PATH;
		extract( $args );
		if ( $this->is_document( $attachment->post_mime_type ) ) {
			$img_src = $file_icon;
		} elseif ( $this->is_audio( $attachment->post_mime_type ) ) {
			$img_src = $audio_icon;
		} elseif ( $this->is_video( $attachment->post_mime_type ) ) {
			$img_src = $video_icon;
		} elseif( $this->is_image( $attachment->post_mime_type ) ) {
			$img_src = $image_icon;
		}//if/elseif

		$img = "<img src='{$img_src}' width='{$icon_size[0]}' height='{$icon_size[1]}' class='pull-left'>";
		return $img;
	} // placeholder_image()

	/**
	* Cleanup Shortcode Attributes
	* @since 1.3.1
	*/
	public function clean_shortcode_atts( $atts, $att_keys )
	{
		foreach ( $att_keys as $key => $type ) {
			if ( gettype( $atts[$key] ) == 'string' ) {
				switch ( $type ) {
					case 'int':
						$atts[$key] = intval( $atts[$key] );
						break;

					case 'boolean':
						$atts[$key] = ( $atts[$key] === 'true' );
						break;

					case 'array':
						$no_spaces = str_replace( ' ', '', $atts[$key] );
						$atts[$key] = explode( ',', $atts[$key] );
						break;

					default:
						$atts[$key] = $atts[$key];
						break;
				} // switch()
			} // if()
		} // foreach()

		return $atts;
	}
} // class()


/**
 * Instantiate class and create return method for easier use later
 */
global $wpba_frontend;
$wpba_frontend = new WPBA_Frontend();