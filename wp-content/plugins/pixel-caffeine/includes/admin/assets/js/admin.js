/**
 * UI scripts for admin settings page
 */

import $ from 'jquery';
import 'bootstrap-sass/assets/javascripts/bootstrap/button';
import 'bootstrap-sass/assets/javascripts/bootstrap/collapse';
import 'bootstrap-sass/assets/javascripts/bootstrap/tooltip';
import 'bootstrap-sass/assets/javascripts/bootstrap/popover';
import 'bootstrap-sass/assets/javascripts/bootstrap/transition';
import 'bootstrap-sass/assets/javascripts/bootstrap/modal';
import 'bootstrap-sass/assets/javascripts/bootstrap/alert';
import 'bootstrap-material-design/scripts/material';
import 'select2';
import Config from './config';
import Utils from './utils';
import Common from './common';

jQuery(document).ready(function(){
    'use strict';

    let dropdown_data = Common.dropdown_data,

		init_configs = function() {
			if ( $.fn.select2 ) {
				$.extend( $.fn.select2.defaults, {
					dropdownCssClass: 'adespresso-select2',
					containerCssClass: 'adespresso-select2',
					formatNoMatches: false
				} );
			}
		},

		showCopyTooltip = function(elem, msg) {
			$( elem ).data({
				title: msg,
				placement: 'bottom'
			}).tooltip('show');
		},

		// Load the dropdown autocomplete suggestions from AJAX on page loading and then apply autocomplete into the dropdown
		load_dropdown_data = function( e ) {
			let context = $( typeof e !== 'undefined' ? e.currentTarget : document.body ),
			    loaders = Config.loaders;

			$.each( loaders, function( index, loader ){
				if ( ! aepc_admin.actions.hasOwnProperty( loader.action ) ) {
					return;
				}

				// If already loaded data, simply apply autocomplete without make ajax request after
				if ( dropdown_data.hasOwnProperty( loader.action ) ) {
					if ( loader.dropdown !== '' ) {
						Utils.apply_autocomplete( context.find( loader.dropdown ), dropdown_data[ loader.action ] );
					}

					return;
				}

				// Create index, so if the function is triggered again before the ajax is complete, it doesn't call a new ajax call
				dropdown_data[ loader.action ] = [];

				$.ajax({
					url: aepc_admin.ajax_url,
					data: {
						action: aepc_admin.actions[ loader.action ].name,
						_wpnonce: aepc_admin.actions[ loader.action ].nonce
					},
					success: function( data ) {
						// Save data to avoid request again
						dropdown_data[ loader.action ] = data;
						if ( loader.dropdown !== '' ) {
							Utils.apply_autocomplete( context.find( loader.dropdown ), data );
						}
						$( document ).trigger( 'loader_data_loaded', [ dropdown_data ] );
					},
					dataType: 'json'
				});
			});

			// Specific cases
			context.find('#taxonomy_key').on( 'change.data', function(){
				let tax = $(this).val().replace( 'tax_', '' );

				if ( dropdown_data.hasOwnProperty( 'get_categories' ) && dropdown_data.get_categories.hasOwnProperty( tax ) ) {
					Utils.apply_autocomplete( context.find( '#taxonomy_terms' ), dropdown_data.get_categories[ tax ] );
				}
			});

			// Specific cases
			context.find('#tag_key').on( 'change.data', function(){
				let tax = $(this).val().replace( 'tax_', '' );

				if ( dropdown_data.hasOwnProperty( 'get_tags' ) && dropdown_data.get_tags.hasOwnProperty( tax ) ) {
					Utils.apply_autocomplete( context.find( '#tag_terms' ), dropdown_data.get_tags[ tax ] );
				}
			});

			// Specific cases
			context.find('#pt_key').on( 'change.data', function(){
				let post_type = $(this).val();

				if ( dropdown_data.hasOwnProperty( 'get_posts' ) && dropdown_data.get_posts.hasOwnProperty( post_type ) ) {
					Utils.apply_autocomplete( context.find( '#pt_posts' ), dropdown_data.get_posts[ post_type ] );
				}
			});

			// Trigger specific cases on fields shown, when is surely loaded ajax requests
			context.find('#event_categories').on( 'change.data', function(){
				context.find('#taxonomy_key').trigger('change.data');
			});

			// Trigger specific cases on fields shown, when is surely loaded ajax requests
			context.find('#event_tax_post_tag').on( 'change.data', function(){
				context.find('#tag_key').trigger('change.data');
			});

			// Trigger specific cases on fields shown, when is surely loaded ajax requests
			context.find('#event_posts').on( 'change.data', function(){
				context.find('#pt_key').trigger('change.data');
			});

			// Trigger specific cases on fields shown, when is surely loaded ajax requests
			context.find('#event_pages').on( 'change.data', function(){
				if ( dropdown_data.hasOwnProperty( 'get_posts' ) && dropdown_data.get_posts.hasOwnProperty( 'page' ) ) {
					Utils.apply_autocomplete( context.find( '#pages' ), dropdown_data.get_posts.page );
				}
			});

			// Trigger specific cases on fields shown, when is surely loaded ajax requests
			context.find('#event_custom_fields').on( 'change.data', function(e){
				let keys = [{ id: '[[any]]', text: aepc_admin.filter_any }];

				// Add the custom fields already loaded via ajax
				keys = $.merge( keys, dropdown_data.get_custom_fields );

				context.find('#custom_field_keys option').remove();
				context.find('#custom_field_keys').append( $.map(keys, function(v, i){
					if ( '[[any]]' === v.id ) {
						v.text = '--- ' + v.text + ' ---';
					}
					return $('<option>', { val: v.id, text: v.text });
				}) );
			});

			// Add ability to write an option not present on list of select
			context.find('.js-ecommerce input').on( 'change.data', function(){
				context.find('#dpa_key')
					.select2({
						placeholder: aepc_admin.filter_custom_field_placeholder,
						searchInputPlaceholder: aepc_admin.filter_custom_field_placeholder,
						data: { results: dropdown_data.get_dpa_params },
						query: function (query) {
							let data = {
								results: dropdown_data.get_dpa_params
							};

							if ( '' !== query.term ) {
								data.results = $.merge( [{id: query.term, text: query.term}], data.results );
							}

							// Filter matched
							data.results = data.results.filter( function( term ){
								return query.matcher( query.term, term.text );
							});

							query.callback(data);
						}
					})

					// Select the val
					.select2( 'data', { id: context.find('#dpa_key').val(), text: context.find('#dpa_key').val() } )

					// Remove value if the key change
					.on( 'change', function() {
						context.find('#dpa_value').val('');
					} )

					// Avoid to add more times the same event when the user changes only event radio
					.off( 'change.dpa' )

					.on( 'change.dpa', function(){
						let key = $(this).val(),
							tags = [];

						if ( 'content_ids' === key ) {
							if ( dropdown_data.hasOwnProperty( 'get_posts' ) ) {

								// WooCommerce product ids
								if ( dropdown_data.get_posts.hasOwnProperty( 'product' ) ) {
									tags = dropdown_data.get_posts.product.concat( tags );
								}

								// EDD product ids
								if ( dropdown_data.get_posts.hasOwnProperty( 'download' ) ) {
									tags = dropdown_data.get_posts.download.concat( tags );
								}
							}
						}

						else if ( 'content_category' === key ) {
							if ( dropdown_data.hasOwnProperty( 'get_categories' ) ) {

								// WooCommerce product categories
								if ( dropdown_data.get_categories.hasOwnProperty( 'product_cat' ) ) {
									tags = dropdown_data.get_categories.product_cat.concat( tags );
								}

								// EDD product categories
								if ( dropdown_data.get_categories.hasOwnProperty( 'download_category' ) ) {
									tags = dropdown_data.get_categories.download_category.concat( tags );
								}
							}
						}

						else if ( 'content_type' === key ) {
							tags = [ 'product', 'product_group' ];
						}

						else if ( 'currency' === key ) {
							if ( dropdown_data.hasOwnProperty( 'get_currencies' ) ) {
								tags = dropdown_data.get_currencies.map( function( tag ) {
									let txt = document.createElement("textarea");
									txt.innerHTML = tag.text;
									tag.text = txt.value;
									return tag;
								} );
							}
						}

						// Remove "anything" item repeated
						tags = tags.filter( function( item, index ){
							return ! ( index !== 0 && item.id === '[[any]]' );
						});

						context.find('#dpa_value').select2({
							tags: tags
						});
					})

					.triggerHandler( 'change.dpa' );
			});
		},

		bootstrap_init = function( e ) {
			let context = $( typeof e !== 'undefined' ? e.currentTarget : document );

			// Collapse for select
			context.find('select.js-collapse').on( 'change.bs', function(){
				let select = $(this),
					selected = select.find('option:selected');

				if ( ! context.find( selected.data('target') ).hasClass('in') ) {
					context.find( select.data('parent') ).find('.collapse').collapse('hide');
					context.find( selected.data('target') ).collapse('show');
				}
			}).trigger('change.bs');

			// Collapse for checkboxes
			context.find('input.js-collapse').on( 'change.bs', function(){
				let check = $(this),
					checked = check.filter(':checked');

				if ( ! context.find( checked.data('target') ).hasClass('in') ) {
					context.find( check.data('parent') ).find('.collapse').collapse('hide');
					context.find( checked.data('target') ).collapse('show');
				}
			}).trigger('change.bs');

			// Collapse out CA fields if event type select is changed
			context.find('#ca_event_type').on( 'change.bs', function(){
				context.find('.collapse-parameters').find('.collapse').collapse('hide');
				context.find('.js-collapse-events').find('input:checked').prop( 'checked', false );
			});

			// Dismiss popover when click outside
			$(document).on('click', function (e) {
				$('[data-toggle="popover"],[data-original-title]').each(function () {
					//the 'is' for buttons that trigger popups
					//the 'has' for icons within a button that triggers a popup
					if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
						(($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
					}

				});
			});

			Common.bootstrap_components( e );
		},

		ca_filter_adjust = function( form ) {
			let includeList = form.find('.js-include-filters'),
				excludeList = form.find('.js-exclude-filters'),
				filters = form.find('.js-ca-filters');

			// Hide the list if become empty
			if ( 0 === includeList.find('ul.list-filter').find('li').length ) {
				includeList.addClass('hide');
			} else {
				includeList.removeClass('hide');
			}
			if ( 0 === excludeList.find('ul.list-filter').find('li').length ) {
				excludeList.addClass('hide');
			} else {
				excludeList.removeClass('hide');
			}

			// Hide message feedback and show the list
			if ( includeList.hasClass('hide') && excludeList.hasClass('hide') ) {
				filters.find('div.no-filters-feedback').removeClass('hide');
			} else {
				filters.find('div.no-filters-feedback').addClass('hide');

				// Remove the AND operator from the first item of each list
				includeList.find('ul.list-filter').find('li:first').find('.filter-and').remove();
				excludeList.find('ul.list-filter').find('li:first').find('.filter-and').remove();
			}
		},

		ca_filter_form = function( e ){
			let modal = $(this),
				target = $( e.relatedTarget ),
				parentForm = target.closest('form');

			// Valid both add and edit
			modal.find( '#ca-filter-form' ).on( 'submit', function(e){
				e.preventDefault();

				let form = $(this),
					scope = form.data('scope'),
					filters = parentForm.find('.js-ca-filters'),
					filter_item = wp.template( 'ca-filter-item' ),
					main_condition = form.find('[name^="ca_rule[][main_condition]"]:checked' ),
					submitButton = form.find('button[type="submit"]'),
					submitButtonText = submitButton.text(),
					filter_list = filters.find( '.js-' + main_condition.val() + '-filters' ),
					fields =  main_condition
						.add( form.find('[name^="ca_rule[][event_type]"]') )
						.add( form.find('[name^="ca_rule[][event]"]:checked') )
						.add( form.find('.collapse-parameters .collapse.in').find('[name^="ca_rule[][conditions]"]') ),

					// Make an AJAX request to retrieve the statement to show
					add_filter = function( statement ){
						let hidden_fields = $('<div />'),
							index = 'add' === scope ? filters.find('li').length : target.closest('li').data('filter-id');

						// Remove feedback loader
						Utils.removeLoader( form );

						// Block and show error message if any event type is selected
						if ( !statement || 0 === statement.length ) {
							Utils.addMessage( form.find('.modal-body'), 'error', aepc_admin.filter_no_condition_error );
							submitButton.text( submitButtonText );
							return;
						}
						// Create all hidden fields with proper name
						fields.each( function(){
							let field = $(this),
								name = field.attr('name'),
								value = field.val();

							hidden_fields.append( $('<input />', {
								type: 'hidden',
								name: name.replace( '[]', '[' + index + ']' ),
								value: value
							}) );
						});

						// Apply template
						let itemTpl = filter_item({
							nfilters: filter_list.find('li').length - ( 'edit' === scope && $.contains( filter_list.get()[0], target.get()[0] ) ? 1 : 0 ),
							statement: statement,
							hidden_inputs: hidden_fields.html(),
							index: index
						});

						// Edit only if we are in edit mode and the element to edit is contained in the list of main_condition
						if ( 'edit' === scope && $.contains( filter_list.get()[0], target.get()[0] ) ) {
							target.closest('li').html( $( itemTpl ).html() );
						} else {
							filter_list.find('ul').append( itemTpl );

							// Remove the element target if we have to change list
							if ( 'edit' === scope && ! $.contains( filter_list.get()[0], target.get()[0] ) ) {
								target.closest('li').remove();
							}
						}

						// Show/hide lists when changed
						ca_filter_adjust( parentForm );

						// close modal
						form.closest('.modal').modal('hide');

						form.off( 'submit' );

					};

				// Remove some eventual error
				Utils.removeMessage( form.find('.modal-body'), 'error' );

				// Block and show error message if any event type is selected
				if ( form.find('.js-collapse-events input:checked').length === 0 ) {
					Utils.addMessage( form.find('.modal-body'), 'error', aepc_admin.filter_no_data_error );
					return;
				}

				// Add feedback loader
				Utils.addLoader( form );

				// Give feedback to user while ajax request run
				submitButton.text( aepc_admin.filter_saving );

				$.ajax({
					url: aepc_admin.ajax_url,
					method: 'GET',
					data: {
						filter: fields.serializeArray(),
						action: aepc_admin.actions.get_filter_statement.name,
						_wpnonce: aepc_admin.actions.get_filter_statement.nonce
					},
					success: add_filter,
					dataType: 'html'
				});
			});

		},

		ca_filter_actions = function( e ) {
			let context = $( typeof e !== 'undefined' ? e.currentTarget : document.body );

			context.find('.list-filter')

				// Delete filter
				.on( 'click', '.btn-delete', function(e) {
					e.preventDefault();

					let form = $(this).closest('form'),
						modal = $('#modal-confirm-delete'),
						itemToRemove = $(this).closest('li');

					modal

					// Show modal
						.modal('show', $(this))

						// confirm action
						.one( 'click', '.btn-ok', function() {
							modal.modal('hide');

							// Remove the item
							itemToRemove.remove();

							// Show/hide lists when changed
							ca_filter_adjust( form );
						});
				})

				// Edit filter
				.on( 'click', '.btn-edit', function(e) {
					e.preventDefault();

					let form = $(this).closest('form'),
						modal = $('#modal-ca-edit-filter'),
						itemToEdit = $(this).closest('li'),
						fields = itemToEdit.find('.hidden-fields input');

					modal

						// Compile form with data
						.on( 'modal-template-loaded', function( event ){
							let form = $(this).find('form');

							// Set main condition
							let main_condition = fields.filter('[name*="[main_condition]"]').val();
							form.find('input[name*="main_condition"][value="' + main_condition + '"]')
								.prop( 'checked', true )
								.closest('label')
								.addClass('active')
								.siblings()
								.removeClass('active');

							// Set event type
							let event_type = fields.filter('[name*="[event_type]"]').val(),
								event_type_field = form.find('select[name*="event_type"]').val( event_type );

							// Set event
							let event_name = fields.filter('[name*="[event]"]').val(),
								event_field = form.find('input[name*="event"][value="' + event_name + '"]').prop( 'checked', true );

							// Set conditions
							let conditions_wrap = form.find( event_field.data('target') ),
								condition_key = fields.filter('[name*="[conditions][0][key]"]').val(),
								condition_operator = fields.filter('[name*="[conditions][0][operator]"]').val(),
								condition_value = fields.filter('[name*="[conditions][0][value]"]').val();

							// Exception for custom fields select, because it will generate the options manually on load_dropdown_data function
							if ( conditions_wrap.find('[name*="[conditions][0][key]"]').is('#custom_field_keys') ) {
								conditions_wrap.find('#custom_field_keys').append( $('<option />', { val: condition_key, text: condition_key }) );
							}

							conditions_wrap.find('[name*="[conditions][0][key]"]').val( condition_key );
							conditions_wrap.find('[name*="[conditions][0][operator]"]').val( condition_operator );
							conditions_wrap.find('[name*="[conditions][0][value]"]').val( condition_value );
						})

						.one( 'show.bs.modal', function(){
							let form = $(this).find('form');

							form.find('[name*="event_type"]:checked').trigger('change.data');
							form.find('[name*="event"]:checked').trigger('change.data');
							form.find('.collapse.in [name*="[conditions][0][key]"]').trigger('change.data');
							form.find('.collapse.in [name*="[conditions][0][operator]"]').trigger('change.data');
							form.find('.collapse.in [name*="[conditions][0][value]"]').trigger('change.data');
						})

						.modal('show', $(this) );
				});
		},

		load_facebook_options_box = function( e ){
			let context = typeof e !== 'undefined' ? $(this) : $( document.body ),  // it could be a modal
				account_ids = context.find('select#aepc_account_id'),
				pixel_ids = context.find('select#aepc_pixel_id'),
				saved_account_id = $('form#mainform').find('#aepc_account_id').val(),
				saved_pixel_id = $('form#mainform').find('#aepc_pixel_id').val(),

				populate_pixel_ids = function() {
					let account_id = account_ids.val() ? JSON.parse( account_ids.val() ).id : '';

					if ( ! dropdown_data.hasOwnProperty( 'get_pixel_ids' ) || ! dropdown_data.get_pixel_ids.hasOwnProperty( account_id ) ) {
						return;
					}

					let keys = $.merge( [{ id: '', text: '' }], dropdown_data.get_pixel_ids[ account_id ] );

					// Add placeholder if any value is present on dropdown
					if ( 1 === keys.length ) {
						keys[0].text = aepc_admin.fb_option_no_pixel;
						pixel_ids.prop( 'disabled', true );
					} else {
						pixel_ids.prop( 'disabled', false );
					}

					pixel_ids.find('option').remove();
					pixel_ids.append( $.map(keys, function(v, i){
						return $('<option>', { val: v.id, text: v.text, selected: v.id === saved_pixel_id });
					}) );

					// Select if there is only one option
					if ( pixel_ids.find('option').length === 2 ) {
						pixel_ids.find('option:eq(1)').prop('selected', true);
					}

					pixel_ids.val( pixel_ids.find('option:selected').val() ).trigger('change');
				},

				load_pixel_ids = function() {
					let account_id = account_ids.val() ? JSON.parse( account_ids.val() ).id : '';

					// Add loader feedback on select
					Utils.addLoader( pixel_ids );

					$.ajax({
						url: aepc_admin.ajax_url,
						data: {
							action: aepc_admin.actions.get_pixel_ids.name,
							_wpnonce: aepc_admin.actions.get_pixel_ids.nonce,
							account_id: account_id
						},
						success: function( data ) {
							// Save data to avoid request again
							if ( ! dropdown_data.hasOwnProperty( 'get_pixel_ids' ) ) {
								dropdown_data.get_pixel_ids = {};
							}
							dropdown_data.get_pixel_ids[ account_id ] = data;
							populate_pixel_ids();

							// Remove loader from select
							Utils.removeLoader( pixel_ids );
						},
						dataType: 'json'
					});
				},

				init_pixel_dropdown = function( e ) {
					if ( typeof e !== 'undefined' && e.hasOwnProperty( 'type' ) && 'change' === e.type ) {
						pixel_ids.val('').trigger('change');
						pixel_ids.find('option').remove();
					}

					if ( account_ids.val() ) {
						let account_id = account_ids.val() ? JSON.parse( account_ids.val() ).id : '';

						if ( ! dropdown_data.hasOwnProperty( 'get_pixel_ids' ) || ! dropdown_data.get_pixel_ids.hasOwnProperty( account_id ) ) {
							load_pixel_ids();
						} else {
							populate_pixel_ids();
						}
					}
				},

				populate_account_ids = function() {
					if ( ! dropdown_data.hasOwnProperty( 'get_account_ids' ) ) {
						return;
					}

					let keys = $.merge( [{ id: '', text: '' }], dropdown_data.get_account_ids );

					account_ids.find('option').remove();
					account_ids.append( $.map(keys, function(v, i){
						return $('<option>', { val: v.id, text: v.text, selected: v.id === saved_account_id });
					}) );

					account_ids.on( 'change', init_pixel_dropdown ).trigger('change');
				},

				load_account_ids = function() {

					// Add loader feedback on select
					Utils.addLoader( account_ids );

					$.ajax({
						url: aepc_admin.ajax_url,
						data: {
							action: aepc_admin.actions.get_account_ids.name,
							_wpnonce: aepc_admin.actions.get_account_ids.nonce
						},
						success: function( data ) {
							if ( false === data.success ) {
								Utils.addMessage( $('.js-options-group'), 'error', data.data );
								Common.set_saved();
							}

							else {
								// Save data to avoid request again
								dropdown_data.get_account_ids = data;
								populate_account_ids();
							}

							// Remove loader from select
							Utils.removeLoader( account_ids );
						},
						dataType: 'json'
					});
				},

				init_account_dropdown = function() {
					if ( account_ids.length <= 0 ) {
						return;
					}

					if ( ! dropdown_data.hasOwnProperty( 'get_account_ids' ) ) {
						load_account_ids();
					} else {
						populate_account_ids();
					}
				};

			if ( saved_account_id && saved_pixel_id ) {
				let saved_account = JSON.parse( saved_account_id ),
					saved_pixel = JSON.parse( saved_pixel_id );

				account_ids.append( $('<option>', { val: saved_account_id, text: saved_account.name + ' (#' + saved_account.id + ')', selected: true }) ).trigger('change');
				pixel_ids.append( $('<option>', { val: saved_pixel_id, text: saved_pixel.name + ' (#' + saved_pixel.id + ')', selected: true }) ).trigger('change');
			}

			// Init dropdown, making ajax requests and loading options into selects
			init_account_dropdown();
			init_pixel_dropdown();

		};

	// Init configurations
	init_configs();

	// Load the custom fields by AJAX
	load_dropdown_data();

	// Inizialization Bootstrap components
	bootstrap_init();

	// Apply custom dropdown
	Common.custom_dropdown();

	// Load the account and pixel ids on facebook options dropdown, if the user is logged in but not configured
	load_facebook_options_box();

	// Inizialization Page components
	Common.fields_components();

	// Initialize filter actions (edit and delete)
	ca_filter_actions();

	// Other delete modals
	$('.modal-confirm').on( 'show.bs.modal', function(e){
		let modal = $(this),
			deleteLink = e.hasOwnProperty('relatedTarget') ? $( e.relatedTarget ).attr('href') : '';

		if ( $.inArray( deleteLink, [ undefined, '', '#', '#_' ] ) < 0 ) {
			modal.one( 'click', '.btn-ok', function(e){
				e.preventDefault();

				let actions = {
						'fb-disconnect': 'fb_pixel_box',
						'ca-delete': 'ca_list',
						'conversion-delete': 'conversions_list'
					},
					action = deleteLink.match( new RegExp( 'action=(' + Object.keys( actions ).join('|') + ')(&|$)' ) );

				// Custom actions
				if ( action ) {

					Utils.addLoader( modal.find('.modal-content') );

					$.ajax({
						url: deleteLink + ( deleteLink.indexOf('?') ? '&' : '?' ) + 'ajax=1',
						method: 'GET',
						success: function( response ) {
							if ( response.success ) {

								$('.sec-overlay').removeClass('sec-overlay');
								$('.sub-panel-fb-connect.bumping').removeClass('bumping');

								Utils.reloadFragment( actions[ action[1] ] );

								// hide modal
								modal.modal('hide');

								// Remove feedback loader
								Utils.removeLoader( modal.find('.modal-content') );

								// Remove eventually fblogin if exists
								if ( window.history && window.history.pushState ) {
									let redirect_uri = window.location.href.replace( /(\?|\&)ref=fblogin/, '' );
									window.history.pushState( { path: redirect_uri }, '', redirect_uri) ;
								}
							}
						},
						dataType: 'json'
					});
				}

				else {
					modal.modal('hide');
					window.location = deleteLink;
				}
			});
		}
	});

	// Edit modals
	$('.js-form-modal')

		// Apply tdynamic template
		.on( 'show.bs.modal', function( event ){
			if ( typeof wp === 'undefined' ) {
				return event;
			}

			let modal = $(this),
				link = $( event.relatedTarget ),
				data = link.data('config'),
				formTmpl = wp.template( modal.attr('id') );

			modal.find('.modal-content').html( formTmpl( data ) );

			// Trigger event to hook somethings
			modal.trigger( 'modal-template-loaded' );
		})

		.on( 'show.bs.modal', bootstrap_init )
		.on( 'show.bs.modal', Common.custom_dropdown )
		.on( 'show.bs.modal', load_dropdown_data )
		.on( 'show.bs.modal', Common.fields_components )
		.on( 'show.bs.modal', ca_filter_form )
		.on( 'show.bs.modal', ca_filter_actions );

	// Submit form via AJAX
	$( document ).on( 'submit', 'form[data-toggle="ajax"]', function(e){
		e.preventDefault();

		let form = $(this),
			messageWrapper = form,
			submitButton = form.find('[type="submit"]'),
			submitText = submitButton.text(),
			formTopPosition = form.offset().top - 50;

		// Adjust message wrapper
		if ( form.find('.modal-body').length ) {
			messageWrapper = form.find('.modal-body').first();
		} else if ( form.find('.panel-body').length ) {
			messageWrapper = form.find('.panel-body').first();
		}

		// Remove all errors and change text of submit button
		Utils.removeMessage( messageWrapper, 'error' );
		form.find( '.has-error' ).removeClass('has-error');
		form.find( '.help-block-error' ).remove();

		// Add feedback loader
		Utils.addLoader( form );

		$.ajax({
			url: aepc_admin.ajax_url,
			method: 'POST',
			data: form.serialize(),
			success: function( response ) {
				if ( response.success ) {
					let modal_actions = {
							'fb-connect-options': 'fb_pixel_box',
							'ca-clone': 'ca_list',
							'ca-edit': 'ca_list',
							'conversion-edit': 'conversions_list'
						},
						modal_ids = Object.keys( modal_actions ).map( function( key ){ return '#modal-' + key; } ).join(','),

						form_actions = {};

					if ( form.closest( '.modal' ).length && form.closest('.modal').is( modal_ids ) ) {
						Utils.reloadFragment( modal_actions[ form.closest( '.modal' ).attr('id').replace('modal-', '') ] );

						// hide modal
						form.closest( '.modal' ).modal('hide');

						// Remove feedback loader
						Utils.removeLoader( form );

						// Remove eventually fblogin if exists
						if ( window.history && window.history.pushState ) {
							let redirect_uri = window.location.href.replace( /(\?|\&)ref=fblogin/, '' );
							window.history.pushState( { path: redirect_uri }, '', redirect_uri) ;
						}
					}

					else if ( Object.keys( form_actions ).indexOf( form.data('action') ) >= 0 )  {
						Utils.reloadFragment( form_actions[ form.data('action') ] );

						// Remove feedback loader
						Utils.removeLoader( form );
					}

					else if ( response.data.html && response.data.fragment )  {
						Utils.refreshFragmentHTML( $( Config.fragments[ response.data.fragment ] ), response );

						// Remove feedback loader
						Utils.removeLoader( form );
					}

					else {
						let action_uri = form.attr( 'action' );

						if ( action_uri ) {
							window.location.href = action_uri;
						} else {
							window.location.reload(false);
						}
					}
				}

				// Perform error
				else {

					// Add main notice
					if ( response.data.hasOwnProperty( 'refresh' ) && response.data.refresh ) {
						window.location.href = window.location.href.replace( /(\?|\&)ref=fblogin/, '' );
						return;
					}

					// Remove feedback loader
					Utils.removeLoader( form );

					// Scroll to form top
					$( 'html, body' ).animate( { scrollTop: formTopPosition }, 300 );

					// Reset text of submit button
					submitButton.text( submitText );

					// Add main notice
					if ( response.data.hasOwnProperty( 'main' ) ) {
						Utils.addMessage( messageWrapper, 'error', response.data.main.map(function(item){ return item.text }).join( '<br/>' ) );
					}

					// Add error to each field
					form.find('input, select').each( function(){
						let field = $(this),
							field_id = field.attr('id'),
							formGroup = field.closest('.form-group'),
							fieldHelper = field.closest('.control-wrap').find('.field-helper');

						if ( response.data.hasOwnProperty( field_id ) ) {
							formGroup.addClass('has-error');
							fieldHelper.append( $('<span />', {
								class: 'help-block help-block-error', html: response.data[ field_id ].map(function(item){ return item.text }).join( '<br/>' )
							}) );
						}

						// Remove the error on change, because bootstrap material remove .has-error on keyup change events
						field.on( 'keyup change', function(){
							fieldHelper.find('.help-block-error').remove();
						});
					});
				}
			},
			dataType: 'json'
		});
	});

	// Alert position
	$( window )
		.on( 'load', Common.analyzed_distance )
		.on( 'scroll', Common.analyzed_distance )
		.on( 'resize', Common.analyzed_distance );

	// Facebook options modal actions
	$( '#modal-fb-connect-options' )

		// Apply tdynamic template
		.on( 'show.bs.modal', function( event ){
			if ( typeof wp === 'undefined' ) {
				return event;
			}

			let modal = $(this),
				formTmpl = wp.template( 'modal-facebook-options' );

			modal.find('.modal-content').html( formTmpl( [] ) );

			// Trigger event to hook somethings
			modal.trigger( 'facebook-options-loaded' );
		})

		.on( 'show.bs.modal', bootstrap_init )
		.on( 'show.bs.modal', Common.custom_dropdown )

		.on( 'show.bs.modal', load_facebook_options_box );

	// Facebook options save
	$( '.sub-panel-fb-connect' )

		.on( 'change', '#aepc_account_id', function() {
			let account_id = $(this).val(),
				pixel_id = $( '#aepc_pixel_id' ).val();

			if ( account_id && pixel_id ) {
				$('.js-save-facebook-options').removeClass('disabled');
			} else {
				$('.js-save-facebook-options').addClass('disabled');
			}
		})

		.on( 'change', '#aepc_pixel_id', function() {
			let account_id = $( '#aepc_account_id' ).val(),
				pixel_id = $(this).val();

			if ( account_id && pixel_id ) {
				$('.js-save-facebook-options').removeClass('disabled');
			} else {
				$('.js-save-facebook-options').addClass('disabled');
			}
		})

		.on( 'click', '.js-save-facebook-options:not(.disabled)', function(e) {
			let account_id = $( '#aepc_account_id' ).val(),
				pixel_id = $( '#aepc_pixel_id' ).val();

			$('.sec-overlay').removeClass('sec-overlay');
			$('.sub-panel-fb-connect.bumping').removeClass('bumping');

			Utils.addLoader( $( '.panel.panel-settings-set-fb-px' ) );

			$.ajax({
				url: aepc_admin.ajax_url,
				method: 'POST',
				data: {
					aepc_account_id: account_id,
					aepc_pixel_id: pixel_id,
					action: aepc_admin.actions.save_facebook_options.name,
					_wpnonce: aepc_admin.actions.save_facebook_options.nonce
				},
				success: function( response ) {

					if ( response.success ) {
						if ( window.history && window.history.pushState ) {
							let redirect_uri = window.location.href.replace( /(\?|\&)ref=fblogin/, '' );
							window.history.pushState( { path: redirect_uri }, '', redirect_uri) ;
						}

						Utils.reloadFragment( 'fb_pixel_box' );
						Common.set_saved();
					}

				},
				dataType: 'json'
			});
		});

	// Custom audience sync action
	$('.wrap-custom-audiences').on('click', '.js-ca-size-sync', function(e){
		let button = $(this),
			ca_id = button.data('ca_id');

		// Remove eventually error messages
		Utils.removeMessage( $('.plugin-content .alert-wrap'), 'error' );

		Utils.addLoader( $('.panel.panel-ca-list') );
		button.addClass( 'loading-data' );

		$.ajax({
			url: aepc_admin.ajax_url,
			method: 'GET',
			data: {
				ca_id: ca_id,
				action: aepc_admin.actions.refresh_ca_size.name,
				_wpnonce: aepc_admin.actions.refresh_ca_size.nonce
			},
			success: function( response ) {
				if ( response.success ) {
					Utils.reloadFragment( 'ca_list' );
				} else {
					Utils.addMessage( $('.plugin-content .alert-wrap'), 'error', response.data.message );
				}
			},
			dataType: 'json'
		});
	});

	// Perform pagination in ajax
	$('.wrap').on( 'click', '.pagination li a', function(e){
		e.preventDefault();

		let link = $(this),
			uri = link.attr('href'),
			paged = uri.match( /paged=([0-9]+)/ );

		if ( $(this).closest( '.panel-ca-list' ).length ) {
			Utils.reloadFragment( 'ca_list', { paged: paged[1] } );
		} else if ( $(this).closest( '.panel-ce-tracking' ).length ) {
			Utils.reloadFragment( 'conversions_list', { paged: paged[1] } );
		} else if ( $(this).closest( '.panel-log-list' ).length ) {
			Utils.reloadFragment( 'logs_list', { paged: paged[1] } );
		}

		if ( window.history && window.history.pushState ) {
			window.history.pushState( { path: uri }, '', uri );
		}
	});

	// Load sidebar feed data
	if ( $('.plugin-sidebar.loading-sec').length ) {
		Utils.reloadFragment( 'sidebar' );
	}

	// HACK avoid scrolling problem when open a modal inside another one and then close the last modal
	let last_modal_opened = [];
	$('.modal')
		.on( 'show.bs.modal', function(e){
			last_modal_opened.push(e);
		})
		.on( 'hidden.bs.modal', function(e){
			if ( $( last_modal_opened[ last_modal_opened.length - 1 ].relatedTarget ).closest('.modal').length ) {
				$('body').addClass('modal-open');
				last_modal_opened.splice( last_modal_opened.length - 1, 1 );
			}
		});

	// Perform clear transient by ajax
	$('#aepc-clear-transients').on( 'click', function(e){
		e.preventDefault();

		let button = $(this);

		Utils.addLoader( button );

		$.ajax({
			url: aepc_admin.ajax_url,
			method: 'POST',
			data: {
				action: aepc_admin.actions.clear_transients.name,
				_wpnonce: aepc_admin.actions.clear_transients.nonce
			},
			success: function( response ) {
				Utils.removeLoader( button );

				if ( response.success ) {
					Utils.addMessage( $('.plugin-content .alert-wrap'), 'success', response.data.message );
				}
			},
			dataType: 'json'
		});
	});

	// Perform clear transient by ajax
	$('#aepc-reset-fb-connection').on( 'click', function(e){
		e.preventDefault();

		let button = $(this);

		Utils.addLoader( button );

		$.ajax({
			url: aepc_admin.ajax_url,
			method: 'POST',
			data: {
				action: aepc_admin.actions.reset_fb_connection.name,
				_wpnonce: aepc_admin.actions.reset_fb_connection.nonce
			},
			success: function( response ) {
				Utils.reloadFragment( 'fb_pixel_box', {
					success: function( data ) {
						Utils.removeLoader( button );

						if ( response.success ) {
							Utils.addMessage( $('.plugin-content .alert-wrap'), 'success', response.data.message );
						}
					}
				} );
			},
			dataType: 'json'
		});
	});

	// Auto-check eCommerce tracking option when one of the events inside is checked
	$('.ecomm-conversions').find('input[type="checkbox"]').on('change', function(){
		let $enable_dpa_input = $('#aepc_enable_dpa');

		if ( ! $enable_dpa_input.is(':checked') ) {
			$enable_dpa_input.prop('checked', true).trigger('change');
		}
	});

	// Trigger ajax actions if any in the dismiss buttons
	$( document ).on( 'click', 'button[data-dismiss][data-dismiss-action]', function(){
		const dismissAction = $( this ).data('dismiss-action');

		$.ajax({
			url: aepc_admin.ajax_url,
			method: 'GET',
			data: {
				notice_id: dismissAction,
				action: aepc_admin.actions.dismiss_notice.name,
				_wpnonce: aepc_admin.actions.dismiss_notice.nonce
			},
			dataType: 'json'
		});
	});

	$( document ).on('click', '.js-remove-logs', function(e){
		e.preventDefault();

		let fragment = $( Config.fragments[ 'logs_list' ] ),
			modal = $('#modal-confirm-delete');

		modal

		// Show modal
			.modal('show', $(this))

			// confirm action
			.one( 'click', '.btn-ok', function() {
				modal.modal('hide');

				Utils.addLoader( fragment );

				$.ajax({
					url: aepc_admin.ajax_url,
					method: 'GET',
					data: {
						action: aepc_admin.actions.clear_logs.name,
						_wpnonce: aepc_admin.actions.clear_logs.nonce
					},
					success: function( response ) {
						Utils.refreshFragmentHTML( fragment, response );
					},
					dataType: 'json'
				});
			});
	});

	// Triggers change in all input fields including text type, must be run after all components init
	Utils.alert_unsaved();

});
