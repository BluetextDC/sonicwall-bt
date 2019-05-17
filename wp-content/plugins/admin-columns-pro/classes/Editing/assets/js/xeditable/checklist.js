'use strict';/**
 * Edit type: taxonomy
 *
 * @since 1.0
 */jQuery.fn.cacie_edit_checklist=function(column,item){var $=jQuery;var el=$(this);var value=el.cacie_get_value(column,item);var options=column.editable.options;// e.g. no terms available
if('false'===value){value=''}el.cacie_xeditable({type:'checklist',value:value,source:cacie_options_format_editable(options)},column,item)};/**
 * Edit type: Checkbox list
 *
 * @since 1.0
 */jQuery.fn.cacie_edit_checkboxlist=function(column,item){var el=jQuery(this);el.cacie_xeditable({type:'checklist'},column,item)};