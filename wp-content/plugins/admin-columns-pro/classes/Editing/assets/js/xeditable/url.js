'use strict';/**
 * Edit type: URL
 *
 * @since 3.6
 */jQuery.fn.cacie_edit_url=function(column,item){var el=jQuery(this);el.cacie_xeditable({type:'url',value:el.cacie_get_value(column,item)},column,item)};