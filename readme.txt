=== Plugin Name ===
Contributors: dholloran
Donate link: http://danholloran.com/
Tags: attachment,file,image,post,page,custom post type
Requires at least: 3.2
Tested up to: 3.5.2
Stable tag: 1.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows you to add/edit/attach/un-attach/sort the files attached to your WordPress posts all from the post editor.

== Description ==
Allows you to add/edit/attach/un-attach/delete/sort the files attached to your WordPress posts all from the post editor.  Integrates seamlessly with WordPress using the default WordPress attachments type and no configuration needed to add WP Better Attachments to custom post types.

You can use `wpba_get_attachments( $post->ID )` to retrieve attachments from a certain post or `wpba_get_attachments()` to get the attachments for the current post.

View more information on GitHub [here](https://github.com/DHolloran/wp-better-attachments/)

Whats new in 1.2.0 Added attachment title edit, Added attachment caption edit, Added ability to disable post types through settings, Added ability to disable parts of the meta box through settings, Added convenience function to get all attachments as an array

== Installation ==
1. Upload `wp-github-recent-commit` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to any page/post/custom post type and start editing your attachments with ease.
4. Use the add attachments button to add new attachments, WordPress<3.5 does not have multi select capabilities.
5. You can Drag and drop your attachments to arrange their menu order.
6. Click the Un-attach link to un-attach the file from your post.


== Frequently Asked Questions ==
None so far... If you have any issues please submit an [issue](https://github.com/DHolloran/wp-better-attachments/issues/new) or fix it/submit a pull request I will try to handle it ASAP. You an also contact me at [support@danholloran.com](mailto:support@danholloran.com).

== Screenshots ==
1. Image of post editor button
2. Image of the attachment editor
3. Image of the added un-attach link in the media library
4. Image of the attachment edit modal


== Changelog ==

= 1.0.0 =
* Initial Release

= 1.0.1 =
* Added support for other file types

= 1.1.0 =
* Added attachment editor pop-up window to access attachment editor from posts page
* Added Re-attach link to media library
* Added delete link to each attachment in the post editor
* Added file names for no image attachments
* Miscellaneous cleanup

= 1.2.0 =
* Added attachment title edit
* Added attachment caption edit
* Added ability to disable post types through settings
* Added ability to disable parts of the meta box through settings

== Upgrade Notice ==

= 1.0.0 =
Initial Release

= 1.0.1 =
Added support for other file types

= 1.1.0 =
Added attachment editor pop-up window to access attachment editor from posts page, Added file names for no image attachments, and more.

= 1.2.0 =
Added settings, retrieve attachments function, attachment title edit, and attachment caption edit