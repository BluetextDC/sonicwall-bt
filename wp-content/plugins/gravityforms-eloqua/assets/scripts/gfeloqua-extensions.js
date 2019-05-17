var GFEloqua = window.GFEloqua || {};

(function($) {

	/**
	 * Storage for Extensions functionality
	 *
	 * @type object
	 */
	GFEloqua.Extensions = {};

	/**
	 * Init method, ran on document ready event.
	 */
	GFEloqua.Extensions.init = function() {
		GFEloqua.Extensions.bind_save_settings_form();
	};

	GFEloqua.Extensions.bind_save_settings_form = function() {
		$( '.gfeloqua-extension-settings' ).on( 'click', '.gfeloqua-save-extension-settings', function( e ) {
			e.preventDefault();

			// show spinner
			GFEloqua.Extensions.show_spinner();

			var $container = $( this ).parents( '.gfeloqua-extension-settings' ),
				$settings_wrapper = $container.find('.gfeloqua-settings-fields'),
				$error = $container.find( '.gfeloqua-extension-settings-errors' ),
				data = $container.find( ':input' ).serialize();

			$error.hide();

			data += '&action=gfeloqua_save_extension_settings';

			$.ajax({
				data: data,
				url: gfeloqua_strings.ajax_url,
				type: 'post',
				success: function( response ){
					if ( ! response.success ) {
						// show error
						$error.html( response.error )
							.show();
					}

					$settings_wrapper.html( response.html );
					GFEloqua.Extensions.hide_spinner();
				}
			});
			return false;
		});
	};

	GFEloqua.Extensions.show_spinner = function() {
		var $parent = $( '#TB_window' ),
			$spinner = $parent.find( '.gfeloqua-spinner' );

		if ( ! $spinner.length ) {
			var $inside = $( '<div />' )
				.addClass( 'inside' );

			var $loader = $( '<span />' )
				.addClass( 'loader' );

			$inside.append( $loader );

			$spinner = $( '<div />' )
				.addClass( 'gfeloqua-spinner' )
				.append( $inside );

			$parent.append( $spinner );

			$spinner = $parent.find( '.gfeloqua-spinner' );
		}

		$spinner.show();
	};

	GFEloqua.Extensions.hide_spinner = function() {
		$( '#TB_window .gfeloqua-spinner' ).hide();
	};

	// On document ready event.
	$(function() {
		GFEloqua.Extensions.init();
	});

})( jQuery );
