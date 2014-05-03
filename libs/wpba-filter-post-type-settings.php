<?php
/**
 * Filters the settings for specific post types.
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


global $wpba_helpers;
$meta_box_id = $wpba_helpers->meta_box_id;
$post_types  = $wpba_helpers->get_post_types();


/**
 * Filters the setting for meta box title for a specific post type.
 *
 * @since 1.4.0
 *
 * @var   string
 */
function wpba_settings_post_type_meta_box_title( $meta_box_title ) {
	$post_type = get_post_type();

	return $meta_box_title;
} // wpba_settings_post_type_meta_box_title()



/**
 * Filters the setting for upload button content for a specific post type..
 *
 * @since 1.4.0
 *
 * @var   string
 */
function wpba_settings_post_type_upload_button_content( $upload_button_content ) {
	$post_type = get_post_type();

	return $upload_button_content;
} // wpba_settings_post_type_upload_button_content()



/**
 * Filters the setting to display the form editor button for a specific post type..
 *
 * @todo  Figure out why passing $post_type from apply_filters() @since 1.4.0 is not working.
 *
 * @var   string
 */
function wpba_settings_post_type_display_editor_form_button( $display_editor_form_button ) {
	$post_type = get_post_type();

	return $display_editor_form_button;
} // wpba_settings_post_type_display_editor_form_button()



/**
 * Filters the enabling/disabling setting for the unattach link for a specific post type.
 *
 * @since 1.4.0
 *
 * @var   string
 */
function wpba_settings_post_type_display_unattach_link( $display_unattach_link ) {
	$post_type = get_post_type();

	return $display_unattach_link;
} // wpba_settings_post_type_display_unattach_link



/**
 * Filters the enabling/disabling setting for the delete link for a specific post type.
 *
 * @since 1.4.0
 *
 * @var   string
 */
function wpba_settings_post_type_display_delete_link( $display_delete_link ) {
	$post_type = get_post_type();

	return $display_delete_link;
} // wpba_settings_post_type_display_delete_link



/**
 * Filters the enabling/disabling setting for the edit link for a specific post type.
 *
 * @since 1.4.0
 *
 * @var   string
 */
function wpba_settings_post_type_display_edit_link( $display_edit_link ) {
	$post_type = get_post_type();

	return $display_edit_link;
} // wpba_settings_post_type_display_edit_link



/**
 * Filters the enable/disable setting for displaying of the attachment ID for a specific post type.
 *
 * @since 1.4.0
 *
 * @var   string
 */
function wpba_settings_post_type_display_attachment_id( $display_attachment_id ) {
	$post_type = get_post_type();

	return $display_attachment_id;
} // wpba_settings_post_type_display_attachment_id()



/**
 * Filters the disabling of the featured image for a specific post type.
 *
 * @since 1.4.0
 *
 * @var   string
 */
function wpba_settings_post_type_disable_featured_image( $disable_featured_image ) {
	$post_type = get_post_type();

	return $disable_featured_image;
} // wpba_settings_post_type_disable_featured_image()



foreach ( $post_types as $post_type ) {
	// Filters the setting for meta box title for a specific post type.
	add_filter( "{$meta_box_id}_{$post_type}_meta_box_title", 'wpba_settings_post_type_meta_box_title', 2 );

	// Filters the setting for upload button content for a specific post type..
	add_filter( "{$meta_box_id}_{$post_type}_upload_button_content", 'wpba_settings_post_type_upload_button_content', 2 );

	// Filters the setting to display the form editor button for a specific post type..
	add_filter( "{$meta_box_id}_{$post_type}_display_editor_form_button", 'wpba_settings_post_type_display_editor_form_button', 2 );

	// Filters the enabling/disabling setting for the unattach link for a specific post type.
	add_filter( "{$meta_box_id}_{$post_type}_display_unattach_link", 'wpba_settings_post_type_display_unattach_link', 2 );

	// Filters the enabling/disabling setting for the delete link for a specific post type.
	add_filter( "{$meta_box_id}_{$post_type}_display_delete_link", 'wpba_settings_post_type_display_delete_link', 2 );

	// Filters the enabling/disabling setting for the edit link for a specific post type.
	add_filter( "{$meta_box_id}_{$post_type}_display_edit_link", 'wpba_settings_post_type_display_edit_link', 2 );

	// Filters the enable/disable setting for displaying of the attachment ID for a specific post type.
	add_filter( "{$meta_box_id}_{$post_type}_display_attachment_id", 'wpba_settings_post_type_display_attachment_id', 2 );

	// Filters the disabling of the featured image for a specific post type.
	add_filter( "{$meta_box_id}_{$post_type}_disable_featured_image", 'wpba_settings_post_type_disable_featured_image', 2 );
} // foreach