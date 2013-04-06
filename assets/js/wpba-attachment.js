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