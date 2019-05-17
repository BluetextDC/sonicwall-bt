/**
 * UI scripts for admin settings page
 */

import $ from 'jquery';
import Utils from './utils';
import Config from './config';
import Common from './common';
import 'select2';

jQuery(document).ready(function(){
    'use strict';

    let productStatusRefreshing = false,

		existing_catalog_options = function() {
    		let productCatalogSelector = 'select.js-product-catalogs',
				productFeedSelector = 'select.js-product-feeds',
				productCatalogOption = $( productCatalogSelector ),
				productFeedOption = $( productFeedSelector ),
				productCatalogNameOption = $('#product_catalog_config_schedule-update_fb_product_catalog_name'),
				productFeedNameOption = $('#product_catalog_config_schedule-update_fb_product_feed_name'),
				optionsWrapper = $( Config.fragments['product_feed_schedule'] );

			// Populate product feeds
			const init_product_feeds_dropdown = function(){
				let saved_product_feed_id = productFeedOption.val(),
					saved_product_catalog_id = productCatalogOption.val();

				if ( ! saved_product_catalog_id || productFeedOption.length <= 0 ) {
					return;
				}

				const
					populate_product_feed_ids = function() {
						if ( ! Common.dropdown_data.hasOwnProperty( 'get_product_feed_ids' ) || ! Common.dropdown_data.get_product_feed_ids.hasOwnProperty( saved_product_catalog_id ) ) {
							return;
						}

						let keys = Common.dropdown_data.get_product_feed_ids[ saved_product_catalog_id ];

						productFeedOption.prop( 'disabled', false );

						productFeedOption.append( $.map(keys, function(v, i){
							return $('<option>', {
								val: v.id,
								text: v.name + ' (#' + v.id + ')',
								selected: v.id === saved_product_feed_id,
								'data-name': v.name
							});
						}) );

						productFeedOption.on( 'change', function() {
							let selected = $(this).val();

							productFeedNameOption.val( $(this).find('option:selected').attr('data-name') );

							optionsWrapper.removeClass('hide');
							Utils.reloadFragment( 'product_feed_schedule', {
								product_feed_id: selected
							} );
						});
					},

					load_product_feed_ids = function() {
						// Add loader feedback on select
						Utils.addLoader( productFeedOption );

						// Reset select
						productFeedOption.find('option[value]').not('.select2-add').remove();

						$.ajax({
							url: aepc_admin.ajax_url,
							data: {
								product_catalog_id: saved_product_catalog_id,
								action: aepc_admin.actions.get_product_feed_ids.name,
								_wpnonce: aepc_admin.actions.get_product_feed_ids.nonce
							},
							success: function( data ) {
								if ( false === data.success ) {
									Utils.addMessage( $('#fb-update-catalog'), 'error', data.data );
									productCatalogOption.select2('val', '');
								}

								else {
									// Save data to avoid request again
									if ( ! Common.dropdown_data.hasOwnProperty( 'get_product_feed_ids' ) ) {
										Common.dropdown_data.get_product_feed_ids = {};
									}

									// Save data to avoid request again
									Common.dropdown_data.get_product_feed_ids[ saved_product_catalog_id ] = data.data;
									populate_product_feed_ids();
								}

								// Remove loader from select
								Utils.removeLoader( productFeedOption );
							},
							dataType: 'json'
						});
					};

				if ( ! Common.dropdown_data.hasOwnProperty( 'get_product_feed_ids' ) || ! Common.dropdown_data.get_product_feed_ids.hasOwnProperty( saved_product_catalog_id )  ) {
					load_product_feed_ids();
				} else {
					populate_product_feed_ids();
				}
			};

			// Populate product catalogs
			const init_product_catalog_dropdown = function(){
				let saved_product_catalog_id = productCatalogOption.val();

				if ( productCatalogOption.length <= 0 ) {
					return;
				}

				const
					populate_product_catalog_ids = function() {
						if ( ! Common.dropdown_data.hasOwnProperty( 'get_product_catalog_ids' ) ) {
							return;
						}

						let keys = $.merge( [{ id: '', name: '' }], Common.dropdown_data.get_product_catalog_ids );

						productCatalogOption.find('option').remove();
						productCatalogOption.append( $.map(keys, function(v, i){
							return $('<option>', {
								val: v.id,
								text: v.name ? v.name + ' (#' + v.id + ')' : '',
								selected: v.id === saved_product_catalog_id,
								'data-name': v.name
							});
						}) );

						productCatalogOption.on( 'change', function() {
							productCatalogNameOption.val( $(this).find('option:selected').attr('data-name') );
							init_product_feeds_dropdown();
						});

						if ( productCatalogOption.find('option:selected').length ) {
							let tmp_unsaved = Common.unsaved;
							productCatalogOption.trigger('change');
							Common.unsaved = tmp_unsaved;
						}
					},

					load_product_catalog_ids = function() {
						// Add loader feedback on select
						Utils.addLoader( productCatalogOption );

						$.ajax({
							url: aepc_admin.ajax_url,
							data: {
								action: aepc_admin.actions.get_product_catalog_ids.name,
								_wpnonce: aepc_admin.actions.get_product_catalog_ids.nonce
							},
							success: function( data ) {
								if ( false === data.success ) {
									Utils.addMessage( $('#fb-update-catalog'), 'error', data.data );
								}

								else {
									// Save data to avoid request again
									Common.dropdown_data.get_product_catalog_ids = data.data;
									populate_product_catalog_ids();
								}

								// Remove loader from select
								Utils.removeLoader( productCatalogOption );
							},
							dataType: 'json'
						});
					};

				if ( ! Common.dropdown_data.hasOwnProperty( 'get_product_catalog_ids' ) ) {
					load_product_catalog_ids();
				} else {
					populate_product_catalog_ids();
				}
			};

			init_product_catalog_dropdown();
			init_product_feeds_dropdown();
		},

		initFields = function() {
			// Load google category select2
			$('.js-google-category').each(function(){
				let select = $(this),
					googleCategorySelect2 = function( el ) {
						let inputsSelector = 'input.js-google-category';

						el.select2({
							data: function() {
								let options = el.data('options');
								return {
									results: options ? options.map( function(item){
										return {
											id: item,
											text: item
										}
									} ) : []
								};
							}
						});

						el
							.on( 'change', function() {
								let select = $(this),
									wrapper = select.closest('.js-categories-wrapper'),
									sample = select.clone()
										.val('')
										.data('options', '')
										.data('level', parseInt( select.data('level') ) + 1 );

								// Remove next selects
								select.nextAll('input').select2('destroy').remove();

								Utils.addLoader( wrapper );

								$.ajax({
									url: aepc_admin.ajax_url,
									method: 'POST',
									data: {
										parents: wrapper.find( inputsSelector ).map(function(){
											return $( this ).val();
										}).get(),
										action: aepc_admin.actions.get_google_categories.name,
										_wpnonce: aepc_admin.actions.get_google_categories.nonce
									},
									complete: function() {
										Utils.removeLoader( wrapper );
									},
									success: function( response ) {
										if ( response.length === 0 ) {
											return;
										}

										sample.data('options', response ).insertAfter( select );
										googleCategorySelect2( sample );
									},
									dataType: 'json'
								});
							});
					};

				googleCategorySelect2( select );
			});

			// Load product types in select2
			$('input.multi-tags[data-tags]').each(function() {
				let input = $(this);
				input.select2({
					tags: function() {
						return input.data('tags')
					}
				});
			});
		},

		heartBeatCB = function() {
			if (
				productStatusRefreshing
				|| $( Config.fragments['product_feed_status'] ).length === 0
				|| ! $( Config.fragments['product_feed_status'] ).hasClass('updating')
			) {
				return;
			}

			setTimeout( function(){
				productStatusRefreshing = true;

				Utils.reloadFragment('product_feed_status', {
					beforeRender: function() {
						Utils.removeAllMainMessages();
					},
					success: function( response ) {
						productStatusRefreshing = false;
						initFields();
						existing_catalog_options();
						heartBeatCB();
					}
				}, false);
			}, 3000 );
		};

    // It necessary for saving action
	$( document ).on( 'fragment_html_refreshed', function() {
		initFields();
		heartBeatCB();
	} );

	// Delete feed
	$( document ).on( 'click', '.js-feed-delete', function(e){
		e.preventDefault();

		let button = $(this),
			modal = $('#modal-confirm-delete'),
			feedId = button.data('feed-id');

		modal

		// Show modal
			.modal('show', button )

			// confirm action
			.one( 'click', '.btn-ok', function() {
				modal.modal('hide');

				Utils.addLoader( button.closest('.panel') );

				$.ajax({
					url: aepc_admin.ajax_url,
					method: 'POST',
					data: {
						name: feedId,
						action: aepc_admin.actions.delete_product_catalog_feed.name,
						_wpnonce: aepc_admin.actions.delete_product_catalog_feed.nonce
					},
					success: function( response ) {
						window.location.reload(false);
						return;
					},
					dataType: 'json'
				});
			});
	});

	// Edit
	$( document ).on( 'click', '.js-feed-edit', function(e){
		e.preventDefault();
		$('.js-edit-form').collapse('show');
	});

	// Delete feed
	$( document ).on( 'click', '.js-product-feed-refresh', function(e){
		e.preventDefault();

		let button = $(this),
			modal  = $('#modal-confirm-refresh-product-feed'),
			feedId = button.data('feed-id');

		modal

		// Show modal
			.modal('show', button )

			// confirm action
			.one( 'click', '.btn-ok', function() {
				modal.modal('hide');

				Utils.addLoader( button.closest('.panel') );
				Utils.removeAllMainMessages();

				$.ajax({
					url: aepc_admin.ajax_url,
					method: 'POST',
					data: {
						name: feedId,
						action: aepc_admin.actions.refresh_product_catalog_feed.name,
						_wpnonce: aepc_admin.actions.refresh_product_catalog_feed.nonce
					},
					complete: function() {
						Utils.removeLoader( button.closest('.panel') );
					},
					success: function( response ) {
						Utils.removeAllMainMessages();
						Utils.refreshFragmentHTML( $( Config.fragments['product_feed_status'] ), response );
						initFields();
						heartBeatCB();
					},
					dataType: 'json'
				});
			});
	});

	// Save refresh interval option
	$(document).on( 'click', '.js-product-feed-save-interval', function(){
		let button = $(this),
			buttonWrapper = button.closest('.js-refresh-interval-option'),
			cycleOption = $('#product_catalog_config_refresh_cycle'),
			cycleTypeOption = $('#product_catalog_config_refresh_cycle_type'),
			productCatalogName = button.data('feed-id');

		Utils.addLoader( buttonWrapper );

		$.ajax({
			url: aepc_admin.ajax_url,
			method: 'POST',
			data: {
				product_catalog_id: productCatalogName,
				cycle: cycleOption.val(),
				cycle_type: cycleTypeOption.val(),
				action: aepc_admin.actions.save_product_feed_refresh_interval.name,
				_wpnonce: aepc_admin.actions.save_product_feed_refresh_interval.nonce
			},
			complete: function() {
				Utils.removeLoader( buttonWrapper );
			},
			success: function( response ) {
				Utils.addMessagesFromResponse( response );
				Common.set_saved();
			},
			dataType: 'json'
		});
	});

	/**
	 * Automatic Facebook Uploading options
	 */

	// Make AJAX request to create the product catalog
	$( document )

		.on( 'click', '.js-catalog-option', function(e){
			let parent = $(this).closest('#automatic-facebook-options'),
				panels = parent.find('.panel'),
				selectedPanel = $( $(this).data('target') );

			if ( ! selectedPanel.is('.hide') ) {
				return e;
			}

			panels.addClass('hide');
			selectedPanel.removeClass('hide');
		})

		.on( 'click', '[data-toggle="schedule-interval"]', function(e){
			let selected = $( '[data-schedule-option="' + $(this).data('dep') + '"]' ),
				fields = $('[data-schedule-option]');

			fields.addClass('hide');
			selected.removeClass('hide');
		});

	// Product feed status box heartbeat
	heartBeatCB();

	// Init the form fields
	initFields();

	// Populate facebook product catalog options
	existing_catalog_options();

});
