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
	 * Default Disabled Post Types.
	 *
	 * @var  array
	 */
	private $default_disabled_post_types = array(
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
	 * Retrieves the post types that can have attachments.
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



	/**
	 * Migrates the settings.
	 *
	 * @return  void
	 */
	public function migrate_settings() {
		if ( is_admin() ) return;
		// Handle global setting migration.
		$old_options = array(
			'Post Types'               => 'wpba-disable-post-types',
			'Global'                   => 'wpba-global-settings',
			'Crop Editor Message'      => 'wpba-crop-editor-mesage',
			'Media Table'              => 'wpba-media-table-settings',
			'Meta Box'                 => 'wpba-meta-box-settings',
			'Disable Attachment Types' => 'wpba-disable-attachment-types',
			'Edit Modal Settings'      => 'wpba-edit-modal-settings',
		);

		// Test Old Options
		foreach ( $old_options as $option_title => $option_key ) {
			pp( $option_title );
			pp( $this->_options[$option_key] );
			pp( '===================================' );
		} // foreach()


		// Disabled Post Types
		$this->migrate_disable_post_types();

		// Global & Crop Editor settings
		$this->migrate_general();

		// Migrate Media Table Settings
		$this->migrate_media_table();

		// Migrate Meta Box Settings
		$this->migrate_meta_box();

		// Migrate Disable Attachment Types
		$this->migrate_disable_attachment_types();

		// Edit modal settings
		$this->migrate_edit_modal();

		// Test New Options
		$new_options = array(
			'Enabled Post Types'      => 'post_types',
			'General'                 => 'general',
			'Crop Editor'             => 'crop_editor',
			'Media'                   => 'media',
			'Meta Box'                => 'meta_box',
			'Disable Attachment Types' => 'disable_attachment_types',
			'Edit Modal'              => 'edit_modal',
		);
		foreach ( $new_options as $option_title => $option_key ) {
			pp( $option_title );
			pp( $this->_options[$option_key] );
			pp( '===================================' );
		} // foreach()

		// Migrate post type specific settings
		$this->migrate_post_type_settings();

		die();
	} // migrate_settings



	/**
	 * Migrates the post type specific settings.
	 *
	 * @return  void
	 */
	public function migrate_post_type_settings() {
		$post_types = $this->get_post_types();

		foreach ( $post_types as $post_type_key => $post_type ) {
			// Set post type meta box title
			$this->migrate_post_type_meta_box_title( $post_type );

			// Set post type enabled pages
			$this->migrate_post_type_enabled_pages( $post_type );

			// Set disabled attachment types
			$this->migrate_post_type_disable_attachment_types( $post_type );
		} // foreach()
	} // migrate_post_type_settings()



	/**
	 * Migrates the post type meta box title setting.
	 *
	 * @param   string  $post_type  The post type to migrate.
	 *
	 * @return  void
	 */
	public function migrate_post_type_meta_box_title( $post_type ) {
		// Set option key
		$option_key = "wpba-{$post_type}-meta-box-title";

		// Make sure options exist
		if ( ! $this->option_exists( $option_key ) ) {
			return;
		} // if()

		// Set new option
		$this->_options[$post_type]['meta_box_title'] = $this->_options[$option_key];

		// Remove old option
		unset( $this->_options[$option_key] );
	} // migrate_post_type_meta_box_title()



	/**
	 * Migrates the post type enabled pages setting.
	 *
	 * @param   string  $post_type  The post type to migrate.
	 *
	 * @return  void
	 */
	public function migrate_post_type_enabled_pages( $post_type ) {
		// Set option key
		$option_key = "wpba-{$post_type}-enabled-pages";

		// Make sure options exist
		if ( ! $this->option_exists( $option_key ) ) {
			return;
		} // if()

		// Set new option
		$this->_options[$post_type]['enabled_pages'] = $this->_options[$option_key];

		// Remove old option
		unset( $this->_options[$option_key] );
	} // migrate_post_type_enabled_pages()



	/**
	 * Handles migrating the disable attachment types settings.
	 *
	 * @since   2.0.0
	 *
	 * @return  void
	 */
	public function migrate_post_type_disable_attachment_types( $post_type ) {
		// Set option key
		$option_key = "wpba-{$post_type}-disable-attachment-types";

		// Make sure options exist
		if ( ! $this->option_exists( $option_key ) ) {
			return;
		} // if()

		// Get ptions
		$options = $this->_options[$option_key];

		// Disable attachment type options
		$enable_keys = array(
			'pt_disable_image'    => 'image',
			'pt_disable_video'    => 'video',
			'pt_disable_audio'    => 'audio',
			'pt_disable_document' => 'document',
		);

		// Set options
		$this->_options[$post_type]['disable_attachment_types'] = $this->migrate_checkbox_keys( $enable_keys, $options );
	} // migrate_post_type_disable_attachment_types()


	/**
	 * Handles migrating the disable attachment types settings.
	 *
	 * @since   2.0.0
	 *
	 * @return  void
	 */
	public function migrate_disable_attachment_types() {
		// Set option key
		$option_key = 'wpba-disable-attachment-types';

		// Make sure options exist
		if ( ! $this->option_exists( $option_key ) ) {
			return;
		} // if()

		// Get ptions
		$options = $this->_options[$option_key];

		// Disable attachment type options
		$enable_keys = array(
			'disable_image'    => 'image',
			'disable_video'    => 'video',
			'disable_audio'    => 'audio',
			'disable_document' => 'document',
		);

		// Set options
		$this->_options['disable_attachment_types'] = $this->migrate_checkbox_keys( $enable_keys, $options );
	} // migrate_disable_attachment_types()



	/**
	 * Handles migrating the edit modal settings.
	 *
	 * @since   2.0.0
	 *
	 * @return  void
	 */
	public function migrate_edit_modal() {
		// Set option key
		$option_key = 'wpba-edit-modal-settings';

		// Make sure options exist
		if ( ! $this->option_exists( $option_key ) ) {
			return;
		} // if()

		// Current options
		$options = $this->_options[$option_key];

		// Edit modal settings.
		$edit_keys = array(
			'gem_caption'          => 'disable_caption',
			'gem_alternative_text' => 'disable_alternative_text',
			'gem_description'      => 'disable_description',
		);

		// Set options
		$this->_options['edit_modal'] = $this->migrate_checkbox_keys( $edit_keys, $options );
	} // migrate_edit_modal()



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

		// Get all post types
		$post_types = $this->get_post_types();

		// Get disabled post types
		$disabled_post_types = $this->_options[$option_key];

		// Remove disabled post types
		foreach ( $post_types as $post_type_key => $post_type_slug ) {
			if ( ! in_array( $post_type_key, $disabled_post_types ) ) continue;

			unset( $post_types[$post_type_key] );
		} // foreach()

		// Set post types option
		$this->_options['post_types'] = $post_types;

		// Remove old options
		unset( $this->_options[$option_key] );
	} // migrate_disable_post_types()



	/**
	 * Handles migrating the media table settings.
	 *
	 * @since   2.0.0
	 *
	 * @return  void
	 */
	public function migrate_media_table() {
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
	} // migrate_media_table()



	/**
	 * Handles migrating the meta box settings.
	 *
	 * @since   2.0.0
	 *
	 * @return  void
	 */
	public function migrate_meta_box() {
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
	} // migrate_meta_box()



	/**
	 * Migrates the global settings.
	 * Splits wpba-global-settings into 'general' and 'crop_editor'.
	 * Also adds 'wpba-crop-editor-mesage' top 'crop_editor'.
	 *
	 * @return  void
	 */
	public function migrate_general() {
		// Set option key
		$option_key = 'wpba-global-settings';

		// Make sure options exist
		if ( ! $this->option_exists( $option_key ) ) {
			return;
		} // if()

		// Get options
		$options = $this->_options[$option_key];

		// General options
		$general_keys = array(
			'thumbnail'     => 'disable_thumbnail',
			'no_shortcodes' => 'disable_shortcodes',
		);

		// Set general options
		$this->_options['general'] = $this->migrate_checkbox_keys( $general_keys, $options );

		// Crop Editor
		$crop_keys = array(
			'no_crop_editor' => 'disable',
			'all_crop_sizes' => 'all_sizes',
		);
		$crop_settings = $this->migrate_checkbox_keys( $crop_keys, $options );

		// Add crop editor message
		$crop_editor_message = ( isset( $this->_options['wpba-crop-editor-mesage'] ) ) ? $this->_options['wpba-crop-editor-mesage'] : false;
		if ( $crop_editor_message ) {
			$crop_settings['message'] = $crop_editor_message;

			// Unset the old crop editor message
			unset( $this->_options['wpba-crop-editor-mesage']  );
		} // if()

		// Set crop editor options
		$this->_options['crop_editor'] = $crop_settings;

		// Remove old global options
		unset( $this->_options[$option_key] );
	} // migrate_general()
} // WPBA_Migrate_Settings

new WPBA_Migrate_Settings();