<?php
/**
* WPBA Media Library
*
* @since 1.3.6
*/
class WPBA_Media_Library extends WP_Better_Attachments
{

	function __construct( $config = array() )
	{
		parent::__construct();
	} // __construct()

	/**
	* Initialization Hooks
	*/
	public function init_hooks() {
		add_filter( 'media_row_actions', array( &$this, 'unattach_media_row_action' ), 10, 2 );
		add_filter("manage_upload_columns", array( &$this, 'upload_columns' ) );
		add_action("manage_media_custom_column", array( &$this, 'media_custom_columns' ), 0, 2 );
	} // init_hooks()


	/**
	* Add unattach link in media editor
	*/
	function unattach_media_row_action( $actions, $post )
	{
		if ( $post->post_parent ) {
			if ( !$this->setting_disabled( 'media-table-unattach' ) )
				$actions['unattach'] = '<a href="#" title="' . __( "Un-attach this media item." ) . '" class="wpba-unattach-library" data-id="'.$post->ID.'">' . __( 'Un-attach' ) . '</a>';
			if ( !$this->setting_disabled( 'media-table-reattach' ) )
				$actions['reattach'] = '<a class="hide-if-no-js wpba-reattach-library" title="' . __( "Re-attach this media item." ) . '" onclick="findPosts.open( '."'media[]','".$post->ID."'". '); return false;" href="#the-list">' . __( 'Re-attach' ) . '</a>';
		} // if()

		return $actions;
	} //unattach_media_row_action()


	/**
	* Media Upload Columns
	*
	* @return null
	* @since 1.3.6
	*/
	function upload_columns( $columns )
	{
		extract( $columns );
		unset($columns['parent']);
		unset($columns['author']);
		unset($columns['comments']);
		unset($columns['date']);
		$columns['author'] = $author;
		$columns['wpba_parent'] = $parent;
		$columns['comments'] = $comments;
		$columns['date'] = $date;
		return $columns;
	} // upload_columns()


	/**
	* Media Custom Columns
	*
	* @return null
	* @since 1.3.6
	*/
	function media_custom_columns($column_name, $id)
	{
		if( $column_name != 'wpba_parent' ) return;

		$post = get_post($id);
		$html = '';
		$unattach = !$this->setting_disabled( 'media-table-unattach' );
		$reattach = !$this->setting_disabled( 'media-table-reattach' );

		if ( $post->post_parent > 0 ) {
			if ( get_post($post->post_parent) )
				$title =_draft_or_post_title($post->post_parent);

			$html .= '<strong>';
			$html .= '<a href="'.get_edit_post_link( $post->post_parent ).'">'.$title.'</a></strong>, '.get_the_time(__('Y/m/d'));
			$html .= '<br>';
			$html .= '<a class="hide-if-no-js" href="#">'.__('Edit').'</a>';
			$html .= '<br>';
			$html .= '<div class="unattach-wrap">';
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
		} // if/else()

		echo $html;
	} // media_custom_columns()
} // class()

global $wpba_media_library;
$wpba_media_library = new WPBA_Media_Library();