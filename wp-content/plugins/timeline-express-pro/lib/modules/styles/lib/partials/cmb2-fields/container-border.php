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
function timeline_express_container_border( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

	$inherit = (bool) isset( $escaped_value['inherit'] ) ? $escaped_value['inherit'] : $field->args['default']['inherit'];
	$style   = (string) isset( $escaped_value['style'] ) ? $escaped_value['style'] : $field->args['default']['style'];
	$width   = (string) isset( $escaped_value['width'] ) ? $escaped_value['width'] : $field->args['default']['width'];
	$radius  = (string) isset( $escaped_value['radius'] ) ? $escaped_value['radius'] : $field->args['default']['radius'];
	$hidden  = $inherit ? ' hidden' : '';
	?>

	<label for="_timeline_styles_container_border[inherit]">

		<input type="checkbox" class="regular-text inherit-toggle" name="_timeline_styles_container_border[inherit]" id="_timeline_styles_container_border[inherit]" <?php checked( $inherit, true ); ?> value="1">

		<strong><?php esc_html_e( 'Inherit', 'timeline-express-pro' ); ?></strong>

	</label>

	<p class="<?php echo esc_attr( $hidden ); ?> container-styles-holder">

		<label for="_timeline_styles_container_border[style]">

			<strong><?php esc_html_e( 'Border Style', 'timeline-express-pro' ); ?></strong><br />

			<?php

				$styles = [
					'none',
					'dotted',
					'dashed',
					'solid',
					'double',
					'grove',
					'ridge',
					'inset',
					'ridge',
				];

				print( '<select class="widefat" name="_timeline_styles_container_border[style]" id="_timeline_styles_container_border[style]">' );

				foreach ( $styles as $name ) {

					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						esc_attr( $name ),
						selected( $style, $name, false ),
						esc_html( ucfirst( $name ) )
					);

				}

				print( '</select>' );

				?>

		</label>

		<label for="_timeline_styles_container_border[width]">

			<strong><?php esc_html_e( 'Border Width (px)', 'timeline-express-pro' ); ?></strong>

			<input class="widefat" name="_timeline_styles_container_border[width]" id="_timeline_styles_container_border[width]" type="number" min="0" placeholder="<?php echo esc_attr( $width ); ?>" value="<?php echo esc_attr( $width ); ?>" />

		</label>

		<label for="_timeline_styles_container_border[radius]">

			<strong><?php esc_html_e( 'Border Radius (px)', 'timeline-express-pro' ); ?></strong>

			<input class="widefat" name="_timeline_styles_container_border[radius]" id="_timeline_styles_container_border[radius]" type="number" min="0" placeholder="<?php echo esc_attr( $radius ); ?>" value="<?php echo esc_attr( $radius ); ?>" />

		</label>

		<label for="_timeline_styles_container_border[color]">

			<strong><?php esc_html_e( 'Border Color', 'timeline-express-pro' ); ?></strong><br />

			<?php

			$colorpicker_atts = array(
				'name'    => $field_type_object->_name( '[color]' ),
				'id'      => $field_type_object->_id( '[color]' ),
				'value'   => (string) $escaped_value['color'],
				'default' => (string) $field->args['default']['color'],
				'class'   => 'cmb2-colorpicker cmb2-text-small wp-color-picker' . $hidden,
			);

			echo $field_type_object->colorpicker( $colorpicker_atts, '#' );

			?>

		</label>

		<span class="cmb2-metabox-description"><?php esc_html_e( 'Customize the border for this container.', 'timeline-express-pro' ); ?></span>

	</p>

	<?php

}
add_action( 'cmb2_render_container_border', 'timeline_express_container_border', 10, 5 );

/**
 * Sanitize our checkbox + color picker field.
 *
 * @param  string $override_value The new value.
 * @param  string $value          The original value.
 *
 * @since 1.0.0
 */
function timeline_express_sanitize_container_border( $override_value, $value ) {

	$final_value = [];

	if ( ! isset( $value['inherit'] ) ) {

		$final_value['inherit'] = false;

	}

	foreach ( $value as $name => $val ) {

		$final_value[ $name ] = ( 'inherit' === $val ) ? (bool) $val : (string) $val;

	}

	return $final_value;

}
add_filter( 'cmb2_sanitize_container_border', 'timeline_express_sanitize_container_border', 10, 2 );
