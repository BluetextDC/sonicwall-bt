'use strict';/**
 * Edit type: Password
 *
 * @since 1.0
 */jQuery.fn.cacie_edit_password=function(column,item){var el=jQuery(this);el.cacie_xeditable({type:'password',value:el.cacie_get_value(column,item)},column,item)};