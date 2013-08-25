<?php
/**
* WP Better Attachments Settings
*
* @package WP_Better_Attachments
*
* @example http://kovshenin.com/2012/the-wordpress-settings-api/
*
* @package WP_Better_Attachments
*/
class WPBA_Settings extends WP_Better_Attachments
{

	/** @var WP_Settings_API_Bootstrap Class */
	private $wp_settings_api;



	/**
	* Constructor
	*/
	function __construct()
	{
		parent::__construct();
		$this->wp_settings_api = new WP_Settings_API_Bootstrap();
		add_action( 'admin_init', array( $this, 'admin_init') );
		add_action( 'admin_menu', array( $this, 'admin_menu') );
	} // __construct()



	/**
	* Initialize the settings on admin_init hook
	*
	* @return Void
	*/
	function admin_init()
	{
		//set the settings
		$this->wp_settings_api->set_sections( $this->get_settings_sections() );
		$this->wp_settings_api->set_fields( $this->get_settings_fields() );

		//initialize settings
		$this->wp_settings_api->admin_init();
	} // admin_init()



	/**
	* Set up all of the Main settings sections
	*
	* @return array
	*/
	function get_settings_sections() {
		$sections = array(
			array(
				'id' => 'wpba_settings',
				'title' => __( 'Settings', 'wpba' )
			)
		);
		return $sections;
	}



	/**
	* Add the menu on admin_menu hook
	*
	* @return  Void
	*/
	function admin_menu()
	{
		add_options_page(
			'WP Better Attachments Settings',
			'WP Better Attachments',
			'activate_plugins',
			'wpba-settings',
			array( $this, 'plugin_page' )
		);
	} // admin_menu(



	/**
	* Returns all the settings fields
	*
	* @return array settings fields
	*/
	function get_settings_fields()
	{
		global $wpba_settings_fields;
		$wpba_settings = array();
		$wpba_settings[] = $wpba_settings_fields->get_post_type_disable_settings();
		$wpba_settings = array_merge( $wpba_settings, $wpba_settings_fields->get_global_settings() );
		$wpba_settings[] = $wpba_settings_fields->get_media_table_settings();
		$wpba_settings[] = $wpba_settings_fields->get_metabox_settings();
		$wpba_settings[] = $wpba_settings_fields->get_attachment_types();
		$wpba_settings[] = $wpba_settings_fields->get_edit_modal_settings();
		$wpba_settings = array_merge( $wpba_settings, $wpba_settings_fields->get_post_type_settings() );

		// Settings
		$settings_fields = array(
			'wpba_settings' => $wpba_settings
		);

		return $settings_fields;
	} // get_settings_fields()



