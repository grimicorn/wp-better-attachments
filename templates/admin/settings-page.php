<div>
	<h2>WP Better Attachments <?php echo esc_attr( WPBA_VERSION ); ?> Settings</h2>
	<form action="options.php" method="post">
		<?php settings_fields( 'wpba_options' ); ?>
		<?php do_settings_sections( 'wpba' ); ?>
		<input name="Submit" type="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Settings' ); ?>">
	</form>
</div>