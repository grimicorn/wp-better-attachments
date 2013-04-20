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
		} else {
			// TODO: Pre 3.5 implementation
		} // if/else()
	} // resize_crop_selection()

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