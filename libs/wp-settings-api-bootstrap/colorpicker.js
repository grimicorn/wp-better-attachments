jQuery(document).ready(function() {
    var color_inputs = jQuery("input.popup-colorpicker");

    color_inputs.each(function(i) {
        jQuery(this).after('<div id="picker-' + i + '" style="z-index: 1000; background: #EEE; border: 1px solid #CCC; position: absolute; display: block;"></div>');
        jQuery('#picker-' + i).hide().farbtastic(jQuery(this));
    })
    .focus(function() {
        jQuery(this).next().show();
    })
    .blur(function() {
        jQuery(this).next().hide();
    });
});


