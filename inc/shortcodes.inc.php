<?php
/**
* Easy Attachment Function Convenience Function
* @since 1.2.0
*/
function wpba_get_attachments( $post_id = 0 )
{
	global $wpba;
	if ( $post_id != 0 ) {
		$post = get_post( $post_id );
	} else {
		global $post;
	} // if/else()
	return $wpba->get_post_attachments( $args = array( 'post' => $post ) );
} // wpba_get_attachments()


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
	return $wpba_frontend->build_flexslider( $atts );
} // wpba_flexslider_shortcode()
add_shortcode( 'wpba-flexslider','wpba_flexslider_shortcode' );


/**
* WPBA FlexSlider Convenience Function
*
* @since 1.3.2
*/
function wpba_flexslider( $atts ) {
	global $wpba_frontend;

	return $wpba_frontend->build_flexslider( $args );
} // wpba_flexslider()