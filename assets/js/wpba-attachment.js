//@codekit-prepend wpba-functions.js
jQuery(function($){
	$(window).load(function(){
		wpbawpbaUpdateSortOrderClickHandler();
		wpbawpbaUnattachAttachmentClickHandler();
		wpbawpbaUnattachAttachmentLibraryClickHandler();
		wpbawpbaDeleteAttachmentClickHandler();
		wpbaEditModalClickHandler();
		wpbaUpdatePostMetaClickHandler();
	}); // $(window).load()
}(jQuery));