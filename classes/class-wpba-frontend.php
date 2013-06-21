<?php
/**
* WPBA Front End
*
* @since 1.3.2
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
	* @since 1.3.2
	*/
	public function build_attachment_list( $args = array() )
	{
		$list = "";
		$nl = "\n";
		$plugin_url = plugins_url('wp-better-attachments');
		$atts_to_be_cleaned = array(
			'post_id'								=> 'int',
			'show_icon'							=> 'boolean',
			'file_type_categories'	=> 'array',
			'file_extensions'				=> 'array',
			'icon_size'							=> 'array',
			'use_attachment_page'		=> 'boolean',
			'open_new_window'				=> 'boolean',
			'show_post_thumbnail'		=> 'boolean'
		);
		$defaults = array(
			'post_id'								=> NULL,
			'show_icon'							=> true,
			'file_type_categories'	=> array(
				'image',
				'file',
				'audio',
				'video'
			),
			'file_extensions'				=> $this->get_allowed_extensions(),
			'image_icon'						=> "{$plugin_url}/assets/img/icons/image-icon.png",
			'file_icon'							=> "{$plugin_url}/assets/img/icons/file-icon.png",
			'audio_icon'						=> "{$plugin_url}/assets/img/icons/audio-icon.png",
			'video_icon'						=> "{$plugin_url}/assets/img/icons/video-icon.png",
			'icon_size'							=> array( 16, 20 ),
			'use_attachment_page'		=> false,
			'open_new_window'				=> false,
			'show_post_thumbnail'		=> true,
			'no_attachments_msg'		=> 'Sorry, no attachments exist.',
			'wrap_class'						=> 'wpba wpba-wrap',
			'list_class'						=> 'wpba-attachment-list unstyled',
			'list_id'								=> 'wpba_attachment_list',
			'list_item_class'				=> 'wpba-list-item pull-left',
			'link_class'						=> 'wpba-link pull-left',
			'icon_class'						=> 'wpba-icon pull-left'
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
		if ( is_null( $attachments ) OR count( $attachments ) == 0 ) {
			return $no_attachments_msg;
		} // if()

		// Go through the restrictions
		$attachments = $this->check_allowed_file_type_categories( $attachments, $file_type_categories );
		$attachments = $this->check_allowed_file_extensions( $attachments, $file_extensions );
		// Build the list
		$list .= "<div id='{$list_id}' class='{$wrap_class}'>" . $nl;
		$list .= "<ul class='{$list_class}'>";
		foreach ( $attachments as $attachment ) {
			$title = $attachment->post_title;
			$link = ( $use_attachment_page ) ? get_attachment_link( $attachment->ID ) : wp_get_attachment_url( $attachment->ID );
			$target = ( $open_new_window ) ? 'target="_blank"' : 'target="_self"';
			$list .= "<li id='{$list_id}_{$attachment->ID}' class='{$list_item_class}'>";
			if ( $show_icon ) $list .= $this->icon( $attachment, shortcode_atts( $defaults, $args ) );
			$list .= "<a href='{$link}' title='{$title}' class='{$link_class}' {$target}>{$title}</a>";
			$list .= "</li>" . $nl;
		} // foreach()
		$list .= "</ul>";
		$list .= '</div>' . $nl;

		return $list;
	} // wpba_build_attachment_list()


	/**
	* Frontend Build FlexSlider
	*
	* @since 1.3.2
	*/
	public function setup_build_flexslider( $args = array() )
	{
		$defaults = array(
			'post_id'							=> NULL,
			'show_post_thumbnail'	=> false,
			'width'								=> '600px',
			'height'							=> 'auto',
			'slider_properties'		=> array( 'animation'	=> 'slide' ) // https://github.com/woothemes/FlexSlider/wiki/FlexSlider-Properties
		);

		if ( !empty( $args['slider_properties'] ) ) {
			$clean_properties = $this->clean_shortcode_atts( $args, array( 'slider_properties' => 'assoc_array' ) );
			$args['slider_properties'] = array_merge( $defaults['slider_properties'], $clean_properties['slider_properties'] );
		}

		$atts = shortcode_atts( $defaults, $args );

		$atts_to_be_cleaned = array(
			'post_id'								=> 'int',
			'show_post_thumbnail'		=> 'boolean'
		);
		$atts = $this->clean_shortcode_atts( $atts, $atts_to_be_cleaned );

		return $atts;
	} // setup_build_flexslider()


	/**
	* Frontend Build FlexSlider
	*
	* @since 1.3.2
	*/
	public function build_flexslider( $args = array() )
	{
		$setup = $this->setup_build_flexslider( $args );
		extract( $setup );

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
		$slider_properties = json_encode( $slider_properties );
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
	* @since 1.3.2
	*/
	public function icon( $attachment, $args )
	{
		$img_src = '';
		$plugin_url = WPBA_PATH;
		extract( $args );
		$icon_class = ( isset( $icon_class ) ) ? $icon_class : '';
		if ( $this->is_document( $attachment->post_mime_type ) ) {
			$img_src = $file_icon;
		} elseif ( $this->is_audio( $attachment->post_mime_type ) ) {
			$img_src = $audio_icon;
		} elseif ( $this->is_video( $attachment->post_mime_type ) ) {
			$img_src = $video_icon;
		} elseif( $this->is_image( $attachment->post_mime_type ) ) {
			$img_src = $image_icon;
		}//if/elseif

		$img = "<img src='{$img_src}' width='{$icon_size[0]}' height='{$icon_size[1]}' class='{$icon_class}'>";
		return $img;
	} // placeholder_image()

	/**
	* Cleanup Shortcode Attributes
	* @since 1.3.2
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
						$atts[$key] = explode( ',', $no_spaces );
						break;

					case 'assoc_array':
						$no_spaces = str_replace( ' ', '', $atts[$key] );
						$exploded_array = explode( ',', $no_spaces );
						$cleaned_array = array();
						foreach ( $exploded_array as $exploded ) {
							list($new_key, $value) = explode("=", $exploded);
							// Clean up values for other types
							$value = ( is_numeric( $value ) ) ? intval( $value ) : $value;
							$value = ( $value == 'true' ) ? true : $value;
							$value = ( $value == 'false' ) ? false : $value;
							$cleaned_array[$new_key] = $value;
						} // foreach()
						$atts[$key] = $cleaned_array;
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
