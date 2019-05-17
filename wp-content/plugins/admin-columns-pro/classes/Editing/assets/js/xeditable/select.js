'use strict';/**
 * Edit type: Select
 *
 * @since 1.0
 */jQuery.fn.cacie_edit_select=function(column,item){var el=jQuery(this);var value=el.cacie_get_value(column,item);var options=column.editable.options;el.cacie_xeditable({type:'select',value:value,source:cacie_options_format_editable(options)},column,item)};