(function($){
//for font style
wp.customize.bind('ready', function() {
	$('body').on('change', 'input.stla_font_style_checkbox[type=checkbox]', function(){

		var $this_el      = $(this),
			$main_option  = $this_el.closest( 'span' ).siblings( 'input.stla_font_styles' ),
			value         = $this_el.val(),
			current_value = $main_option.val(),
			values        = ( current_value != 'false' ) ? current_value.split( '|' ) : [],
			query         = $.inArray( value, values ),
			result        = '';
	
		if ( $this_el.prop('checked' ) === true ) {
	
			if ( current_value.length ) {
	
				if ( query < 0 ) {
					values.push( value );
	
					result = values.join( '|' );
				}
			} else {
				result = value;
			}
		} else {
	
			if ( current_value.length !== 0 ) {
	
				if ( query >= 0 ) {
					values.splice( query, 1 );
	
					result = values.join( '|' );
				} else {
					result = current_value;
				}
			}
		}
	
		$main_option.val( result ).trigger( 'change' );
	});
	
	$( 'body' ).on('click', 'span.stla_font_style', function() {
		var style_checkbox = $( this ).find( 'input' );
		$( this ).toggleClass( 'stla_font_style_checked' );
	
		if ( style_checkbox.is( ':checked' ) ) {
			style_checkbox.prop( 'checked', false );
		} else {
			style_checkbox.prop( 'checked', true );
		}
	
		style_checkbox.change();
	});

	//Text alignment control

	$('body').on('change', 'input.stla_text_alignment_radio[type=radio]', function(){

		var $this_el      = $(this),
			$main_option  = $this_el.closest( 'span' ).siblings( 'input.stla_text_alignment_control' ),
			value         = $this_el.val();
			//console.log(value);
			//current_value = $main_option.val(),
			// values        = ( current_value != 'false' ) ? current_value.split( '|' ) : [],
			// query         = $.inArray( value, values ),
			// result        = '';
	
		// if ( $this_el.prop('checked' ) === true ) {
	
		// 	if ( current_value.length ) {
	
		// 		if ( query < 0 ) {
		// 			values.push( value );
	
		// 			result = values.join( '|' );
		// 		}
		// 	} else {
		// 		result = value;
		// 	}
		// } else {
	
		// 	if ( current_value.length !== 0 ) {
	
		// 		if ( query >= 0 ) {
		// 			values.splice( query, 1 );
	
		// 			result = values.join( '|' );
		// 		} else {
		// 			result = current_value;
		// 		}
		// 	}
		// }
	
		$main_option.val( value ).trigger( 'change' );
	});

	$( 'body' ).on('click', 'span.stla_text_alignment', function() {
		//console.log( this);
		var style_radio = $( this ).find( 'input' );
		var remove_radios = $( this ).siblings( 'span' );
		remove_radios.each(function(){
			$(this).removeClass( 'stla_text_alignment_checked');
			$(this).find('input').prop( 'checked', false );
		});
		$( this ).addClass( 'stla_text_alignment_checked' );
		style_radio.prop( 'checked', true );
	
		style_radio.change();
	});

	//to switch between different views ( Desktop, Mobile, Tablet)

	$( 'body' ).on('click', 'span.stla_desktop_text_input:not(.active)', function() {
		$('.stla_desktop_text_input').addClass('active');
		$('.preview-desktop').trigger('click');
		$('.stla_tab_text_input').removeClass('active');
		$('.stla_mobile_text_input').removeClass('active');
	});

	$( 'body' ).on('click', 'span.stla_tab_text_input:not(.active)', function() {
		$('.stla_tab_text_input').addClass('active');
		$('.preview-tablet').trigger('click');
		$('.stla_desktop_text_input').removeClass('active');
		$('.stla_mobile_text_input').removeClass('active');
	});

	$( 'body' ).on('click', 'span.stla_mobile_text_input:not(.active)', function() {
		$('.stla_mobile_text_input').addClass('active');
		$('.preview-mobile').trigger('click');
		$('.stla_tab_text_input').removeClass('active');
		$('.stla_desktop_text_input').removeClass('active');
	});

	// $( function () {
		// Range Slider
		$( 'body' ).on( 'mousedown', 'input.gf-stla-range-btn' , function () {
			var $range = $( this ),
				$range_input = $range.parent().children( '.gf-stla-range-input' );

			value = $( this ).attr( 'value' );
			$range_input.val( value );

			$( this ).mousemove( function () {
				value = $( this ).attr( 'value' );
				$range_input.val( value );
			} );
		} );

		




		// Range Slider
		$( 'body' ).on( 'change', 'input.gf-stla-range-input' , function () {
			var $range_input = $( this ),
				$range = $range_input.parent().children( '.gf-stla-range-btn' );

			value = $( this ).attr( 'value' );
			$range.val( value );
		//	$('input.gf-stla-range-btn').trigger(' change ');


	});
	var et_range_input_number_timeout;

		function et_autocorrect_range_input_number( input_number, timeout ) {
			var $range_input = input_number,
				$range       = $range_input.parent().find('input.gf-stla-range-btn'),
				value        = parseFloat( $range_input.val() ),
			//	reset        = parseFloat( $range.attr('data-reset_value') ),
				step         = parseFloat( $range_input.attr('step') ),
				min          = parseFloat( $range_input.attr('min') ),
				max          = parseFloat( $range_input.attr('max') );
			// console.log(input_number);
			clearTimeout( et_range_input_number_timeout );

			et_range_input_number_timeout = setTimeout( function() {
				if ( isNaN( value ) ) {
					$range_input.val( min );
					$range.val( min ).trigger( 'change' );
					return;
				}

				if ( step >= 1 && value % 1 !== 0 ) {
					value = Math.round( value );
					$range_input.val( value );
					$range.val( value );
				}

				if ( value > max ) {
					$range_input.val( max );
					$range.val( max ).trigger( 'change' );
				}

				if ( value < min ) {
					$range_input.val( min );
					$range.val( min ).trigger( 'change' );
				}
			}, timeout );

			$range.val( value ).trigger( 'change' );
		}

		$('body').on( 'change keyup', 'input.gf-stla-range-input' , function() {
			et_autocorrect_range_input_number( $(this), 1000 );
		}).on( 'focusout', 'input.gf-stla-range-input', function() {
			et_autocorrect_range_input_number( $(this), 0 );
		});
	// } );
	});
})(jQuery);
