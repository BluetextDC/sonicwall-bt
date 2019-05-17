'use strict';/**
 * Edit type: Number
 *
 * @since 1.0
 */jQuery.fn.cacie_edit_number=function(column,item){var el=jQuery(this);el.cacie_xeditable({type:'number',value:el.cacie_get_value(column,item)},column,item)};