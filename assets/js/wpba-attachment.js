//@codekit-prepend wpba-functions.js
jQuery(function($){
	$(window).load(function(){
		wpba.resetClickHandlers();
		var settingsCheckBoxElem = $('#wpba-disable-post-types-wrap input[type="checkbox"]'),
				settingsHideElem = $('.wpba-settings-hide'),
				globalSettingsThumbnailElem = $('input[type="checkbox"].wpba-settings-thumbnail'),
				cropEditorSettingsElem = $('input[type="checkbox"].wpba-settings-no_crop_editor'),
				mbGlobalTitleElem = $('input[type="checkbox"].wpba-settings-gmb_title'),
				mbGlobalCaptionElem = $('input[type="checkbox"].wpba-settings-gmb_caption'),
				mbGlobalAttachmentIdElem = $('input[type="checkbox"].wpba-settings-gmb_show_attachment_id'),
				mbGlobalUnattachElem = $('input[type="checkbox"].wpba-settings-gmb_unattach_link'),
				mbGlobalEditElem = $('input[type="checkbox"].wpba-settings-gmb_edit_link'),
				mbGlobalDeleteElem = $('input[type="checkbox"].wpba-settings-gmb_delete_link'),
				emGlobalCaptionElem = $('input[type="checkbox"].wpba-settings-gem_caption'),
				emGlobalAltTextElem = $('input[type="checkbox"].wpba-settings-gem_alternative_text'),
				emGlobalDescriptionElem = $('input[type="checkbox"].wpba-settings-gem_description'),
				gDisableImageElem = $('input[type="checkbox"].wpba-settings-disable_image'),
				gDisableVideoElem = $('input[type="checkbox"].wpba-settings-disable_video'),
				gDisableAudioElem = $('input[type="checkbox"].wpba-settings-disable_audio'),
				gDisableDocElem = $('input[type="checkbox"].wpba-settings-disable_document')
		;

		// Hide Post Options
		settingsHideElem.on('click', function(e){
			var that = $(this),
					elem = that.parents('th').siblings('td')
			;

			if ( that.text() == 'hide' ) {
				elem.hide();
				that.text('show');
			} else {
				elem.show();
				that.text('hide');
			}

			e.preventDefault();
			return false;
		});


		// Settings Checkboxes
		wpba.settingsCheckBoxSetupHandler( gDisableImageElem, '.wpba-settings-pt_disable_image' );
		wpba.settingsCheckBoxSetupHandler( gDisableVideoElem, '.wpba-settings-pt_disable_video' );
		wpba.settingsCheckBoxSetupHandler( gDisableAudioElem, '.wpba-settings-pt_disable_audio' );
		wpba.settingsCheckBoxSetupHandler( gDisableDocElem, '.wpba-settings-pt_disable_document' );
		wpba.settingsCheckBoxSetupHandler( emGlobalCaptionElem, '.wpba-settings-em_caption' );
		wpba.settingsCheckBoxSetupHandler( emGlobalAltTextElem, '.wpba-settings-em_alternative_text' );
		wpba.settingsCheckBoxSetupHandler( emGlobalDescriptionElem, '.wpba-settings-em_description' );
		wpba.settingsCheckBoxSetupHandler( mbGlobalTitleElem, '.wpba-settings-title' );
		wpba.settingsCheckBoxSetupHandler( mbGlobalCaptionElem, '.wpba-settings-caption' );
		wpba.settingsCheckBoxSetupHandler( mbGlobalAttachmentIdElem, '.wpba-settings-mb_show_attachment_id' ); //
		wpba.settingsCheckBoxSetupHandler( mbGlobalUnattachElem, '.wpba-settings-mb_unattach_link' );
		wpba.settingsCheckBoxSetupHandler( mbGlobalEditElem, '.wpba-settings-mb_edit_link' );
		wpba.settingsCheckBoxSetupHandler( mbGlobalDeleteElem, '.wpba-settings-mb_delete_link' );
		wpba.settingsCheckBoxSetupHandler( cropEditorSettingsElem, '.wpba-settings-all_crop_sizes' );
		wpba.settingsCheckBoxSetupHandler( globalSettingsThumbnailElem, '.wpba-settings-mb_thumbnail' );

		// Settings Disable Post Types
		settingsCheckBoxElem.on('change', function(){
			wpba.settingsDisablePostTypes( $(this) );
		}).each(function(){
			wpba.settingsDisablePostTypes( $(this) );
		});

	}); // $(window).load()
}(jQuery));