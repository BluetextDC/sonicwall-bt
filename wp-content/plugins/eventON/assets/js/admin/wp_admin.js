/*
 * Script that runs on all over the backend pages
 * @version 3.0
 */
jQuery(document).ready(function($){	
	
	// Event Singular tax term form create new or edit form
		$('body').on('click','.evo_tax_term_form',function(){
			OBJ = $(this);
			PAR = OBJ.closest('.evo_singular_tax_for_event');
			var ajaxdataa = { };
				ajaxdataa['action']='eventon_get_event_tax_term_section';
				ajaxdataa['type']= OBJ.data('type');
				ajaxdataa['tax']=  PAR.data('tax');
				ajaxdataa['eventid']=  PAR.data('eventid');
				ajaxdataa['termid']=  OBJ.data('id');

			$.ajax({
				beforeSend: function(){
					text = OBJ.data('type')=='new'? 'Add new item':'Edit item';
					$('.evo_term_lightbox').find('.ajde_lightbox_title').html( text );
					$('.evo_term_lightbox').find('.ajde_popup_text').addClass( 'loading');
				},
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){						
						$('.evo_term_lightbox').find('.ajde_popup_text').html( data.content);
					}else{}
				},complete:function(){
					$('.evo_term_lightbox').find('.ajde_popup_text').removeClass( 'loading');
				}
			});	
		});
		
		// get term list
		$('body').on('click','.evo_tax_term_list',function(){
			OBJ = $(this);
			PAR = OBJ.closest('.evo_singular_tax_for_event');
			var ajaxdataa = { };
				ajaxdataa['action']='eventon_event_tax_list';
				ajaxdataa['tax']=  PAR.data('tax');
				ajaxdataa['eventid']=  PAR.data('eventid');
				ajaxdataa['termid']=  OBJ.data('id');

			$.ajax({
				beforeSend: function(){
					$('.evo_term_lightbox').find('.ajde_lightbox_title').html( 'Select from list');
					$('.evo_term_lightbox').find('.ajde_popup_text').addClass( 'loading');
				},
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){						
						$('.evo_term_lightbox').find('.ajde_popup_text').html( data.content);
						$('.evo_term_lightbox').find('select.field').select2();						
					}
				},complete:function(){
					$('.evo_term_lightbox').find('.ajde_popup_text').removeClass( 'loading');
				}
			});	
		});

		// save changes
		$('body').on('click','.evo_term_submit',function(){
			OBJ = $(this);
			PAR = OBJ.closest('.evo_tax_entry');
			var ajaxdataa = { };
				ajaxdataa['action']='eventon_event_tax_save_changes';
				ajaxdataa['tax']=  PAR.data('tax');
				ajaxdataa['eventid']=  PAR.data('eventid');
				ajaxdataa['type']=  PAR.data('type');

			PAR.find('.field').each(function(){
				if($(this).val() != ''){
					ajaxdataa[ $(this).attr('name')]=  $(this).val();
				}
			});

			$.ajax({
				beforeSend: function(){
					$('.evo_term_lightbox').find('.ajde_lightbox_title').html( 'Select from list');
					$('.evo_term_lightbox').find('.ajde_popup_text').addClass( 'loading');
				},
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){						
						$('.evo_term_lightbox').find('.message').html( data.content).show();
						$('.evo_singular_tax_for_event.'+PAR.data('tax')).html(data.htmldata);
					}
				},complete:function(){
					setTimeout(function () {
					   $('.ajde_close_pop_btn').trigger('click');
					}, 2000);					
					$('.evo_term_lightbox').find('.ajde_popup_text').removeClass( 'loading');
				}
			});	
		});

		// remove location
		$('body').on('click','.evo_tax_remove',function(){
			OBJ = $(this);
			PAR = OBJ.closest('.evo_singular_tax_for_event');
			var ajaxdataa = { };
				ajaxdataa['action']='eventon_event_tax_remove';
				ajaxdataa['tax']=  PAR.data('tax');
				ajaxdataa['eventid']=  PAR.data('eventid');
				ajaxdataa['termid']=  OBJ.data('id');

			$.ajax({
				beforeSend: function(){
					PAR.addClass( 'loading');
				},
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){						
						PAR.html(data.htmldata);
					}
				},complete:function(){
					PAR.removeClass( 'loading');
				}
			});	
		});

	// Upload custom images to eventon custom image meta fields
		var file_frame,
			BOX;
	  
	    $('body').on('click','.custom_upload_image_button',function(event) {
	    	var obj = jQuery(this);
	    	BOX = obj.closest('.evo_metafield_image');

	    	IMG_URL = '';

	    	// choose image
	    	if(obj.hasClass('chooseimg')){

	    		event.preventDefault();

				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					file_frame.open();
					return;
				}
				// Create the media frame.
				file_frame = wp.media.frames.downloadable_file = wp.media({
					title: 'Choose an Image',
					button: {text: 'Use Image',},
					multiple: false
				});

				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					attachment = file_frame.state().get('selection').first().toJSON();

					BOX.find('.evo_meta_img').val( attachment.id );
					BOX.find('.image_src img').attr('src', attachment.url ).fadeIn();
					var old_text = obj.attr('value');
					var new_text = obj.data('txt');

					obj.attr({'value': new_text, 'data-txt': old_text, 'class': 'custom_upload_image_button button removeimg'});
				});

				// Finally, open the modal.
				file_frame.open();

			}else{
				
				BOX.find('.evo_meta_img').val( '' );
		  		BOX.find('.image_src img').fadeOut(function(){
		  			$(this).attr('src', '' );
		  		});
		  		var old_text = obj.attr('value');
				var new_text = obj.attr('data-txt');

				obj.attr({'value': new_text, 'data-txt': old_text, 'class': 'custom_upload_image_button button chooseimg'});

				return false;
			}
	    });  
 			
	// widget
		$('.widgets-sortables').on('click','.evowig_chbx', function(){			
			if($(this).hasClass('selected')){
				$(this).removeClass('selected');
				
				$(this).siblings('input').val('no');
				$(this).parent().siblings('.evo_wug_hid').slideUp('fast');
			}else{
				$(this).addClass('selected');
				
				$(this).siblings('input').val('yes');
				$(this).parent().siblings('.evo_wug_hid').slideDown('fast');
			}	
		});

