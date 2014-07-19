<div>
	<h2>WBA Settings</h2>
	<form action="options.php" method="post">
		<?php settings_fields( 'wpba_options' ); ?>
		<?php do_settings_sections( 'wpba' ); ?>
		<input name="Submit" type="submit" class="button button-primary" value="<?php esc_attr_e( 'Save Settings' ); ?>">
	</form>
</div>