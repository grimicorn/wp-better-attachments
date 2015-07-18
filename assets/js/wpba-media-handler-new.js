// @codekit-prepend "wpba-functions"
// @codekit-prepend "wpba-attachment"
// @codekit-prepend "vendor/jquery.imgareaselect.pack"
// @codekit-prepend "wpba-crop-3.5"
jQuery(function($){
	$(window).load(function(){
		var file_frame,
			saveElem = $('.wpba-saving');

		/**
		* Attach Image
		*/
		// Uploading files
		$('#wpba_attachments_button, #wpba_attachments_button2, #wpba_form_attachments_button').on('click', function( event ){
			event.preventDefault();
			var that = $(this);
			// If the media frame already exists, reopen it.
			if ( file_frame ) {
				file_frame.open();
				return;
			}

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				title: $( this ).data( 'uploader_title' ),
				button: {
					text: 'Add Attachments'//$( this ).data( 'uploader_button_text' )
				},
				multiple: true
			});

			file_frame.on( 'select', function() {
				var attachments = file_frame.state().get('selection').toJSON(),
						ajaxData = {
							attachments: attachments,
							action: 'wpba_add_attachment',
							parentid: $('.wpba').data('postid')
						}
				;
				saveElem.removeClass('hide');
				$.post(ajaxurl, ajaxData, function(data) {
					resp = $.parseJSON(data);
					if ( resp ) {
						$( "#wpba_image_sortable" ).append( resp.image );
						wpba.updateSortOrder($( "#wpba_image_sortable" ));
						wpba.resetClickHandlers();
						$('#wpba_attachments_button2').removeClass('hide');
					}

				});
			});

			// Finally, open the modal
			file_frame.open();
		});

		// Show the 2nd Add Attachments button if there are any attachments
		if ($('.wpba-attachment-item').length) {
			$('#wpba_attachments_button2').removeClass('hide');
		}
	}); // $(window).load()
}(jQuery));
