jQuery(function ($) {
	$('.wpba-img-size-select').each(function(){
		var that = $(this),
				width = that.data('width'),
				height = that.data('height'),
				id = that.data('id'),
				src = that.attr('src'),
				srcw = that.data('srcwidth'),
				srch = that.data('srcheight'),
				cropPoints = that.data('croppoints'),
				x1 = 0,
				x2 = width,
				y1 = 0,
				y2 = height
		;
		if ( cropPoints !== 0 ) {
			points = cropPoints.split(',');
			x1 = parseInt( points[0], 10 );
			x2 = parseInt( points[1], 10 );
			y1 = parseInt( points[2], 10 );
			y2 = parseInt( points[3], 10 );
		};

		that.imgAreaSelect({
			handles: false,
			resizeable: false,
			maxHeight: height,minHeight: height,
			maxWidth: width,minWidth:width,
			x1:x1, y1:y1, x2:x2, y2:y2,
			onSelectEnd: function (img, selection) {
				var ajaxData = {
							src_x: selection.x1, // Start Width
							src_y: selection.y1, // Start Height
							src_h: selection.y2,	// End Height
							orig_w: srcw,
							orig_h: srch,
							final_h: height,
							final_w: width,
							id: id,
							src: src,
							action: 'wpba_image_area_select'
						},
						ajaxurl = 'http://localhost/~mothership/plugin-dev/wp-admin/admin-ajax.php'
				;

				$.post(ajaxurl, ajaxData, function(data){
					// console.log(data);
				});
			}
		});
	})
}(jQuery));