<?php
/**
*
*/
class WPBA_Crop_Resize extends WP_Better_Attachments
{

	/**
	* Constructor
	*/
	public function __construct( $config = array() )
	{
		parent::__construct();
		if ( !isset( $this->global_settings['no_crop_editor'] ) ) {
			add_action( 'admin_head', array( &$this, 'init_hooks' ) );
		}
	} // __construct()


	/**
	 * Initialization Hooks
	 */
	public function init_hooks() {
		global $post;
		if ( !is_null( $post ) AND wp_attachment_is_image( $post->ID ) ) {
			add_filter( 'attachment_fields_to_edit', array( &$this, 'output_attachments' ), 11, 2 );
		} // if()
	} // init_hooks()


	/**
	* Resize/Crop Selection
	*/
	public function resize_crop_selection( $args = array() )
	{
		extract( $_POST );
		extract( $args );
		global $wp_version;
		$thumb_offset = $orig_h/$final_h;
		$thumb_h = $orig_h/$thumb_offset;
		$thumb_w = $orig_w/$thumb_offset;
		$src_w = $final_w;
		$mime_type = get_post_mime_type( $id );

		$editor_supports_args = array(
			'mime_type' => $mime_type,
			'methods' => array(
				'crop',
				'resize',
				'save'
			)
		);
		$img_editor_test = wp_image_editor_supports( $editor_supports_args );

		if ( floatval( $wp_version ) >= 3.5 AND $img_editor_test !== false  ) {
			$upload_dir = wp_upload_dir();
			extract( $upload_dir );
			$pathinfo = pathinfo( $src );
			extract( $pathinfo );
			// Make an URI out of an URL
			$img_src_uri = trailingslashit( $basedir ) . str_replace($baseurl, '', $src);

			// Make an Directory URI out of an URL
			$img_dir_uri = str_replace($basename, '', $img_src_uri);

			// Image Edit
			$img = wp_get_image_editor( $img_src_uri );
			if ( ! is_wp_error( $img ) ) {
				$resize = $img->resize( $thumb_w, $thumb_h, false );
				$crop = $img->crop( $src_x, $src_y, $src_w, $src_h, NULL, NULL, false );
				$filename = $img->generate_filename( $img->get_suffix(), $img_dir_uri, $extension );
				$saved = $img->save();

				if ( !is_wp_error( $saved ) ) {
					return true;
				} else {
					return false;
				} // if/else()
			} // if()

			return false;
		}
	} // resize_crop_selection()


	/**
	* Get Attachment Sizes
	*/
	public function get_attachment_sizes( $id='', $args = array() ) {
		extract( $args );
		if ( $id == '' ) {
			global $post;
			$id = get_post_thumbnail_id( $post->ID );
		} // if()

		$img_sizes = get_intermediate_image_sizes();
		$attachments = array();
		foreach ( $img_sizes as $img_size ) {
			// Todo: there has to be a bettwr way!!!
			$att_src = wp_get_attachment_image_src( $id, $img_size );

			// Since we are getting the full size we have to replace the width/height with the correct sizes
			$attachment_src = wp_get_attachment_image_src( $id, 'full' );
			$attachment_src[1] = $att_src[1]; // width
			$attachment_src[2] = $att_src[2]; // height
			$attachment_src[3] = $id;
			$attachments[$att_src[1].$att_src[2]] = $attachment_src;
		} // foreach()

		ksort( $attachments, SORT_NUMERIC );
		return $attachments;
	} // get_attachment_sizes()


	/**
	* Check Equal Aspect Ratio
	*/
	public function is_equal_aspect_ratio( $orig_w, $orig_h, $crop_w, $crop_h )
	{
		if ( $orig_w/$orig_h <= $crop_w/$crop_h ) {
			return false;
		} // if()

		return true;
	} // is_equal_aspect_ratio()

	/**
	* Output Attachments
	*/
	public function output_attachments( $form_fields, $post = null ) {
		$id = $post->ID;
		$attachments = $this->get_attachment_sizes( $id );
		$html = '';
		$nl = "\n";
		$html .= '<div class="wpba-attachment-editor">' . $nl;
		$html .= "<h2>WPBA Image Crop Editor</h2>" . $nl;
		// $html .= '<a href="#" class="button">Show Thumbnails</a>' . $nl;
		$html .= '<ul class="wpba-attachment-editor-list hide unstyled pull-left">' . $nl;
		$html .= '<li class="description">Below are all the available attachment sizes that will be cropped from the original image the other sizes will be scaled to fit.  Drag the dashed box to select the portion of the image that you would like to be used for the cropped image.</li>' . $nl;

		foreach ( $attachments as $attachment ) {
			$image_src = $attachment[0];
			$image_width = $attachment[1];
			$image_height = $attachment[2];
			$id = $attachment[3];
			$crop_points = 0;
			$attachment_meta = get_post_meta( $id, 'wpba_crop_points', true );
			$crop_src = wp_get_attachment_image_src( $id, 'full');
			$crop_src_width = $crop_src[1];
			$crop_src_height = $crop_src[2];

			// Get the crop points
			if ( $attachment_meta AND isset( $attachment_meta["{$image_width}x{$image_height}"] ) ) {
				$crop_points = implode( ',', $attachment_meta["{$image_width}x{$image_height}"] );
			} else {
				$aspect_ratio = $crop_src_width/$crop_src_height;
				$start_width = (($aspect_ratio * $image_width) - $image_width)/2;
				$end_width = $start_width + $image_width;
				$crop_points = "{$start_width},{$end_width},0,{$image_height}";
			} // if/else()

			$equal_aspect_ratio = !$this->is_equal_aspect_ratio( $crop_src_width, $crop_src_height, $image_width, $image_height);
			$image_larger_size = ( $image_width < $crop_src_width AND $image_height < $crop_src_height );
			if( $image_width  AND $image_larger_size AND !$equal_aspect_ratio ) {
				$image_style = "width:auto;height:{$image_height}px";
				$html .= '<li>' . $nl;
				$html .= "<h3 class='pull-left'>{$image_width}px x {$image_height}px</h3>" . $nl;
				// $html .= '<a href="#" class="reset-thumbnails pull-left">Reset Original Thumbnail</a>' . $nl;
				$html .= "<div class='clear'>" . $nl;
				$html .= "<img src='{$image_src}' " . $nl;
				$html .= "class='wpba-img-size-select' " . $nl;
				$html .= "style='{$image_style}' " . $nl;
				$html .= "data-srcwidth='{$crop_src_width}' " . $nl;
				$html .= "data-srcheight='{$crop_src_height}' " . $nl;
				$html .= "data-width='{$image_width}' " . $nl;
				$html .= "data-height='{$image_height}' " . $nl;
				$html .= "data-croppoints='{$crop_points}' " . $nl;
				$html .= "data-id='{$id}' />" . $nl;
				$html .= '<div class="clear">' . $nl;
				$html .= '</li>' . $nl;
			} // if()
		} // foreach
		$html .= '</div>' . $nl;
		$html .= '</ul>' . $nl;

		$form_fields['wpba_thumbnails'] = array(
				'label' => '',
				'input' => 'html',
				'html'  => $html,
			);

		return $form_fields;
	} // output_attachments()


} // class()


/**
 * Instantiate class and create return method for easier use later
 */
global $wpba_crop_resize;
$wpba_crop_resize = new WPBA_Crop_Resize();

function call_WPBA_Crop_Resize() {
	return new WPBA_Crop_Resize();
} // call_WPBA_Crop_Resize()
if ( is_admin() )
	add_action( 'load-post.php', 'call_WPBA_Crop_Resize' );