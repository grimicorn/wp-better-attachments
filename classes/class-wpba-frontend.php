<?php
/**
* WPBA Front End
*
* @package WP_Better_Attachments
*
* @since 1.3.2
*
* @author Dan Holloran dan@danholloran.com
*/
class WPBA_Frontend extends WP_Better_Attachments
{
	/**
	* Constructor
	*
	* @param array $config Class configuration
	*
	* @since 1.3.2
	*/
	public function __construct( $config = array() )
	{
		parent::__construct();
	} // constructor()



	/**
	* Frontend Build Attachments List
	*
	* @since 1.3.2
	*
	* @param string[] {
	* 	@type integer 'post_id'              Default: NULL
	* 	@type boolean 'show_icon'            Default: true
	* 	@type string 'file_type_categories' Default: array( 'image', 'file', 'audio', 'video' )
	* 	@type string 'file_extensions'      Default: $this->get_allowed_extensions()
	* 	@type string 'image_icon'           Default: "{$plugin_url}/assets/img/icons/image-icon.png"
	* 	@type string 'file_icon'            Default: "{$plugin_url}/assets/img/icons/file-icon.png"
	* 	@type string 'audio_icon'           Default: "{$plugin_url}/assets/img/icons/audio-icon.png"
	* 	@type string 'video_icon'           Default: "{$plugin_url}/assets/img/icons/video-icon.png"
	* 	@type string 'icon_size'            Default: array( 16, 20 )
	* 	@type boolean 'use_attachment_page'  Default: false
	* 	@type boolean 'open_new_window'      Default: false
	* 	@type boolean 'show_post_thumbnail'  Default: true
	* 	@type string 'no_attachments_msg'   Default: 'Sorry, no attachments exist.'
	* 	@type string 'unstyled_list'        DEPRECATED Default: null
	* 	@type string 'float_class'          DEPRECATED Default: null
	* 	@type string 'wrap_class'           Default: 'wpba wpba-wrap'
	* 	@type string 'list_class'           Default: 'wpba-attachment-list unstyled'
	* 	@type string 'list_id'              Default: 'wpba_attachment_list'
	* 	@type string 'list_item_class'      Default: 'wpba-list-item pull-left'
	* 	@type string 'link_class'           Default: 'wpba-link pull-left'
	* 	@type string 'icon_class'           Default: 'wpba-icon pull-left'
	* }
	*
	* @return string WPBA Attachment List HTML
	*/
	public function build_attachment_list( $args = array() )
	{
		$list = "";
		$nl = "\n";
		$plugin_url = plugins_url('wp-better-attachments');
		$atts_to_be_cleaned = array(
			'post_id'               => 'int',
			'show_icon'             => 'boolean',
			'file_type_categories'  => 'array',
			'file_extensions'       => 'array',
			'icon_size'             => 'array',
			'use_attachment_page'   => 'boolean',
			'open_new_window'       => 'boolean',
			'show_post_thumbnail'   => 'boolean'
		);
		$defaults = array(
			'post_id'               => NULL,
			'show_icon'             => true,
			'file_type_categories'  => array(
				'image',
				'file',
				'audio',
				'video'
			),
			'file_extensions'       => $this->get_allowed_extensions(),
			'image_icon'            => "{$plugin_url}/assets/img/icons/image-icon.png",
			'file_icon'             => "{$plugin_url}/assets/img/icons/file-icon.png",
			'audio_icon'            => "{$plugin_url}/assets/img/icons/audio-icon.png",
			'video_icon'            => "{$plugin_url}/assets/img/icons/video-icon.png",
			'icon_size'             => array( 16, 20 ),
			'use_attachment_page'   => false,
			'open_new_window'       => false,
			'show_post_thumbnail'   => true,
			'no_attachments_msg'    => 'Sorry, no attachments exist.',
			'unstyled_list'         => null,
			'float_class'           => null,
			'wrap_class'            => 'wpba wpba-wrap',
			'list_class'            => 'wpba-attachment-list unstyled',
			'list_id'               => 'wpba_attachment_list',
			'list_item_class'       => 'wpba-list-item pull-left',
			'link_class'            => 'wpba-link pull-left',
			'icon_class'            => 'wpba-icon pull-left'
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

		// Classes from original pull request to add ability to change classes
		$link_class = ( isset( $float_class ) ) ? $float_class : $link_class;
		$list_class = ( isset( $unstyled_list ) ) ? $unstyled_list : $list_class;

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
	* Setup FlexSlider properties
	*
	* @since 1.3.2
	*
	* @param  string[]  $args {
	* 	@type integer  post_id             Default NULL    Post ID of the post to retrieve attachments from
	* 	@type boolean  show_post_thumbnail Default false   Overrides the show post thumbnail settings
	* 	@type string   width               Default '600px' CSS Width of the slider
	* 	@type string   height              Default 'auto'  CSS Height of the slider
	* 	@type string   slider_properties   Default array( 'animation'	=> 'slide' )
	* }
	*
	* @return array FlexSlider Properties https://github.com/woothemes/FlexSlider/wiki/FlexSlider-Properties
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
	*
	* @param  string[]  $args {
	* 	@type integer  post_id             Default NULL    Post ID of the post to retrieve attachments from
	* 	@type boolean  show_post_thumbnail Default false   Overrides the show post thumbnail settings
	* 	@type string   width               Default '600px' CSS Width of the slider
	* 	@type string   height              Default 'auto'  CSS Height of the slider
	* 	@type string   slider_properties   Default array( 'animation'	=> 'slide' )
	* }
	*
	* @return string FlexSlider HTML
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
	* Registers FlexSlider Assets.
	*
	* @since 1.3.6
	*
	* @return Void
	*/
	public function register_flexslider() {
		// Enqueue FlexSlider
		wp_register_script( 'wpba_jquery_flexslider_min_js', plugins_url( 'assets/bower_components/flexslider/jquery.flexslider-min.js', dirname( __FILE__ ) ), array( 'jquery' ), WPBA_VERSION, true );
		wp_register_style( 'wpba_jquery_flexslider_css', plugins_url( 'assets/bower_components/flexslider/flexslider.css', dirname( __FILE__ ) ), array( 'wpba_front_end_styles' ), WPBA_VERSION );
	} // register_flexslider()



	/**
	* Attachment placeholder image name
	*
	* @since 1.3.2
	*
	* @param  object $attachment A single attachment post type object
	* @param  string[] $args {
	* 	@type string icon_class Class to be added to the placeholder HTML image tag. Default '' empty string.
	* }
	*
	* @return string Icon image HTML
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
	* Set all values in an attribute array to the correct data type
	*
	* @since 1.3.2
	*
	* @param  array $atts     Attributes to be cleaned '[attribute_name]' => '[attribute_value]'
	* @param  array $att_keys Attribute data type '[attribute_name]' => '[attribute_data_type]'
	*
	* @return array All values set to there correct data type
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
	} // clean_shortcode_atts()
} // END class WPBA_Frontend()


/**
* Instantiate class and create return method for easier use later
*/
global $wpba_frontend;
$wpba_frontend = new WPBA_Frontend();
