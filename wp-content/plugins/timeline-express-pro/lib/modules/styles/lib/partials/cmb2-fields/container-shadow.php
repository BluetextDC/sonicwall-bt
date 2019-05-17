<?php
/**
 * Custom background/border color field (checkbox + colorpicker)
 *
 * @param object $field         The current field object.
 * @param string $escaped_value Escaped value.
 * @param string $object_type
 * @param object $field_object
 *
 * @since 1.0.0
 */
function timeline_express_container_shadow( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

	$inherit  = (bool) isset( $escaped_value['inherit'] ) ? $escaped_value['inherit'] : $field->args['default']['inherit'];
	$x_offset = (string) isset( $escaped_value['x_offset'] ) ? $escaped_value['x_offset'] : $field->args['default']['x_offset'];
	$y_offset = (string) isset( $escaped_value['y_offset'] ) ? $escaped_value['y_offset'] : $field->args['default']['y_offset'];
	$blur     = (string) isset( $escaped_value['blur'] ) ? $escaped_value['blur'] : $field->args['default']['blur'];
	$spread   = (string) isset( $escaped_value['spread'] ) ? $escaped_value['spread'] : $field->args['default']['spread'];
	$color    = (string) isset( $escaped_value['color'] ) ? $escaped_value['color'] : $field->args['default']['color'];
	$hidden   = $inherit ? ' hidden' : '';

	?>

	<label for="_timeline_styles_container_shadow[inherit]">

		<input type="checkbox" class="regular-text inherit-toggle" name="_timeline_styles_container_shadow[inherit]" id="_timeline_styles_container_shadow[inherit]" <?php checked( $inherit, true ); ?> value="1">

		<strong><?php esc_html_e( 'Inherit', 'timeline-express-pro' ); ?></strong>

	</label>

	<p class="<?php echo esc_attr( $hidden ); ?> container-styles-holder">

		<label for="_timeline_styles_container_shadow[x_offset]">

			<strong><?php esc_html_e( 'X-Offset (px)', 'timeline-express-pro' ); ?></strong><br />

			<input class="widefat" name="_timeline_styles_container_shadow[x_offset]" id="_timeline_styles_container_shadow[x_offset]" type="number" placeholder="<?php echo esc_attr( $x_offset ); ?>" value="<?php echo esc_attr( $x_offset ); ?>" />

		</label>

		<label for="_timeline_styles_container_shadow[y_offset]">

			<strong><?php esc_html_e( 'Y-Offset (px)', 'timeline-express-pro' ); ?></strong>

			<input class="widefat" name="_timeline_styles_container_shadow[y_offset]" id="_timeline_styles_container_shadow[y_offset]" type="number" placeholder="<?php echo esc_attr( $y_offset ); ?>" value="<?php echo esc_attr( $y_offset ); ?>" />

		</label>

		<label for="_timeline_styles_container_shadow[blur]">

			<strong><?php esc_html_e( 'Blur (px)', 'timeline-express-pro' ); ?></strong>

			<input class="widefat" name="_timeline_styles_container_shadow[blur]" id="_timeline_styles_container_shadow[blur]" type="number" min="0" placeholder="<?php echo esc_attr( $blur ); ?>" value="<?php echo esc_attr( $blur ); ?>" />

		</label>

		<label for="_timeline_styles_container_shadow[spread]">

			<strong><?php esc_html_e( 'Spread (px)', 'timeline-express-pro' ); ?></strong>

			<input class="widefat" name="_timeline_styles_container_shadow[spread]" id="_timeline_styles_container_shadow[spread]" type="number" min="-30" placeholder="<?php echo esc_attr( $spread ); ?>" value="<?php echo esc_attr( $spread ); ?>" />

		</label>

		<label for="_timeline_styles_container_shadow[color]">

			<strong><?php esc_html_e( 'Color', 'timeline-express-pro' ); ?></strong><br />

			<?php

			echo $field_type_object->rgba_colorpicker();

			?>

		</label>

		<span class="cmb2-metabox-description"><?php esc_html_e( 'Customize the shadow behind this container.', 'timeline-express-pro' ); ?></span>

	</p>

	<?php

}
add_action( 'cmb2_render_container_shadow', 'timeline_express_container_shadow', 10, 5 );

/**
 * Sanitize our checkbox + color picker field.
 *
 * @param  string $override_value The new value.
 * @param  string $value          The original value.
 *
 * @since 1.0.0
 */
function timeline_express_sanitize_container_shadow( $override_value, $value ) {

	$final_value = [];

	if ( ! isset( $value['inherit'] ) ) {

		$final_value['inherit'] = false;

	}

	foreach ( $value as $name => $val ) {

		$final_value[ $name ] = ( 'inherit' === $val ) ? (bool) $val : (string) $val;

	}

	return $final_value;

}
add_filter( 'cmb2_sanitize_container_shadow', 'timeline_express_sanitize_container_shadow', 10, 2 );
