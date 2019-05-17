<?php

if ( ! function_exists( 'te_adjust_brightness' ) ) {

	/**
	 * Tweak a hex color into a lighter rgba value.
	 *
	 * @param  string  $hex   Hex color value.
	 * @param  integer $steps How light to make the color.
	 *
	 * @since 2.0.0
	 *
	 * @return string         rgba() color value.
	 */
	function te_adjust_brightness( $hex, $steps = 30 ) {

		// Steps should be between -255 and 255. Negative = darker, positive = lighter
		$steps = max( -255, min( 255, $steps ) );

		// Normalize into a six character long hex string
		$hex = str_replace( '#', '', $hex );

		if ( 3 === strlen( $hex ) ) {

			$hex = str_repeat( substr( $hex, 0, 1 ), 2 ) . str_repeat( substr( $hex, 1, 1 ), 2 ) . str_repeat( substr( $hex, 2, 1 ), 2 );

		}

		// Split into three parts: R, G and B
		$color_parts = str_split( $hex, 2 );
		$return      = '#';

		foreach ( $color_parts as $color ) {

			$color   = hexdec( $color ); // Convert to decimal
			$color   = max( 0, min( 255, $color + $steps ) ); // Adjust color
			$return .= str_pad( dechex( $color ), 2, '0', STR_PAD_LEFT ); // Make two char hex code

		}

		return $return;

	} // @codingStandardsIgnoreLine

} // End if().
