<?php
/**
 * WP Settings API Bootstrap class
 *
 * @author Tareq Hasan <tareq@weDevs.com>
 * @author Derek Marcinyshyn <derek@marcinyshyn.com>
 */
	class WP_Settings_API_Bootstrap {

		/**
		 * settings sections array
		 *
		 * @var array
		 */
		private $settings_sections = array();

		/**
		 * Settings fields array
		 *
		 * @var array
		 */
		private $settings_fields = array();

		/**
		 * Singleton instance
		 *
		 * @var object
		 */
		private static $_instance;

		/**
		 * Constructor
		 */
		public function __construct() {

		}

		/**
		 * @return object|WP_Settings_API_Bootstrap
		 */
		public static function getInstance() {
			if ( !self::$_instance ) {
				self::$_instance = new WP_Settings_API_Bootstrap();
			}

			return self::$_instance;
		}

		/**
		 * Set settings sections
		 *
		 * @param array   $sections setting sections array
		 */
		function set_sections( $sections ) {
			$this->settings_sections = $sections;
		}

		/**
		 * Add a single section
		 *
		 * @param array   $section
		 */
		function add_section( $section ) {
			$this->settings_sections[] = $section;
		}

		/**
		 * Set settings fields
		 *
		 * @param array   $fields settings fields array
		 */
		function set_fields( $fields ) {
			$this->settings_fields = $fields;
		}

		/**
		 * Add settings field
		 *
		 * @param $section
		 * @param $field
		 */
		function add_field( $section, $field ) {
			$defaults = array(
				'name' => '',
				'label' => '',
				'desc' => '',
				'type' => 'text'
			);

			$arg = wp_parse_args( $field, $defaults );
			$this->settings_fields[$section][] = $arg;
		}

		/**
		 * Initialize and registers the settings sections and fields to WordPress
		 *
		 * Usually this should be called at `admin_init` hook.
		 *
		 * This function gets the initiated settings sections and fields. Then
		 * registers them to WordPress and ready for use.
		 */
		function admin_init() {

			//register settings sections
			foreach ( $this->settings_sections as $section ) {
				if ( false == get_option( $section['id'] ) ) {
					add_option( $section['id'] );
				}

				add_settings_section( $section['id'], $section['title'], '__return_false', $section['id'] );
			}

			//register settings fields
			foreach ( $this->settings_fields as $section => $field ) {
				foreach ( $field as $option ) {

					$type = isset( $option['type'] ) ? $option['type'] : 'text';

					$args = array(
						'id'            => $option['name'],
						'desc'          => isset( $option['desc'] ) ? $option['desc'] : '',
						'name'          => $option['label'],
						'section'       => $section,
						'size'          => isset( $option['size'] ) ? $option['size'] : null,
						'options'       => isset( $option['options'] ) ? $option['options'] : '',
						'std'           => isset( $option['default'] ) ? $option['default'] : '',
						'btn_title'     => isset( $option['btn_title'] ) ? $option['btn_title'] : ''
					);
					//var_dump($args);
					add_settings_field( $section . '[' . $option['name'] . ']', $option['label'], array( $this, 'callback_' . $type ), $section, $section, $args );
				}
			}

			// creates our settings in the options table
			foreach ( $this->settings_sections as $section ) {
				register_setting( $section['id'], $section['id'] );
			}

			// enqueue color picker js and css
			wp_enqueue_script(
				'artus-field-color-js',
				WP_PLUGIN_URL . '/' . dirname( plugin_basename(__FILE__) ) . '/colorpicker.js',
				array('jquery', 'farbtastic'),
				time(),
				true
			);
			wp_enqueue_style( 'farbtastic' );

			// import media uploader javascript
			// wp_register_script(
			//     'settings-api-upload',
			//     WP_PLUGIN_URL . '/' . dirname( plugin_basename( __FILE__ ) ) . '/settings-api-upload.js',
			//     array( 'jquery', 'media-upload', 'thickbox' ),
			//     time(),
			//     true
			// );

			// wp_enqueue_script( 'thickbox' );
			// wp_enqueue_style( 'thickbox' );
			// wp_enqueue_script( 'media-upload' );
			// wp_enqueue_script( 'settings-api-upload' );
		}

		/**
		 * Displays the WordPress Media Manager
		 *
		 * @param $args
		 */
		function callback_media( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

			$html = '';
			$html .= sprintf( '<input type="text" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', 'regular', $args['section'], $args['id'], $value );
			$html .= sprintf( '<input type="button" class="button" id="upload_logo_button" value="%1$s"/>', $args['btn_title'] );
			$html .= '<br /><br />';
			$html .= '<div id="upload_image_preview" style="min-height: 100px">';
			$html .= '<img style="max-width:100%;" src="' . $value . '" />';
			$html .= '</div>';

			echo $html;
		}

		/**
		 * Display plain ol' html
		 *
		 * @param $args
		 */
		function callback_about( $args ) {

			$html = '';
			$html .= $args['desc'];

			echo $html;
		}

		/**
		 * Displays the builtin Farbastic colorpicker
		 *
		 * @param array     $args settings field args
		 */
		function callback_colorpicker( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size = 70;

			$html = '<div class="color-picker" style="position: relative;">';
			$html .= sprintf( '<input type="text" class="%1$s-text popup-colorpicker" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
			//$html .= '<div id="' . $args['id'] . 'picker" style="position: absolute"></div>';
			$html .= '<br>';
			$html .= sprintf( '<span class="description"> %s</span>', $args['desc'] );
			$html .= '</div>';

			echo $html;
		}


		/**
		 * Displays a text field for a settings field
		 *
		 * @param array   $args settings field args
		 */
		function callback_text( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
// '#wpba-'+that.val()+'-meta-box-title'
			$html = sprintf( '<input type="text" class="%1$s-text %3$s" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
			$html .= '<br>';
			$html .= sprintf( '<span class="description %2$s"> %1$s</span>', $args['desc'], $args['id'] );

			echo $html;
		}

		/**
		 * Displays a checkbox for a settings field
		 *
		 * @param array   $args settings field args
		 */
		function callback_checkbox( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );

			$html = sprintf( '<input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="on"%4$s />', $args['section'], $args['id'], $value, checked( $value, 'on', false ) );
			$html .= sprintf( '<label for="%1$s[%2$s]"> %3$s</label>', $args['section'], $args['id'], $args['desc'] );

			echo $html;
		}

		/**
		 * Displays a multicheckbox a settings field
		 *
		 * @param array   $args settings field args
		 */
		function callback_multicheck( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );

			$html = '';
			$html .= '<div id="'.$args['id'].'-wrap">';
			foreach ( $args['options'] as $key => $label ) {
				$checked = isset( $value[$key] ) ? $value[$key] : '0';
				$html .= sprintf( '<input type="checkbox" class="checkbox wpba-settings-%3$s" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s"%4$s />', $args['section'], $args['id'], $key, checked( $checked, $key, false ) );
				$html .= sprintf( '<label for="%1$s[%2$s][%4$s]" class="wpba-settings-%4$s"> %3$s</label><br>', $args['section'], $args['id'], $label, $key );
			}
			$html .= '<br>';
			$html .= '<br>';
			$html .= sprintf( '<span class="description"> %s</label>', $args['desc'] );
			$html .= '</div>';
			echo $html;
		}

		/**
		 * Displays a multicheckbox a settings field
		 *
		 * @param array   $args settings field args
		 */
		function callback_radio( $args ) {

			$value = $this->get_option( $args['id'], $args['section'], $args['std'] );

			$html = '';
			foreach ( $args['options'] as $key => $label ) {
				$html .= sprintf( '<input type="radio" class="radio" id="%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s"%4$s />', $args['section'], $args['id'], $key, checked( $value, $key, false ) );
				$html .= sprintf( '<label for="%1$s[%2$s][%4$s]"> %3$s</label><br>', $args['section'], $args['id'], $label, $key );
			}
			$html .= '<br>';
			$html .= sprintf( '<span class="description"> %s</label>', $args['desc'] );

			echo $html;
		}

		/**
		 * Displays a selectbox for a settings field
		 *
		 * @param array   $args settings field args
		 */
		function callback_select( $args ) {

			$value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html = sprintf( '<select class="%1$s" name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id'] );
			foreach ( $args['options'] as $key => $label ) {
				$html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
			}
			$html .= sprintf( '</select>' );
			$html .= '<br>';
			$html .= sprintf( '<span class="description"> %s</span>', $args['desc'] );

			echo $html;
		}

		/**
		 * Displays a textarea for a settings field
		 *
		 * @param array   $args settings field args
		 */
		function callback_textarea( $args ) {

			$value = esc_textarea( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';

			$html = sprintf( '<textarea rows="5" cols="55" class="%1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]">%4$s</textarea>', $size, $args['section'], $args['id'], $value );
			$html .= '<br>';
			$html .= sprintf( '<br><span class="description"> %s</span>', $args['desc'] );

			echo $html;
		}

		/**
		 * Displays a textarea for a settings field
		 *
		 * @param array   $args settings field args
		 */
		function callback_html( $args ) {
			echo $args['desc'];
		}

		/**
		 * Displays a rich text textarea for a settings field
		 *
		 * @param array   $args settings field args
		 */
		function callback_wysiwyg( $args ) {

			$value = wpautop( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
			$size = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : '500px';

			echo '<div style="width: ' . $size . ';">';

			wp_editor( $value, $args['section'] . '[' . $args['id'] . ']', array( 'teeny' => true, 'textarea_rows' => 10 ) );

			echo '</div>';
			echo '<br>';
			echo sprintf( '<br><span class="description"> %s</span>', $args['desc'] );
		}

		/**
		 * Get the value of a settings field
		 *
		 * @param string  $option  settings field name
		 * @param string  $section the section name this field belongs to
		 * @param string  $default default text if it's not found
		 * @return string
		 */
		function get_option( $option, $section, $default = '' ) {

			$options = get_option( $section );

			if ( isset( $options[$option] ) ) {
				return $options[$option];
			}

			return $default;
		}

		/**
		 * Show navigations as tab
		 *
		 * Shows all the settings section labels as tab
		 */
		function show_navigation() {
			$html = '<h2 class="wpba-nav-tab-wrapper nav-tab-wrapper">';

			foreach ( $this->settings_sections as $tab ) {
				$html .= sprintf( '<a href="#%1$s" class="nav-tab" id="%1$s-tab">%2$s</a>', $tab['id'], $tab['title'] );
			}

			$html .= '</h2>';

			echo $html;
		}

		/**
		 * Show the section settings forms
		 *
		 * This function displays every sections in a different form
		 */
		function show_forms() {
			?>
		<div class="metabox-holder">
			<div>
				<?php foreach ( $this->settings_sections as $form ) { ?>
				<div id="<?php echo $form['id']; ?>" class="group">
					<form method="post" action="options.php">

						<?php settings_fields( $form['id'] ); ?>
						<?php do_settings_sections( $form['id'] ); ?>

						<div style="padding-left: 10px">
							<?php submit_button(); ?>
						</div>
					</form>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php
			$this->script();
		}

		/**
		 * Tabbable JavaScript codes
		 *
		 * This code uses localstorage for displaying active tabs
		 */
		function script() {
			?>
		<script>
			jQuery(document).ready(function($) {
				// Switches option sections
				$('.group').hide();
				var activetab = '';
				if (typeof(localStorage) != 'undefined' ) {
					activetab = localStorage.getItem("activetab");
				}
				if (activetab != '' && $(activetab).length ) {
					$(activetab).fadeIn();
				} else {
					$('.group:first').fadeIn();
				}
				$('.group .collapsed').each(function(){
					$(this).find('input:checked').parent().parent().parent().nextAll().each(
							function(){
								if ($(this).hasClass('last')) {
									$(this).removeClass('hidden');
									return false;
								}
								$(this).filter('.hidden').removeClass('hidden');
							});
				});

				if (activetab != '' && $(activetab + '-tab').length ) {
					$(activetab + '-tab').addClass('nav-tab-active');
				}
				else {
					$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
				}
				$('.nav-tab-wrapper a').click(function(evt) {
					$('.nav-tab-wrapper a').removeClass('nav-tab-active');
					$(this).addClass('nav-tab-active').blur();
					var clicked_group = $(this).attr('href');
					if (typeof(localStorage) != 'undefined' ) {
						localStorage.setItem("activetab", $(this).attr('href'));
					}
					$('.group').hide();
					$(clicked_group).fadeIn();
					evt.preventDefault();
				});

			});
		</script>
		<?php
		}

	}
	// initiate the class
	global $wpba_wp_settings_api;
	$wpba_wp_settings_api = new WP_Settings_API_Bootstrap();
