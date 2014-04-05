<?php
/**
* WPBA Media Library
*
* @package WP_Better_Attachments
*
* @since 1.3.6
*
* @author Dan Holloran dan@danholloran.com
*/
class WPBA_Media_Library extends WP_Better_Attachments
{
	/**
	* Constructor
	*
	* @since 1.3.6
	*/
	function __construct( $config = array() )
	{
		parent::__construct();
	} // __construct()



	/**
	* Initialization Hooks
	*
	* @since 1.3.6
	*
	* @return Void
	*/
	public function init_hooks() {
		add_filter( 'media_row_actions', array( &$this, 'custom_row' ), 10, 2 );
		add_filter( 'manage_upload_columns', array( &$this, 'upload_columns' ) );
		add_action( 'manage_media_custom_column', array( &$this, 'custom_columns' ), 0, 2 );
		add_action( 'admin_footer', array( &$this, 'output_edit_modal' ) );
	} // init_hooks()



	/**
	* Add unattach link in media editor
	*
	* @since 1.3.6
	*
	* @return array Custom media library row
	*/
	function custom_row( $actions, $post )
	{
		if ( $post->post_parent ) {
			if ( !$this->setting_disabled( 'media-table-unattach-link' ) )
				$actions['unattach'] = '<a href="#" title="' . __( "Un-attach this media item." ) . '" class="wpba-unattach-library" data-id="'.$post->ID.'">' . __( 'Un-attach' ) . '</a>';
			if ( !$this->setting_disabled( 'media-table-reattach-link' ) )
				$actions['reattach'] = '<a class="hide-if-no-js wpba-reattach-library" title="' . __( "Re-attach this media item." ) . '" onclick="findPosts.open( '."'media[]','".$post->ID."'". '); return false;" href="#the-list">' . __( 'Re-attach' ) . '</a>';
		} // if()

		return $actions;
	} //custom_row()



	/**
	* Media Upload Columns
	*
	* @since 1.3.6
	*
	* @return array Media post display columns
	*/
	function upload_columns( $columns )
	{
		extract( $columns );
		unset($columns['parent']);
		unset($columns['author']);
		unset($columns['comments']);
		unset($columns['date']);
		$columns['author'] = $author;
		$parent = ( isset( $parent ) ) ? $parent : 'Uploaded To';
		$columns['wpba_parent'] = $parent;
		$columns['comments'] = $comments;
		$columns['date'] = $date;
		return $columns;
	} // upload_columns()



	/**
	* Media Custom Columns
	*
	*
	* @since 1.3.6
	*
	* @return Void
	*/
	function custom_columns($column_name, $id)
	{
		if( $column_name != 'wpba_parent' ) return;

		$post = get_post( $id );

		$html = '';
		$unattach = !$this->setting_disabled( 'media-table-unattach-col' );
		$reattach = !$this->setting_disabled( 'media-table-reattach-col' );
		$attachment_edit_link = admin_url( "post.php?post={$id}&action=edit" );
		$is_image = $this->is_image( $post->post_mime_type );
		$edit = ( !$this->setting_disabled( 'media-table-edit-col' ) AND $is_image );

		if ( $post->post_parent > 0 ) {
			if ( get_post($post->post_parent) )
				$title =_draft_or_post_title($post->post_parent);

			$html .= '<strong>';
			$html .= '<a href="'.get_edit_post_link( $post->post_parent ).'">'.$title.'</a></strong>, '.get_the_time(__('Y/m/d'));
			if ( $edit )
				$html .= "<br><a href='{$attachment_edit_link}' class='wpba-edit'>Edit</a>";
			$html .= '<br><div class="unattach-wrap">';
			if ( $unattach )
				$html .= '<a href="#" title="'.__( "Un-attach this media item." ).'" class="wpba-unattach-library" data-id="'.$post->ID.'">'.__( 'Un-attach' ).'</a>';
			if ( $unattach AND $reattach )
				$html .= '<br>';
			if ( $reattach )
				$html .= '<a class="hide-if-no-js wpba-reattach-library" title="' . __( "Re-attach this media item." ) . '" onclick="findPosts.open( '."'media[]','".$post->ID."'". '); return false;" href="#the-list">' . __( 'Re-attach' ) . '</a>';
			$html .= '</div>';
		} else {
			$html .= __('(Unattached)').'<br>';
			$html .= '<a class="hide-if-no-js" onclick="findPosts.open( '."'media[]','".$post->ID."'".'); return false;" href="#the-list">' . __( 'Attach' ) . '</a>';
			if ( $edit )
				$html .= "<br><a href='{$attachment_edit_link}' class='wpba-edit'>Edit</a>";
		} // if/else()

		echo $html;
	} // custom_columns()



	/**
	* Media Edit Modal
	*
	* @since 1.3.6
	*
	* @return Void
	*/
	public function output_edit_modal()
	{
		$current_page = get_current_screen();
		if( !isset( $current_page->id ) OR $current_page->id != 'upload' ) return;
		global $wpba_meta_box;
		echo $wpba_meta_box->edit_modal();
	} // output_edit_modal()
} // class()

global $wpba_media_library;
$wpba_media_library = new WPBA_Media_Library();