/**
* Updates Sort Order
*/
function updateSortOrder(elem) {
	var $ = jQuery,
			sortLi = elem.find('li'),
			sortOrder = [],
			ajaxData = { 'action' : 'wpba_update_sort_order'},
			saveElem = $('.wpba-saving')
	;
	sortLi.each(function() {
		var that = $(this);
		sortOrder.push(that.data('id'));
	});

	ajaxData.attids = sortOrder;
	saveElem.removeClass('hide');
	$.post(ajaxurl, ajaxData, function(data) {
		saveElem.addClass('hide');
		return false;
	});
}

/**
* Unattachs an attachment
*/
function unattachAttachment(that) {
	var $ = jQuery,
			sortableElem = $( "#wpba_sortable" ),
			linkParent = that.parent('li').parent('ul'),
			attachmentId = linkParent.data('id'),
			ajaxData = {
				'action' : 'wpba_unattach_image',
				'attachmentid' : attachmentId
			},
			saveElem = $('.wpba-saving')
	;
	saveElem.removeClass('hide');
	$.post(ajaxurl, ajaxData, function(data) {
		var resp = $.parseJSON(data);
		if (resp) {
			linkParent.parent('li').remove();
			updateSortOrder( sortableElem );
		}
	});
}

/**
* Deletes and attachment
*/
function deleteAttachment(that) {
	var $ = jQuery,
			makeSure = confirm("Are you sure you want to permanently delete this attachment? This will permanently remove the attachment from the media gallery!!"),
			saveElem = $('.wpba-saving')
	;
	if ( makeSure ) {
		var linkParent = that.parent('li').parent('ul'),
				attachmentId = linkParent.data('id'),
				ajaxData = {
					'action' : 'wpba_delete_attachment',
					'attachmentid' : attachmentId
				};
		saveElem.removeClass('hide');
		$.post(ajaxurl, ajaxData, function(data) {
			var resp = $.parseJSON(data);
			if (resp) {
				linkParent.parent('li').remove();
				updateSortOrder( sortableElem );
			}
		});
	}
}

jQuery(function($){
	$(window).load(function(){
		var sortableElem = $( "#wpba_sortable" ),
				unattachElem = $('.wpba-unattach'),
				deleteElem = $('.wpba-delete'),
				saveElem = $('.wpba-saving')
		;

		/**
		* Image Sorting
		*/
		sortableElem.sortable();
		sortableElem.disableSelection();
		sortableElem.on( "sortupdate", function( e, ui ) {
			updateSortOrder( sortableElem );
		});
		// Update the sort order when the page loads
		// updateSortOrder( sortableElem );


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
						'action' : 'wpba_unattach_image',
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