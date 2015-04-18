<?php
/**
 * WPBA Settings Migration.
 * Handles migrating settings from v1.x.x to v2.x.x
 *
 * @version      2.0.0
 *
 * @package      WordPress
 * @subpackage   WP_Better_Attachments
 *
 * @author       Dan Holloran    <dtholloran@gmail.com>
 *
 * @copyright    2013 - Present  Dan Holloran
 */
if ( ! class_exists( 'WPBA_Migrate_Settings' ) ) {
	class WPBA_Migrate_Settings extends WPBA_Utilities {
		/**
		 * The options.
		 *
		 * @since  2.0.0
		 *
		 * @var    array
		 */
		private $_migration_options = array();



		/**
		 * Class constructor
		 *
		 * @since   2.0.0
		 */
		function __construct() {
			// Call parents constructor
			parent::__construct();

			// Call actions and filters
			$this->_migrate_settings_filters_actions();
		} // __construct()



		/**
		 * Handles class actions and filters
		 *
		 * @since   2.0.0
		 *
		 * @return  void
		 */
		private function _migrate_settings_filters_actions() {
			add_action( 'init', array( &$this, 'migrate_settings' ) );
		} // _migrate_settings_filters_actions()



		/**
		 * Retrieves the 1.x.x settings
		 *
		 * @since   2.0.0
		 *
		 * @return  array  The 1.x.x settings.
		 */
		public function get_options() {
			return get_option( $this->option_key, $this->_migration_options );
		} // get_options()



		/**
		 * Migrates the settings.
		 *
		 * @since   2.0.0
		 *
		 * @return  void
		 */
		public function migrate_settings() {
			// Get current options
			$this->_migration_options = $this->get_options();

			$migration_version = '2.x.x';

			// Check if the migration has bee ran
			if ( $this->_migration_options['v'] == $migration_version ) return;

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

			// Migrate post type specific settings
			$this->migrate_post_type_settings();

			// Add migration version
			$this->_migration_options['v'] = $migration_version;

			// Save the migrated options
			update_option( $this->option_key, $this->_migration_options );
		} // migrate_settings



		/**
		 * Migrates the post type specific settings.
		 *
		 * @since   2.0.0
		 *
		 * @return  void
		 */
		public function migrate_post_type_settings() {
			$post_types = $this->get_post_types();

			foreach ( $post_types as $post_type_key => $post_type ) {
				// Set meta box settings
				$this->migrate_post_type_meta_box( $post_type );

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
		 * @since   2.0.0
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
			$this->_migration_options[$post_type]['meta_box']['title'] = $this->_migration_options[$option_key];

			// Remove old option
			unset( $this->_migration_options[$option_key] );
		} // migrate_post_type_meta_box_title()



		/**
		 * Handles migrating the post type specific meta box settings.
		 *
		 * @since   2.0.0
		 *
		 * @return  void
		 */
		public function migrate_post_type_meta_box( $post_type ) {
			// Set option key
			$option_key = "wpba-{$post_type}-settings";

			// Make sure options exist
			if ( ! $this->option_exists( $option_key ) ) {
				return;
			} // if()

			// Current options
			$options = $this->_migration_options[$option_key];

			// Migrate settings.
			$meta_box_keys = array(
				'title'                 => 'title',
				'caption'               => 'caption',
				'mb_show_attachment_id' => 'attachment_id',
				'mb_unattach_link'      => 'unattach',
				'mb_edit_link'          => 'edit',
				'mb_delete_link'        => 'delete',
			);

			// Set option
			$this->_migration_options[$post_type]['meta_box'] = $this->migrate_checkbox_keys( $meta_box_keys, $options );

			// Remove Old Options
			unset( $this->_migration_options[$option_key] );
		} // migrate_post_type_meta_box()


		/**
		 * Migrates the post type enabled pages setting.
		 *
		 * @since   2.0.0
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
			$this->_migration_options[$post_type]['enabled_pages'] = $this->_migration_options[$option_key];

			// Remove old option
			unset( $this->_migration_options[$option_key] );
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
			$options = $this->_migration_options[$option_key];

			// Disable attachment type options
			$enable_keys = array(
				'pt_disable_image'    => 'image',
				'pt_disable_video'    => 'video',
				'pt_disable_audio'    => 'audio',
				'pt_disable_document' => 'document',
			);

			// Set options
			$this->_migration_options[$post_type]['disable_attachment_types'] = $this->migrate_checkbox_keys( $enable_keys, $options );

			// Remove Old Options
			unset( $this->_migration_options[$option_key] );
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
			$options = $this->_migration_options[$option_key];

			// Disable attachment type options
			$enable_keys = array(
				'disable_image'    => 'image',
				'disable_video'    => 'video',
				'disable_audio'    => 'audio',
				'disable_document' => 'document',
			);

			// Set options
			$this->_migration_options['disable_attachment_types'] = $this->migrate_checkbox_keys( $enable_keys, $options );

			// Remove Old Options
			unset( $this->_migration_options[$option_key] );
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
			$options = $this->_migration_options[$option_key];

			// Edit modal settings.
			$edit_keys = array(
				'gem_caption'          => 'disable_caption',
				'gem_alternative_text' => 'disable_alternative_text',
				'gem_description'      => 'disable_description',
			);

			// Set options
			$this->_migration_options['edit_modal'] = $this->migrate_checkbox_keys( $edit_keys, $options );

			// Remove Options
			unset( $this->_migration_options[$option_key] );
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

			return isset( $this->_migration_options[$option_key] );
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

			// Set post types option
			$this->_migration_options['post_types'] = $post_types;

			// Remove old options
			unset( $this->_migration_options[$option_key] );
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
			$options = $this->_migration_options[$option_key];

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
			$this->_migration_options['media'] = array(
				'hover'  => $this->migrate_checkbox_keys( $hover_keys, $options ),
				'column' => $this->migrate_checkbox_keys( $col_keys, $options ),
			);

			// Remove Old Options
			unset( $this->_migration_options[$option_key] );
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
			$options = $this->_migration_options[$option_key];

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
			$this->_migration_options['meta_box'] = $this->migrate_checkbox_keys( $meta_box_keys, $options );

			// Remove Old Options
			unset( $this->_migration_options[$option_key] );
		} // migrate_meta_box()



		/**
		 * Migrates the global settings.
		 * Splits wpba-global-settings into 'general' and 'crop_editor'.
		 * Also adds 'wpba-crop-editor-mesage' top 'crop_editor'.
		 *
		 * @since   2.0.0
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
			$options = $this->_migration_options[$option_key];

			// General options
			$general_keys = array(
				'thumbnail'     => 'disable_thumbnail',
				'no_shortcodes' => 'disable_shortcodes',
			);

			// Set general options
			$this->_migration_options['general'] = $this->migrate_checkbox_keys( $general_keys, $options );

			// Crop Editor
			$crop_keys = array(
				'no_crop_editor' => 'disable',
				'all_crop_sizes' => 'all_sizes',
			);
			$crop_settings = $this->migrate_checkbox_keys( $crop_keys, $options );

			// Add crop editor message
			$crop_editor_message = ( isset( $this->_migration_options['wpba-crop-editor-mesage'] ) ) ? $this->_migration_options['wpba-crop-editor-mesage'] : false;
			if ( $crop_editor_message ) {
				$crop_settings['message'] = $crop_editor_message;

				// Unset the old crop editor message
				unset( $this->_migration_options['wpba-crop-editor-mesage']  );
			} // if()

			// Set crop editor options
			$this->_migration_options['crop_editor'] = $crop_settings;

			// Remove old global options
			unset( $this->_migration_options[$option_key] );
		} // migrate_general()
	} // WPBA_Migrate_Settings

	new WPBA_Migrate_Settings();
} // if()