	/**
	* Display the admin page
	*
	* @return Void
	*/
	function plugin_page()
	{?>
		<div class="wrap wpba wpba-loading">
		<div id="icon-options-general" class="icon32"></div>
		<h2>WP Better Attachments <?php echo WPBA_VERSION; ?> Settings</h2>
		<div class="wpba-settings-content pull-left">
		<?php
		settings_errors( 'wpba-disable-post-types', false, true );
		// $this->wp_settings_api->show_navigation();
		$this->wp_settings_api->show_forms(); ?>
		</div>
		<div class="wpba-settings-sidebar pull-left">
			<h3>Thanks for using WP Better Attachments!</h3>
			<p>If you have an issue, found a bug, or like to request a feature please submit it on <a href="https://github.com/DHolloran/wp-better-attachments/issues/" target="_blank">GitHub</a>.</p>
			<h4>To help me help you please include the following.</h4>
			<ul>
				<li>Version of WordPress</li>
				<li>Browser(s) the issue occurred.</li>
				<li>Any error debugging information you have encountered.</li>
				<li>Did you try it on a clean WordPress install?</li>
				<li>What did you do? What did you expect to happen? What did happen?</li>
				<li>Screenshots if available.</li>
			</ul>
			<p>Posting your issue on GitHub with relevant information will help me fix issues and add new features ASAP, thanks in advance. Issues and feature requests post to GitHub directly get top priority.</p>
			<hr>
			<p>I appreciate your feedback, please leave a review <a href="http://wordpress.org/support/view/plugin-reviews/wp-better-attachments/" target="_blank">here</a> and let me know what your thoughts.</p>
			<p>If you have a general question or just want to say high post a message on the <a href="http://wordpress.org/support/plugin/wp-better-attachments/" target="_blank">forums</a></p>
			<hr>
			<h4>Now for some shameless self promotion...</h4>
			<p>
				<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://wordpress.org/plugins/wp-better-attachments/" data-text="Check out WP Better Attachments WordPress plugin" data-via="DHolloran">Tweet</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
			</p>
			<p>
				<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://wordpress.org/plugins/wp-better-attachments/'); ?>)" target="_blank" class="fb-share hide-text">Share on Facebook</a>
			</p>
			<p>
				<iframe src="http://ghbtns.com/github-btn.html?user=dholloran&repo=wp-better-attachments&type=watch&count=true" allowtransparency="true" frameborder="0" scrolling="0" width="75" height="20"></iframe>
			</p>
			<p>
				<a href='https://alpha.app.net/intent/post/?text=Check%20out%20WP%20Better%20Attachments%20WordPress%20plugin. url=http%3A%2F%2Fwordpress.org%2Fplugins%2Fwp-better-attachments%2F' class='adn-button' target='_blank' data-type='share' data-width='121' data-height='20' data-text='Check out WP Better Attachments WordPress plugin.' data-url='http:&#x2F;&#x2F;wordpress.org&#x2F;plugins&#x2F;wp-better-attachments&#x2F;' >Share on App.net</a>
			<script>(function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src='//d2zh9g63fcvyrq.cloudfront.net/adn.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'adn-button-js'));</script>
			</p>
			<p>
				<script id='flattrbtn'>(function(i){var f,s=document.getElementById(i);f=document.createElement('iframe');f.src='//api.flattr.com/button/view/?uid=DHolloran&button=compact&url='+encodeURIComponent(document.URL);f.title='Flattr';f.height=20;f.width=110;f.style.borderWidth=0;s.parentNode.insertBefore(f,s);})('flattrbtn');</script>
			</p>
			<p>
				<a href='http://www.pledgie.com/campaigns/20476'><img alt='Click here to lend your support to: WP Better Attachments and make a donation at www.pledgie.com !' src='http://www.pledgie.com/campaigns/20476.png?skin_name=chrome' border='0' /></a>
			</p>

			<hr>
			<p>If you want to hide this sidebar I understand, I develop client sites too just add this <code>.wpba-settings-sidebar{ display:none!important;}</code> to you administrator CSS.</p>
			<hr>
			<p>If you want to remove the menu item to prevent tampering with the plugin you can place this in your functions.php. You can still accces this page using this url <a href="<?php echo network_admin_url( 'options-general.php?page=wpba-settings' ); ?>"><?php echo network_admin_url( 'options-general.php?page=wpba-settings' ); ?></a></p>
			<pre><code>add_action( 'admin_menu', 'wpba_remove_menu_pages' );
				function wpba_remove_menu_pages() {
				 remove_submenu_page(
				  'options-general.php',
				  'wpba-settings'
				 );
				}
			</code></pre>
		</div>
		</div>
	<?php } // plugin_page()



	/**
	* Get all the pages
	*
	* @return array page names with key value pairs
	*/
	function get_pages()
	{
		$pages = get_pages();
		$pages_options = array();
		if ( $pages )
			foreach ($pages as $page)
				$pages_options[$page->ID] = $page->post_title;

		return $pages_options;
	} // get_pages()
} // END Class WPBA_Settings()

// initiate the class
global $wpba_settings;
$wpba_settings = new WPBA_Settings();
