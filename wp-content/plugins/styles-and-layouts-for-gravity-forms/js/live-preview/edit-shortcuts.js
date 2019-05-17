

jQuery(document).ready( function( $ ) {
	var formId = gf_stla_localize_edit_shortcuts.formId;
//Add Visible Edit Shortcuts Customizer

//Edit button code for different fields
var editButtonSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M13.89 3.39l2.71 2.72c.46.46.42 1.24.03 1.64l-8.01 8.02-5.56 1.16 1.16-5.58s7.6-7.63 7.99-8.03c.39-.39 1.22-.39 1.68.07zm-2.73 2.79l-5.59 5.61 1.11 1.11 5.54-5.65zm-2.97 8.23l5.58-5.6-1.07-1.08-5.59 5.6z"></path></svg>';
var editLabelButton = '<button aria-label="Edit Label" title="Open Label Settings" class="customize-partial-edit-shortcut-button">'+editButtonSvg+'</button>';
var editInputButton = '<button aria-label="Edit Input" title="Open Input Settings" class="customize-partial-edit-shortcut-button">'+editButtonSvg+'</button>';
var editRadioButton = '<button aria-label="Edit Radio" title="Open Radio Settings" class="customize-partial-edit-shortcut-button">'+editButtonSvg+'</button>';
var editCheckboxButton = '<button aria-label="Edit Checkbox" title="Open Checkbox Settings" class="customize-partial-edit-shortcut-button">'+editButtonSvg+'</button>';
var editSelectButton = '<button aria-label="Edit Dropdown" title="Open Dropdown Settings" class="customize-partial-edit-shortcut-button">'+editButtonSvg+'</button>';
var editTextareaButton = '<button aria-label="Edit Textarea" title="Open Textarea Settings" class="customize-partial-edit-shortcut-button">'+editButtonSvg+'</button>';
var editSectionTitleButton = '<button aria-label="Edit Section Title" title="Open Section Title Settings" class="customize-partial-edit-shortcut-button">'+editButtonSvg+'</button>';
var editFormWrapperButton = '<button aria-label="Edit Form Wrapper" title="Open Form Wrapper Settings" class="customize-partial-edit-shortcut-button">'+editButtonSvg+'</button>';
var editFormHeaderButton = '<button aria-label="Edit Form Header" title="Open Form Header Settings" class="customize-partial-edit-shortcut-button">'+editButtonSvg+'</button>';
var editFormTitleButton = '<button aria-label="Edit Form Title" title="Open Form Title Settings" class="customize-partial-edit-shortcut-button">'+editButtonSvg+'</button>';
var editFormDescriptionButton = '<button aria-label="Edit Form Description" title="Open Form Description Settings" class="customize-partial-edit-shortcut-button">'+editButtonSvg+'</button>';
var editSubLabelButton = '<button aria-label="Edit Sub Labels" title="Open Sub Labels Settings" class="customize-partial-edit-shortcut-button">'+editButtonSvg+'</button>';
var editFieldDescriptionButton = '<button aria-label="Edit Field Description" title="Open Field Description Settings" class="customize-partial-edit-shortcut-button">'+editButtonSvg+'</button>';
var editSectionDescriptionButton = '<button aria-label="Edit Section Description" title="Open Section Description Settings" class="customize-partial-edit-shortcut-button">'+editButtonSvg+'</button>';
var editListFieldButton = '<button aria-label="Edit List Field" title="Open List Field Settings" class="customize-partial-edit-shortcut-button">'+editButtonSvg+'</button>';
var editListFieldHeadingButton = '<button aria-label="Edit List Field Heading" title="Open List Field Heading Settings" class="customize-partial-edit-shortcut-button">'+editButtonSvg+'</button>';
var editListFieldCellButton = '<button aria-label="Edit List Field Cell" title="Open List Field Cell Settings" class="customize-partial-edit-shortcut-button">'+editButtonSvg+'</button>';
var editSubmitButtonButton = '<button aria-label="Edit Submit Button" title="Open Submit Button Settings" class="customize-partial-edit-shortcut-button">'+editButtonSvg+'</button>';
// Different type of fields

var allEditableElements = '.gfield_label, .gform_fields .gfield .ginput_container:not(.ginput_container_radio) input[type=text], .gform_fields .gfield input[type=email], .gform_fields .gfield input[type=tel], .gform_fields .gfield input[type=url], .gform_fields .gfield input[type=password], .gfield textarea, .gfield select, .gfield_radio, .gfield_checkbox, .gfield_select, .gsection_title, .gform_title, .gform_description, .gfield_description, .gsection_description, .ginput_list, .gform_footer .button, .gform_page_footer .button, .gfield .ginput_complex label';

var input_fields = ['.gform_fields .gfield .ginput_container:not(.ginput_container_radio) input[type=text]', '.gform_fields .gfield input[type=email]', '.gform_fields .gfield input[type=tel]', '.gform_fields .gfield input[type=url]', '.gform_fields .gfield input[type=password]'];

var sub_labels = ['.gfield .ginput_complex label'];

var button = ['.gform_footer input[type=submit]', '.gform_page_footer input[type=submit]','.gform_page_footer input[type=button]', '.gform_footer button.mdl-button', '.gform_page_footer .gform_next_button','.gform_page_footer .gform_previous_button'];

//Insert edit button before fields

$('<span class="customize-partial-edit-shortcut stla-partial-label-shortcut">'+editLabelButton+'</span>').insertBefore('.gfield_label');

$('<span class="customize-partial-edit-shortcut stla-partial-textarea-shortcut">'+editTextareaButton+'</span>').insertBefore('.gfield textarea');

$('<span class="customize-partial-edit-shortcut stla-partial-radio-shortcut">'+editRadioButton+'</span>').insertBefore('.gfield_radio');

$('<span class="customize-partial-edit-shortcut stla-partial-checkbox-shortcut">'+editCheckboxButton+'</span>').insertBefore('.gfield_checkbox');

$('<span class="customize-partial-edit-shortcut stla-partial-select-shortcut">'+editSelectButton+'</span>').insertBefore('select');


$('<span class="customize-partial-edit-shortcut stla-partial-sectiontitle-shortcut">'+editSectionTitleButton+'</span>').insertBefore('.gsection_title');

$('<span class="customize-partial-edit-shortcut stla-partial-formwrapper-shortcut">'+editFormWrapperButton+'</span>').insertBefore('.gform_wrapper');

$('<span class="customize-partial-edit-shortcut stla-partial-formheader-shortcut">'+editFormHeaderButton+'</span>').insertBefore('.gform_heading');

$('<span class="customize-partial-edit-shortcut stla-partial-formtitle-shortcut">'+editFormTitleButton+'</span>').insertBefore('.gform_title');

$('<span class="customize-partial-edit-shortcut stla-partial-formdescription-shortcut">'+editFormDescriptionButton+'</span>').insertBefore('.gform_description');

$('<span class="customize-partial-edit-shortcut stla-partial-fielddescription-shortcut">'+editFieldDescriptionButton+'</span>').insertBefore('.gfield_description');

$('<span class="customize-partial-edit-shortcut stla-partial-sectiondescription-shortcut">'+editSectionDescriptionButton+'</span>').insertBefore('.gsection_description');

$('<span class="customize-partial-edit-shortcut stla-partial-listfield-shortcut">'+editListFieldButton+'</span>').insertBefore('.ginput_list');

$('<span class="customize-partial-edit-shortcut stla-partial-listfieldheading-shortcut">'+editListFieldHeadingButton+'</span>').insertBefore('.gfield_list thead th');

$('<span class="customize-partial-edit-shortcut stla-partial-listfieldcell-shortcut">'+editListFieldCellButton+'</span>').insertBefore('td.gfield_list_cell input');

$('<span class="customize-partial-edit-shortcut stla-partial-radioother-shortcut">'+editListFieldCellButton+'</span>').insertBefore('td.gfield_list_cell gf_other_choice');

var allRadioFields = $('.ginput_container_radio li');

$.each( allRadioFields , function( index, value){
	if($(this).has('input[value=gf_other_choice]').length){
		$('<span class="customize-partial-edit-shortcut stla-partial-input-shortcut">'+editInputButton+'</span>').insertBefore(this);
		allEditableElements += ', .'+ $(this).attr( 'class');
	}
});

$.each(input_fields, function(index, value){
	$('<span class="customize-partial-edit-shortcut stla-partial-input-shortcut">'+editInputButton+'</span>').insertBefore(value);
})

// for sub labels

$.each(sub_labels, function(index, value){
	$('<span class="customize-partial-edit-shortcut stla-partial-sublabel-shortcut">'+editSubLabelButton+'</span>').insertBefore(value);
})

$.each(button, function(index, value){
	$('<span class="customize-partial-edit-shortcut stla-partial-submitbutton-shortcut">'+editSubmitButtonButton+'</span>').insertBefore(value);
})





$(allEditableElements).on('mouseenter', function(){
	if (!$(this).parents('.stla-live-edit-disabled').length) {
		$(allEditableElements).removeClass('stla-selected-field');
		$('.gform_heading').removeClass('stla-selected-field');
		$('.gform_wrapper .customize-partial-edit-shortcut').css('display', 'none');
	
			$(this).addClass('stla-selected-field');
			$(this).prev('.customize-partial-edit-shortcut').css('display', 'block');
			$('.stla-partial-formwrapper-shortcut').css('display', 'block');
		//console.log($(this));
	
		if( $(this).hasClass('gform_title' ) || $(this).hasClass( 'gform_description' )){
			$('.stla-partial-formheader-shortcut').css('display', 'block');
			$('.gform_heading').addClass('stla-selected-field');		
		}
	}

});

function removeShortcut(editShortcut){
	$('.gform_wrapper .customize-partial-edit-shortcut').css('display', 'none');
	$(editShortcut).next().removeClass('stla-selected-field');
}

//send control 

$( document.body ).on( 'click', '.stla-partial-input-shortcut', function(){
	var data={'form_id':formId, 'control_type':'text-input'};
	wp.customize.preview.send( 'stla-focus-control', data );
	removeShortcut(this);
});

$( document.body ).on( 'click', '.stla-partial-label-shortcut', function(){
	var data={'form_id':formId, 'control_type':'field-labels'};
	wp.customize.preview.send( 'stla-focus-control', data );
	removeShortcut(this);
});

$( document.body ).on( 'click', '.stla-partial-textarea-shortcut', function(){
	var data={'form_id':formId, 'control_type':'paragraph-textarea'};
	wp.customize.preview.send( 'stla-focus-control', data );
	removeShortcut(this);
});

$( document.body ).on( 'click', '.stla-partial-radio-shortcut', function(){
	var data={'form_id':formId, 'control_type':'radio-inputs'};
	wp.customize.preview.send( 'stla-focus-control', data );
	removeShortcut(this);
});

$( document.body ).on( 'click', '.stla-partial-checkbox-shortcut', function(){
	var data={'form_id':formId, 'control_type':'checkbox-inputs'};
	wp.customize.preview.send( 'stla-focus-control', data );
	removeShortcut(this);
});

$( document.body ).on( 'click', '.stla-partial-select-shortcut', function(){
	var data={'form_id':formId, 'control_type':'dropdown-fields'};
	wp.customize.preview.send( 'stla-focus-control', data );
	removeShortcut(this);
});

$( document.body ).on( 'click', '.stla-partial-sectiontitle-shortcut', function(){
	var data={'form_id':formId, 'control_type':'section-break-title'};
	wp.customize.preview.send( 'stla-focus-control', data );
	removeShortcut(this);
});

$( document.body ).on( 'click', '.stla-partial-formwrapper-shortcut', function(){
	var data={'form_id':formId, 'control_type':'form-wrapper'};
	wp.customize.preview.send( 'stla-focus-control', data );
	removeShortcut(this);
});

$( document.body ).on( 'click', '.stla-partial-formheader-shortcut', function(){
	var data={'form_id':formId, 'control_type':'form-header'};
	wp.customize.preview.send( 'stla-focus-control', data );
	removeShortcut(this);
});

$( document.body ).on( 'click', '.stla-partial-formtitle-shortcut', function(){
	var data={'form_id':formId, 'control_type':'form-title'};
	wp.customize.preview.send( 'stla-focus-control', data );
	removeShortcut(this);
});

$( document.body ).on( 'click', '.stla-partial-formdescription-shortcut', function(){
	var data={'form_id':formId, 'control_type':'form-description'};
	wp.customize.preview.send( 'stla-focus-control', data );
	removeShortcut(this);
});

$( document.body ).on( 'click', '.stla-partial-sublabel-shortcut', function(){
	var data={'form_id':formId, 'control_type':'field-sub-labels'};
	wp.customize.preview.send( 'stla-focus-control', data );
	removeShortcut(this);
});

$( document.body ).on( 'click', '.stla-partial-fielddescription-shortcut', function(){
	var data={'form_id':formId, 'control_type':'field-descriptions'};
	wp.customize.preview.send( 'stla-focus-control', data );
	removeShortcut(this);
});

$( document.body ).on( 'click', '.stla-partial-sectiondescription-shortcut', function(){
	var data={'form_id':formId, 'control_type':'section-break-description'};
	wp.customize.preview.send( 'stla-focus-control', data );
	removeShortcut(this);
});

$( document.body ).on( 'click', '.stla-partial-listfield-shortcut', function(){
	var data={'form_id':formId, 'control_type':'list-field-table'};
	wp.customize.preview.send( 'stla-focus-control', data );
	removeShortcut(this);
});

// $( document.body ).on( 'click', '.stla-partial-listfieldheading-shortcut', function(){
// 	var data={'form_id':formId, 'control_type':'list-field-heading'};
// 	wp.customize.preview.send( 'stla-focus-control', data );
// });

// $( document.body ).on( 'click', '.stla-partial-listfieldcell-shortcut', function(){
// 	var data={'form_id':formId, 'control_type':'list-field-cell'};
// 	wp.customize.preview.send( 'stla-focus-control', data );
// });

$( document.body ).on( 'click', '.stla-partial-submitbutton-shortcut', function(){
	var data={'form_id':formId, 'control_type':'submit-button'};
	wp.customize.preview.send( 'stla-focus-control', data );
	removeShortcut(this);
});

$( document.body ).on( 'click', '.textarea-container textarea', function(){
	var data={'form_id':formId, 'control_type':'paragraph-textarea'};
	wp.customize.preview.send( 'stla-focus-control', data );
	removeShortcut(this);
});
// $( document.body ).on( 'click', '.listcountry-container .nf-field-element,.liststate-container .nf-field-element, .listselect-container .nf-field-element', function(){
// 	var data={'form_id':formId, 'control_type':'dropdown'};
// 	wp.customize.preview.send( 'stla-focus-control', data );
// });
// $( document.body ).on( 'click', '.stla-partial-label-shortcut', function(){
// 	var data={'form_id':formId, 'control_type':'field-labels'};
// 	wp.customize.preview.send( 'stla-focus-control', data );
// });
// $( document.body ).on( 'click', '.nf-error-msg', function(){
// 	var data={'form_id':formId, 'control_type':'error-msg'};
// 	wp.customize.preview.send( 'stla-focus-control', data );
// });	

} );