// Multi Data Types for event type posts
	$('body').on('click','.evomdt_add_new_btn',function(){
		OBJ = $(this);
		var ajaxdataa = { };
			ajaxdataa['action']='evo_mdt';
			ajaxdataa['type']= 'newform';
			ajaxdataa['tax']=  OBJ.data('tax');
			ajaxdataa['eventid']=  OBJ.data('eventid');

		$.ajax({
			type: 'POST',
			url:evo_admin_ajax_handle.ajaxurl,
			data: ajaxdataa,
			dataType:'json',
			success:function(data){
				if(data.status=='good'){
					$('.evomdt_new').find('.ajde_popup_text').html( data.content);
				}else{}
			},complete:function(){		}
		});			
	});

	// edit term
		$('.evomdt_selection').on('click','i.fa-pencil',function(){
			OBJ = $(this);
			var ajaxdataa = { };
				ajaxdataa['action']='evo_mdt';
				ajaxdataa['type']= 'editform';
				ajaxdataa['tax']=  OBJ.closest('ul').data('tax');
				ajaxdataa['eventid']=  OBJ.closest('ul').data('eventid');
				ajaxdataa['termid']=  OBJ.parent().data('termid');

			$.ajax({
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){
						$('.evomdt_new').find('.ajde_popup_text').html( data.content);
					}else{}
				},complete:function(){		}
			});			
		});

	// delete term relationship
		$('.evomdt_selection').on('click','i.fa-close',function(){
			OBJ = $(this);
			EVOMB = OBJ.closest('.evomb_body');
			var ajaxdataa = { };
				ajaxdataa['action']='evo_mdt';
				ajaxdataa['type']= 'removeterm';
				ajaxdataa['tax']=  OBJ.closest('ul').data('tax');
				ajaxdataa['eventid']=  OBJ.closest('ul').data('eventid');
				ajaxdataa['termid']=  OBJ.parent().data('termid');

			$.ajax({
				beforeSend: function(){	EVOMB.addClass('loading'); },
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){
						$('.'+ ajaxdataa['tax']+'_display_list').html(data.content);
					}else{}
				},complete:function(){	EVOMB.removeClass('loading'); }
			});			
		});

	// submit mdt form
		$('.ajde_admin_lightbox').on('click','.evomdt_new_mdt_submit',function(){
			console.log('t');
			OBJ = $(this);
			FORM = OBJ.closest('.ev_admin_form');
			BOX = OBJ.closest('.ajde_popup_text');

			var ajaxdataa = { };
				ajaxdataa['action']='evo_mdt';
				ajaxdataa['type']= 'save';

			error = 0;
			FORM.find('.field').each(function(){
				THIS = $(this);
				// required field missing
				if( THIS.hasClass('req') && (THIS.val() === undefined || THIS.val()=='')) error++;
				ajaxdataa[ THIS.attr('name')] = THIS.val();
			});

			if(error == 0 ){
				$.ajax({
					beforeSend: function(){	BOX.addClass('loading'); },
					type: 'POST',
					url:evo_admin_ajax_handle.ajaxurl,
					data: ajaxdataa,
					dataType:'json',
					success:function(data){
						if(data.status=='good'){
							$('.'+ ajaxdataa['tax']+'_display_list').html(data.content);
							$('body').trigger('ajde_lightbox_show_msg',[ data.msg,'evomdt_new']);
						}else{	}
					},
					complete:function(){ BOX.removeClass('loading');	}
				});	
			}else{
				msg = 'Required Fields Missing!';
				$('body').trigger('ajde_lightbox_show_msg',[ msg,'evomdt_new','bad']);
			}		
		});
	// select from list
		$('body').on('click','.evomdt_get_list',function(){
			OBJ = $(this);
			var ajaxdataa = { };
				ajaxdataa['action']='evo_mdt';
				ajaxdataa['type']= 'list';
				ajaxdataa['eventid']= OBJ.data('eventid');
				ajaxdataa['tax']= OBJ.data('tax');

			$.ajax({
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){
						$('.evomdt_list').find('.ajde_popup_text').html( data.content);
					}else{	}
				},complete:function(){		}
			});			
		});	

	// save list
		$('body').on('click','.evomdt_save_list_submit',function(){
			OBJ = $(this);
			BOX = OBJ.closest('.ajde_popup_text');

			var mdt = [];
			OBJ.parent().parent().find('input').each(function() {
				if($(this).is(':checked'))
		    		mdt.push($(this).val());
		    });

			var ajaxdataa = { };
				ajaxdataa['action']='evo_mdt';
				ajaxdataa['type']= 'savelist';
				ajaxdataa['eventid']= OBJ.data('eventid');
				ajaxdataa['tax']= OBJ.data('tax');
				ajaxdataa['mdt']= mdt;

			
			$.ajax({
				beforeSend: function(){	BOX.addClass('loading'); },
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: ajaxdataa,
				dataType:'json',
				success:function(data){
					if(data.status=='good'){
						$('.'+ ajaxdataa['tax']+'_display_list').html(data.content);
							$('body').trigger('ajde_lightbox_show_msg',[ data.msg,'evomdt_list']);
					}else{

					}
				},complete:function(){BOX.removeClass('loading');}
			});		
		});	
	
