( function( $ ) {


	tinymce.PluginManager.add( 'timeline_express_tinymce', function( editor, url ) {

		editor.addButton( 'timeline_express_tinymce', {

			title:   timeline_express_tinymce.i10n.popupTitle,
			image:   timeline_express_tinymce.icon,
			onclick: function() {

				editor.windowManager.open( {

					title:   timeline_express_tinymce.i10n.popupTitle,
					body:    JSON.parse( timeline_express_tinymce.data.fields ),
					buttons: JSON.parse( timeline_express_tinymce.data.buttons ),
					id:      'timeline_express_tinymce',
					onsubmit: function( e ) {

						var params = JSON.parse( timeline_express_tinymce.shortcode_params ),
						    atts   = [];

						$.each( params, function( elementClass, parameter ) {

							var element    = $( '.' + elementClass ),
							    isCheckbox = element.hasClass( 'mce-checkbox' ),
							    value      = false;

							if ( isCheckbox ) {

								value = element.hasClass( 'mce-checked' ) ? '1' : false;

							} else {

								if ( element.hasClass( 'mce-number-type' ) ) {

									value = ( 0 < element.val() ) ? element.val() : false;

								} else {

									value = ( null === element.val() || element.val().length === 0 ) ? false : element.val().join( ', ' );

								}

							}

							if ( value ) {

								atts[ parameter ] = value;

							}

						} );

						var shortcodeString = '';

						for ( var key in atts ) {

							shortcodeString += ' ' + key + '="' + atts[ key ] + '"';

						}

						console.log( shortcodeString );

						var shortcode = ( shortcodeString.length !== 0 ) ? '[timeline-express ' + shortcodeString + ']' : '[timeline-express]';

						editor.insertContent( shortcode );

					},
				} );

				$( '.mce-number-type' ).attr({ 'type':'number', 'min':'0' });

				$( '.mce-select2' ).each( function() {

					if ( $( this ).hasClass( 'mce-multiple' ) ){

						$( this ).attr( 'multiple', 'multiple' );

					}

					$( this ).select2( {
						dropdownCssClass: 'timeline-express-select2-dropdown',
						placeholder:      $( this ).prev().html(),
					} ).val( '' ).trigger( 'change' );

				} );

				$( '.mce-intro-text' ).css( 'top', '5px' );

				$( '.mce-support-help' ).html( timeline_express_tinymce.i10n.supportText ).css( 'line-height', '60px' );

				$( '.mce-formitem' ).each( function() {

					$( this ).find( '.mce-label' ).css( 'top', 0 );

				} );

				$( '#timeline_express_tinymce' ).css( 'margin-left', ( $( '#timeline_express_tinymce' ).css( 'width' ).replace( 'px', '' )  / 2.5 ) + 'px' );

			}

		} );

	} );

} )( jQuery );
