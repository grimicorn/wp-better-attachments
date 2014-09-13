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
$options     = $wpba_settings->options;


/**
 * Filters the setting for meta box title for all post types.
 *
 * @since   1.4.0
 *
 * @return  string  The setting for meta box title for all post types.
 */
function wpba_settings_meta_box_title( $meta_box_title ) {
	global $wpba_settings;
	$setting        = $wpba_settings->get_setting( 'global_meta_box_title_setting' );
	$meta_box_title = ( $setting == '' ) ? $meta_box_title : $setting;

	return $meta_box_title;
} // wpba_settings_meta_box_title()
add_filter( "{$meta_box_id}_meta_box_title", 'wpba_settings_meta_box_title', 1 );



/**
 * Filters the setting for upload button content for all post types.
 *
 * @since   1.4.0
 *
 * @return  string  The setting for upload button content for all post types.
 */
function wpba_settings_upload_button_content( $upload_button_content ) {
	global $wpba_settings;
	$setting               = $wpba_settings->get_setting( 'global_upload_button_content' );
	$upload_button_content = ( $setting == '' ) ? $upload_button_content : $setting;

	return $upload_button_content;
} // wpba_settings_upload_button_content()
add_filter( "{$meta_box_id}_upload_button_content", 'wpba_settings_upload_button_content', 1 );



/**
 * Filters the setting to display the form editor button for all post types.
 *
 * @todo    Add setting.
 *
 * @since   1.4.0
 *
 * @return  string  The setting to display the form editor button for all post types.
 */
function wpba_settings_display_editor_form_button( $display_editor_form_button ) {
	return $display_editor_form_button;
} // wpba_settings_display_editor_form_button()
add_filter( "{$meta_box_id}_display_editor_form_button", 'wpba_settings_display_editor_form_button', 1 );



/**
 * Filters the enabling/disabling setting for the unattach link for all post types.
 *
 * @since   1.4.0
 *
 * @return  boolean  The enabling/disabling setting for the unattach link for all post types.
 */
function wpba_settings_display_unattach_link( $display_unattach_link ) {
	global $wpba_settings;
	$setting               = $wpba_settings->get_setting( 'meta_box_setting_disable_un_attach_link' );
	$display_unattach_link = ( $setting == '' ) ? $display_unattach_link : false;

	return $display_unattach_link;
} // wpba_settings_display_unattach_link
add_filter( "{$meta_box_id}_display_unattach_link", 'wpba_settings_display_unattach_link', 1 );



/**
 * Filters the enabling/disabling setting for the delete link for all post types.
 *
 * @since   1.4.0
 *
 * @return  boolean  The enabling/disabling setting for the delete link for all post types.
 */
function wpba_settings_display_delete_link( $display_delete_link ) {
	global $wpba_settings;
	$setting               = $wpba_settings->get_setting( 'meta_box_setting_disable_delete_link' );
	$display_delete_link = ( $setting == '' ) ? $display_delete_link : false;

	return $display_delete_link;
} // wpba_settings_display_delete_link
add_filter( "{$meta_box_id}_display_delete_link", 'wpba_settings_display_delete_link', 1 );



/**
 * Filters the enabling/disabling setting for the edit link for all post types.
 *
 * @since   1.4.0
 *
 * @return  boolean  The enabling/disabling setting for the edit link for all post types.
 */
function wpba_settings_display_edit_link( $display_edit_link ) {
	global $wpba_settings;
	$setting               = $wpba_settings->get_setting( 'meta_box_setting_disable_edit_link' );
	$display_edit_link = ( $setting == '' ) ? $display_edit_link : false;

	return $display_edit_link;
} // wpba_settings_display_edit_link
add_filter( "{$meta_box_id}_display_edit_link", 'wpba_settings_display_edit_link', 1 );



/**
 * Filters the enable/disable setting for displaying of the attachment ID for all post types.
 *
 * @since   1.4.0
 *
 * @return  boolean  The enable/disable setting for displaying of the attachment ID for all post types.
 */
function wpba_settings_display_attachment_id( $display_attachment_id ) {
	global $wpba_settings;
	$setting               = $wpba_settings->get_setting( 'meta_box_setting_disable_attachment_id' );
	$display_attachment_id = ( $setting == '' ) ? $display_attachment_id : false;

	return $display_attachment_id;
} // wpba_settings_display_attachment_id()
add_filter( "{$meta_box_id}_display_attachment_id", 'wpba_settings_display_attachment_id', 1 );



/**
 * Filters the disabling of the featured image for all post types.
 *
 * @since   1.4.0
 *
 * @todo   Add setting.
 *
 * @return  string  The disabling of the featured image for all post types.
 */
function wpba_settings_disable_featured_image( $disable_featured_image ) {
	global $wpba_settings;
	$setting                = $wpba_settings->get_setting( 'global_settings_do_not_include_thumbnail' );
	$disable_featured_image = ( $setting == '' ) ? $disable_featured_image : true;

	return $disable_featured_image;
} // wpba_settings_disable_featured_image()
add_filter( "{$meta_box_id}_disable_featured_image", 'wpba_settings_disable_featured_image', 1 );



/**
 * Filters the settings for the allowed post types.
 *
 * @since  1.4.0
 *
 * @var    array  The settings for the allowed post types.
 */
function wpba_settings_wpba_post_types( $post_types ) {
	global $wpba_settings;

	foreach ( $post_types as $key => $value ) {
		$setting = $wpba_settings->get_setting( "disable_post_type_{$key}" );
		if ( $setting == 'on' ) {
			unset( $post_types[$key] );
		} // if()
	} // foreach()

	return $post_types;
} // wpba_settings_wpba_post_types
add_filter( "{$meta_box_id}_post_types", 'wpba_settings_wpba_post_types', 1 );