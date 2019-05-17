<?php
/**
 * Notes Display template part
 *
 * @package gfeloqua
 */

$alternate = false;

foreach ( $notes as $note ) :
	if ( is_string( $note ) ) :
		$output = $note;
	elseif ( is_array( $note ) && isset( $note['type'] ) ) :
		$debug = '';
		if ( isset( $note['data'] ) && ! empty( $note['data'] ) ) :
			$debug = sprintf( '<a href="#gfeloqua-toggle-debug" class="gfeloqua-debug-toggle">Show Debug Detail</a><pre class="gfeloqua-advanced-debug" style="display: none;">%s</pre>', print_r( $note['data'], true ) );
		endif;
		$output = sprintf( '<div class="gfeloqua-' . esc_attr( $note['type'] ) . '" title="' . esc_attr( date( 'Y-m-d H:i:s' , $note['timestamp'] ) ) . '">%s%s</div>', $note['note'], $debug );
	else :
		$output = '<pre class="gfeloqua-debug">' . print_r( $note, true ) . '</pre>';
	endif;

	$alternate = ! $alternate ? ' class="alternate"' : '';
	printf( '<tr%s><td>%s</td></tr>', $alternate, $output );
endforeach;