// settings
	// themes section
		$('.evo_theme_selection select').on('change',function(){
			var theme = $(this).val();
			
			// switch to default
			if(theme =='default'){
				$('.colorselector ').each(function(){
					var item = $(this).siblings('input');
					item.attr({'value': item.attr('default') });
					$(this).attr({'style':'background-color:#'+item.attr('default'), 'hex':item.attr('default')});					
				});
				$('.evo_theme').find('span').each(function(){
					$(this).attr({'style':'background-color:#'+ $(this).attr('data-default')});
				});
	
			}else{
				themeSel = JSON.parse( $('#evo_themejson').html());

				// each theme array
				$.each(themeSel, function(i, item){			
					if(item.name== theme){
						$.each(item.content, function(key, value){
							var thisItem = $('body').find('input[name='+key+']');
							thisItem.val(value);
							thisItem.siblings('span.colorselector')
								.attr({'style':'background-color:#'+value, 'hex':value});
							$('.evo_theme').find('span[name='+key+']').attr({'style':'background-color:#'+value});
						});
					}
				});

			}
		});
	// google maps styles section
	// @since	2.2.22
		$('p.evcal_gmap_style select').on('change', function(){

			baseurl = 'https://snazzy-maps-cdn.azureedge.net/assets/';
			var styles = {
				'default':'58-simple-labels.png?v=20150113051357',
				paleretrogold : '86430-pale-retro-gold.png',
				richblack : '2720-rich-black.png?v=20150113113807',
				apple : '42-apple-maps-esque.png?v=20150113070431',
				blueessence : '/61-blue-essence.png?v=20150113072113',
				coolgrey : '80-cool-grey.png',
				shift : '27-shift-worker.png?v=20150113052049',
				bluewater : '25-blue-water.png?v=20150113093754',
				bentley : '43-bentley.png?v=20150113085831',
				hotpink : '24-hot-pink.png?v=20150113074419',
				muted : '91-muted-monotone.png?v=20150113093728',
				vintageyellowlight : '78128-vintage-yellow-light.png',
				avacado : '35-avocado-world.png?v=20150113094526',
			};

			var gmapSTY = $(this).val();
			var obj = $(this).siblings('i').find('span');
			var url = obj.attr('data-url');

			var styleVAL = '';
			// get url for map image
			$.each(styles, function(index, value){
				if( index == gmapSTY){
					styleVAL = baseurl+value;
				}
			});

			obj.css({'background':'url('+styleVAL+') center center no-repeat','display':'block','height':'150px','margin-top':'10px','max-width':'600px'});
			obj.parent().css({'opacity':'1'});
		});
	// Export settings
		$('body').on('click','#evo_settings_import',function(event){
			event.preventDefault();
			OBJ = $(this);

			OBJ.parent().siblings('.import_box').fadeIn();

			var form = document.getElementById('evo_settings_import_form');
			var fileSelect = document.getElementById('file-select');
			var box = $('#import_box');
			msg = box.find('.msg');
			msg.hide();

			$('#evo_settings_import_form').submit(function(event) {
			  	event.preventDefault();
			  	// Update button text.
			  	msg.html('Processing.').slideDown();

			  	var data = null;
			  	var files = fileSelect.files;
			  	var file = fileSelect.files[0];

			  	//console.log(file);
			  	if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
			      	alert('The File APIs are not fully supported in this browser.');
			      	return;
			    }

			  	if( file.name.indexOf('.json') == -1 ){
			  		msg.html('Only accept JSON file format.');
			  	}else{
			  		var reader = new FileReader();
				  	reader.readAsText(file);
		            reader.onload = function(event) {
		                var jsonData = event.target.result ;

		                // console.log(jsonData);
		                // console.log( $.parseJSON( jsonData) );
		             
		                $.ajax({
							beforeSend: function(){	},
							type: 'POST',
							url:evo_admin_ajax_handle.ajaxurl,
							data: {	
								action:'eventon_import_settings',
								nonce: evo_admin_ajax_handle.postnonce,
								jsondata: $.parseJSON( jsonData)
							},
							dataType:'json',
							success:function(data){
								msg.html(data.msg);
							},complete:function(){	}
						});
		            };
		            reader.onerror = function() {
		            	msg.html('Unable to read file.');
		            };
			  	}
			});
		});

