jQuery(function($){
	$(window).load(function(){
		var sortableElem = $( "#wpba_sortable" ),
				unattachElem = $('.wpba-unattach')
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
		unattachElem.on('click', function(e){
			var that = $(this),
					linkParent = that.parent('li'),
					attachmentId = linkParent.data('id'),
					ajaxData = {
						'action' : 'wpba_unattach_image',
						'attachmentid' : attachmentId
					};
			$.post(ajaxurl, ajaxData, function(data) {
				var resp = $.parseJSON(data);
				if (resp) {
					linkParent.remove();
					updateSortOrder( sortableElem );
				}
			});
			e.preventDefault();
			return false;
		});

		// @codekit-prepend "wpba-attachment-sorting.js"
		// // Create the media frame.
		// file_frame = wp.media.frames.file_frame = wp.media({
		// 	title: jQuery( this ).data( 'uploader_title' ),
		// 	button: {
		// 		text: jQuery( this ).data( 'uploader_button_text' ),
		// 	},
		// 	multiple: true  // Set to true to allow multiple files to be selected
		// });
		// // When an image is selected, run a callback.
		// file_frame.on( 'select', function() {

		// var selection = file_frame.state().get('selection');

		// selection.map( function( attachment ) {

		// 	attachment = attachment.toJSON();

		// 	// Do something with attachment.id and/or attachment.url here
		// });
		// });
	}); // $(window).load()
}(jQuery));