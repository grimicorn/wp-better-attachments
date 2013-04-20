<?php
/**
 * WP Better Attachments
 */
class WPBA_Ajax extends WP_Better_Attachments
{

	/**
	 * Constructor
	 */
	public function __construct( $config = array() )
	{
		parent::__construct();
		$this->ajax_hooks();
		$this->init_hooks();
	} // __construct


	/**
	 * Initialization Hooks
	 */
	public function init_hooks()
	{
	} // init_hooks()


	/**
	 * AJAX Hooks
	 */
	public function ajax_hooks()
	{
		add_action( 'wp_ajax_wpba_update_sort_order', array( &$this, 'update_sort_order_callback' ) );
		add_action( 'wp_ajax_wpba_unattach_attachment', array( &$this, 'unattach_attachment_callback' ) );
		add_action( 'wp_ajax_wpba_add_attachment', array( &$this, 'add_attachment_callback' ) );
		add_action( 'wp_ajax_wpba_add_attachment_old', array( &$this, 'add_attachment_old_callback' ) );
		add_action( 'wp_ajax_wpba_delete_attachment', array( &$this, 'delete_attachment_callback' ) );
		add_action( 'wp_ajax_wpba_refresh_attachments', array( &$this, 'refresh_attachments_callback' ) );
		add_action( 'wp_ajax_wpba_update_post', array( &$this, 'update_post_callback' ) );
		add_action('wp_ajax_wpba_image_area_select', array( &$this, 'image_area_select_callback' ) );
	} // ajax_hooks()


