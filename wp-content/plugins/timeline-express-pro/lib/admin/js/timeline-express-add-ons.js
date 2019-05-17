var cnkt_installer = cnkt_installer || {},
    alertStyle     = 'margin: 12px 20px 2px; padding: 10px; border: 1px solid transparent; border-radius: 3px;'
    warningStyle   = 'color: #a94442; border-color: #ebccd1; background-color: #f2dede;',
    successStyle   = 'color: #3c763d; border-color: #d6e9c6; background-color: #dff0d8;';

jQuery( document ).ready( function( $ ) {

	"use strict";

	var is_loading = false;

	/**
	 *  install_plugin
	 *  Install the plugin
	 *
	 *
	 *  @param el       object Button element
	 *  @param plugin   string Plugin slug
	 *  @since 1.0
	*/
	cnkt_installer.install_plugin = function( el, plugin ) {

		// Confirm activation
		var r = confirm( te_installer_localize.install_now );

		if ( r ) {

			is_loading = true;
			el.addClass( 'installing' );

			$.ajax( {
				type: 'POST',
				url: ajaxurl,
				data: {
					action:   'timeline_express_add_on_installer',
					plugin:   plugin,
					nonce:    te_installer_localize.admin_nonce,
					dataType: 'json'
				},
				success: function( data ) {
					if( data ) {

						if( data.status === 'success' ) {

							el.attr( 'class', 'activate button button-primary' );
							el.html( te_installer_localize.activate_btn );

						} else {

							el.removeClass( 'installing' );

						}

					} else {

						el.removeClass( 'installing' );

					}

					is_loading = false;

				},
				error: function( xhr, status, error ) {

					console.log( status );
					el.removeClass( 'installing' );
					is_loading = false;

				}

			} );

		}
	};

	/**
	 *  activate_plugin
	 *  Activate the plugin
	 *
	 *  @param el       object Button element
	 *  @param plugin   string Plugin slug
	 *  @since 1.0
	 */
	cnkt_installer.activate_plugin = function( el, plugin ) {

		// Disable the button
		el.attr( 'disabled', 'disabled' );

		$.ajax( {
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'timeline_express_add_on_activation',
				plugin: plugin,
				pluginName: el.data( 'name' ),
				premium: el.hasClass( 'premium' ),
				nonce: te_installer_localize.admin_nonce,
				dataType: 'json'
			},
			success: function( response ) {

				if ( ! response.success ) {

					el.closest( '.plugin-card' ).prepend( '<p class="plugin-alert" style="' + alertStyle + warningStyle + '">' + response.data.msg + '</p>' );
					el.removeAttr( 'disabled' );
					is_loading = false;

					return;

				}

				el.closest( '.plugin-card' ).prepend( '<p class="plugin-alert" style="' + alertStyle + successStyle + '">' + response.data.msg + '</p>' );

				var $notice = el.closest( '.plugin-card' ).find( '.plugin-alert' );

				el.attr( 'class', 'installed button disabled' );
				el.html( te_installer_localize.installed_btn );

				is_loading = false;

				setTimeout( function() {

					$notice.fadeOut( '_default', function() {
						$( this ).remove();
					} );

				}, 4000 );

			},
			error: function( xhr, status, error ) {

				console.log( xhr );
				console.log( status );
				console.log( error );

				el.removeAttr( 'disabled' );
				is_loading = false;

			}

		} );

	};



	/**
	 *  Install/Activate Button Click
	 *
	 *  @since 1.0
	 */
	$( document ).on( 'click', '.plugin-card a.button', function( e ) {

		var el     = $( this ),
		    plugin = el.data( 'slug' );

		if ( is_loading || el.hasClass( 'disabled' ) ) {

			return;

		}

		e.preventDefault();

		// Installation
		if ( el.hasClass( 'install' ) ) {

			cnkt_installer.install_plugin( el, plugin );

		}

		// Activation
		if ( el.hasClass( 'activate' ) ) {

			cnkt_installer.activate_plugin( el, plugin );

		}

	} );


} );
