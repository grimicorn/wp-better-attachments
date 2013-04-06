<?php
/**
* WP Better Attachments
*/
class WP_Better_Attachments
{

	/**
	* Constructor
	*/
	function __construct( $config = array() )
	{
	} // __construct

	public function attach_image( $args = array() )
	{
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
		} else {
			return array(
				'$att_id' => $att_id,
				'$attach'	=>	$attach,
				'$attach_string'	=>	$attach_string,
				'$parent_id'		=>	$parent_id
			);
		}

	  // return $attach_id;
	} //attach_image()
} // END Class WP_Better_Attachments

global $wpbg;
$wpbg = new WP_Better_Attachments();