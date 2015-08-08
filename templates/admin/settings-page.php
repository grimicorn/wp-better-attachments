<?php
/**
 * WPBA Settings page
 *
 * @version      2.0.0
 *
 * @package      WordPress
 * @subpackage   WPBA
 *
 * @since        2.0.0
 *
 * @author       Dan Holloran          <dholloran@matchboxdesigngroup.com>
 *
 * @copyright    2013 - Present         Dan Holloran
 */

global $wpba_settings;
?>
<div>
	<h2>WP Better Attachments <?php echo esc_attr( WPBA_VERSION ); ?> Settings</h2>
	<form action="options.php" method="post">
		<?php settings_fields( $wpba_settings->option_group ); ?>
		<?php do_settings_sections( 'wpba' ); ?>
		<input name="Submit" type="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Settings' ); ?>">
	</form>
</div>
