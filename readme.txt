=== Plugin Name ===
Contributors: dholloran
Donate link: http://danholloran.com/
Tags: attachment,file,image,post,page,custom post type,crop,image editor,attachment list
Requires at least: 3.5
Tested up to:  4.1.1
Stable tag: 1.3.10
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows you to add/edit/attach/un-attach/sort the files attached to your WordPress posts all from the post editor.

== Description ==
Allows you to add/edit/attach/un-attach/delete/sort the files attached to your WordPress posts all from the post editor.  Integrates seamlessly with WordPress using the default WordPress attachments type, full control over cropping of the different attachment image sizes crop editor and no configuration needed to add WP Better Attachments to custom post types.

= Shortcodes =
* Information on the settings available [here](http://dholloran.github.io/wp-better-attachments)
* `[wpba-attachment-list]` Outputs a list of attachments

= Functions =
* Information on the settings available [here](http://dholloran.github.io/wp-better-attachments)
* `wpba_attachments_exist()` Checks for post attachments
* `wpba_get_attachments()` Retrieves an array of attachments
* `wpba_attachment_list()` Outputs a list of attachments


View more information on GitHub [here](https://github.com/DHolloran/wp-better-attachments/)

If you have any issues please submit an [issue](https://github.com/DHolloran/wp-better-attachments/issues/new).

== Installation ==
1. Upload `wp-github-recent-commit` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to any page/post/custom post type and start editing your attachments with ease.
4. Use the add attachments button to add new attachments.
5. You can Drag and drop your attachments to arrange their menu order.
6. Click the Un-attach link to un-attach the file from your post.

== Frequently Asked Questions ==
None so far... If you have any issues please submit an [issue](https://github.com/DHolloran/wp-better-attachments/issues/new) or fix it/submit a pull request I will try to handle it ASAP. You an also contact me at [support@danholloran.com](mailto:support@danholloran.com).

== Screenshots ==
1. Post editor button
2. The meta box
3. The added un-attach link in the media library
4. The attachment edit modal
5. The attachment editor
6. The `[wpba-attachment-list]` shortcode


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

= 1.2.1 =
* Fixed issue with settings not being an array

= 1.2.2 =
* Fixed Un-attach/Delete links not updating attachment list
* Fixed issue with adding files to a post from the media uploader
* Removed support WordPress version < 3.5
* Fixed updating of title and caption edit
* Added setting to disable thumbnails in attachments list
* General code cleanup

= 1.3.0 =
* Added crop editor to media editor
* Misc. cleanup and bug fixes

= 1.3.1 =
* Fixed over generic selector for settings navigation

= 1.3.2 =
* Attachment list shortcode/function
* Improved inline title caption editor saving
* Misc. style cleanup
* Misc. cleanup

== 1.3.3 ==
* Added `wpba_attachments_exist()` to check if a post has attachments.
* Added no attachments message for attachment list.
* Fixed wpba_get_attachments() not retrieving post thumbnail images correctly.
* Added settings to disable front end files, post thumbnails globally, and crop editor.
* Fixed compatibility issue with Wysija Newsletter Plugin.
* Fixed other miscellaneous issues and general clean up.

== 1.3.4 ==
* Fixed activation error

== 1.3.5 ==
* Added alternate styling of meta box when editor is not activated.
* Fixed attachment list generating extra list items
* Added class and id parameters for each HTML element in the attachment list


== 1.3.6 ==
* Code cleanup and PHPDoc
* Fixed metabox inputs not selectable in Firefox
* Added granular settings
* Added edit modal, un-attach link and re-attach link to Uploaded To column
* Added ability to edit metabox title
* Fixed crop editor not showing correct image sizes

== 1.3.7 ==

== 1.3.8 ==

== 1.3.9 ==
* Adds survey notification key
* Adds filters for types of files
* Removes Flexslider from compiled CSS
* Miscellaneous tweaks

== 1.3.10 ==
* Fixes an issue with notifications not able to disable on sub folder installs.

== Upgrade Notice ==

= 1.0.0 =
Initial Release

= 1.0.1 =
Added support for other file types

= 1.1.0 =
Added attachment editor pop-up window to access attachment editor from posts page, Added file names for no image attachments, and more.

= 1.2.0 =
Added settings, retrieve attachments function, attachment title edit, and attachment caption edit

= 1.2.1 =
Fixed issue with settings not being an array when not settings are set

= 1.2.2 =
Fixes regressions that I know of as well as the issues/requests on the forum.

= 1.3.0 =
Added crop editor to media editor

= 1.3.1 =
Fixed over generic selector for settings navigation

= 1.3.2 =
Added a new list shortcode/function, improved inline title caption editor saving, and misc. style/cleanup

== 1.3.3 ==
Should resolve issues from last update and adds more settings control

== 1.3.4 ==
Fixed activation error

== 1.3.5 ==
Fixed attachment list generating extra list items, Added alternate styling of meta box when editor is not activated and class/id parameters for each HTML element in the attachment list function/shortcode.

== 1.3.6 ===
Added granular settings; edit modal, un-attach link and re-attach link to Uploaded To column, miscellaneous bug fixes

== 1.3.7 ===

== 1.3.8 ==

== 1.3.9 ==
Adds filters for adding file types, removes Flexslider from CSS and miscellaneous tweaks.

== 1.3.10 ==
Fixes an issue with notifications not able to disable on sub folder installs.