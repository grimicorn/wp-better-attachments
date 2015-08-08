<?php
/**
 * WP Better Attachments functions.
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



/**
 * Checks if the post has any attachments.
 *
 * <code>$attachments_exist = wpba_attachments_exist( get_the_id(), false );</code>
 *
 * @deprecated $args Deprecated 1.4.0 in favor of passing each parameter individually and exposing the query_args to alter the query.
 *
 * @param   object|integer  $post_parent             Optional, the post parent object or ID, defaults to current post.
 * @param   boolean         $disable_featured_image  Optional, if the featured image should NOT be included as an attachment, default false.
 * @param   array           $query_args              Optional, arguments to alter the query, accepts anything WP_Query does.
 *
 * @return  boolean                                  If the post has any attachments.
 */
function wpba_attachments_exist( $post_parent, $disable_featured_image = true, $query_args = array() ) {
	// Backwards compatibility with wpba < 1.4.0
	if ( gettype( $post_parent ) === 'array' ) {
		extract( $post_parent );
		$post_parent            = $post_id;
		$disable_featured_image = ( ! $show_post_thumbnail );
	} // if()

	$attachments = wpba_get_attachments( $post_parent, $disable_featured_image, $query_args );

	if ( empty( $attachments ) ) {
		return false;
	} // if()

	return true;
} // wpba_attachments_exist()



/**
 * Retrieves the attachments.
 *
 * <code>$attachments = wpba_get_attachments( get_the_id(), false );</code>
 *
 * @deprecated $args Deprecated 1.4.0 in favor of passing each parameter individually and exposing the query_args to alter the query.
 *
 * @param   object|integer  $post_parent             Optional, the post parent object or ID, defaults to current post.
 * @param   boolean         $disable_featured_image  Optional, if the featured image should NOT be included as an attachment, default false.
 * @param   array           $query_args              Optional, arguments to alter the query, accepts anything WP_Query does.
 *
 * @return  array                                    The post attachments.
 */
function wpba_get_attachments( $post_parent, $disable_featured_image = true, $query_args = array() ) {
	// Backwards compatibility with wpba < 1.4.0
	if ( gettype( $post_parent ) === 'array' ) {
		extract( $post_parent );
		$post_parent            = $post_id;
		$disable_featured_image = ( ! $show_post_thumbnail );
	} // if()

	global $wpba_helpers;
	return $wpba_helpers->get_attachments( $post_parent, $disable_featured_image, $query_args );
} // wpba_get_attachments()