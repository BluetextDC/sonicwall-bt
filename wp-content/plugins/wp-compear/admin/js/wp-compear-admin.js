(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */




	$(function() {

		/* 
		* 
		* js for common uses
		* 
		*/

		// set options page to variable
		var wp_compear_page = $('body.post-type-wp-compear-lists');


		$('body').on('click', '#insert_wpcompear_slider_shortcode', function(e) {
			var temp_ID_value = $('.wp_compear_shortcode_selector').attr('value');
			if(temp_ID_value=='0'){
				alert("You must choose a WP ComPEAR list to insert");
				e.preventDefault();
			}
			else {
				var slider_shortcode = '[wp-compear id="'+temp_ID_value+'"]';
				tinymce.activeEditor.execCommand('mceInsertContent', false, slider_shortcode);
				self.parent.tb_remove();
			}
		});


		// holds width on table row when sorting
		var fixHelper = function(e, ui) {
		    ui.children().each(function() {
		        $(this).width($(this).width());
		    });
		    return ui;
		};

		// calls sortable() on tables
		$( "#wpcompear-specs-tbody.ui-sortable" ).sortable({
			handle: ".js-sort-handle",
			helper: fixHelper,
			axis: 'y',
			opacity: 0.5,
			placeholder: 'sortable-placeholder',
			start: function(e, ui){
				ui.placeholder.height(ui.helper.outerHeight());
			}
		});

		var is_tinyMCE_active = false;

		var textareaID;

		// http://wordpress.stackexchange.com/questions/134374/sortable-wysiwyg-editor

		// calls sortable() on tables
		$( "#wpcompear-products-tbody.ui-sortable" ).sortable({
			handle: ".js-sort-handle",
			helper: fixHelper,
			axis: 'y',
			opacity: 0.5,
			placeholder: 'sortable-placeholder',
			start: function(e, ui){
				ui.placeholder.height(ui.helper.outerHeight());
			    $(this).find('.wp-editor-area').each(function(){
			        tinymce.execCommand( 'mceRemoveEditor', false, $(this).attr('id') );
			    });
			},
			stop: function(e,ui) {
			    $(this).find('.wp-editor-area').each(function(){
			        tinymce.execCommand( 'mceAddEditor', false, $(this).attr('id') );
			        //$(this).sortable("refresh");
			    });
			}
		});


		// add a new product button
		wp_compear_page.on( "click", ".js-delete-list", function(e){ 
			alert('deleted list');
		});


		// add a new product button
		wp_compear_page.on( "click", ".js-duplicate-list", function(e){ 
			alert('duplicated list');
		});


		// collapses / expands rows on products for easier sorting
		wp_compear_page.on( "click", "#js-collapse-expand-row", function(e){ 
			if ($(this).is(':checked')) {
	            $('.product-spec-value-inner').addClass('closed');
	        }
	        else {
	        	$('.product-spec-value-inner').removeClass('closed');
	        }
		});


		

		// copies shortcode to cliboard
		wp_compear_page.on( "click", ".js-copy-shortcode", function(e){ 
			var text = $(this).attr('data-shortcode');
			window.prompt("Copy to clipboard: Ctrl+C, Enter", text);
		});

		
		var wpcompear_image_media_file_frame;

		wp_compear_page.on( "click", ".upload_wpcompear_image_button", function(e){ 

	        e.preventDefault();

	        // Create media frame using data elements from the clicked element
	        wpcompear_image_media_file_frame = wp.media.frames.file_frame = wp.media( {
	            title: 'Select or Upload an Image for this row',
	            button: { text: 'Insert This Image' },
	            class: $(this).attr('id')
	        } );

	        var this_img_btn = $(this);
	        var this_img_stored = $(this).closest('td').find( '.hidden-image-url' );
	        var this_img_prev = $(this).closest('td').find( '.slide-img-prev-wrapper .img-preview img' );
	        var this_icon = $(this).closest('td').find( '.wpcompear-prev-img' );

	        // What to do when the image is selected
	        wpcompear_image_media_file_frame.on( 'select', function() {
	            var attachment = wpcompear_image_media_file_frame.state().get('selection').first().toJSON();
	            this_img_stored.attr( 'value', attachment.url );
	            this_img_prev.attr('src', attachment.url);
	            this_icon.removeClass('inactive').addClass( 'active' );
	        } );
	        // Open the modal
	        wpcompear_image_media_file_frame.open();
	    } );



		/* 
		* 
		* js for custom specs table
		* 
		*/

		
		// add a new custom spec button
		wp_compear_page.on( "click", "#js-add-spec", function(e){ 
			var specs_table = $('#wpcompear-specs-tbody');

			var spec_id = new Date().getTime();

			var new_spec = '<tr>'+
				'<td>'+
					'<input name="wpcompear_custom_specs[spec_id][]" id="" type="hidden" value="'+spec_id+'" class="regular-text" value="custom spec">'+
					'<input name="wpcompear_custom_specs[spec_name][]" id="" type="text" value="custom specification" class="regular-text" value="">'+
				'</td>'+

				'<td>'+
					'<select name="wpcompear_custom_specs[spec_type][]" id="">'+
						'<option value="text-field" selected="selected">Simple Text</option>'+
						'<option value="text-paragraph">Paragraph Text</option>'+
						'<option value="image">Image</option>'+
						'<option value="wysiwyg">WYSIWYG</option>'+
						'<option value="star-rating">Star Rating</option>'+
						'<option value="yes-no">Yes/No</option>'+
					'</select>'+
				'</td>'+

				'<td class="actions-td">'+
					'<span class="js-sort-handle">&#8645;</span>'+
					'<i class="fi-x-circle js-delete-spec" data-spec-id="'+spec_id+'"></i>'+
				'</td>'+
			'</tr>';

			specs_table.append(new_spec);

			// will complete and add in a later verison.

			//var product_spec_col_th = $('<th class="product-spec-name" data-spec-id="'+spec_id+'"><span>custom spec</span></th>');
			//product_spec_col_th.insertAfter('#wp-compear-table_products thead tr .product-spec-name:last');

			// $('#wp-compear-table_products tbody tr').each(function () {

			// 	var product_spec_col_td = $('<td class="product-spec-value" data-spec-id="'+spec_id+'">'+
			// 	'<input name="wpcompear_list_products['+spec_id+'][]" id="" type="text" value="" class="regular-text">'+
			// 	'</td>');

			// 	product_spec_col_td.insertAfter($(this).find('.product-spec-value:last'));
				 
			// });

			//product_spec_col_td.insertAfter('#wp-compear-table_products tbody tr .product-spec-value:last');


		});


		// deletes custom spec button
		wp_compear_page.on( "click", ".js-delete-spec", function(e){ 

			var numItems = $('.js-delete-spec').length;

			if (numItems<=1) {
				alert('You must leave one specification here. To delete this, first create a new one.');
			}

			else {

				if(confirm('Are you sure you want to delete this specification? It will also delete any data from the products list below. This action cannot be undone.')) { 

					var this_delete_spec_id = $(this).attr('data-spec-id');
					//alert(this_delete_spec_id);

					$(this).closest('tr').animate({
						opacity: 0
					}, 200, function() {
						$(this).closest('tr').remove();
					});

					$('#wp-compear-table_products tr').each(function () {

					    $('td,th', this).each(function () {
					        var this_spec_id = $(this).attr('data-spec-id');

					        if(this_spec_id==this_delete_spec_id) {
					        	$(this).animate({
									opacity: 0
								}, 200, function() {
									$(this).remove();
								});
					        }
					     })

					})
				}

			}

			
		});



		/* 
		* 
		* js for products table
		* 
		*/

		// add a new product button
		wp_compear_page.on( "click", "#js-add-product", function(e){ 

			$('tr.hidden-clone').find('.wp-editor-area').each(function(){
		        tinymce.execCommand( 'mceRemoveEditor', false, $(this).attr('id') );
		    });
			
			var products_table = $('#wpcompear-products-tbody');
			var new_product = $('tr.hidden-clone').clone();
			var rowCount = $('#wpcompear-products-tbody tr').length;

			$(new_product).find('.wp-editor-area').each(function (idx, item) {

				var editor_ID = $(this).attr('id');
				var editor_ID_small = editor_ID.slice(0,-1);
				var editor_ID_new = editor_ID_small+rowCount;

				$(this).attr('id', editor_ID_new);

			});

			products_table.append(new_product);

			$('#wpcompear-products-tbody tr:last-child').removeClass('hidden-clone');

			$('#wpcompear-products-tbody tr:last-child').find('.wp-editor-area').each(function(){
		        tinymce.execCommand( 'mceAddEditor', false, $(this).attr('id') );
		    });

			
		});



		// deletes custom spec button
		wp_compear_page.on( "click", ".js-delete-product", function(e){ 
			if(confirm('Are you sure you want to delete this product? You cannot undo this action.')) { 
				$(this).closest('tr').remove();
			}
		});



		wp_compear_page.on( "click", ".wpcompear-prev-img.active", function(e){ 
			$(this).closest('.slide-img-prev-wrapper').find('.img-preview').toggle();
		});


	
	});



})( jQuery );
