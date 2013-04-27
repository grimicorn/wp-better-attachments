<?php
/**
*
*/
class WPBA_Crop_Resize extends WP_Better_Attachments
{

	/**
	* Constructor
	*/
	function __construct( $config = array() )
	{
		parent::__construct();
		$this->init_hooks();
	} // __construct()


	/**
	 * Initialization Hooks
	 */
	public function init_hooks() {
		// add_filter( 'attachment_fields_to_edit', array( &$this, 'output_attachments' ), 11, 2 );
	} // init_hooks()


	/**
	* Resize/Crop Selection
	*/
	function resize_crop_selection( $args = array() )
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
	function get_attachment_sizes( $id='', $args = array() ) {
		extract( $args );
		if ( $id == '' ) {
			global $post;
			$id = get_post_thumbnail_id( $post->ID );
		} // if()

		$img_sizes = get_intermediate_image_sizes();
		$attachments = array();
		foreach ( $img_sizes as $img_size ) {
			// This will get the set sizes for each media size
			if ( isset( $_wp_additional_image_sizes[$img_size] ) ) {
				$width = intval( $_wp_additional_image_sizes[$img_size]['width'] );
				$height = intval( $_wp_additional_image_sizes[$img_size]['height'] );
			} else {
				$width = get_option( $img_size.'_size_w' );
				$height = get_option( $img_size.'_size_h' );
			} // if/else()

			// Since we are getting the full size we have to replace the width/height with the correct sizes
			$attachment_src = wp_get_attachment_image_src( $id, 'full' );
			$attachment_src[1] = $width;
			$attachment_src[2] = $height;
			$attachment_src[3] = $id;
			$attachments[] = $attachment_src;
		} // foreach()

		return $attachments;
	} // get_attachment_sizes()


	/**
	* Output Attachments
	*/
	function output_attachments( $form_fields, $post = null ) {
	$id = $post->ID;
	$attachments = $this->get_attachment_sizes( $id );
	$html = '';
	$nl = "\n";

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
		} // if()

		if( $image_width AND $image_width < $crop_src_width AND $image_height < $crop_src_height ) {
			$html .= "<img src='{$image_src}' " . $nl;
			$html .= "class='wpba-img-size-select' " . $nl;
			$html .= "style='height:{$image_height}px;width:auto;' " . $nl;
			$html .= "data-srcwidth='{$crop_src_width}' " . $nl;
			$html .= "data-srcheight='{$crop_src_height}' " . $nl;
			$html .= "data-width='{$image_width}' " . $nl;
			$html .= "data-height='{$image_height}' " . $nl;
			$html .= "data-croppoints='{$crop_points}' " . $nl;
			$html .= "data-id='{$id}'>" . $nl;
			$html .= "<br><br><br>" . $nl;
		} // if()
	} // foreach

	$form_fields['wpba_thumbnails'] = array(
			'label' => 'Thumbnail Images',
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