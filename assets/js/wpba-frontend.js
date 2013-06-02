//@codekit-prepend vendor/jquery.flexslider.js
// Can also be used with $(document).ready()
jQuery(function($){

	// Instantiate FlexSlider
	$(window).load(function() {
		var	sliderElem = $('.wpba-flexslider'),
				sliderProperties = sliderElem.data( 'sliderproperties' )
		;
		sliderProperties.animation = "slide"
		sliderElem.flexslider( sliderProperties );
	});

}(jQuery));