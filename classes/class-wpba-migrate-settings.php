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
	} // __construct()
} // WPBA_Migrate_Settings

new WPBA_Migrate_Settings();