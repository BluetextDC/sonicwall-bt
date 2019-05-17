'use strict';/**
 * Edit type: Float
 *
 * @since 1.0
 */jQuery.fn.cacie_edit_float=function(column,item){var el=jQuery(this);el.cacie_xeditable({type:'text',value:el.cacie_get_value(column,item),validate:function validate(value){if(value&&!cacie_is_float(value)){return ACP_Editing.i18n.errors.invalid_float}}},column,item)};