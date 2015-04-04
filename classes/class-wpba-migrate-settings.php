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
} // WPBA_Migrate_Settings

new WPBA_Migrate_Settings();