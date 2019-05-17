'use strict';/**
 * Edit type: togglable
 *
 * @since 1.0
 */jQuery.fn.cacie_edit_togglable=function(column,item){var el=jQuery(this);var options=column.editable.options;// Toggle on click
jQuery(this).on('click',function(){if(!window.cacie_edit_enabled||!options){return}var currentvalue=el.cacie_get_value(column,item);var num_values=options.length;var current_index=0;var newvalue=void 0;for(var i in options){if(options.hasOwnProperty(i)&&currentvalue===options[i].label){current_index=options[i].value;break}}if(typeof column.editable.required!=='undefined'&&column.editable.required){if(current_index!==0){el.cacie_show_message(ACP_Editing.i18n.errors.field_required);return}}newvalue=options[(current_index+1)%num_values].label;// Save column
el.cacie_savecolumn(column,item,newvalue,true)})};