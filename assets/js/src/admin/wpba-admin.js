/* global WPBA_ADMIN_JS */
jQuery((function($) {
	var meta = {};

	/**
	 * Initializes sorting of the attachments.
	 *
	 * @return  {void}
	 */
	meta.initSorting = function() {
		meta.sortableElem.sortable({
			placeholder : 'ui-state-highlight',
			items       : '.wpba-sortable-item'
		});
	}; // meta.initSorting()

	/**
	 * Initialize the meta box.
	 *
	 * @return  {void}
	 */
	meta.init = function() {
		meta.sortableElem = $( "#wpba_sortable" );
		meta.initSorting();
	};

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
