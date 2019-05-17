'use strict';/**
 * Edit type: Textarea
 *
 * @since 1.0
 */jQuery.fn.cacie_edit_textarea=function(column,item){var el=jQuery(this);el.cacie_xeditable({type:'textarea',rows:10,value:el.cacie_get_value(column,item)},column,item)};