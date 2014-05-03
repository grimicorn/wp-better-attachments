/* global WPBA_ADMIN_JS, tinymce */
jQuery((function($) {
	var meta = {
		wyswig     : {},
		attachment : {}
	};

	/**
	 * Initializes new WYSWIG editors added by AJAX.
	 *
	 * @since   1.4.0
	 *
	 * @return  {Void}
	 */
	meta.wyswig.initNew = function() {

		var wyswigAJAX = $('.wpba-wyswig-input-wrap.ajax'),
				wyswigElem = wyswigAJAX.find('textarea')
		;

		if ( wyswigElem.length === 0 ) {
			return;
		} // if()

		wyswigElem.each(function(index, el) {
			var that           = $(el),
					id             = that.attr('id'),
					editorSelector = '#' + id
			;

			// Init tinymce
			tinymce.init({
				selector: editorSelector,
				menubar : false
			});
		});
	}; // meta.wyswig.initNew()

	/**
	 * Initializes the WYSWIG editor.
	 *
	 * @since   1.4.0
	 *
	 * @return  {Void}
	 */
	meta.wyswig.init = function() {
		var wyswigSelector = '.wpba-wyswig';
		meta.wyswigEditors = $(wyswigSelector);
	}; // meta.wyswig.init()

	/**
	 * Adds attachment ID(s) to the list of currently attached posts.
	 *
	 * @since   1.4.0
	 *
	 * @param   {String}  ids  Comma separated list of ID(s) to add.
	 *
	 * @return  {Boolean}      false
	 */
	meta.attachment.addIDs = function(ids) {
		// Make sure the IDs do not have a trailing comma
		ids = ids.replace(/,\s*$/, "");

		var currentIDs    = meta.sortableElem.data('attachmentids'),
				newIDs = ( currentIDs === '' || typeof currentIDs === 'undefined' ) ? ids : currentIDs + ',' + ids
		;
		meta.sortableElem.data('attachmentids', newIDs);

		return false;
	}; // meta.attachment.addIDs()

	/**
	 * Removes attachment ID from the list of currently attached posts.
	 *
	 * @since   1.4.0
	 *
	 * @param   {String}  id   The ID to remove.
	 *
	 * @return  {Boolean}      false
	 */
	meta.attachment.removeIDs = function(id) {
		var currentIDs = meta.sortableElem.data('attachmentids').toString().split(',');

		$.each(currentIDs, function(index, val) {
			if ( val === id ) {
				currentIDs.splice( index, 1 );
			} // if()
		});

		// Update the IDs
		meta.sortableElem.data('attachmentids', currentIDs.join());

		return false;
	}; // meta.attachment.removeIDs()

	/**
	 * Initializes sorting of the attachments.
	 *
	 * @since   1.4.0
	 *
	 * @return  {Void}
	 */
	meta.attachment.initSorting = function() {
		meta.sortableElem = $( "#wpba_sortable" );

		meta.sortableElem.sortable({
			placeholder : 'ui-state-highlight',
			items       : '.wpba-sortable-item',
			handle      : '.wpba-sort-handle',
			cursor      : "move",
			start       : function( event, ui ) {
				// Makes the box the correct size when sorting
				$('.ui-state-highlight').css({
					'height' : ui.item.outerHeight(),
					'width'  : ui.item.outerWidth(),
				});

				// Handles TinyMCE sorting issue
				meta.sortableElem.find(meta.wyswigEditors).each(function(index, el){
					tinymce.execCommand( 'mceRemoveEditor', false, $(el).attr('id') ); // TinyMCE 4.x
					tinymce.execCommand( 'mceRemoveControl', false, $(el).attr('id') ); // TinyMCE 3.x
				});
			},
			stop: function() {
				// Handles TinyMCE sorting issue
				meta.sortableElem.find(meta.wyswigEditors).each(function(index, el){
						tinymce.execCommand( 'mceAddEditor', true, $(el).attr('id') ); // TinyMCE 4.x
						tinymce.execCommand( 'mceAddControl', true, $(el).attr('id') ); // TinyMCE 3.x
						meta.sortableElem.sortable("refresh");
				});
			},
			update      : function() {
				// Set the correct menu order
				meta.sortableElem.find('.wpba-sortable-item').each(function(index, el) {
					$(el).find('.menu-order-input').val(index + 1);
				});
			}
		});
	}; // meta.attachment.initSorting()

	/**
	 * Handles removing of an attachment item.
	 *
	 * @since   1.4.0
	 *
	 * @param   {Object}   elem  jQuery selector object of either the child of an attachment item or the attachment item to remove.
	 *
	 * @return  {Boolean}  false
	 */
	meta.attachment.remove = function(elem) {
		if ( meta.sortableElem.length === 0 || typeof meta.sortableElem === 'undefined' ) {
			return false;
		} // if()

		// Remove the element
		if ( elem.hasClass('attachment-item') ) {
			elem.remove();
		} else {
			elem.parentsUntil('.attachment-item').remove();
		} // if/else()

		// Refreshes sortable elements
		meta.sortableElem.sortable( 'refresh' );

		return false;
	};

	/**
	 * Retrieves the current post ID if available.
	 *
	 * @since  1.4.0
	 *
	 * @return  {Number|Boolean}  The posts ID if available and false if not.
	 */
	meta.getCurrentPostID = function() {
		var postID = meta.sortableElem.data('postid');
		if ( typeof postID !== 'undefined' ) {
			return postID;
		} // if()

		return false;
	};

	/**
	 * Handles adding an attachment.
	 * @since  1.4.0
	 *
	 * @param  {Array}     attachmentIDs  The IDs of the attachments to add.
	 * @param  {Function}  callback        Optional, function to execute after adding attachments is complete, receives the success/failure as a parameter.
	 *
	 * @return boolean     false
	 */
	meta.attachment.add = function( attachmentIDs, callback ) {
		var currentAttachments = meta.sortableElem.data('attachmentids'),
				ajaxData = {
					postid             : meta.getCurrentPostID(),
					attachmentids      : attachmentIDs,
					currentattachments : currentAttachments.toString().split(','),
					action             : 'wpba_add_attachments'
				}
		;

		$.post( ajaxurl, ajaxData, function(data, textStatus) {
			data = $.parseJSON(data);
			var success            = ( textStatus === 'success' && data.success === true ) ? true : false,
					sortableElemExists = meta.sortableElem.length !== 0 && typeof meta.sortableElem !== 'undefined'
			;

			// Add the new attachments
			if ( success && sortableElemExists && data.html !== '' ) {
				meta.sortableElem.prepend(data.html).sortable('refresh');
				// Reset handlers
				meta.resetEventHandlers();

				// Init any WYSWIG editors
				meta.wyswig.initNew();

				// Add the new post(s) to the  current post id(s)
				var newIDs = ( attachmentIDs.length === 0 ) ? '' : attachmentIDs.join();
				meta.attachment.addIDs(newIDs);
			} // if()

			// Execute optional callback
			if ( typeof callback !== 'undefined' ) {
				callback(success);
			} // if()
		});

		return false;
	}; // meta.attachment.add()

	/**
	 * Handles for when an attachment is added.
	 *
	 * @since   1.4.0
	 *
	 * @return  boolean  false
	 */
	meta.attachment.addHandler = function() {
		WPBA_ADMIN_JS.media.uploader.init($('.wpba-add-link'), function( attachments ) {
			var attachmentIDs = [];
			$.each(attachments, function(index, val) {
				attachmentIDs.push( val.id );
			});

			meta.attachment.add(attachmentIDs);
		});

		return false;
	}; // meta.attachment.addHandler()

	/**
	 * Deletes an attachment.
	 *
	 * @since   1.4.0
	 *
	 * @todo    Allow for multiple meta boxes.
	 *
	 * @param   {Number|String}  id        The attachment ID to delete.
	 * @param   {Function}       callback  Optional, function to execute after delete is complete, receives the success/failure as a parameter.
	 *
	 * @return  {Boolean}         false
	 */
	meta.attachment.delete = function(id, callback) {
		var ajaxParams = {
					action : 'wpba_delete_attachment',
					id     : id,
				}
		;

		// Delete attachment
		$.post(ajaxurl, ajaxParams, function(data, textStatus) {
			var success = ( textStatus === 'success' && $.parseJSON(data) === true ) ? true : false;

			if ( success ) {
				meta.attachment.remove( $('#wpba_attachment_' + id) );
				meta.attachment.removeIDs( id );
			} // if()

			// Execute optional callback
			if ( typeof callback !== 'undefined' ) {
				callback(success);
			} // if()
		});

		return false;
	}; // meta.attachment.delete()

	/**
	 * Delete link event click handler.
	 *
	 * @since   1.4.0
	 *
	 * @param   {Object}   elem  Optional, jQuery selector object.
	 *
	 * @return  {Boolean}        false
	 */
	meta.attachment.deleteHandler = function(elem) {
		elem = ( typeof elem === 'undefined' ) ? $('.wpba-delete-link').not('.wpba-has-delete-handler') : elem.not('.wpba-has-delete-handler');

		elem.on('click', function(e) {
			e.preventDefault();

			// Make sure this is not an accident.
			var makeSure = confirm('Are you sure you want to permanently delete this attachment?');
			if ( ! makeSure ) {
				return false;
			} // if()

			// Delete the attachment
			var attachmentId = $(this).attr('id').replace('wpba_delete_', '');
			meta.attachment.delete( attachmentId );

			return false;
		});

		// Add class for delete handler
		elem.addClass('wpba-has-delete-handler');

		return false;
	}; // meta.attachment.deleteHandler()

	/**
	 * Unattaches an attachment.
	 *
	 * @since   1.4.0
	 *
	 * @todo    Allow for multiple meta boxes.
	 *
	 * @param   {Number|String}  id        The attachment ID to unattach.
	 * @param   {Function}       callback  Optional, function to execute after unattach is complete, receives the success/failure as a parameter.
	 *
	 * @return  {Boolean}                  false
	 */
	meta.attachment.unattach = function(id, callback) {
		var ajaxParams = {
			action : 'wpba_unattach_attachment',
			id     : id,
		};

		// Unattach attachment
		$.post(ajaxurl, ajaxParams, function(data, textStatus) {
			var success = ( textStatus === 'success' && $.parseJSON(data) === true ) ? true : false;

			if ( success ) {
				meta.attachment.remove( $('#wpba_attachment_' + id) );
				meta.attachment.removeIDs( id );
			} // if()

			// Execute optional callback
			if ( typeof callback !== 'undefined' ) {
				callback(success);
			} // if()
		});

		return false;
	}; // meta.attachment.unattach()

	/**
	 * Unattach link event click handler.
	 *
	 * @since   1.4.0
	 *
	 * @param   {Object}   elem  Optional, jQuery selector object.
	 *
	 * @return  {Boolean}        false
	 */
	meta.attachment.unattachHandler = function(elem) {
		elem = ( typeof elem === 'undefined' ) ? $('.wpba-unattach-link').not('.wpba-has-unattach-handler') : elem.not('.wpba-has-unattach-handler');

		elem.on('click', function(e) {
			e.preventDefault();

			// Unattach the attachment
			var attachmentId = $(this).attr('id').replace('wpba_unattach_', '');
			meta.attachment.unattach( attachmentId );

			return false;
		});

		// Add class for unattach handler
		elem.addClass('wpba-has-unattach-handler');

		return false;
	}; // meta.attachment.unattachHandler()

	/**
	 * All of the meta event handlers.
	 *
	 * @since   1.4.0
	 *
	 * @param   {Object}   elem  Optional, jQuery selector object.
	 *
	 * @return  {Boolean}        false
	 */
	meta.resetEventHandlers = function(elem) {
		// Unattach Attachment Handler
		meta.attachment.unattachHandler(elem);

		// Delete Attachment Handler
		meta.attachment.deleteHandler(elem);

		// Refreshes sortable elements
		meta.sortableElem.sortable( 'refresh' );

		return false;
	}; // meta.resetEventHandlers()

	/**
	 * Initialize the meta box.
	 *
	 * @since   1.4.0
	 *
	 * @return  {Void}
	 */
	meta.init = function() {
		meta.wyswig.init();
		meta.attachment.initSorting();
		meta.attachment.addHandler();
		meta.resetEventHandlers();
	}; // meta.init()

	/**
	 * Document Ready
	 */
	$(document).ready(function() {
		meta.init();
	});

	// Allow other scripts to have access to meta methods/properties.
	WPBA_ADMIN_JS.meta = meta;
})(jQuery));