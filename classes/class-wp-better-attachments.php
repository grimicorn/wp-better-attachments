<?php
/**
* WP Better Attachments Base class.
*
* @package WP_Better_Attachments
*
* @author Dan Holloran dtholloran@gmail.com
*
* @since   2.0.0
*/
class WP_Better_Attachments {
	/**
	 * The settings option key.
	 *
	 * @since  2.0.0
	 *
	 * @var    slug
	 */
	public $option_key = 'wpba_settings';



	/**
	 * Default Disabled Post Types.
	 *
	 * @since  2.0.0
	 *
	 * @var    array
	 */
	public $default_disabled_post_types = array(
		'attachment'    => 'attachment',
		'revision'      => 'revision',
		'nav_menu_item' => 'nav_menu_item',
	);



	/**
	 * Class constructor
	 *
	 * @since   2.0.0
	 */
	function __construct() {
	} // __construct()



	/**
	 * Retrieves the post types that can have attachments.
	 *
	 * @since   2.0.0
	 *
	 * @return  array  The post types that can have attachments.
	 */
	public function get_post_types() {
		$post_types          = get_post_types();
		$disabled_post_types = $this->default_disabled_post_types;

		// Remove disabled post types
		foreach ( $post_types as $post_type_key => $post_type_slug ) {
			if ( ! in_array( $post_type_key, $disabled_post_types ) ) continue;

			unset( $post_types[$post_type_key] );
		} // foreach()

		return $post_types;
	} // get_post_types()
} // WP_Better_Attachments

new WP_Better_Attachments();