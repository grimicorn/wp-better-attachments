//@codekit-prepend wpba-functions.js
jQuery(function($){
	$(window).load(function(){
		var sortableImageElem = $( "#wpba_image_sortable" ),
				unattachElem = $('.wpba-unattach'),
				deleteElem = $('.wpba-delete'),
				saveElem = $('.wpba-saving')
		;

		/**
		* Image Sorting
		*/
		sortableImageElem.sortable();
		sortableImageElem.disableSelection();
		sortableImageElem.on( "sortupdate", function( e, ui ) {
			updateSortOrder( sortableImageElem );
		});
		// Update the sort order when the page loads
		// updateSortOrder( sortableImageElem );


		/**
		* Unattach Image
		*/
		unattachElem.on('click', function(e){
			unattachAttachment($(this));
			e.preventDefault();
			return false;
		});


		/**
		* Unattach From Library
		*/
		$('.wpba-unattach-library').on('click', function(e){
			var that = $(this),
					attachmentId = that.data('id'),
					ajaxData = {
						'action' : 'wpba_unattach_',
						'attachmentid' : attachmentId
					};
			$.post(ajaxurl, ajaxData, function(data) {
				var resp = $.parseJSON(data);
				if (resp) {
					that.remove();
					$('#post-'+attachmentId+' .view').empty().append('<a href="http://localhost/~mothership/plugin-dev/?attachment_id='+attachmentId+'" title="View “Hello world!”" rel="permalink">View</a>');
					$('#post-'+attachmentId+' .column-parent').empty().append('(Unattached)<br><a class="hide-if-no-js" onclick="findPosts.open' + "( 'media[]','"+attachmentId+"'"+' ); return false;" href="#the-list">Attach</a>');
				}
			});
			e.preventDefault();
			return false;
		});


		/**
		* Edit Modal
		*/
		if($('#wpba_edit_screen').length > 0 ) {
			var editElem = $('.wpba-edit'),
			editScreen = $('#wpba_edit_screen'),
			editScreenIframe = editScreen.find('iframe'),
			attid;
			// Edit Modal Open
			editElem.on('click',function(e){
				var that = $(this);
				// Add the correct edit link to the iframe
				editScreenIframe.attr('src', that.attr('href'));

				// Once the iframe loads add the required css, add click handler, and show editor
				editScreenIframe.load(function() {
					css = '#adminmenuwrap,' +
								'#adminmenuback,' +
								'#wpadminbar,' +
								'#screen-meta-links,' +
								'#wpfooter,' +
								'.add-new-h2 { display: none; }' +
								'#wpcontent { width: 96%; margin: 0 2%; }';
					editScreenIframe.contents().find("head").append($("<style type='text/css'>"+css+"</style>"));
					attid = editScreenIframe.contents().find("#post_ID").val();

					// This will help with the fouc when updating an attachment
					editScreenIframe.contents().find('#publish').on('click', function(){
						editScreenIframe.hide();
						editScreenIframe.load(function() {
							editScreenIframe.contents().find("head").append($("<style type='text/css'>"+css+"</style>"));
							editScreenIframe.show();
						});
					});

					// Show Screen
					editScreen.show();
				});

				e.preventDefault();
				return false;
			});

			// Edit Modal Close
			$('#wpba_edit_screen_close').on('click', function(e){
				var that = $(this),
						bustCache = '?v=' + Math.round( Math.random()*10000000000000000 );

				editScreen.hide();

				e.preventDefault();
				return false;
			});

		} // editmodal


		/**
		* Delete Attachment
		*/
		deleteElem.on('click', function(e){
			deleteAttachment($(this));
			e.preventDefault();
			return false;
		});

	}); // $(window).load()
}(jQuery));