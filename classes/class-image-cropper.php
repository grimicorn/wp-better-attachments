<?php
/**
 * This class contains the image cropper functionality.
 *
 * @version      1.4.0
 *
 * @package      WordPress
 * @subpackage   WPBA
 *
 * @since        1.4.0
 *
 * @author       Dan Holloran          <dholloran@matchboxdesigngroup.com>
 *
 * @copyright    2013 - Present         Dan Holloran
 */
class WPBA_Image_Cropper extends WPBA_Helpers {
	/**
	 * Enable/Disable the image cropper functionality.
	 *
	 * @since  1.4.0
	 *
	 * @var    boolean
	 */
	private $_disable = false;



	/**
	 * WPBA_Meta class constructor.
	 *
	 * @since  1.4.0
	 *
	 * @param  array  $config  Class configuration.
	 */
	function __construct( $config = array() ) {
		$disable = $this->_disable;
		$this->_disable = apply_filters( 'wpba_disable_image_cropper', $disable );
	} // __construct


	/**
	 * Crops an image to the given x/y width/height.
	 *
	 * @since  1.4.0
	 *
	 * @uses WP_Image_Editor
	 *
	 * @see http://codex.wordpress.org/Class_Reference/WP_Image_Editor
	 *
	 *
	 * @param  string   $src The source file or Attachment ID.
	 * @param  int      $src_x The start x position to crop from.
	 * @param  int      $src_y The start y position to crop from.
	 * @param  int      $src_w The width to crop.
	 * @param  int      $src_h The height to crop.
	 * @param  int      $dst_w Optional. The destination width.
	 * @param  int      $dst_h Optional. The destination height.
	 * @param  boolean  $src_abs Optional. If the source crop points are absolute.
	 * @return          boolean|WP_Error
	 */
	function crop( $src, $src_x, $src_y, $src_w, $src_h, $dst_w = null, $dst_h = null, $src_abs = false ) {
		$path_info = pathinfo( $src );
		$base_dir  = $pathinfo['dirname'];
		$base_name = $pathinfo['basename'];
		$image     = wp_get_image_editor( $src );

		if ( is_wp_error( $image ) ) {
			return $image;
		} // if()

		$image->crop( $src_x, $src_y, $src_w, $src_h, $dst_w, $dst_h, $src_abs );
		$saved_image = $image->save( "{$base_dir}/" );

		return true;
	} // crop
} // WPBA_Image_Cropper