// Diagnose
	$('#evo_send_test_email').on('click', function(){
		if($('#evo_admin_test_email_address').val() == undefined|| $('#evo_admin_test_email_address').val() ==''){
			$('#evodiagnose_message').html('Email Address Missing!');
		}else{
			$.ajax({
				beforeSend: function(){	$('#evodiagnose_message').html('Sending Test Email...')},
				type: 'POST',
				url:evo_admin_ajax_handle.ajaxurl,
				data: {	
					action:'eventon_admin_test_email',
					nonce: evo_admin_ajax_handle.postnonce,
					email: $('#evo_admin_test_email_address').val()
				},
				dataType:'json',
				success:function(data){
					$('#evodiagnose_message').html(data.msg);
				},complete:function(){	}
			});
		}		
	});

// LANGUAGE SETTINGS
	// language tab
		$('.eventon_cl_input').focus(function(){
			$(this).parent().addClass('onfocus');
		});
		$('.eventon_cl_input').blur(function(){
			$(this).parent().removeClass('onfocus');
		});
	
	// change language
		$('#evo_lang_selection').change(function(){
			var val = $(this).val();
			var url = $(this).attr('url');
			window.location.replace(url+'?page=eventon&tab=evcal_2&lang='+val);
		});
	
	// toggeling language subheaders
		$('.evo_settings_toghead').on('click',function(){
			$(this).next('.evo_settings_togbox').toggle();
			$(this).toggleClass('open');
		});
	// export language
		$('body').on('click','#evo_lang_export', function(){
			string = {};
			var tmpArr = [];
  			var tmpStr = '';
			var csvData = [];

			$('#evcal_2').find('input').each(function(){
				csvData.push( $(this).attr('name')+','+ $(this).val());
			});

			var output = csvData.join('\n');
		  	var uri = 'data:application/csv;charset=UTF-8,' + encodeURIComponent(output);
		  	//window.open(uri);
		  	$(this).attr({
		  		'download':'evo_lang_'+$('#evo_lang_selection').val()+'.csv',
		  		'href':uri,
		  		'target':'_blank'
		  	});
		});

	// import language
		$('body').on('click','#evo_lang_import',function(){
			$('#import_box').fadeIn();

			var form = document.getElementById('file-form');
			var fileSelect = document.getElementById('file-select');
			var uploadButton = document.getElementById('upload-button');
			var box = $('#import_box');
			msg = box.find('.msg');
			msg.hide();

			$('#file-form').submit(function(event) {
				  	event.preventDefault();
				  	// Update button text.
				  	
				  	msg.html('Processing.').slideDown();

				  	var data = null;
				  	var files = fileSelect.files;
				  	var file = fileSelect.files[0];

				  	//console.log(file);
				  	if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
				      	alert('The File APIs are not fully supported in this browser.');
				      	return;
				    }

				  	if( file.name.indexOf('.csv') == -1 ){
				  		msg.html('Incorrect file format.');
				  	}else{
				  		var reader = new FileReader();
					  	reader.readAsText(file);
			            reader.onload = function(event) {
			                var csvData = event.target.result;

			                var allTextLines = csvData.split(/\r\n|\n/);
			                //console.log(allTextLines[0]);
			                for (var i=0; i<allTextLines.length; i++) {
			                	var data = allTextLines[i].split(',');
			                	// update new values
			                	$('#evcal_2').find("input[name='"+data[0]+"']").val(data[1]);
			                	//console.log(data[0]+'='+data[1]);
			                	msg.html('Updating language values.');   
				        	}

				        	msg.html('Language fields updated. Please save changes.');   
			            };
			            reader.onerror = function() {
			            	msg.html('Unable to read file.');
			            };
				  	}
			});
		});
		$('body').on('click','#import_box #close',function(){
			$('#import_box').fadeOut();
		});
		

		function processData(allText) {
		    var allTextLines = allText.split(/\r\n|\n/);
		    var headers = allTextLines[0].split(',');
		    var lines = [];

		    for (var i=1; i<allTextLines.length; i++) {
		        var data = allTextLines[i].split(',');
		        if (data.length == headers.length) {

		            var tarr = [];
		            for (var j=0; j<headers.length; j++) {
		                tarr.push(headers[j]+":"+data[j]);
		            }
		            lines.push(tarr);
		        }
		    }
		    console.log(lines);
		}

});