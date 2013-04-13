<?php
/**
 * WP Better Attachments MEta Box
 */
class WPBA_Meta_Box extends WP_Better_Attachments
{

	/**
	 * Constructor
	 */
	public function __construct( $config = array() ) {
		parent::__construct();
		$this->init_hooks();
	} // __construct


	/**
	 * Initialization Hooks
	 */
	public function init_hooks() {
		add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ) );
	} // init_hooks()


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
		echo $this->edit_modal();
	} // render_meta_box_content()


	/**
	 * Output Post Attachments
	 */
	public function output_post_attachments( $args = array() ) {
		extract( $args );

		$html = '';
		$nl = "\n";
		$attachments = $this->get_post_attachments( $args );
		$html .= '<ul id="wpba_image_sortable" class="unstyled wpba-image-attachments">';
		// Build Attachments Output
		if ( !empty( $attachments ) ) {
			$html .= $this->build_image_attachment_li( $attachments );
		} // if (!empty($attachments))
		$html .= '</ul>';

		return $html;
	} // output_post_attachments()


	/**
	* Edit Modal
	*/
	protected function edit_modal()
	{
		// Modal does not exist pre 3.5
		global $wp_version;
		if ( floatval( $wp_version ) <= 3.4 )
			return '';

		$html = '';
		$nl = "\n";
		$html .= '<div tabindex="0" id="wpba_edit_screen" class="supports-drag-drop" style="display: none;">' . $nl;
		$html .= '<div class="media-modal wp-core-ui">' . $nl;
		$html .= '<a id="wpba_edit_screen_close" class="media-modal-close" href="#" title="Close"><span class="media-modal-icon"></span></a>' . $nl;
		$html .= '<div class="media-modal-content">' . $nl;
		$html .= '<iframe seamless="seamless" src=""></iframe>' . $nl;
		$html .= '</div>' . $nl;
		$html .= '</div>' . $nl;
		$html .= '<div class="media-modal-backdrop"></div>' . $nl;
		$html .= '</div>' . $nl;

		return $html;
	} // edit_modal()

} // END Class WP_Better_Attachments

/**
 * Instantiate class and create return method for easier use later
 */
global $wpbamb;
$wpbamb = new WPBA_Meta_Box();

function call_WPBA_Meta_Box() {
	return new WPBA_Meta_Box();
} // call_WPBA_Meta_Box()
if ( is_admin() )
	add_action( 'load-post.php', 'call_WPBA_Meta_Box' );
