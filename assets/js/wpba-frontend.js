//@codekit-prepend vendor/jquery.flexslider.js
jQuery(function($){

	// Instantiate FlexSlider
	$(window).load(function() {
		if ( $('.wpba-flexslider').length > 0 ) {
			var	sliderElem = $('.wpba-flexslider'),
					sliderProperties = sliderElem.data( 'sliderproperties' )
			;

			sliderElem.flexslider( sliderProperties );
		}
	});

}(jQuery));