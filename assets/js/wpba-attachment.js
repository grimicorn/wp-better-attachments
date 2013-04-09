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
		* Delete Attachment
		*/
		deleteElem.on('click', function(e){
			deleteAttachment($(this));
			e.preventDefault();
			return false;
		});

	}); // $(window).load()
}(jQuery));