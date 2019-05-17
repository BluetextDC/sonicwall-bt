'use strict';jQuery.fn.cacie_edit_media=function(column,item){jQuery(this).cacie_edit_attachment(column,item)};/**
 * Edit type: media
 *
 * @since 1.0
 */jQuery.fn.cacie_edit_attachment=function(column,item){var $=jQuery;var el=$(this);// Media upload
el.on('click',function(e){e.preventDefault();if(!window.cacie_edit_enabled){return}var current_selection=el.cacie_get_value(column,item);if(!$.isArray(current_selection)){current_selection=[current_selection]}var args={multiple:typeof column.editable.multiple!=='undefined'&&column.editable.multiple,title:ACP_Editing.i18n.media};if(typeof column.editable.attachment!=='undefined'&&typeof column.editable.attachment.library!=='undefined'){args.library={};if(typeof column.editable.attachment.library.uploaded_to_post!=='undefined'){args.library.uploadedTo=item.ID}if(typeof column.editable.attachment.library.type!=='undefined'){args.library.type=column.editable.attachment.library.type}// Title
if('image'===column.editable.attachment.library.type){args.title=ACP_Editing.i18n.image}if('audio'===column.editable.attachment.library.type){args.title=ACP_Editing.i18n.audio}}// Merge with column type-specific arguments
if('js'in column.editable){args=$.extend(args,column.editable.js)}// Init
var uploader=wp.media(args);// Add current selection
uploader.on('open',function(){var selection=uploader.state().get('selection');current_selection.forEach(function(id){var attachment=wp.media.attachment(id);attachment.fetch();selection.add(attachment?[attachment]:[])})});// Store selection
uploader.on('select',function(){var selection=uploader.state().get('selection').toJSON();var multiple=uploader.options.multiple;// multiple attachments
var attachment_ids=[];for(var k in selection){if(selection.hasOwnProperty(k)){var attachment=selection[k];attachment_ids.push(attachment.id)}}// Single attachment ( integer )
if(1===attachment_ids.length&&!multiple){attachment_ids=attachment_ids[0]}// Save column
el.cacie_savecolumn(column,item,attachment_ids)});if(typeof column.editable.attachment!=='undefined'){if(typeof column.editable.attachment.disable_select_current!=='undefined'&&column.editable.attachment.disable_select_current){uploader.on('ready',function(){setTimeout(function(){},1)})}}uploader.open()})};