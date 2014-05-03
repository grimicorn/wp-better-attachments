/* global WPBA_ADMIN_JS */
/* global tinymce */
jQuery((function($) {
	var meta = {
		wyswig : {}
	};

	/**
	 * Initializes new WYSWIG editors added by AJAX.
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
	 * @return  {Void}
	 */
	meta.wyswig.init = function() {
		var wyswigSelector = '.wpba-wyswig';
		meta.wyswigEditors = $(wyswigSelector);
	}; // meta.wyswig.init()

	/**
	 * Initializes sorting of the attachments.
	 *
	 * @since   1.4.0
	 *
	 * @return  {Void}
	 */
	meta.initSorting = function() {
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
	}; // meta.initSorting()

	/**
	 * Handles removing of an attachment item.
	 *
	 * @since   1.4.0
	 *
	 * @param   {Object}   elem  jQuery selector object of either the child of an attachment item or the attachment item to remove.
	 *
	 * @return  {Boolean}  false
	 */
	meta.removeAttachmentItem = function(elem) {
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
	 * @since  1.4.0
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
	 * @param  {Array}     attachmentIDs  The IDs of the attachments to add.
	 * @param  {Function}  callback        Optional, function to execute after adding attachments is complete, receives the success/failure as a parameter.
	 *
	 * @return boolean     false
	 */
	meta.add = function( attachmentIDs, callback ) {
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
				// Init any WYSWIG editors
				meta.wyswig.initNew();
			} // if()

			// Execute optional callback
			if ( typeof callback !== 'undefined' ) {
				callback(success);
			} // if()
		});

		return false;
	}; // meta.add()

	/**
	 * Handles for when an attachment is added.
	 *
	 * @since   1.4.0
	 *
	 * @return  boolean  false
	 */
	meta.addHandler = function() {
		WPBA_ADMIN_JS.media.uploader.init($('.wpba-add-link'), function( attachments ) {
			var attachmentIDs = [];
			$.each(attachments, function(index, val) {
				attachmentIDs.push( val.id );
			});

			meta.add(attachmentIDs);
		});

		return false;
	}; // meta.addHandler()

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
	meta.delete = function(id, callback) {
		var ajaxParams = {
					action : 'wpba_delete_attachment',
					id     : id,
				}
		;

		// Delete attachment
		$.post(ajaxurl, ajaxParams, function(data, textStatus) {
			var success = ( textStatus === 'success' && $.parseJSON(data) === true ) ? true : false;

			if ( success ) {
				meta.removeAttachmentItem( $('#wpba_attachment_' + id) );
			} // if()

			// Execute optional callback
			if ( typeof callback !== 'undefined' ) {
				callback(success);
			} // if()
		});

		return false;
	}; // meta.delete()

	/**
	 * Delete link event click handler.
	 *
	 * @since   1.4.0
	 *
	 * @param   {Object}   elem  Optional, jQuery selector object.
	 *
	 * @return  {Boolean}        false
	 */
	meta.deleteHandler = function(elem) {
		elem = ( typeof elem === 'undefined' ) ? $('.wpba-delete-link') : elem;

		elem.on('click', function(e) {
			e.preventDefault();

			// Make sure this is not an accident.
			var makeSure = confirm('Are you sure you want to permanently delete this attachment?');
			if ( ! makeSure ) {
				return false;
			} // if()

			// Delete the attachment
			var attachmentId = $(this).attr('id').replace('wpba_delete_', '');
			meta.delete( attachmentId );

			return false;
		});

		return false;
	}; // meta.deleteHandler()

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
	 * @return  {Boolean}            false
	 */
	meta.unattach = function(id, callback) {
		var ajaxParams = {
			action : 'wpba_unattach_attachment',
			id     : id,
		};

		// Unattach attachment
		$.post(ajaxurl, ajaxParams, function(data, textStatus) {
			var success = ( textStatus === 'success' && $.parseJSON(data) === true ) ? true : false;

			if ( success ) {
				meta.removeAttachmentItem( $('#wpba_attachment_' + id) );
			} // if()

			// Execute optional callback
			if ( typeof callback !== 'undefined' ) {
				callback(success);
			} // if()
		});

		return false;
	}; // meta.unattach()

	/**
	 * Unattach link event click handler.
	 *
	 * @since   1.4.0
	 *
	 * @param   {Object}   elem  Optional, jQuery selector object.
	 *
	 * @return  {Boolean}        false
	 */
	meta.unattachHandler = function(elem) {
		elem = ( typeof elem === 'undefined' ) ? $('.wpba-unattach-link') : elem;

		elem.on('click', function(e) {
			e.preventDefault();

			// Unattach the attachment
			var attachmentId = $(this).attr('id').replace('wpba_unattach_', '');
			meta.unattach( attachmentId );

			return false;
		});

		return false;
	}; // meta.unattachHandler()

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
		meta.unattachHandler(elem);

		// Delete Attachment Handler
		meta.deleteHandler(elem);

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
		meta.initSorting();
		meta.addHandler();
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

/**
 * Avoid `console` errors in browsers that lack a console.
 */
(function() {
	var method,
			noop    = function() {},
			methods = [
				'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
				'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
				'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
				'timeStamp', 'trace', 'warn'
			],
			length  = methods.length,
			console = ( window.console = window.console || {} )
	;

	while ( length-- ) {
		method = methods[length];

		// Only stub undefined methods.
		if (!console[method]) {
			console[method] = noop;
		}
	}
}());
