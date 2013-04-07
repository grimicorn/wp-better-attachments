<?php
/**
 * WP Better Attachments
 */
class WP_Better_Attachments {

	/**
	 * Constructor
	 */
	public function __construct( $config = array() ) {
		$this->init_hooks();
	} // __construct


	/**
	 * Initialization Hooks
	 */
	public function init_hooks() {
		add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ) );
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_scripts' ) );
		add_filter( 'media_row_actions',  array( &$this, 'unattach_media_row_action' ), 10, 2 );
	} // init_hooks()


	/**
	 * Add unattach link in media editor
	 * TODO: Added reattach link as well
	 */
	function unattach_media_row_action( $actions, $post ) {

		if ( $post->post_parent ) {
			$actions['unattach'] = '<a href="#" title="' . __( "Unattach this media item." ) . '" class="wpba-unattach-library" data-id="'.$post->ID.'">' . __( 'Unattach' ) . '</a>';
		} // if()

		return $actions;
	} //unattach_media_row_action()


	/**
	 * Enqueue Administrator Scripts and Styles
	 */
	public function enqueue_admin_scripts() {
		$current_screen = get_current_screen();
		if ( $current_screen->base == 'edit' or $current_screen->base == 'upload' or $current_screen->base == 'post' ) {
			wp_enqueue_style( 'wpba-admin-css', plugins_url( 'assets/css/wpba-admin.css' , dirname( __FILE__ ) ), null, WPBA_VERSION );

			global $wp_version;
			$deps = array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-widget',
				'jquery-ui-mouse',
				'jquery-ui-sortable'
			);
			if ( floatval($wp_version) >= 3.5 ) {
				// Make sure to enqueue media
				if ( ! did_action( 'wp_enqueue_media' ) )
			    wp_enqueue_media();

				wp_register_script(
					'wpba-media-handler',
					plugins_url( 'assets/js/wpba-media-handler-new.min.js' , dirname( __FILE__ ) ),
					$deps,
					WPBA_VERSION,
					true
				);
			} else {
				wp_enqueue_script( 'media-upload' );
	   		add_thickbox();
				$deps[] = 'media-upload';
				$deps[] = 'thickbox';
				wp_register_script(
					'wpba-media-handler',
					plugins_url( 'assets/js/wpba-media-handler-old.min.js' , dirname( __FILE__ ) ),
					$deps,
					WPBA_VERSION,
					true
				);
			} // if/else()

			wp_enqueue_script( 'wpba-media-handler' );
		} // if()
	} // enqueue_admin_scripts()


	/**
	 * Get attachment ID from src url
	 */
	public function get_attachment_id_from_src( $image_src ) {
		global $wpdb;
		$query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
		$id = $wpdb->get_var( $query );
		return $id;
	} // get_attachment_id_from_src()


	/**
	 * Adds the meta box container
	 */
	public function add_meta_box() {
		$post_types = get_post_types();
		unset( $post_types["attachment"] );
		unset( $post_types["revision"] );
		unset( $post_types["nav_menu_item"] );

		foreach ( $post_types as $post_type ) {
			add_meta_box(
				'wpba_meta_box',
				__( 'WP Better Attachments', WPBA_LANG ),
				array( &$this, 'render_meta_box_content' ),
				$post_type,
				'advanced',
				'high'
			);
		} // foreach()
	} // add_meta_box()


	/**
	 * Render Meta Box content
	 */
	public function render_meta_box_content() {
		global $post; ?>
		<div id="wpba-post-<?php echo $post->ID; ?>" data-postid="<?php echo $post->ID; ?>" class="clearfix wpba">
			<div class="uploader pull-left">
			<?php global $wp_version;
			if ( floatval( $wp_version ) >= 3.5 ) { ?>
				<a class="button wpba-attachments-button" id="wpba_attachments_button" href="#">Add Attachments</a>
			<?php } else {?>
				<a class="button wpba-attachments-button" id="wpba_attachments_button" href="#">Add Attachment</a>
			<?php } //if() ?>
			</div>
			<div class="pull-left wpba-saving hide">
				<span>Saving Attachments </span>
				<img src="<?php echo admin_url( 'images/wpspin_light.gif' ); ?>">
			</div>
			<div class="clear"></div>
			<?php echo $this->output_post_attachments(); ?>
			<div class="clear"></div>
		</div>
		<?php
	} // render_meta_box_content()


	/**
	 * Get Post Attachments
	 */
	protected function get_post_attachments( $args = array() ) {
		extract( $args );
		if ( !isset( $post ) )
			global $post;

		$show_thumbnail = ( isset( $show_thumbnail ) ) ? $show_thumbnail : true;
		// Should we exclude the thumb?
		if ( !$show_thumbnail ) {
			$get_posts_args['meta_query'] = array(
				array(
					'key' => '_thumbnail_id',
					'compare' => 'NOT EXISTS'
				)
			);
		}

		$get_posts_args = array(
			'post_type'     => 'attachment',
			'posts_per_page'  =>  -1,
			'post_parent'    => $post->ID,
			'order'       => 'ASC',
			'orderby'      => 'menu_order'
		);

		// Get the attachments
		$attachments = get_posts( $get_posts_args );
		$image_attachments = array();
		foreach ( $attachments as $attachment ) {
			if ( $this->is_image( $attachment->post_mime_type ) ) {
				$image_attachments[] = $attachment;
			} // if(is_image())
		} // foreach();


		return $image_attachments;
	} // get_post_attachments()


	/**
	 * Output Post Attachments
	 */
	protected function output_post_attachments( $args = array() ) {
		extract( $args );

		$html = '';
		$nl = "\n";
		$attachments = $this->get_post_attachments();
		$html .= '<ul id="wpba_sortable" class="unstyled wpba-attchments">';
		// Build Attachments Output
		if ( !empty( $attachments ) ) {
			$html .= $this->build_attachment_li( $attachments );
		} // if (!empty($attachments))
		$html .= '</ul>';

		return $html;
	} // output_post_attachments()


	/**
	 * Build Attachment List
	 */
	protected function build_attachment_li( $attachments, $args = array() ) {
		extract( $args );
		$html = '';
		$nl = "\n";

		foreach ( $attachments as $attachment ) {
			$attachment_id = ( isset( $a_array ) and $a_array ) ? $attachment['id'] : $attachment->ID;
			$attachment_src = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
			$attachment_edit_link = admin_url( "post.php?post={$attachment_id}&action=edit" );
			$html .= '<li class="pull-left ui-state-default" data-id="'.$attachment_id.'">';
			$html .= '<ul class="unstyled wpba-edit-attachment hide-if-no-js" data-id="'.$attachment_id.'">';
			$html .= '<li class="pull-left"><a href="#" class="wpba-unattach">Unattach | </a></li>';
			$html .= '<li class="pull-left"><a href="'.$attachment_edit_link.'" class="wpba-edit">Edit</a></li>';
			// $html .= '<li class="pull-left"><a href="#" class="wpba-delete">Delete</a></li>';
			$html .= '</ul>';
			$html .= '<img src="'.$attachment_src[0].'" width="'.$attachment_src[1].'" height="'.$attachment_src[2].'" class="img-polaroid" >';
			$html .= '</li>';
		} // foreach();

		return $html;
	} // build_attachment_li()


	/**
	 * Attachment is an image
	 */
	protected function is_image( $mime_type ) {
		if ( $mime_type == 'image/jpeg' or $mime_type == 'image/png' or $mime_type == 'image/gif' )
			return true;

		return false;
	} // is_image()


	/**
	 * Attach
	 */
	public function attach( $args = array() ) {
		extract( $args );
		global $wpdb;

		if ( !$parent_id )
			return;

		$parent = get_post( $parent_id );

		$attach = array();
		foreach ( (array) $media as $att_id ) {
			$att_id = (int) $att_id;

			$attach[] = $att_id;
		}

		if ( ! empty( $attach ) ) {
			$attach_string = implode( ',', $attach );
			$attached = $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_parent = %d WHERE post_type = 'attachment' AND ID IN ( $attach_string )", $parent_id ) );
			foreach ( $attach as $att_id ) {
				clean_attachment_cache( $att_id );
			}
		}

		if ( isset( $attached ) ) {
			return true;
		}

		return false;
	} //attach()


	/**
	 * Unattach
	 */
	public function unattach( $args = array() ) {
		extract( $args );

		// Can not do anything with out the attachment id
		if ( empty( $attachment_id ) )
			return false;

		global $wpdb;
		$wpdb->update( $wpdb->posts, array( 'post_parent'=>0 ),
			array( 'id' => (int)$attachment_id, 'post_type' => 'attachment' ) );

		return true;
	} // unattach()

	/**
	 * Insert Attachment
	 */
	public function insert_attachment( $url ) {
		$wp_upload_dir = wp_upload_dir();
		$filename = str_replace( $wp_upload_dir['url'] . '/', '', $url );
		$wp_filetype = wp_check_filetype( basename( $filename ), null );

		$attachment = array(
			'guid' => $url,
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment( $attachment, $url );
		// you must first include the image.php file
		// for the function wp_generate_attachment_metadata() to work
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	} // insert_attachemt()


} // END Class WP_Better_Attachments

/**
 * Instantiate class and create return method for easier use later
 */
global $wpba;
$wpba = new WP_Better_Attachments();

function call_WP_Better_Attachments() {
	return new WP_Better_Attachments();
} // call_WP_Better_Attachments()
if ( is_admin() )
	add_action( 'load-post.php', 'call_WP_Better_Attachments' );
