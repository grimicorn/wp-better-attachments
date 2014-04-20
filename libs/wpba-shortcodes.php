<?php
/**
 * WP Better Attachments shortcodes.
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


// post_id="current_post_id"
// show_icon="false"
// file_type_categories="image,file,audio,video"
// file_extensions="png,pdf" Array of file extensions, defaults to WordPress allowed attachment types (get_allowed_mime_types())
// image_icon="path/to/directory/image-icon.png"
// file_icon="path/to/directory/file-icon.png"
// audio_icon="path/to/directory/audio-icon.png"
// video_icon="path/to/directory/video-icon.png"
// icon_size="16,20" width, height
// use_attachment_page="true"
// open_new_window="true"
// show_post_thumbnail="true"
// no_attachments_msg="Sorry, no attachments exist."
// wrap_class="wpba wpba-wrap"
// list_class="unstyled"
// list_id="wpba_attachment_list"
// list_item_class="wpba-list-item pull-left"
// link_class="wpba-link pull-left"
// icon_class="wpba-icon pull-left"
function wpba_attachment_list_shortcode( $atts ) {
} // function
add_shortcode( 'wpba-attachment-list','wpba_attachment_list_shortcode' );