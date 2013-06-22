<?php
/**
* WPBA Settings Check
*
* @since 1.3.5
*/
class WPBA_Settings_Check extends WP_Better_Attachments
{

	function __construct( $config = array() )
	{
		parent::__construct();
	}

	/**
	* Check if disabled in settings
	*
	* @return boolean
	* @since 1.3.5
	*/
	public function not_disabled()
	{
		if ( is_admin() ) return;
		// pp($this->current_post_obj);

		// pp($this->current_post_type);
		// pp($this->current_post_type_obj);
		// pp($this->current_post_type_settings);
		// pp($this->current_post_type_disabled_file_types);
		// pp($this->global_settings);
		// pp($this->disabled_post_types);
		// pp($this->media_table_settings);
		// pp($this->meta_box_settings);
		// pp($this->edit_modal_settings);
		// pp($this->disabled_file_types);
	} // not_disabled()

	// public function post_type_disabled();
} // class

// initiate the class
global $wpba_settings_check;
$wpba_settings_check = new WPBA_Settings_Check();