//@codekit-prepend wpba-functions.js
jQuery(function($){
	$(window).load(function(){
		wpba.resetClickHandlers();

		/**
		* Settings Disable Post Types Change Handler
		*/
		$('#wpba-disable-post-types-wrap input[type="checkbox"]').on('change', function(){
			var that = $(this),
					postTypeOptionsWrap = $('#wpba-'+that.val()+'-settings-wrap'),
					postTypeOptionsSect = postTypeOptionsWrap.parent('td').parent('tr')
			;
			if( that.is(':checked' ) ) {
				postTypeOptionsSect.hide();
			} else {
				postTypeOptionsSect.show();
			}
		});

		/**
		* Settings Disable Post Types Setup
		*/
		$('#wpba-disable-post-types-wrap').parent('td').parent('tr').show();
		$('#wpba-disable-post-types-wrap input[type="checkbox"]').each(function(){
			var that = $(this),
					postTypeOptionsWrap = $('#wpba-'+that.val()+'-settings-wrap'),
					postTypeOptionsSect = postTypeOptionsWrap.parent('td').parent('tr')
			;
			if( that.is(':checked' ) ) {
				postTypeOptionsSect.hide();
			} else {
				postTypeOptionsSect.show();
			}
		});

	}); // $(window).load()
}(jQuery));