<?PHP
/**
* Custom Meta Boxes
*/
function wpba_add_custom_meta_boxes() {

	/*
	* Team Member
	*/
	add_meta_box(
		'wpba_meta_box',					// $id
		'WP Better Attachments',	// $title
		'show_wpba_meta_box', 		// $callback
		'post',										// $page
		'normal',									// $context
		'high'										// $priority
	);

} // wpba_add_custom_meta_boxes()
add_action('add_meta_boxes', 'wpba_add_custom_meta_boxes');


/*
* Custom Fields
*/
function wpba_meta_fields()
{
	$description = '<div class="wpba-note">'.
						'<p></p>'.
					'</div>';

	$prefix = 'wpba';

	return array(
		array(
			'label'		=> '',
			'desc'		=> $description,
			'id'		=> 'info',
			'type'		=> 'info'
		),
		array(
			'label'		=> 'First Name',
			'desc'  	=> '',
			'id'    	=> $prefix.'FirstName',
			'type'  	=> 'text',
		),
		array(
			'label'		=> 'WPBA',
			'desc'  	=> '',
			'id'    	=> $prefix.'FirstName',
			'type'  	=> 'text',
		),
	);
} // wpba_meta_fields()


/*
* Create the form
*/
function show_wpba_meta_box()
{

	$meta_helper = new WPBA_Meta_Helper();

	global $post;

	$meta_fields = wpba_meta_fields();

	$meta_helper->make_form( array( 'meta_fields' => $meta_fields) );
} // show_wpba_meta_box()


/*
* Save WPBA meta
*/
function save_wpba_meta($post_id) {

	$meta_helper = new WPBA_Meta_Helper();
	$meta_fields = wpba_meta_fields();

	$meta_helper->save_custom_meta(array(
		'post_id'				=> $post_id,
		'custom_meta_fields'	=> $meta_fields
	));
} // save_wpba_meta($post_id)
add_action('save_post', 'save_wpba_meta');
