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
		// Disabled Post Types
		$this->migrate_disable_post_types();

		// Global & Crop Editor settings
		$this->migrate_general_settings();

		// Migrate Media Table Settings
		$this->migrate_media_table_settings();

		// Migrate Meta Box Settings
		$this->migrate_meta_box_settings();

		// Migrate Enable Attachment Types
		$this->migrate_disable_attachment_types_settings();
	} // migrate_settings



	/**
	 * Handles migrating the enable attachment types settings.
	 *
	 * @since   2.0.0
	 *
	 * @return  void
	 */
	public function migrate_disable_attachment_types_settings() {
		// Set option key
		$option_key = 'wpba-disable-attachment-types';

		// Make sure options exist
		if ( ! $this->option_exists( $option_key ) ) {
			return;
		} // if()

		// Get ptions
		$options = $this->_options[$option_key];

		// Enable attachment type options
		$enable_keys = array(
			'disable_image'    => 'image',
			'disable_video'    => 'video',
			'disable_audio'    => 'audio',
			'disable_document' => 'document',
		);

		// Set options
		$this->_options['disable_attachment_types'] = $this->migrate_checkbox_keys( $enable_keys, $options );
	} // migrate_disable_attachment_types_settings()
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
	 * Migrate old keys to new keys
	 *
	 * @param   array  $keys  The keys to migrate.
	 *
	 * @return array          The migrated keys.
	 */
	public function migrate_checkbox_keys( $keys, $options ) {
		$migrated_options = array();
		foreach ( $keys as $current_key => $new_key ) {
			// Make sure the option is set
			if ( ! isset( $options[$current_key] ) or $options[$current_key] != $current_key ) continue;

			$migrated_options[$new_key] = 'on';
		} // foreach()

		return $migrated_options;
	} // migrate_checkbox_keys()



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



	/**
	 * Handles migrating the media table settings.
	 *
	 * @since   2.0.0
	 *
	 * @return  void
	 */
	public function migrate_media_table_settings() {
		// Set option key
		$option_key = 'wpba-media-table-settings';

		// Make sure options exist
		if ( ! $this->option_exists( $option_key ) ) {
			return;
		} // if()

		// Current options
		$options = $this->_options[$option_key];

		// Column settings.
		$col_keys = array(
			'col_edit_link'     => 'edit',
			'col_unattach_link' => 'unattach',
			'col_reattach_link' => 'reattach',
		);

		// Hover settings
		$hover_keys = array(
			'unattach_link' => 'unattach',
			'reattach_link' => 'reattach',
		);

		// Set options
		$this->_options['media'] = array(
			'hover'  => $this->migrate_checkbox_keys( $hover_keys, $options ),
			'column' => $this->migrate_checkbox_keys( $col_keys, $options ),
		);
	} // migrate_media_table_settings()



	/**
	 * Handles migrating the meta box settings.
	 *
	 * @since   2.0.0
	 *
	 * @return  void
	 */
	public function migrate_meta_box_settings() {
		// Set option key
		$option_key = 'wpba-meta-box-settings';

		// Make sure options exist
		if ( ! $this->option_exists( $option_key ) ) {
			return;
		} // if()

		// Current options
		$options = $this->_options[$option_key];

		// Migrate settings.
		$meta_box_keys = array(
			'gmb_title'              => 'title',
			'gmb_caption'            => 'caption',
			'gmb_show_attachment_id' => 'attachment_id',
			'gmb_unattach_link'      => 'unattach',
			'gmb_edit_link'          => 'edit',
			'gmb_delete_link'        => 'delete',
		);

		// Set option
		$this->_options['meta_box'] = $this->migrate_checkbox_keys( $meta_box_keys, $options );
	} // migrate_meta_box_settings()



	/**
	 * Migrates the global settings.
	 * Splits wpba-global-settings into 'general' and 'crop_editor'.
	 * Also adds 'wpba-crop-editor-mesage' top 'crop_editor'.
	 *
	 * @return  void
	 */
	public function migrate_general_settings() {
		// Set option key
		$option_key = 'wpba-global-settings';

		// Make sure options exist
		if ( ! $this->option_exists( $option_key ) ) {
			return;
		} // if()

		// Get options
		$options         = $this->_options[$option_key];
		$global_settings = array();
		$crop_settings   = array();

		// Handle thumbnail option
		if ( isset( $options['thumbnail'] ) and $options['thumbnail'] == 'thumbnail' ) {
			$global_settings['disable_thumbnail'] = 'on';
		} // if()

		// Handle the shortcodes option
		if ( isset( $options['no_shortcodes'] ) and $options['no_shortcodes'] == 'no_shortcodes' ) {
			$global_settings['disable_shortcodes'] = 'on';
		} // if()

		// Handle the disable crop editor option
		if ( isset( $options['no_crop_editor'] ) and $options['no_crop_editor'] == 'no_crop_editor' ) {
			$crop_settings['disable'] = 'on';
		} // if()

		// Handle the show all crop sizes editor option
		if ( isset( $options['all_crop_sizes'] ) and $options['all_crop_sizes'] == 'all_crop_sizes' ) {
			$crop_settings['all_sizes'] = 'on';
		} // if()

		// Handle crop editor message
		$crop_editor_message = ( isset( $this->_options['wpba-crop-editor-mesage'] ) ) ? $this->_options['wpba-crop-editor-mesage'] : false;
		if ( $crop_editor_message ) {
			$crop_settings['message'] = $crop_editor_message;

			// Unset the old crop editor message
			unset( $this->_options['wpba-crop-editor-mesage']  );
		} // if()

		// Set new options
		$this->_options['global']      = $global_settings;
		$this->_options['crop_editor'] = $crop_settings;

		// Remove old global options
		unset( $this->_options[$option_key] );
	} // migrate_general_settings()
} // WPBA_Migrate_Settings

new WPBA_Migrate_Settings();