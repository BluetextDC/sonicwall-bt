/**
 * Timeline Express Color Picker Scripts
 * Initialize our color pickers
 *
 * @since 1.2
 * By: CodeParrots
 * @link http://www.codeparrots.com
*/
jQuery( document ).ready(function() {

	// initialize the colorpickers
	if ( jQuery( '.color-picker-field' ).length ) {

		jQuery( '.color-picker-field' ).wpColorPicker();

		jQuery( '.meta-box-sortables' ).sortable( {
			disabled: true
		} );

		jQuery( '.postbox .hndle' ).css( 'cursor', 'pointer' );

	}

	// initialize our tooltips
	if ( jQuery( '.te-tooltip' ).length ) {

		tlite( function ( el ) {

			return el.classList.contains( 'te-tooltip' );

		} );

	}

});

function changeRandomTrimLengthCheckbox() {
	var newOptinValue = jQuery( 'input[name="excerpt-random-length"]' ).prop( 'checked' );
	if ( newOptinValue == '1' ) {
		jQuery( 'input[name="excerpt-trim-length"]' ).fadeOut('fast',function() {
			jQuery( 'input[name="excerpt-random-length"]' ).css( 'margin-left','0em' );
		});
		jQuery( '#random-lenth-text-container' ).removeClass( 'random-length-text' );
	} else {
		jQuery( 'input[name="excerpt-random-length"]' ).css( 'margin-left','.5em' );
		jQuery( 'input[name="excerpt-trim-length"]' ).fadeIn( 'fast' );
		jQuery( '#random-lenth-text-container' ).addClass( 'random-length-text' );
	}
}
