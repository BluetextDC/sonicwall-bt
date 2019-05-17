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
function timeline_express_container_title_styles( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

	$inherit     = (bool) isset( $escaped_value['inherit'] ) ? $escaped_value['inherit'] : $field->args['default']['inherit'];
	$color       = (string) isset( $escaped_value['color'] ) ? $escaped_value['color'] : $field->args['default']['color'];
	$size        = (string) isset( $escaped_value['size'] ) ? $escaped_value['size'] : $field->args['default']['size'];
	$style       = (string) isset( $escaped_value['style'] ) ? $escaped_value['style'] : $field->args['default']['style'];
	$font_weight = (string) isset( $escaped_value['font_weight'] ) ? $escaped_value['font_weight'] : $field->args['default']['font_weight'];
	$hidden      = $inherit ? ' hidden' : '';

	?>

	<label for="_timeline_styles_container_title_styles[inherit]">

		<input type="checkbox" class="regular-text inherit-toggle" name="_timeline_styles_container_title_styles[inherit]" id="_timeline_styles_container_title_styles[inherit]" <?php checked( $inherit, true ); ?> value="1">

		<strong><?php esc_html_e( 'Inherit', 'timeline-express-pro' ); ?></strong>

	</label>

	<p class="<?php echo esc_attr( $hidden ); ?> container-styles-holder">

		<label for="_timeline_styles_container_title_styles[size]">

			<strong><?php esc_html_e( 'Size (px)', 'timeline-express-pro' ); ?> <span class="dashicons dashicons-editor-help te-tooltip" title="<?php esc_html_e( 'Note: Set this value to 0 to inherit the font size from the current theme.', 'timeline-express-pro' ); ?>"></span></strong>

			<input class="widefat" name="_timeline_styles_container_title_styles[size]" min="0" id="_timeline_styles_container_title_styles[size]" type="number" placeholder="<?php echo esc_attr( $size ); ?>" value="<?php echo esc_attr( $size ); ?>" />

		</label>

		<label for="_timeline_styles_container_title_styles[style]">

			<strong><?php esc_html_e( 'Font Style', 'timeline-express-pro' ); ?></strong>

			<?php

			print( '<select class="widefat" name="_timeline_styles_container_title_styles[style]" id="_timeline_styles_container_title_styles[style]">' );

			$styles = [
				'inherit',
				'normal',
				'italic',
				'oblique',
				'initial',
			];

			foreach ( $styles as $style_type ) {

				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $style_type ),
					selected( $style_type, $style, false ),
					ucwords( $style_type )
				);

			}

			print( '</select>' );

			?>

		</label>

		<label for="_timeline_styles_container_title_styles[font_weight]">

			<strong><?php esc_html_e( 'Font Weight', 'timeline-express-pro' ); ?></strong>

			<?php

			print( '<select class="widefat" name="_timeline_styles_container_title_styles[font_weight]" id="_timeline_styles_container_title_styles[font_weight]">' );

			$ranges = range( 100, 900, 100 );

			array_unshift( $ranges, __( 'inherit', 'timeline-express-pro' ) );

			foreach ( $ranges as $weight ) {

				$text_equivelent = '';

				switch ( $weight ) {

					case 400:
						$text_equivelent = ' - ' . __( 'Normal', 'timeline-express-pro' );

						break;

					case 700:
						$text_equivelent = ' - ' . __( 'Bold', 'timeline-express-pro' );

						break;

				}

				printf(
					'<option value="%1$s" %2$s>%3$s</option>',
					esc_attr( $weight ),
					selected( $font_weight, $weight, false ),
					sprintf(
						'%1$s%2$s',
						ucwords( $weight ),
						$text_equivelent
					)
				);

			}

			print( '</select>' );

			?>

		</label>

		<label for="_timeline_styles_container_title_styles[color]">

			<strong><?php esc_html_e( 'Color', 'timeline-express-pro' ); ?></strong><br />

			<?php

			$colorpicker_atts = array(
				'name'    => $field_type_object->_name( '[color]' ),
				'id'      => $field_type_object->_id( '[color]' ),
				'value'   => (string) $color,
				'default' => (string) $field->args['default']['color'],
				'class'   => 'cmb2-colorpicker cmb2-text-small wp-color-picker',
			);

			echo $field_type_object->colorpicker( $colorpicker_atts, '#' );

			?>

		</label>

		<span class="cmb2-metabox-description">
			<?php
			printf(
				/* translators: 1. Singular announcement name (eg: announcement). */
				esc_html__( 'Customize the title styles for this %s.', 'timeline-express-pro' ),
				esc_html( Timeline_Express_Styles::$announcement_singular )
			);
			?>
		</span>

	</p>

	<?php

}
add_action( 'cmb2_render_container_title_styles', 'timeline_express_container_title_styles', 10, 5 );

/**
 * Sanitize our checkbox + color picker field.
 *
 * @param  string $override_value The new value.
 * @param  string $value          The original value.
 *
 * @since 1.0.0
 */
function timeline_express_sanitize_container_title_styles( $override_value, $value ) {

	$final_value = [];

	if ( ! isset( $value['inherit'] ) ) {

		$final_value['inherit'] = false;

	}

	foreach ( $value as $name => $val ) {

		$final_value[ $name ] = ( 'inherit' === $val ) ? (bool) $val : (string) $val;

	}

	return $final_value;

}
add_filter( 'cmb2_sanitize_container_title_styles', 'timeline_express_sanitize_container_title_styles', 10, 2 );
