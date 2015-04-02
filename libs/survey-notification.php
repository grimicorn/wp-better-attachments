<?php
/**
 * Sets the dismiss status for a warning for the current user.
 *
 * <code>
 * <a href="{$current_page_url}?warning_get_var_key=0"
 * add_action( 'admin_init', 'wpba_warning_dismiss' );
 * </code>
 *
 * @since   1.1.1
 *
 * @return  void
 */
function wpba_warning_dismiss() {
	global $current_user;
	$user_id = $current_user->ID;

	// The possible warning $_GET var key.
	// @todo possibly global or if moved into class make it a property.
	$warning_get_var_keys = array(
		'wpba_survey_notification',
	);

	// If user clicks to dismiss the notice, add that to their user meta
	foreach ( $warning_get_var_keys as $key_value ) {
		if ( isset($_GET[$key_value]) && '0' == $_GET[$key_value] ) {
			add_user_meta( $user_id, "_{$key_value}_dismiss_warning", 'true' );
		} // if()
	} // foreach()
} // wpba_warning_dismiss()
add_action( 'admin_init', 'wpba_warning_dismiss' );



/**
 * Checks if the user has dismissed the current warning.
 *
 * <code>
 * if ( wpba_warning_has_been_dismissed( 'warning_key' ) ) {
 * 	return;
 * }
 *
 * @todo solve the warning aka update-nag class better than injecting a style into the element.
 *
 * @param   string   $key  The key value of the current warning.
 *
 * @return  boolean        If the user has dismissed the warning.
 */
function wpba_warning_has_been_dismissed( $key ) {
	global $current_user;
	$user_id        = $current_user->ID;
	$user_dismissed = get_user_meta( $user_id, "_{$key}_dismiss_warning", true );

	if ( $user_dismissed != '' or $user_dismissed == 'true' ) {
		return true;
	} // if()

	return false;
} // wpba_warning_has_been_dismissed()


function wpba_alert_wrap( $alert_content, $class = 'error', $alert_key = '' ) {
	if ( $alert_content == '' or wpba_warning_has_been_dismissed( $alert_key ) ) {
		return '';
	} // if()

	// Build get parameter string
	$get_params = '';
	foreach ( $_GET as $key => $value) {
		$value = urlencode( $value );
		$get_params = "{$get_params}{$key}={$value}&";
	} // foreach

	$disable_url  = "?{$get_params}{$alert_key}=0";
	$disable_link = "<a href='{$disable_url}' style='float:right;'>Dismiss</a>";
	return "<div class='{$class}'><p>{$alert_content} {$disable_link}</p></div>";
} // wpba_alert_wrap()



/**
 * Display an error message when the blog is set to private.
 *
 * <code>add_action( 'wpba_notifcations_init', 'wpba_is_survey_notification' );</code>
 *
 * @since 1.0.0
 *
 * @return void
 */
function wpba_is_survey_notification() {
	$alert  = 'Thank you for updating WP Better Attachments! ';
	$alert .= '<br>';
	$alert .= '<br>';
	$alert .= sprintf( __( 'I would greatly appreciate it if you could take this short %sSurvey%s about WP Better Attachments, thanks.', 'wpba' ), '<a href="https://www.surveymonkey.com/s/K9LSWYX" target="_blank">', '</a>' );
	$alert .= sprintf( __( ' You can read more about the future of WP Better Attachments %shere%s', 'wpba' ), '<a href="http://danholloran.ghost.io/wpba-thoughts-and-road-map/" target="_blank">', '</a>' );
	echo wp_kses( wpba_alert_wrap( $alert, 'updated', 'wpba_survey_notification' ), 'post' );
} // wpba_is_survey_notification()
add_action( 'wpba_notifcations_init', 'wpba_is_survey_notification' );




/**
 * Plugin setup
 *
 * <code>add_action( 'admin_head', 'wpba_admin_check' );</code>
 *
 * @since 1.0.0
 *
 * @return  void
 */
function wpba_admin_check() {
	if ( ! current_user_can( 'install_plugins' ) ) {
		return;
	}

	// To disable screens from displaying the alerts add the current screens base to the disabled screens
	$current_screen   = get_current_screen();
	$disabled_screens = array(
		'plugin-install',
		'update',
	);

	if ( in_array( $current_screen->base, $disabled_screens ) ) {
		return;
	} // if()

	do_action( 'wpba_notifcations_init' );
} // wpba_admin_check()
add_action( 'admin_head', 'wpba_admin_check' );