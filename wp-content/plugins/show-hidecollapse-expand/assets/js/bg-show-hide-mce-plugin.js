(function() {
	var bgShowMoreCustomButton, bgShowMoreCustomButtonColor;
	tinymce.PluginManager.add('bg_show_hide_tc_button', function( editor, url ) {

        editor.addButton('bg_show_hide_tc_button', {
            text: 'Collapse-Expand',
            icon: 'icon dashicons-sort',
			title: 'Insert Collapse-Expand shortcode',
			onclick: function() {
				editor.windowManager.open( {
					title: 'Insert Collapse-Expand shortcode',
					classes: 'bg-show-more',
					body: [
						{
							type: 'listbox',
							name: 'bgShowMoreCustomButton',
							label: 'Expand/collapse content using',
							classes: 'bgButtonForPreview',
							values: [
								{ text: 'Button (orange)', value: 'button-orange' },
								{ text: 'Button (red)', value: 'button-red' },
								{ text: 'Button (green)', value: 'button-green' },
								{ text: 'Button (blue)', value: 'button-blue' },
								{ text: 'Simple Link', value: 'link' },
								{ text: 'Inline Link', value: 'link-inline' },
								{ text: 'Link in List', value: 'link-list' }
							]
						},
						{
							type   : 'colorbox', 
							name   : 'colorbox',
							label  : 'Select button/link text color',
							onaction: createColorPickAction(),
							value: '#4a4949',
						},
						{
							type: 'textbox',
							name: 'expand',
							label: 'Label for expanding content',
							value: 'Show More'
						},
						{
							type: 'textbox',
							name: 'collapse',
							label: 'Label for collapsing content',
							value: 'Show Less'
						},
						{
							type: 'listbox',
							name: 'bgShowMoreCustomIcon',
							label: 'Select button icon',
							classes: 'bgShowMoreCustomIcon',
							values: [
								{ text: '', value: '' },
								{ text: 'Arrow Down', value: 'arrow' },
								{ text: 'Eye (show)', value: 'eye' },
								{ text: 'Zoom (plus)', value: 'zoom' }
							]
						},
						{
							type: 'checkbox',
							name: 'multilevel',
							label: 'This shortcode should be nested in another shortcode'
						},
						{
							type: 'textbox',
							name: 'inline_css',
							label: 'Inline CSS',
							value: ''
						},
						{
							type: 'textbox',
							name: 'onclick',
							label: 'Onclick JS function',
							value: ''
						},
						{
							type   : 'label',
							name   : 'label',
							label  : 'Button samples'
						},
						{
							type   : 'container',
							layout: 'fit',
							minWidth: 160,
							minHeight: 60,
							items:[
							  {
							  type  : 'container', 
							  label  : 'Control type samples', 
							  html   : '<table><tr><td>(orange)</td> <td><button id="bg-showmore-action" class="bg-showmore-plg-button bg-orange-button" style=" color:#4a4949 ;">Show More</button> </td><td>&nbsp;</td>' +
									   '<td>(green+arrow)</td><td><button id="bg-showmore-action" class="bg-showmore-plg-button bg-green-button bg-arrow" style=" color:#4a4949 ;">Show More</button> </td></tr>'+
									   '<tr><td>(red+zoom)</td> <td><button id="bg-showmore-action" class="bg-showmore-plg-button bg-red-button bg-zoom" style=" color:#ffffff ;">Show More</button> </td><td>&nbsp;</td>'+
									   '<td>(blue+eye)</td> <td><button id="bg-showmore-action" class="bg-showmore-plg-button bg-blue-button bg-eye" style=" color:#ffffff ;">Show More</button> </td></tr></table>'
							  },
							]
						  },
					],
					onsubmit: function( e ) {
						var contents = editor.selection.getContent();
						var bg_shortcode_name = 'bg_collapse';
						var bg_shortcode = 'view="'+e.data.bgShowMoreCustomButton+'" ';
						if (e.data.colorbox) {
							bg_shortcode += 'color="'+e.data.colorbox+'" ';
						}
						if (e.data.bgShowMoreCustomIcon) {
							bg_shortcode += 'icon="'+e.data.bgShowMoreCustomIcon+'" ';
						}
						if (e.data.expand) {
							bg_shortcode += 'expand_text="'+e.data.expand+'" ';
						}
						if (e.data.collapse) {
							bg_shortcode += 'collapse_text="'+e.data.collapse+'" ';
						}
						if (e.data.inline_css) {
							bg_shortcode += 'inline_css="'+e.data.inline_css+'" ';
						}
						if (e.data.onclick) {
							bg_shortcode += 'onclick="'+e.data.onclick+'" ';
						}
						if (e.data.multilevel){
							bg_shortcode_name = 'bg_collapse_level2';
						}
						editor.insertContent('[' + bg_shortcode_name + ' ' + bg_shortcode + ']' + contents +'[/' + bg_shortcode_name + ']');
					}
				});
			}
		});
	});
	
	tinymce.init({
		plugins: 'colorpicker',
	  });
	  
	  
	  // Taken from core plugins
	  var editor = tinymce.activeEditor;
	  
	  function createColorPickAction() {
		  if (editor == null) {
		  	  var editor = tinyMCE.activeEditor;
		  }
				var colorPickerCallback = editor.settings.color_picker_callback;

				if (colorPickerCallback) {
					return function() {
						var self = this;

						colorPickerCallback.call(
							editor,
							function(value) {
								self.value(value).fire('change');
							},
							self.value()
						);
						
					};
				}
			}

})();
