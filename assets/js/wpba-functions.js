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
			sortableImageElem = $( "#wpba_image_sortable" ),
			linkParent = that.parent('li').parent('ul'),
			attachmentId = linkParent.data('id'),
			ajaxData = {
				'action' : 'wpba_unattach_',
				'attachmentid' : attachmentId
			},
			saveElem = $('.wpba-saving')
	;
	saveElem.removeClass('hide');
	$.post(ajaxurl, ajaxData, function(data) {
		var resp = $.parseJSON(data);
		if (resp) {
			linkParent.parent('li').remove();
			updateSortOrder( sortableImageElem );
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
				updateSortOrder( sortableImageElem );
			}
		});
	}
}