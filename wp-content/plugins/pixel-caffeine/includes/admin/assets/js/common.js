/**
 * UI scripts for admin settings page
 */

import $ from 'jquery';
import 'bootstrap-sass/assets/javascripts/bootstrap/collapse';
import 'bootstrap-sass/assets/javascripts/bootstrap/tooltip';
import 'bootstrap-sass/assets/javascripts/bootstrap/popover';
import 'bootstrap-sass/assets/javascripts/bootstrap/transition';
import 'bootstrap-sass/assets/javascripts/bootstrap/modal';
import 'bootstrap-material-design/scripts/material';
import 'select2';

const Common = {

	dropdown_data: [],

	unsaved: false,

	set_unsaved: function () {
		Common.unsaved = true;
	},

	set_saved: function () {
		Common.unsaved = false;
	},

	bootstrap_components: function( e ) {
		let context = $( typeof e !== 'undefined' ? e.currentTarget : document );

		context.find('.collapse').collapse({toggle: false});

		context.find('[data-toggle="tooltip"], [data-tooltip]').tooltip();

		context.find('[data-toggle="popover"]').popover({
			container: '#wpbody .pixel-caffeine-wrapper' // If it is relative to page body the css doesn't work.
		});

		$.material.init();
	},

	custom_dropdown: function( e ) {
		let context = $( typeof e !== 'undefined' ? e.currentTarget : document );

		context.find('select').select2({
			minimumResultsForSearch: 5
		});

		context.find('input.multi-tags').select2({
			tags:[]
		});

		context.find('select.dropdown-width-max').select2({
			minimumResultsForSearch: 5,
			dropdownCssClass: 'dropdown-width-max'
		});
	},

	fields_components: function( e ) {
		let context = $( typeof e !== 'undefined' ? e.currentTarget : document.body );

		// Option dependencies
		context.find('select.js-dep').on( 'change', function(){
			let select = $(this),
				form = select.closest('form'),
				selected = select.val(),
				toggleDiv = select.attr('id'),
				ps = form.find('div[class*="' + toggleDiv + '"]'),
				p = form.find( '.' + toggleDiv + '-' + selected );

			ps.hide();

			if ( p.length ) {
				p.show();
			}
		}).trigger('change');

		// When input is inside of checkbox label, check automatically
		context.find('.control-wrap .checkbox .inline-text').on( 'focus', function(){
			$(this).siblings('input[type="checkbox"]').prop( 'checked', true ).trigger('change');
		});

		// For all checkbox options, put a class on own container to know if checked or unchecked, useful for the other siblings elements
		context.find('.control-wrap .checkbox input[type="checkbox"]').on( 'change', function(){
			let checkbox = $(this),
				checked = checkbox.is(':checked');

			checkbox
				.closest('div.checkbox')
				.removeClass('checked unchecked')
				.addClass( checked ? 'checked' : 'unchecked' )
				.find('input.inline-text')
				.prop( 'disabled', ! checked );
		}).trigger('change');

		// Toggle advanced data box
		context.find('.js-show-advanced-data').on( 'change.components', function(){
			let checkbox = $(this),
				form = checkbox.closest('form');

			// Show box
			form.find('div.advanced-data').collapse( checkbox.is(':checked') ? 'show' : 'hide' );
		}).trigger('change.components');

		// Toggle event parameters, depending by event select
		context.find('select#event_standard_events').on( 'change.components', function(){
			let select = $(this),
				form = select.closest('form'),
				fields = select.find('option:selected').data('fields');

			form.find('div.event-field').hide();

			$.each( fields.split(',').map( function(str) { return str.trim(); } ), function( index, field ) {
				form.find( 'div.event-field.' + field + '-field' ).show();
			});
		}).trigger('change.components');

		// Label below switches need to be saved
		context.find('input.js-switch-labeled-tosave').on( 'change.components', function(){
			let checkbox = $(this),
				status = checkbox.closest('.form-group').find('.text-status'),
				value = checkbox.is(':checked') ? 'yes' : 'no',
				togglebutton = checkbox.closest('.togglebutton'),
				original_value = checkbox.data('original-value');

			// Save the original status message in data to use if the change will be reverted
			if ( typeof status.data( 'original-status' ) === 'undefined' ) {
				status.data( 'original-status', status.clone() );
			}

			// Init
			if ( original_value !== value ) {
				if ( ! status.hasClass('text-status-pending') ) {
					togglebutton.addClass('pending');
				}
				status.addClass( 'text-status-pending' ).text( aepc_admin.switch_unsaved );
			} else {
				if ( ! $( status.data( 'original-status' ) ).hasClass('text-status-pending') ) {
					togglebutton.removeClass('pending');
				}
				status.replaceWith( status.data( 'original-status' ) );
			}
		}).trigger('change.components');

		// Label below switches
		context.find('input.js-switch-labeled').on( 'change.components', function(){
			let checkbox = $(this),
				switchStatus = checkbox.closest('.form-group').find('.text-status');

			// Change switch label
			switchStatus.removeClass('hide');
			if ( checkbox.is(':checked') ) {
				switchStatus.filter('.text-status-off').addClass('hide');
			} else {
				switchStatus.filter('.text-status-on').addClass('hide');
			}
		});

		let reindex_params = function() {
			context.find('div.js-custom-params').children('div').each(function(index){
				let div = $(this);

				div.find('input[type="text"]').each(function(){
					let input = $(this);

					input.attr('name', input.attr('name').replace( /\[[0-9]+\]/, '[' + index + ']' ) );
					input.attr('id', input.attr('id').replace( /_[0-9]+$/, '_' + index ) );
				});
			});
		};

		// Custom parameters option
		context.find('.js-add-custom-param').on( 'click', function(e){
			if ( typeof wp === 'undefined' ) {
				return e;
			}

			e.preventDefault();

			let paramsTmpl = wp.template( 'custom-params' ),
				divParameters = $(this).closest('div.js-custom-params'),
				index = parseInt( divParameters.children('div').length );

			if ( divParameters.find('.js-custom-param:last').length ) {
				divParameters.find('.js-custom-param:last').after( paramsTmpl( { index: index-1 } ) );
			} else {
				divParameters.prepend( paramsTmpl( { index: index-1 } ) );
			}
		});

		// Custom parameters delete action
		context.find('.js-custom-params').on( 'click', '.js-delete-custom-param', function(e){
			e.preventDefault();

			let button = $(this),
				modal = $('#modal-confirm-delete'),
				params = button.closest('.js-custom-param'),

				remove = function() {
					modal.modal('hide');
					params.remove();
					reindex_params();
				};

			// If any value is defined, remove without confirm
			if ( params.find('input[id^="event_custom_params_key"]').val() === '' && params.find('input[id^="event_custom_params_value"]').val() === '' ) {
				remove();

				// If some value is written inside inputs, confirm before to delete
			} else {

				modal

				// Show modal
					.modal('show')

					// confirm action
					.one('click', '.btn-ok', remove);
			}
		});

		// Set selected in the dropdown, if data-selected is defined
		context.find('select[data-selected]').each( function() {
			let select = $(this),
				selected = select.data('selected');

			select.data('selected', '').val( selected ).trigger('change');
		});

		// Set selected in the dropdown, if data-selected is defined
		context.find('select[data-selected]').each( function() {
			let select = $(this),
				selected = select.data('selected');

			select.val( selected ).trigger('change');
		});
	},

	analyzed_distance: function () {
		const calc_distance_top = function( el ) {
			let scrollTop	  = $( window ).scrollTop(),
				elementOffset = $( el ).offset().top;

			return elementOffset - scrollTop;
		};

		let distance = calc_distance_top( '.plugin-content' ),
			heightWP = parseFloat( $('.wp-toolbar').css('padding-top') ),
			alertWrap = $( '.alert-wrap' ),
			alertHeight = alertWrap.height(),
			alertGhost = $( '.alert-wrap-ghost' );

		if ( distance <= heightWP ) {
			if ( alertGhost.length === 0 ) {
				alertWrap
					.after('<div class="alert-wrap-ghost"></div>')
					.next('.alert-wrap-ghost').height(alertHeight);
			}
			alertWrap
				.addClass('alert-fixed')
				.css({ 'top': heightWP })
				.width( $('.plugin-content').width() );
		} else {
			alertWrap
				.removeClass('alert-fixed')
				.width('100%');
			alertGhost.remove();
		}
	}

};

export default Common;
