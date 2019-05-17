'use strict';/**
 * Edit type: Text
 *
 * @since 1.0
 */jQuery.fn.cacie_edit_text=function(column,item){var el=jQuery(this);jQuery(this).cacie_xeditable({type:'text',value:el.cacie_get_value(column,item)},column,item)};