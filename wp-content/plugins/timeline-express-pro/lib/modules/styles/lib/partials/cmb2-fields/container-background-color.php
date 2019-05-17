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
function timeline_express_container_background_color( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

	$inherit = (bool) isset( $escaped_value['inherit'] ) ? $escaped_value['inherit'] : $field->args['default']['inherit'];

	?>

	<label for="_timeline_styles_container_background_color[inherit]">

		<input type="checkbox" class="regular-text inherit-toggle" name="_timeline_styles_container_background_color[inherit]" id="_timeline_styles_container_background_color[inherit]" <?php checked( $inherit, true ); ?> value="1">

		<strong><?php esc_html_e( 'Inherit', 'timeline-express-pro' ); ?></strong>&nbsp;

	</label>

	<p class="container-background-style-holder<?php echo $inherit ? ' hidden' : ''; ?>"><label for="_timeline_styles_container_background_color[color]">

		<strong><?php esc_html_e( 'Background Color', 'timeline-express-pro' ); ?></strong><br />

		<?php

		$color_picker_atts = array(
			'name'    => $field_type_object->_name( '[color]' ),
			'id'      => $field_type_object->_id( '[color]' ),
			'value'   => (string) $escaped_value['color'],
			'default' => (string) $field->args['default']['color'],
			'class'   => 'cmb2-colorpicker cmb2-text-small wp-color-picker',
		);

		echo $field_type_object->colorpicker( $color_picker_atts, '#' );

		?>

		<span class="cmb2-metabox-description"><?php esc_html_e( 'Select the background color for this container.', 'timeline-express-pro' ); ?></span>

	</label></p>

	<?php

}
add_action( 'cmb2_render_container_background_color', 'timeline_express_container_background_color', 10, 5 );

/**
 * Sanitize our checkbox + color picker field.
 *
 * @param  string $override_value The new value.
 * @param  string $value          The original value.
 *
 * @since 1.0.0
 */
function timeline_express_sanitize_container_background_color( $override_value, $value ) {

	$override_value = [
		'inherit' => (bool) isset( $value['inherit'] ) ? true : false,
		'color'   => (string) $value['color'],
	];

	return $override_value;

}
add_filter( 'cmb2_sanitize_container_background_color', 'timeline_express_sanitize_container_background_color', 10, 2 );
