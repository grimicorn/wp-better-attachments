<?php
/**
 * Easy Attachment Convenience Function
 *
 * @deprecated $post_id Deprecated 1.3.3 in favor of $args array.
 *
 * @since 1.2.0
 *
 * @param  string[]  $args {
 * 	@type integer $post                Optional Post object used to retrieve attachments
 * 	@type boolean $show_post_thumbnail Optional To include thumbnail as attachment. Default false
 * }
 *
 * @return array       Retrieved attachment post objects
 */
function wpba_get_attachments( $args = array() )
{
	global $wpba;

	if ( gettype( $args ) == 'array' ) {
		extract( $args );
	} else {
		// Deprecated $post_id parameter 1.3.3
		// Fallback since the original parameter
		// was a post id and now it should be included
		// in the $args object
		$post_id = $args;
		$args = array();
	}	// if/else()


	if ( isset( $post_id ) ) {
		$post = get_post( $post_id );
	} else {
		global $post;
	} // if/else()

	$args['post'] = $post;
	return $wpba->get_post_attachments( $args );
} // wpba_get_attachments()



/**
* Check if post has attachment
*
* @uses wpba_get_attachments()
*
* @since 1.3.3
*/
function wpba_attachments_exist( $args = array() )
{
	if ( count( wpba_get_attachments( $args ) ) > 0 ) {
		return true;
	} //if()

	return false;
} // wpba_attachments_exist()



/**
* WPBA Attachment List Shortcode
*
* @since 1.3.2
*/
function wpba_attachment_list_shortcode( $atts )
{
	// Make sure atts is an array
	$atts = ( gettype( $atts ) != 'array' ) ? array() : $atts;
	global $wpba_frontend;
	return $wpba_frontend->build_attachment_list( $atts );
} // wpba_attachment_list_shortcode()
add_shortcode( 'wpba-attachment-list','wpba_attachment_list_shortcode' );


/**
* WPBA Attachment List Convenience Function
*
* @since 1.3.2
*/
function wpba_attachment_list( $args = array() ) {
	global $wpba_frontend;

	return $wpba_frontend->build_attachment_list( $args );
} // wpba_attachment_list()


/**
* WPBA FlexSlider Shortcode
*
* @since 1.3.2
*/
function wpba_flexslider_shortcode( $atts ) {
	// Make sure atts is an array
	$atts = ( gettype( $atts ) != 'array' ) ? array() : $atts;

	global $wpba_frontend;

	$wpba_frontend->register_flexslider();
	wp_enqueue_script( 'wpba_front_end_styles' );

	return $wpba_frontend->build_flexslider( $atts );
} // wpba_flexslider_shortcode()
add_shortcode( 'wpba-flexslider','wpba_flexslider_shortcode' );