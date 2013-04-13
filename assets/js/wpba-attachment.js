//@codekit-prepend wpba-functions.js
jQuery(function($){
	$(window).load(function(){
		updateSortOrderClickHandler();
		unattachAttachmentClickHandler();
		unattachAttachmentLibraryClickHandler();
		deleteAttachmentClickHandler();
		editModalClickHandler();
	}); // $(window).load()
}(jQuery));