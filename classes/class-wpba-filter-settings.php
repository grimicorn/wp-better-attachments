<?php
/**
 * WPBA Settings Filter.
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
if ( ! class_exists( 'WPBA_Filter_Settings' ) ) {
	class WPBA_Filter_Settings extends WPBA_Utilities {
		/**
		 * The filtered options.
		 *
		 * @since   2.0.0
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
			// Call parents constructor
			parent::__construct();

			// Call actions and filters
			$this->_filter_settings_filters_actions();
		} // __construct()



		/**
		 * Handles class actions and filters
		 *
		 * @since   2.0.0
		 *
		 * @return  void
		*/
		private function _filter_settings_filters_actions() {
		} // _filter_settings_filters_actions()



		/**
		 * Sets the options.
		 *
		 * @since   2.0.0
		 *
		 * @return  void
		 */
		public function get_options() {
			if ( ! empty( $this->_options ) ) return $this->_options;

			// Set the options if not already set
			$this->_set_options();

			return $this->_options;
		} // get_options()



		/**
		 * Sets the options.
		 *
		 * @since   2.0.0
		 *
		 * @return  void
		 */
		public function _set_options() {
			if ( ! empty( $this->_options ) ) return;

			$this->_options = $this->_get_filtered_options();
		} // _set_options()



		/**
		 * Retrieves the default options.
		 *
		 * @since   2.0.0
		 *
		 * @return  array  The default options
		 */
		private function _get_default_options() {
			$options    = array();
			$post_types = $this->get_post_types();

			// Sets enabled post type defaults
			$options['post_types'] = $post_types;

			// Sets general defaults
			$options['general']['disable_thumbnail']  = '';
			$options['general']['disable_shortcodes'] = '';

			// Sets crop editor defaults
			$options['crop_editor']['disable']   = '';
			$options['crop_editor']['all_sizes'] = '';
			$options['crop_editor']['message']   = 'Below are all the available attachment sizes that will be cropped from the original image the other sizes will be scaled to fit.  Drag the dashed box to select the portion of the image that you would like to be used for the cropped image.';

			// Sets media hover defaults
			$options['media']['hover']['unattach'] = '';
			$options['media']['hover']['reattach'] = '';

			// Sets media column defaults
			$options['media']['column']['edit'] = '';
			$options['media']['column']['unattach'] = '';
			$options['media']['column']['reattach'] = '';

			// Sets meta box defaults
			$options['meta_box']['title']         = '';
			$options['meta_box']['caption']       = '';
			$options['meta_box']['attachment_id'] = '';
			$options['meta_box']['unattach']      = '';
			$options['meta_box']['edit']          = '';
			$options['meta_box']['delete']        = '';

			// Sets disable attachment type defaults
			$options['disable_attachment_types']['image']    = '';
			$options['disable_attachment_types']['video']    = '';
			$options['disable_attachment_types']['audio']    = '';
			$options['disable_attachment_types']['document'] = '';

			// Sets edit modal defaults
			$options['edit_modal']['disable_caption'] = '';
			$options['edit_modal']['disable_alternative_text'] = '';
			$options['edit_modal']['disable_description'] = '';

			// Set post type defaults
			foreach ( $post_types as $post_type ) {
				// Sets post type meta box defaults
				$options[$post_type]['meta_box']['title'] = '';
				$options[$post_type]['meta_box']['caption'] = '';
				$options[$post_type]['meta_box']['attachment_id'] = '';
				$options[$post_type]['meta_box']['unattach'] = '';
				$options[$post_type]['meta_box']['edit'] = '';
				$options[$post_type]['meta_box']['delete'] = '';

				// Sets post type enabled page defaults
				$options[$post_type]['enabled_pages'] = '';

				// Sets post type disable attachment type defaults
				$options[$post_type]['disable_attachment_types']['image'] = '';
				$options[$post_type]['disable_attachment_types']['video'] = '';
				$options[$post_type]['disable_attachment_types']['audio'] = '';
				$options[$post_type]['disable_attachment_types']['document'] = '';
			} // foreach()

			return $options;
		} // _get_default_options()



		/**
		 * Merges the current options with the default options.
		 *
		 * @since   2.0.0
		 *
		 * @return  array  The merged options.
		 */
		private function _get_merged_options() {
			$defaults = $this->_get_default_options();
			$options  = get_option( $this->option_key, $this->_options );

			return array_merge( $defaults, $options );
		} // _get_merged_options()



		/**
		 * Filters all of the current options.
		 *
		 * @since   2.0.0
		 *
		 * @return  array  The filtered options.
		 */
		public function _get_filtered_options() {
			$options    = $this->_get_merged_options();
			$post_types = $this->get_post_types();

			// Filters enabled post type defaults
			$options['post_types'] = apply_filters( 'wpba_post_types', $options['post_types'] );

			// Filters general defaults
			$general                       = $options['general'];
			$general['disable_thumbnail']  = apply_filters( 'wpba_disable_thumbnail', $general['disable_thumbnail'] );
			$general['disable_shortcodes'] = apply_filters( 'wpba_disable_shortcodes', $general['disable_shortcodes'] );
			$options['general']            = $general;

			// Filters crop editor defaults
			$ce                     = $options['crop_editor'];
			$ce['disable']          = apply_filters( 'wpba_crop_editor_disable', $ce['disable'] );
			$ce['all_sizes']        = apply_filters( 'wpba_crop_editor_all_sizes', $ce['all_sizes'] );
			$ce['message']          = apply_filters( 'wpba_crop_editor_message', $ce['message'] );
			$options['crop_editor'] = $ce;

			// Filters media hover defaults
			$mh                        = $options['media']['hover'];
			$mh['unattach']            = apply_filters( 'wpba_media_hover_unattach', $mh['unattach'] );
			$mh['reattach']            = apply_filters( 'wpba_media_hover_reattach', $mh['reattach'] );
			$options['media']['hover'] = $mh;

			// Filters media column defaults
			$mc                        = $options['media']['column'];
			$mc['edit']                = apply_filters( 'wpba_media_column_edit', $mc['edit'] );
			$mc['unattach']            = apply_filters( 'wpba_media_column_unattach', $mc['unattach'] );
			$mc['reattach']            = apply_filters( 'wpba_media_column_reattach', $mc['reattach'] );
			$options['media']['hover'] = $mc;

			// Filters meta box defaults
			$mb                  = $options['meta_box'];
			$mb['title']         = apply_filters( 'wpba_meta_box_title', $mb['title'] );
			$mb['caption']       = apply_filters( 'wpba_meta_box_caption', $mb['caption'] );
			$mb['attachment_id'] = apply_filters( 'wpba_meta_box_attachment_id', $mb['attachment_id'] );
			$mb['unattach']      = apply_filters( 'wpba_meta_box_unattach', $mb['unattach'] );
			$mb['edit']          = apply_filters( 'wpba_meta_box_edit', $mb['edit'] );
			$mb['delete']        = apply_filters( 'wpba_meta_box_delete', $mb['delete'] );
			$options['meta_box'] = $mb;

			// Filters disable attachment type defaults
			$dat                                 = $options['disable_attachment_types'];
			$dat['image']                        = apply_filters( 'wpba_disable_attachment_type_image', $dat['image'] );
			$dat['video']                        = apply_filters( 'wpba_disable_attachment_type_video', $dat['video'] );
			$dat['audio']                        = apply_filters( 'wpba_disable_attachment_type_audio', $dat['audio'] );
			$dat['document']                     = apply_filters( 'wpba_disable_attachment_type_document', $dat['document'] );
			$options['disable_attachment_types'] = $dat;

			// Filters edit modal defaults
			$em                             = $options['edit_modal'];
			$em['disable_caption']          = apply_filters( 'wpba_edit_modal_disable_caption', $em['disable_caption'] );
			$em['disable_alternative_text'] = apply_filters( 'wpba_edit_modal_disable_alternative_text', $em['disable_alternative_text'] );
			$em['disable_description']      = apply_filters( 'wpba_edit_modal_disable_description', $em['disable_description'] );
			$options['edit_modal']          = $em;

			// Set post type defaults
			foreach ( $post_types as $post_type ) {
				// Aliases
				$pt     = $options[$post_type];
				$pt_mb  = $pt['meta_box'];
				$pt_dat = $pt['disable_attachment_types'];

				// Filters post type meta box defaults
				$pt_mb['title'] = apply_filters( "wpba_{$post_type}_meta_box_title", $pt_mb['title'] );
				$pt_mb['caption'] = apply_filters( "wpba_{$post_type}_meta_box_caption", $pt_mb['caption'] );
				$pt_mb['attachment_id'] = apply_filters( "wpba_{$post_type}_meta_box_attachment_id", $pt_mb['attachment_id'] );
				$pt_mb['unattach'] = apply_filters( "wpba_{$post_type}_meta_box_unattach", $pt_mb['unattach'] );
				$pt_mb['edit'] = apply_filters( "wpba_{$post_type}_meta_box_edit", $pt_mb['edit'] );
				$pt_mb['delete'] = apply_filters( "wpba_{$post_type}_meta_box_delete", $pt_mb['delete'] );

				// Filters post type enabled page defaults
				$pt['enabled_pages'] = apply_filters( "wpba_{$post_type}_enabled_pages", $pt['enabled_pages'] );

				// Filters post type disable attachment type defaults
				$pt_dat['image'] = apply_filters( "wpba_{$post_type}_disable_attachment_type_image", $pt_dat['image'] );
				$pt_dat['video'] = apply_filters( "wpba_{$post_type}_disable_attachment_type_video", $pt_dat['video'] );
				$pt_dat['audio'] = apply_filters( "wpba_{$post_type}_disable_attachment_type_audio", $pt_dat['audio'] );
				$pt_dat['document'] = apply_filters( "wpba_{$post_type}_disable_attachment_type_document", $pt_dat['document'] );


				$pt['disable_attachment_types'] = $pt_dat;
				$pt['meta_box']                 = $pt_mb;
				$options[$post_type]            = $pt;
			} // foreach()

			return $options;
		} // _get_filtered_options()
	} // WPBA_Filter_Settings
} // if()