// @codekit-prepend wpba-attachment.js
jQuery(function($){
	$(window).load(function(){
		$('#wpba_attachments_button').on('click', function() {
			tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');

			// store send_to_event so at end of function normal editor works
			window.original_send_to_editor = window.send_to_editor;

			// override function so you can have multiple uploaders pre page
			window.send_to_editor = function(html) {
				imgurl = jQuery(html).attr('src');
				if(!imgurl){
					imgurl = jQuery('img', html).attr('src');
				}

				if(!imgurl){
					// might be a file (pdf)
					// let's try this
					imgurl = jQuery(html).attr('href');
				}
				var sortableImageElem = $('#wpba_image_sortable'),
						saveElem = $('.wpba-saving'),
						ajaxData = {
							attachmenturl: imgurl,
							action: 'wpba_add_attachment_old',
							parentid: $('.wpba').data('postid')
						}
				;
				saveElem.removeClass('hide');
				$.post(ajaxurl, ajaxData, function(data) {
					resp = $.parseJSON(data);
					if ( resp ) {
						sortableImageElem.append( resp.image );
						wpba.updateSortOrder(sortableImageElem);
						wpba.resetClickHandlers();
					}
				});

				tb_remove();
				// Set normal uploader for editor
				window.send_to_editor = window.original_send_to_editor;
			};

			return false;
		});
	}); // $(window).load()
}(jQuery));