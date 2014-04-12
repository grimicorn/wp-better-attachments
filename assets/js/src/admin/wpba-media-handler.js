/* global WPBA_ADMIN_JS */
jQuery((function($) {
	var media = {
		uploader : {
			fileFrame : undefined
		}
	};

	/**
	 * Opens the media uploader and handles the upload.
	 *
	 * @param  {object}    elem      The element that was clicked to open the uploader.
	 * @param  {Function}  callback  Optional, function to execute once complete, receives data about the uploaded attachments.
	 */
	media.uploader.open = function(elem, callback) {
		// If the media frame already exists, reopen it.
		if ( media.uploader.fileFrame ) {
			media.uploader.fileFrame.open();
			return;
		} // if()

		// Create the media frame.
		var titleText      = elem.attr('title'),
				title          = ( typeof titleText === 'undefined' ) ? 'Add Attachment(s)' : titleText,
				buttonLinkText = elem.find('.wpba-add-link-title').text(),
				buttonText     = ( typeof buttonLinkText === 'undefined' ) ? title : buttonLinkText
		;
		media.uploader.fileFrame = wp.media({
			title    : title,
			button   : { text: buttonText },
			multiple : true
		});

		// Upload handler
		media.uploader.fileFrame.on( 'select', function() {
			var attachments = media.uploader.fileFrame.state().get('selection').toJSON();

			if ( typeof callback !== 'undefined' ) {
				callback( $.parseJSON( attachments ) );
			} // if()
		});

		// Finally, open the modal
		media.uploader.fileFrame.open();
	};

	/**
	 * Add attachment link click handler.
	 *
	 * @param  {object}    elem      Optional, jQuery selector object to apply the handler to.
	 * @param  {Function}  callback  Optional, function to execute once complete.
	 */
	media.uploader.addAttachmentHandler = function(elem, callback) {
		elem = ( typeof activatorElem === 'undefined' ) ? $('.wpba-add-link') : elem;

		// Uploading files
		elem.on('click', function( e ){
			e.preventDefault();

		media.uploader.open( $(this), callback );

			return false;
		});
	};

	media.uploader.init = function(activatorElem, callback) {
		media.uploader.addAttachmentHandler(activatorElem, callback);
	};

	/**
	 * Window load
	 */
	$(window).load(function(){
		media.uploader.init($('.wpba-add-link'), function(attachments) {
			console.log(attachments);
		});
	}); // $(window).load()

	// Allow other scripts to have access to media uploader methods/properties.
	WPBA_ADMIN_JS.media = media;
})(jQuery));