	/**
	 * AJAX Update Sort Order
	 */
	public function update_sort_order_callback()
	{
		extract( $_POST );

		// Check for empty values
		foreach ( $attids as $key => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $key2 => $value2 ) {
					if ( empty( $value2 ) )
						unset( $attids[ $key ][ $key2 ] );
				}
			}
			if ( empty( $attids[ $key ] ) )
				unset( $attids[ $key ] );
		}

		// Takes an array of attachment ids
		$index = 1;
		foreach ( $attids as $att_id ) {
			$attachment = array();
			$attachment['ID'] = $att_id;
			$attachment['menu_order'] = $index;
			wp_update_post( (array) $attachment );
			$index = $index + 1;
		} //foreach()

		echo json_encode( true );
		die();
	} // update_sort_order_callback()


	/**
	 * AJAX Unattach Image
	 */
	public function unattach_attachment_callback()
	{
		extract( $_POST );

		if ( !isset( $attachmentid ) ) {
			echo json_encode( false );
			die();
		} // if()

		$unattach = $this->unattach( array( 'attachment_id' => $attachmentid ) );
		echo json_encode( $unattach );
		die();
	} // unattach_attachment_callback()


	/**
	 * AJAX Add Attachment
	 */
	public function add_attachment_callback()
	{
		extract( $_POST );
		$args = array(
			'post' => get_post( $parentid )
		);
		$current_attachments = $this->get_post_attachments( $args );

		// Make sure we have something to work with
		if ( !isset( $attachments ) and !isset( $parentid ) ) {
			echo json_encode( false );
			die();
		} // if()


		// Make sure the attachment doesn't already exist
		// TODO: Find a better way to do this????
		foreach ( $attachments as $attachment_key => $attachment ) {
			foreach ( $current_attachments as $current_attachment ) {
				if ( $attachment['id'] == $current_attachment->ID and $attachment['menuOrder'] != 0 ) {
					unset( $attachments[$attachment_key] );
				} // if()
			} // foreach()
		} // foreach()

		// Add new attachments
		foreach ( $attachments as $attachment ) {
			$this->attach( array(
					'media'   => $attachment['id'],
					'parent_id' => $parentid
				) );
		} // foreach

		global $wpbamb;
		$image_html = $wpbamb->build_image_attachment_li( $attachments, array( 'a_array' => true ) );

		echo json_encode( array(
			'image' => $image_html
		));
		die();
	} // add_attachment_callback()


	/**
	 * AJAX Add Attachment - Old Media Uploader
	 */
	public function add_attachment_old_callback()
	{
		extract( $_POST );
		$args = array(
			'post' => get_post( $parentid )
		);
		$current_attachments = $this->get_post_attachments( $args );

		// Make sure we have something to work with
		if ( !isset( $attachmenturl ) and !isset( $parentid ) ) {
			echo json_encode( false );
			die();
		} // if()
		$attachments = array(
			array( 'id' => $this->get_attachment_id_from_src( $attachmenturl ) )
		);

		// Make sure the attachment doesn't already exist
		// TODO: Find a better way to do this????
		foreach ( $attachments as $attachment_key => $attachment ) {
			foreach ( $current_attachments as $current_attachment ) {
				if ( $attachment['id'] == $current_attachment->ID and $attachment['menuOrder'] != 0 ) {
					unset( $attachments[$attachment_key] );
				} // if()
			} // foreach()
		} // foreach()

		// Add new attachments
		foreach ( $attachments as $attachment ) {
			$this->attach( array(
					'media'   => $attachment['id'],
					'parent_id' => $parentid
				) );
		} // foreach

		global $wpbamb;
		$html = $wpbamb->build_image_attachment_li( $attachments, array( 'a_array' => true ) );

		echo json_encode( $html );
		die();
	} // add_attachment_old_callback()


	/**
	 * AJAX Update Sort Order
	 */
	public function delete_attachment_callback()
	{
		extract( $_POST );
		// Make sure we have something to work with
		if ( !isset( $attachmentid ) ) {
			echo json_encode( 'noid' );
			die();
		} // if()

		$deleted = wp_delete_attachment( $attachmentid, true );
		if ( false === $deleted ) {
			echo json_encode( false );
		} else {
			echo json_encode( true );
		} // if/else()
		die();
	} // delete_attachment_callback()


	/**
	* AJAX Refresh Attachments
	*/
	public function refresh_attachments_callback()
	{
		global $wpbamb;
		extract( $_GET );

		$html = '';
		$nl = "\n";
		$attachments = $this->get_post_attachments( array( 'post' => get_post( $postid ) ) );
		// Build Attachments Output
		if ( !empty( $attachments ) ) {
			global $wpbamb;
			$html .= $wpbamb->build_image_attachment_li( $attachments );
		} // if (!empty($attachments))

		echo json_encode( $html );
		die();
	} // refresh_attachments_callback()


	/**
	* Update Post
	*/
	public function update_post_callback()
	{
		extract( $_POST );
		$my_post = array();
		$my_post['ID'] = $id;
		$my_post[$key] = $value;

		// Update the post into the database
		echo json_encode( wp_update_post( $my_post ) );
		die();
	} //update_post_callback()


	/**
	* Image Area Select
	*/
	public function image_area_select_callback()
	{
		extract( $_POST );
		$resize_crop_selection = resize_crop_selection();
		// Save data we need for displaying new crop through JS
		if ( $resize_crop_selection ) {
			$attachment_meta = get_post_meta( $id, 'wpba_crop_points', true );
			$crop_points = ( $attachment_meta ) ? $attachment_meta : array();
			$crop_key = "{$final_w}x{$final_h}";
			$crop_points[$crop_key] = array(
				'x1'	=>	$src_x,
				'x2' 	=>	$src_x + $final_w,
				'y1'	=>	$src_y,
				'y2'	=>	$src_h
			);
			update_post_meta( $id, 'wpba_crop_points', $crop_points );
		} // if()
		echo json_encode( $resize_crop_selection );
		die();
	} // image_area_select_callback()


} // END Class WPBA_Ajax

/**
 * Instantiate class and create return method for easier use later
 */
global $wpba_ajax;
$wpba_ajax = new WPBA_Ajax();

function call_WPBA_Ajax() {
	return new WPBA_Ajax();
} // call_WPBA_Ajax()
if ( is_admin() )
	add_action( 'load-post.php', 'call_WPBA_Ajax' );
