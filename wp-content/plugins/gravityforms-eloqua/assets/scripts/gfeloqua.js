var GFEloqua = window.GFEloqua || {},
	// deprecated functions:
	gfeloqua_clear_form_transient, gfeloqua_clear_forms_transient, gfeloqua_oauth_window;

(function($) {

	/**
	 * Storage for OAuth window object
	 *
	 * @type object
	 */
	GFEloqua.oauth_window = false;

	/**
	 * Init method, ran on document ready event.
	 */
	GFEloqua.init = function() {
		this.bind_form_refresh();
		this.bind_field_refresh();
		this.bind_oauth();
		this.bind_select_ui();
		this.bind_retry();
		this.bind_entry_notes_ui();
		this.bind_test();
	};

	/**
	 * Bind form refresh button
	 */
	GFEloqua.bind_form_refresh = function(){
		$( 'a[href$="#gfe-forms-refresh"]' ).on( 'click', function( e ){
			e.preventDefault();
			GFEloqua.clear_forms_transient();
			return false;
		});
	};

	/**
	 * Bind field refresh button
	 */
	GFEloqua.bind_field_refresh = function(){
		$( 'a[href$="#gfe-form-fields-refresh"]' ).on( 'click', function( e ){
			e.preventDefault();
			GFEloqua.clear_form_transient();
			return false;
		});
	};

	/**
	 * Bind OAuth Window opener
	 */
	GFEloqua.bind_oauth = function() {
		$( '#gfeloqua_oauth' ).on( 'click', function( e ){
			e.preventDefault();

			var href = $( this ).attr( 'href' ),
				width = $( this ).data( 'width' ) ? $( this ).data( 'width' ) : 600,
				height = $( this ).data( 'height' ) ? $( this ).data( 'height' ) : 600;

			var new_window = GFEloqua.popup( href, 'gfeloqua_oauth', width, height );

			$( this ).hide();
			$( '#gfeloqua_oauth_code' ).show();

			var repeat_checks = function() {
				setTimeout( function() {
					if ( new_window.closed ) {
						$( '#gfeloqua_oauth_code' ).html( gfeloqua_strings.oauth_complete );
						location.reload( true );
					} else {
						repeat_checks();
					}
				}, 500 );
			};

			repeat_checks();

			return false;
		});
	};

	/**
	 * Bind Select UI using Select2
	 */
	GFEloqua.bind_select_ui = function() {
		if ( $.fn.select2 && $( 'select#gfeloqua_form' ).length ) {
			$( 'select#gfeloqua_form' ).select2({
				minimumResultsForSearch: 10,
				width: '100%'
			}).on( 'change', function() {
				// start spinner
				var $spinner = $( '<div />' ).addClass( 'spinner' ),
					$mapped_fields = $( '#gaddon-setting-row-mapped_fields td' ),
					form_id = $( '#gfeloqua_form' ).val();

				if ( ! form_id ) {
					return false;
				}

				$spinner.show().css( 'visibility', 'visible' );

				if ( $mapped_fields.length ) {
					$mapped_fields.find( 'table' ).hide();
					$mapped_fields.append( $spinner );
				} else {
					$spinner.css( 'display', 'inline-block' )
						.css( 'float', 'none' );
					$('#gform-settings-save').after( $spinner );
				}

				$( this ).parents( 'form' ).submit();
			});
		}
	};

	/**
	 * Bind Note Detail toggle buttons
	 */
	GFEloqua.bind_note_detail_toggle = function() {
		$( 'a.toggle-debug-detail, a.toggle-note-detail' ).off( 'click' ).on( 'click', function( e ) {
			e.preventDefault();
			if ( $( this ).hasClass( 'toggle-note-detail' ) ) {
				$( this ).next( '.gfeloqua-note-detail' ).slideToggle( 'fast' );
			} else {
				$( this ).next( '.gfeloqua-debug-detail' ).slideToggle( 'fast' );
			}
		});
	};

	/**
	 * Bind Entry Notes UI
	 */
	GFEloqua.bind_entry_notes_ui = function() {
		$( 'a.gfeloqua-debug-toggle' ).off( 'click' ).on( 'click', function( e ) {
			e.preventDefault();
			$( this ).next( '.gfeloqua-advanced-debug' ).slideToggle( 'fast' );
		});
	};

	/**
	 * Bind Entry Retry button
	 */
	GFEloqua.bind_retry = function() {
		$( '.gfeloqua-retry' ).on( 'click', function( e ) {
			e.preventDefault();

			var $btn = $( this ),
				entry_id = $btn.data( 'entry-id' ),
				form_id = $btn.data( 'form-id' );

			// start spinner
			var $spinner = $( '.gfeloqua-right .spinner' ),
				$retries_text = $( '.gfeloqua-retries' );

			if ( ! $spinner.length ) {
				$spinner = $( '<div />' ).addClass( 'spinner' );
				$retries_text.before( $spinner );
			}

			$spinner.show().css( 'visibility','visible' )
				.css( 'float', 'left' );

			$.ajax({
				url: gfeloqua_strings.ajax_url,
				data: {
					action: 'gfeloqua_resubmit_entry',
					entry_id : entry_id,
					form_id : form_id
				},
				success: function( response ) {
					if ( response.success ) {
						$( '.gfeloqua-retries' ).remove();
						$btn.remove();
					} else {
						$( '.gfeloqua-retries span' ).html( response.retries );
						if ( response.limit_reached ) {
							$( '.gfeloqua-retries' ).addClass( 'gfeloqua-limit-reached' );
						}
					}

					$spinner.hide();

					$( '#gfeloqua-notes' ).html( response.notes );
					GFEloqua.bind_entry_notes_ui();
				}
			});
		});
	};

	/**
	 * Bind false positive entry to reset entry
	 */
	GFEloqua.bind_false_positives = function() {
		$( '.gfeloqua-false-positive' ).on( 'click', function( e ){
			e.preventDefault();

			if ( ! confirm( gfeloqua_strings.confirm_reset ) ) {
				return false;
			}

			var $btn = $( this ),
				entry_id = $btn.data( 'entry-id' ),
				form_id = $btn.data( 'form-id' );

			$.ajax({
				url: gfeloqua_strings.ajax_url,
				data: {
					action: 'gfeloqua_reset_entry',
					entry_id : entry_id,
					form_id : form_id
				},
				success: function( response ){
					if ( response.success ) {
						location.reload();
					}
				}
			});
		});
	};

	/**
	 * Clear form transient cache via AJAX
	 */
	GFEloqua.clear_form_transient = function() {
		// start spinner
		var $spinner = $( '<div />' ).addClass( 'spinner' ),
			$mapped_fields = $( '#gaddon-setting-row-mapped_fields td' ),
			form_id = $( '#gfeloqua_form' ).val();

		if ( ! form_id ) {
			return false;
		}

		$spinner.show().css( 'visibility', 'visible' );
		$mapped_fields.find( 'table' ).hide();
		$mapped_fields.append( $spinner );

		$.ajax({
			url: gfeloqua_strings.ajax_url,
			data: {
				action: 'gfeloqua_clear_transient',
				transient : 'assets/form/' + form_id
			},
			success: function( response ) {
				$( '#gform-settings' ).submit();
			}
		});
	};

	/**
	 * Clear all forms transient cache via AJAX
	 */
	GFEloqua.clear_forms_transient = function() {
		// start spinner
		var $spinner = $( '<div />' ).addClass( 'spinner' ),
			$form_list = $( '#gaddon-setting-row-gfeloqua_form td' );

		$spinner.show().css( 'visibility','visible' );
		$form_list.find( 'select, .select2-container' ).hide();
		$form_list.append( $spinner );

		$.ajax({
			url: gfeloqua_strings.ajax_url,
			data: {
				action: 'gfeloqua_clear_transient',
				transient : 'assets/forms'
			},
			success: function( response ) {
				$( '#gform-settings' ).submit();
			}
		});
	};

	/**
	 * Popup a window
	 *
	 * @param {string} url    URL to open in popup.
	 * @param {string} title  Title of window.
	 * @param {int}    w      Width of window.
	 * @param {int}    h      Height of window.
	 *
	 * @return {object}  Window object.
	 */
	GFEloqua.popup = function ( url, title, w, h ) {
		// Fixes dual-screen position                         Most browsers       Firefox
		var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left,
			dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top,
			width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width,
			height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height,
			left = ((width / 2) - (w / 2)) + dualScreenLeft,
			top = ((height / 2) - (h / 2)) + dualScreenTop;

		GFEloqua.oauth_window = window.open( url, title, 'scrollbars=yes, chrome=yes, menubar=no, toolbar=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left );

		// Puts focus on the newWindow
		if ( window.focus ) {
			GFEloqua.oauth_window.focus();
		}

		return GFEloqua.oauth_window;
	};

	GFEloqua.bind_test = function() {
		$( '#gfeloqua_test' ).on( 'click', function( e ) {
			$( '.gfeloqua-connection-test-result' ).remove();

			// start spinner
			var $spinner = $( this ).parents( 'td' ).find( '.spinner' );

			if ( ! $spinner.length ) {
				$spinner = $( '<div />' ).addClass( 'spinner' ).hide();
				$( '#gfeloqua_test' ).after( $spinner );
			}

			if ( $spinner.is( ':visible' ) ) {
				return false;
			}

			$spinner.show().css( 'visibility','visible' )
				.css( 'float', 'none' );

			e.preventDefault();
			$.ajax({
				url: gfeloqua_strings.ajax_url,
				data: { action: 'gfeloqua_connection_test' },
				type: 'post',
				dataType: 'json',
				success: function( response ) {
					$spinner.hide();
					$( '#gfeloqua_test' ).after( response.html );
				}
			});
		});
	};

	/**
	 * @deprecated Since version 1.6.0.
	 * Use GFEloqua.clear_form_transient() instead.
	 */
	gfeloqua_clear_form_transient = function() {
		console.warn( 'gfeloqua_clear_form_transient has been deprecated.' );
		return GFEloqua.clear_form_transient();
	};

	/**
	 * @deprecated Since version 1.6.0.
	 * Use GFEloqua.clear_forms_transient() instead.
	 */
	gfeloqua_clear_forms_transient = function() {
		console.warn( 'gfeloqua_clear_forms_transient has been deprecated.' );
		return GFEloqua.clear_forms_transient();
	};

	/**
	 * @deprecated Since version 1.6.0.
	 * Use GFEloqua.popup() instead.
	 */
	PopupCenter = function( url, title, w, h ) {
		console.warn( 'PopupCenter has been deprecated.' );
		return GFEloqua.popup( url, title, w, h );
	};

	/**
	 * @deprecated Since version 1.6.0.
	 * Use GFEloqua.bind_note_detail_toggle() instead.
	 */
	gfeloqua_bind_note_detail_toggle = function() {
		console.warn( 'gfeloqua_bind_note_detail_toggle has been deprecated.' );
		return GFEloqua.bind_note_detail_toggle();
	};

	// On document ready event.
	$(function() {
		GFEloqua.init();
	});

})( jQuery );
