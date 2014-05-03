<?php
/**
 * This class contains any sort of helper methods/properties that should be shared throughout the classes.
 * This class should be extended by all other classes except WP_Better_Attachments.
 *
 * @version      1.4.0
 *
 * @package      WordPress
 * @subpackage   WPBA
 *
 * @since        1.4.0
 *
 * @author       Dan Holloran          <dholloran@matchboxdesigngroup.com>
 *
 * @copyright    2013 - Present         Dan Holloran
 */
if ( ! class_exists( 'WPBA_Helpers' ) ) {
	class WPBA_Helpers extends WP_Better_Attachments {
		/**
		 * The length to cache a transient, default 24 hours.
		 *
		 * @since  1.4.0
		 *
		 * @todo   Add setting/filter to update the cache duration.
		 *
		 * @var    integer
		 */
		protected $cache_duration = 86400;


		/**
		 * WPBA_Helpers class constructor.
		 *
		 * @since  1.4.0
		 *
		 * @param array   $config Class configuration.
		 */
		public function __construct( $config = array() ) {
			parent::__construct();

			$this->_add_wpba_helpers_actions_filters();
		} // __construct()



		/**
		 * Handles adding all of the WPBA helpers actions and filters.
		 *
		 * <code>$this->_add_wpba_helpers_actions_filters();</code>
		 *
		 * @internal
		 *
		 * @since   1.4.0
		 *
		 * @return  void
		 */
		private function _add_wpba_helpers_actions_filters() {
			// Cleans the cache for the attachments.
			add_action( 'add_attachment', array( &$this, 'clean_attachments_cache' ) );
			add_action( 'edit_attachment', array( &$this, 'clean_attachments_cache' ) );
			add_action( 'delete_attachment', array( &$this, 'clean_attachments_cache' ) );
			add_action( 'save_post', array( &$this, 'clean_attachments_cache' ) );
			Add_action( 'wpba_attachment_unattached', array( &$this, 'clean_attachments_cache' ) );
			Add_action( 'wpba_attachment_unattached', array( &$this, 'clean_attachments_cache' ) );
			Add_action( 'wpba_attachment_deleted', array( &$this, 'clean_attachments_cache' ) );
		} // _add_wpba_helpers_actions_filters()



		/**
		 * Retrieves the attachment post parent ID.
		 *
		 * <code>$post_parent_id = $this->get_attachment_post_parent_id( $post_parent );</code>
		 *
		 * @since   1.4.0
		 *
		 * @param   object|integer  $post_parent  Optional, the post parent object or ID, defaults to current post.
		 *
		 * @return  integer                     The attachments post parent ID.
		 */
		public function get_attachment_post_parent_id( $post_parent = null ) {
			// Get post parent ID
			if ( is_null( $post_parent ) ) {
				global $post;
				return get_the_id();
			} else if ( gettype( $post_parent ) === 'object' ) {
				return $post_parent->ID;
			} else if ( gettype( intval( $post_parent ) ) == 'integer' ) {
				return $post_parent;
			} else {
				return false;
			} // if/elseif/else()
		} // get_attachment_post_parent_id()



		/**
		 * Retrieves the attachment ID.
		 *
		 * <code>$attachment_id = $this->get_attachment_id( $attachment );</code>
		 *
		 * @since   1.4.0
		 *
		 * @param   object|integer  $attachment  Optional, the post parent object or ID, defaults to current post.
		 *
		 * @return  integer                    The attachments post parent ID.
		 */
		public function get_attachment_id( $attachment ) {
			// Get post parent ID
			if ( gettype( $attachment ) === 'object' ) {
				return $attachment->ID;
			} else if ( gettype( intval( $post->ID ) ) == 'integer' ) {
					return $attachment;
			} else {
				return false;
			} // if/elseif/else()
		} // get_attachment_id()



		/**
		 * Retrieves the transient ID for the current query.
		 *
		 * <code>$this->get_transient_id( $attachments_query_args );</code>
		 *
		 * @since   1.4.0
		 *
		 * @param array     $query_args  The arguments for the query.
		 *
		 * @return  string                The transient ID.
		 */
		public function get_transient_id( $query_args ) {
			$flattened_array = array();

			$flatten_array = array_walk_recursive( $query_args, function( $key, $value ) use ( &$flattened_array ) { $flattened_array[$key] = $value; } );
			$keys          = implode( '', array_keys( $flattened_array ) );
			$values        = implode( '' , $flattened_array );
			$transient_id  = md5( "{$keys}{$values}" );
			$transient_id  = "_wpba{$transient_id}";
			return $transient_id;
		} // get_transient_id()



		/**
		 * Caches the attachments query.
		 *
		 * <code>$this->cache_attachments( $transient_id, $attachments );</code>
		 *
		 * @since   1.4.0
		 *
		 * @param   string  $transient_id    The transient ID to use.
		 * @param   array   $attachments     The attachments to cache.
		 * @param   integer $cache_duration  Optional, the length of time to store the attachments, default 24 hours.
		 *
		 * @return  boolean                  False if value was not set and true if value was set.
		 */
		public function cache_attachments( $transient_id, $attachments, $cache_duration = null ) {
			$post_type      = get_post_type();
			$cache_duration = ( is_null( $cache_duration ) ) ? $this->cache_duration : $cache_duration;


			/**
			 * Allows filtering of the cache duration for all post types.
			 * Either the time duration in seconds or false to disable the cache, useful for development.
			 *
			 * <code>
			 * function myprefix_cache_duration( $input_fields ) {
			 * 	return ( 12 * HOUR_IN_SECONDS );
			 * }
			 * add_filter( 'wpba_meta_box_cache_duration', 'myprefix_cache_duration' );
			 * </code>
			 *
			 * @see https://codex.wordpress.org/Transients_API
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 *
			 * @var   string
			 */
			$cache_duration = apply_filters( "{$this->meta_box_id}_cache_duration", $cache_duration );



			/**
			 * Allows filtering of the cache duration for a specific post type.
			 * Either the time duration in seconds or false to disable the cache, useful for development.
			 *
			 * <code>
			 * function myprefix_post_type_cache_duration( $input_fields ) {
			 * 	return ( 12 * HOUR_IN_SECONDS );
			 * }
			 * add_filter( 'wpba_meta_box_post_type_cache_duration', 'myprefix_post_type_cache_duration' );
			 * </code>
			 *
			 * @see https://codex.wordpress.org/Transients_API
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 *
			 * @var   string
			 */
			$cache_duration = apply_filters( "{$this->meta_box_id}_{$post_type}_cache_duration", $cache_duration );


			// Allows for disabling of the cache
			$cache_duration = ( $cache_duration === false ) ? 0 : $cache_duration;

			if ( is_multisite() ) {
				return set_site_transient( $transient_id, $attachments, $cache_duration );
			} else {
				return set_transient( $transient_id, $attachments, $cache_duration );
			} // if/else()
		} // cache_attachments()



		/**
		 * Get the cached attachments.
		 *
		 * <code>$cached_attachments = $this->get_attachments_cache( $transient_id );</code>
		 *
		 * @since 1.4.0
		 *
		 * @param string            $transient_id  The transient ID to retrieve.
		 *
		 * @return  boolean|array                  Either false on failure/does not exist or the cached attachments.
		 */
		public function get_attachments_cache( $transient_id ) {
			if ( is_multisite() ) {
				return get_site_transient( $transient_id );
			} else {
				return get_transient( $transient_id );
			} // if()

			return false;
		} // get_attachments_cache()



		/**
		 * Delete an attachments cache.
		 *
		 * <code>$this->delete_attachments_cache( $transient_id );</code>
		 *
		 * @since   1.4.0
		 *
		 * @param   string  $transient_id  The transient ID to delete.
		 *
		 * @return  boolean                True if successful, false otherwise.
		 */
		public function delete_attachments_cache( $transient_id ) {
			if ( is_multisite() ) {
				return delete_site_transient( $transient_id );
			} else {
				return delete_transient( $transient_id );
			} // if()
		} // delete_attachments_cache()



		/**
		 * Retrieves all of the current transient IDs.
		 *
		 * <code>$this->_get_all_cached_attachment_transient_ids();</code>
		 *
		 * @internal
		 *
		 * @since   1.4.0
		 *
		 * @uses    $wpdb
		 *
		 * @param   string  $prefix  Optional, the transient id prefix to search for default _wpba.
		 *
		 * @return  array            All of the current transient IDs.
		 */
		private function _get_all_cached_attachment_transient_ids( $prefix = '_wpba' ) {
			global $wpdb;
			$sql = "SELECT `option_name` AS `name`, `option_value` AS `value`
							FROM  $wpdb->options
							WHERE `option_name` LIKE '%transient_%'
							ORDER BY `option_name`";

			$results = $wpdb->get_results( $sql );

			// Find the transient IDs with the supplied prefix.
			$transients = array();
			foreach ( $results as $result ) {
				if ( strpos( $result->name, "_transient_{$prefix}" ) !== false ) {
					$transient_id = str_replace( '_transient_', '', $result->name );
					$transients[] = $transient_id;
				} // if()
			} // foreach()

			return array_unique( $transients );
		} // _get_all_cached_attachment_transient_ids()



		/**
		 * Clean the cached attachments.
		 *
		 * <code>add_action( 'add_attachment', array( &$this, 'clean_attachments_cache' ) );</code>
		 *
		 * @since   1.4.0
		 *
		 * @param   string  $prefix  Optional, the transient id prefix to search for default _wpba.
		 *
		 * @return  void
		 */
		public function clean_attachments_cache( $prefix = '_wpba' ) {
			$transient_ids = $this->_get_all_cached_attachment_transient_ids();

			foreach ( $transient_ids as $transient_id ) {
				$this->delete_attachments_cache( $transient_id );
			} // foreach()
		} // clean_attachments_cache()



		/**
		 * Retrieves all of the posts for the current/supplied post.
		 *
		 * <code>$attachments = $this->get_attachments( $post );</code>
		 *
		 * @see     http://codex.wordpress.org/Class_Reference/WP_Query
		 *
		 * @since   1.4.0
		 *
		 * @uses    WP_Query
		 *
		 * @todo    Add the ability to add attachment to multiple posts.
		 *
		 * @param   object|integer  $post_parent             Optional, the post parent object or ID, defaults to current post.
		 * @param   boolean         $disable_featured_image  Optional, if the featured image should NOT be included as an attachment, default false.
		 * @param   array           $query_args              Optional, arguments to alter the query, accepts anything WP_Query does.
		 *
		 * @return  array                                    The attachments.
		 */
		public function get_attachments( $post_parent = null, $disable_featured_image = false, $query_args = array() ) {
			// Debugging purposes only
			// $this->clean_attachments_cache();

			$post_parent_id = $this->get_attachment_post_parent_id( $post_parent );
			$post_type      = get_post_type( $post_parent_id );

			// Post parent ID does not exist
			if ( ! $post_parent_id ) {
				return array();
			} // if()

			// Default query arguments.
			$default_query_args = array(
				'post_type'   => 'attachment',
				'post_status' => 'inherit',
				'post_parent' => $post_parent_id,
				'order'       => 'ASC',
				'orderby'     => 'menu_order',
			);


			/**
			 * Allows filtering of disabling the featured image for all post types.
			 *
			 * <code>
			 * function myprefix_disable_featured_image( $input_fields ) {
			 * 	return true;
			 * }
			 * add_filter( 'wpba_meta_box_disable_featured_image', 'myprefix_disable_featured_image' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 *
			 * @var   string
			 */
			$disable_featured_image = apply_filters( "{$this->meta_box_id}_disable_featured_image", $disable_featured_image );

			/**
			 * Allows filtering of disabling the featured image for a specific post type.
			 *
			 * <code>
			 * function myprefix_post_type_disable_featured_image( $input_fields ) {
			 * 	return true;
			 * }
			 * add_filter( 'wpba_meta_box_post_type_disable_featured_image', 'myprefix_post_type_disable_featured_image' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 *
			 * @var   string
			 */
			$disable_featured_image = apply_filters( "{$this->meta_box_id}_{$post_type}_disable_featured_image", $disable_featured_image, $post_type );

			// Disable the featured image? It is an attachment ya'know.
			if ( $disable_featured_image ) {
				$default_query_args['post__not_in'] = array( get_post_thumbnail_id( $post_parent_id ) );
			} // if()

			// Merge default and supplied query arguments
			$attachments_query_args = array_merge( $default_query_args, $query_args );

			// Attachments transient
			$transient_id       = $this->get_transient_id( $attachments_query_args );
			$cached_attachments = $this->get_attachments_cache( $transient_id );
			if ( $cached_attachments !== false ) {
				return $cached_attachments;
			} // if()

			// Get the attachments
			$attachments_query = new WP_Query( $attachments_query_args );
			$attachments       = $attachments_query->get_posts();

			// Cache the attachments for the default time
			$this->cache_attachments( $transient_id, $attachments );

			return $attachments;
		} // get_attachments()



		/**
		 * All of the allowed tags when outputting form fields.
		 *
		 * @return array Allowed HTML tags.
		 */
		public function get_form_kses_allowed_html() {
			$allowed_tags          = wp_kses_allowed_html( 'post' );
			$allowed_tags['<hr>']  = array();
			$allowed_tags['input'] = array(
				'type'        => array(),
				'name'        => array(),
				'id'          => array(),
				'value'       => array(),
				'size'        => array(),
				'class'       => array(),
				'placeholder' => array(),
				'checked'     => array(),
			);
			$allowed_tags['option'] = array(
				'value'    => array(),
				'selected' => array(),
			);
			$allowed_tags['select'] = array(
				'name'     => array(),
				'id'       => array(),
				'class'    => array(),
				'style'    => array(),
				'multiple' => array()
			);
			$allowed_tags['span'] = array(
				'class' => array(),
				'id'    => array(),
			);
			$allowed_tags['textarea'] = array(
				'name'        => array(),
				'id'          => array(),
				'cols'        => array(),
				'rows'        => array(),
				'class'       => array(),
			);
			return $allowed_tags;
		} // get_form_kses_allowed_html()



		/**
		 * Retrieves the enabled post types.
		 *
		 * @todo    Add setting to restrict post types.
		 *
		 * @since   1.4.0
		 *
		 * @return  array  The enabled post types.
		 */
		public function get_post_types() {
			$post_types = get_post_types();

			// Remove post types that can not have attachments
			unset( $post_types['attachment'] );
			unset( $post_types['revision'] );
			unset( $post_types['nav_menu_item'] );
			unset( $post_types['deprecated_log'] );

			/**
			 * Allows filtering of the allowed post types.
			 *
			 * <code>
			 * function myprefix_wpba_post_types( $post_types ) {
			 * 	unset( $post_types['page'] ); // Removes the "page" post type.
			 * }
			 * add_filter( 'wpba_meta_box_post_types', 'myprefix_wpba_post_types' );
			 * </code>
			 *
			 * @since 1.4.0
			 *
			 * @todo  Create example documentation.
			 * @todo  Allow for multiple meta boxes.
			 *
			 * @var   array
			 */
			$post_types = apply_filters( "{$this->meta_box_id}_post_types", $post_types );

			return $post_types;
		} // get_post_types()
	} // WPBA_Helpers()

	// Instantiate Class
	global $wpba_helpers;
	$wpba_helpers = new WPBA_Helpers();
} // if()
