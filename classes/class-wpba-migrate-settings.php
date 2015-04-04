<?php
/**
* WPBA Settings Migration.
* Handles migrating settings from v1.x.x to v2.x.x
*
* @package WP_Better_Attachments
*
* * @author Dan Holloran dtholloran@gmail.com
*
* @since   2.0.0
*/
class WPBA_Migrate_Settings {
	/**
	 * The settings option key.
	 *
	 * @var  slug
	 */
	private $_option_key = 'wpba_settings';



	/**
	 * The options.
	 *
	 * @var  array
	 */
	private $_options = array();



	/**
	 * Class constructor
	 *
	 * @since   2.0.0
	 */
	function __construct() {
		$this->_options = $this->get_options();

		$this->migrate_settings();
	} // __construct()



	/**
	 * Retrieves the 1.x.x settings
	 *
	 * @since   2.0.0
	 *
	 * @return  array  The 1.x.x settings.
	 */
	public function get_options() {
		return get_option( $this->_option_key, $this->_options );
	} // get_options()



	/**
	 * Migrates the settings.
	 *
	 * @return  void
	 */
	public function migrate_settings() {
	} // migrate_settings



	/**
	 * Checks if the option exists.
	 *
	 * @since   2.0.0
	 *
	 * @param   string   $option_key  The key of the option tp check
	 *
	 * @return  boolean               True if the option exists and false if not.
	 */
	public function option_exists( $option_key ) {

		return isset( $this->_options[$option_key] );
	} // option_exists()



	/**
	 * Handles migrating the disabled post type settings.
	 * Now the settings will be "enabled" post types instead.
	 *
	 * @since   2.0.0
	 *
	 * @return  void
	 */
	public function migrate_disable_post_types() {
		// Set option key
		$option_key = 'wpba-disable-post-types';

		// Make sure options exist
		if ( ! $this->option_exists( $option_key ) ) {
			return;
		} // if()

		$post_types          = get_post_types();
		$disabled_post_types = array(
			'attachment'    => 'attachment',
			'revision'      => 'revision',
			'nav_menu_item' => 'nav_menu_item',
		);

		// Get disabled post types
		$disabled_post_types = array_merge( $disabled_post_types, $this->_options[$option_key] );
		unset( $this->_options[$option_key] );

		// Remove disabled post types
		foreach ( $post_types as $post_type_key => $post_type_slug ) {
			if ( ! in_array( $post_type_key, $disabled_post_types ) ) continue;

			unset( $post_types[$post_type_key] );
		} // foreach()

		// Set post types option
		$this->_options['post_types'] = $post_types;
	} // migrate_disable_post_types()
} // WPBA_Migrate_Settings

new WPBA_Migrate_Settings();