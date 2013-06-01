<?php
/**
* WPBA Front End
*
* @since 1.3.1
*/
class WPBA_Frontend extends WP_Better_Attachments
{

	/**
	* Constructor
	*/
	function __construct( $config = array() )
	{

	} // constructor()


	/**
	* Frontend Build Attachments List
	*
	* @since 1.3.1
	*/
	function build_attachment_list( $args = array() )
	{
		$list = "";
		$nl = "\n";
		$plugin_url = plugins_url('wp-better-attachments');
		$defaults = array(
			'post_id'								=>	NULL,
			'show_icon'							=>	true,
			'file_type_categories'	=>	array(
				'image',
				'file',
				'audio',
				'video'
			),
			'file_extensions'				=>	$this->get_allowed_extensions(),
			'image_icon'						=>	"{$plugin_url}/assets/img/icons/image-icon.png",
			'file_icon'							=>	"{$plugin_url}/assets/img/icons/file-icon.png",
			'audio_icon'						=>	"{$plugin_url}/assets/img/icons/audio-icon.png",
			'video_icon'						=>	"{$plugin_url}/assets/img/icons/video-icon.png",
			'icon_size'							=>	array( 16, 20 )
		);
		$atts = extract( shortcode_atts( $defaults, $args ) );


		// Get the attachments
		if ( !is_null( $post_id ) ) {
			$attachments = wpba_get_attachments( $post_id );
		} else {
			global $post;
			$attachments = wpba_get_attachments( $post->ID );
		} // if/else()

		// Make sure we have attachments
		if ( is_null( $attachments ) ) {
			return '';
		} // if()

		// Go through the restrictions
		$attachments = $this->check_allowed_file_type_categories( $attachments, $file_type_categories );
		$attachments = $this->check_allowed_file_extensions( $attachments, $file_extensions );

		$list .= "<ul>";
		foreach ( $attachments as $attachment ) {

			$title = $attachment->post_title;
			$link = wp_get_attachment_url( $attachment->ID );
			$list .= "<li>";
			if ( $show_icon ) $list .= $this->icon( $attachment, shortcode_atts( $defaults, $args ) );
			$list .= "<a href='{$link}' title='{$title}'>{$title}</a>";
			$list .= "<li>" . $nl;
		} // foreach()
		$list .= "</ul>";

		return $list;
	} // wpba_build_attachment_list()

		/**
	* Attachment placeholder image name
	*/
	protected function icon( $attachment, $args )
	{
		$img_src = '';
		$plugin_url = WPBA_PATH;
		extract( $args );
		if ( $this->is_document( $attachment->post_mime_type ) ) {
			$img_src = $file_icon;
		} elseif ( $this->is_audio( $attachment->post_mime_type ) ) {
			$img_src = $audio_icon;
		} elseif ( $this->is_video( $attachment->post_mime_type ) ) {
			$img_src = $video_icon;
		} elseif( $this->is_image( $attachment->post_mime_type ) ) {
			$img_src = $image_icon;
		}//if/elseif

		$img = "<img src='{$img_src}' width='{$icon_size[0]}' height='{$icon_size[1]}'>";
		return $img;
	} // placeholder_image()
} // class()


/**
 * Instantiate class and create return method for easier use later
 */
global $wpba_frontend;
$wpba_frontend = new WPBA_Frontend();