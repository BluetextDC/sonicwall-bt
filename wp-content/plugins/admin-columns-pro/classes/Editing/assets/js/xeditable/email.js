'use strict';/**
 * Edit type: Email
 *
 * @since 1.0
 */jQuery.fn.cacie_edit_email=function(column,item){var el=jQuery(this);el.cacie_xeditable({type:'email',value:el.cacie_get_value(column,item)},column,item)};