/**
 * UI scripts for admin settings page
 */

import $ from 'jquery';
import 'select2';
import Config from './config';
import Common from './common';

const Utils = {

	alert_unsaved: function() {
		$( '.wrap form' )

			.on('change.unsaved', ':input:not(#date-range)', function(){
				Common.set_unsaved();
			})

			// Prevent alert if user submitted form
			.on( 'submit.unsaved', function() {
				Common.set_saved();
			});

		window.onbeforeunload = function(){
			if ( Common.unsaved ) {
				return aepc_admin.unsaved;
			}
		};
	},

	unset_alert_unsaved: function() {
		$( '.wrap form' ).off('change.unsaved submit.unsaved');
		window.onbeforeunload = function(){};
	},

	apply_autocomplete: function( el, data ) {
		if ( el.is('select') ) {
			el.select2({
				data: { results: data }
			});
		} else {
			el.select2({
				tags: data
			});
		}
	},

	addLoader: function( el ) {
		if ( typeof el.data('select2') !== 'undefined' ) {
			let select2 = el.data('select2'),
				select2container = select2.container;

			select2container.addClass( 'loading-data' );
		}

		else if ( el.is( 'div, form' ) ) {
			el.addClass( 'loading-data loading-box' );
		}

		else if ( el.is( 'a' ) ) {
			el.addClass( 'loading-data' );
		}
	},

	removeLoader: function( el ) {
		if ( typeof el.data('select2') !== 'undefined' ) {
			let select2 = el.data('select2'),
				select2container = select2.container;

			select2container.removeClass( 'loading-data' );
		}

		else if ( el.is( 'div, form' ) ) {
			el.removeClass( 'loading-data loading-box' );
		}

		else if ( el.is( 'a' ) ) {
			el.removeClass( 'loading-data' );
		}
	},

	removeMessage: function( el, type ) {
		if ( 'error' === type ) {
			type = 'danger';
		}

		if ( el.find( '.alert-' + type ).length ) {
			el.find( '.alert-' + type ).remove();
		}
	},

	removeMainMessages: function( type ) {
		Utils.removeMessage( $('.plugin-content'), type );
	},

	removeAllMainMessages: function() {
		Utils.removeMainMessages( 'success' );
		Utils.removeMainMessages( 'error' );
	},

	addMessage: function( el, type, msg ) {
		if ( 'error' === type ) {
			type = 'danger';
		}

		const { text: text, dismiss_action: dismissAction = false } = typeof msg === 'object' ? msg : { text: msg };
		let dismissButton = $( '<button />', { type: 'button', class: 'close', "data-dismiss": 'alert', text: '×' } );

		if ( dismissAction ) {
			dismissButton.data( 'data-dismiss-action', dismissAction );
		}

		Utils.removeMessage( el, type );

		el.prepend( $( '<div />', {
			class: 'alert alert-' + type + ' alert-dismissable',
			role: 'alert',
			html: text
		}).prepend( dismissButton ) );
	},

	addMessagesFromResponse: function( response ) {
		if ( response.data.hasOwnProperty( 'messages' ) ) {
			response.data.messages.length && Utils.removeMessage( $('.plugin-content'), 'success' );

			if ( response.data.messages.hasOwnProperty( 'success' ) && response.data.messages.success.hasOwnProperty( 'main' ) ) {
				response.data.messages.success.main.forEach(function( message ) {
					Utils.addMessage( $('.plugin-content .alert-wrap'), 'success', message );
				});
			}

			if ( response.data.messages.hasOwnProperty( 'error' ) && response.data.messages.error.hasOwnProperty( 'main' ) ) {
				response.data.messages.error.main.forEach(function( message ) {
					Utils.addMessage( $('.plugin-content .alert-wrap'), 'error', message );
				});
			}

		} else if ( response.hasOwnProperty( 'success' ) && ! response.success && response.data.hasOwnProperty( 'main' ) ) {
			response.data.main.forEach(function( message ) {
				Utils.addMessage( $('.plugin-content .alert-wrap'), 'error', message );
			});
		}
	},

	refreshFragmentHTML: function ( el, response ) {
		if ( response.success ) {
			// Unset register unsaved status on input changes
			Utils.unset_alert_unsaved();

			el.replaceWith( response.data.html );

			Utils.addMessagesFromResponse( response );

			// Reinit some components
			Common.bootstrap_components( { currentTarget: el } );
			Common.custom_dropdown();
			Common.fields_components();
			Common.analyzed_distance();

			// Register back unsaved status on input changes
			Utils.alert_unsaved();

			$( document ).triggerHandler( 'fragment_html_refreshed' );
		} else {
			Utils.addMessagesFromResponse( response );
		}
	},

	reloadFragment: function( fragment, args, feedbackSpinner = true ) {
		if ( ! Config.fragments.hasOwnProperty( fragment ) || ! aepc_admin.actions.hasOwnProperty( 'load_' + fragment ) ) {
			return;
		}

		let el = $( Config.fragments[ fragment ] ),
			successCB = function(){},
			beforeRender = function(){},
			data = {
				action: aepc_admin.actions[ 'load_' + fragment ].name,
				_wpnonce: aepc_admin.actions[ 'load_' + fragment ].nonce
			};

		// Remove success messages
		if ( feedbackSpinner && $.inArray( fragment, [ 'sidebar' ] ) < 0 ) {
			Utils.removeMessage( $('.plugin-content'), 'success' );
		}

		// add feedback loader
		feedbackSpinner && Utils.addLoader( el );

		// Add query string from current url to data
		window.location.href.slice( window.location.href.indexOf('?') + 1 ).split('&').forEach( function( val ) {
			let qs = val.split('=');

			if ( $.inArray( qs[0], [ 'page', 'tab' ] ) ) {
				data[ qs[0] ] = qs[1];
			}
		});

		// Check if there is some custom arguments to add to the call data
		if ( typeof args !== 'undefined' ) {
			if ( args.hasOwnProperty( 'success' ) ) {
				successCB = args.success;
				delete args.success;
			}

			if ( args.hasOwnProperty( 'beforeRender' ) ) {
				beforeRender = args.beforeRender;
				delete args.beforeRender;
			}

			$.extend( data, args );
		}

		$.ajax({
			url: aepc_admin.ajax_url,
			data: data,
			complete: function() {
				Utils.removeLoader( el );
			},
			success: function( response ) {

				if ( response.success ) {

					// Execute eventual callback before to render the HTML
					beforeRender();

					Utils.refreshFragmentHTML( el, response );
				}

				// Execute eventual callback defined in the arguments previously
				successCB( response );

			},
			dataType: 'json'
		});
	}

};

export default Utils;
