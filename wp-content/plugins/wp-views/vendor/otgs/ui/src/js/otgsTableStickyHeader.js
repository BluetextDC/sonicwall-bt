/*global jQuery*/

import stickyTableHeaders from 'sticky-table-headers';

window.addEventListener("DOMContentLoaded", () => {

	/**
	 * @param {NodeList} elementS
	 */
	const elements = document.querySelectorAll('.js-otgs-table-sticky-header');
	const args = {
		fixedOffset: jQuery('#wpadminbar')
	};

	/**
	 * @param {Element} element
	 */
	elements.forEach(element => {

		jQuery(element).stickyTableHeaders(args);

	});
});
