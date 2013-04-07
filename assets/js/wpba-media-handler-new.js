jQuery(function($){
	$(window).load(function(){
		var sortableElem = $( "#wpba_sortable" ),
				unattachElem = $('.wpba-unattach'),
				deleteElem = $('.wpba-delete'),
				file_frame
		;
		/**
		* Update Sort Order
		*/
		function updateSortOrder(elem) {
			var sortLi = elem.find('li'),
					sortOrder = [],
					ajaxData = { 'action' : 'wpba_update_sort_order'}
			;
			sortLi.each(function() {
				var that = $(this);
				sortOrder.push(that.data('id'));
			});

			ajaxData.attids = sortOrder;
			$.post(ajaxurl, ajaxData, function(data) {
				return false;
			});
		}

		/**
		* Image Sorting
		*/
		sortableElem.sortable();
		sortableElem.disableSelection();
		sortableElem.on( "sortupdate", function( e, ui ) {
			updateSortOrder( sortableElem );
		});
		// Update the sort order when the page loads
		updateSortOrder( sortableElem );


		/**
		* Unattach Image
		*/
		function unattachAttachment(that) {
			var linkParent = that.parent('li').parent('ul'),
					attachmentId = linkParent.data('id'),
					ajaxData = {
						'action' : 'wpba_unattach_image',
						'attachmentid' : attachmentId
					};
			$.post(ajaxurl, ajaxData, function(data) {
				var resp = $.parseJSON(data);
				if (resp) {
					linkParent.parent('li').remove();
					updateSortOrder( sortableElem );
				}
			});
		}
		unattachElem.on('click', function(e){
			unattachAttachment($(this));
			e.preventDefault();
			return false;
		});


		/**
		* Attach Image
		*/
		// Uploading files
		$('#wpba_attachments_button').on('click', function( event ){

			event.preventDefault();

			// If the media frame already exists, reopen it.
			if ( file_frame ) {
				file_frame.open();
				return;
			}

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				title: $( this ).data( 'uploader_title' ),
				button: {
					text: $( this ).data( 'uploader_button_text' )
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
				console.log(attachments);
				$.post(ajaxurl, ajaxData, function(data) {
					resp = $.parseJSON(data);
					if ( resp ) {
						sortableElem.append(resp);
						// Apply unattach click handlers to new elements
						$('.wpba-unattach').on('click', function(e){
							unattachAttachment($(this));
							e.preventDefault();
							return false;
						});
					}

				});

				// Do something with attachment.id and/or attachment.url here
			});

			// Finally, open the modal
			file_frame.open();
		});

		/**
		* Delete Image
		*/
		function deleteAttachment(that) {
			var makeSure = confirm("Are you sure you want to permanently delete this attachment? This will permanently remove the attachment from the media gallery!!");
			if ( makeSure ) {
				var linkParent = that.parent('li').parent('ul'),
						attachmentId = linkParent.data('id'),
						ajaxData = {
							'action' : 'wpba_delete_attachment',
							'attachmentid' : attachmentId
						};
				$.post(ajaxurl, ajaxData, function(data) {
					var resp = $.parseJSON(data);
					if (resp) {
						linkParent.parent('li').remove();
						updateSortOrder( sortableElem );
					}
				});
			}
		}

		deleteElem.on('click', function(e){
			deleteAttachment($(this));
			e.preventDefault();
			return false;
		});

	}); // $(window).load()
}(jQuery));