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
function timeline_express_container_style_one( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

	$title         = (string) isset( $escaped_value['.cd-timeline-title-container .cd-timeline-item-title:background-color'] ) ? $escaped_value['.cd-timeline-title-container .cd-timeline-item-title:background-color'] : $field->args['defaults']['.cd-timeline-title-container .cd-timeline-item-title']['background-color'];
	$content       = (string) isset( $escaped_value['.cd-timeline-content:background'] ) ? $escaped_value['.cd-timeline-content:background'] : $field->args['defaults']['.cd-timeline-content']['background'];
	$content_color = (string) isset( $escaped_value['.cd-timeline-content:color'] ) ? $escaped_value['.cd-timeline-content:color'] : $field->args['defaults']['.cd-timeline-content']['color'];

	?>

	<p class="container-styles-holder">

		<label for="_timeline_styles_container_style_one[.cd-timeline-title-container]">

			<strong><?php esc_html_e( 'Title Background', 'timeline-express-pro' ); ?></strong><br />

			<?php

			$colorpicker_atts = array(
				'name'    => $field_type_object->_name( '[style-one][.cd-timeline-title-container .cd-timeline-item-title][background-color]' ),
				'id'      => $field_type_object->_id( '[style-one][.cd-timeline-title-container .cd-timeline-item-title][background-color]' ),
				'value'   => $title,
				'default' => $title,
				'class'   => 'cmb2-colorpicker cmb2-text-small wp-color-picker',
			);

			echo $field_type_object->colorpicker( $colorpicker_atts, '#' );

			?>

		</label>

		<label for="_timeline_styles_container_style_one[.cd-timeline-content]">

			<strong><?php esc_html_e( 'Content Background', 'timeline-express-pro' ); ?></strong><br />

			<?php

			$colorpicker_atts = array(
				'name'    => $field_type_object->_name( '[style-one][.cd-timeline-content][background]' ),
				'id'      => $field_type_object->_id( '[style-one][.cd-timeline-content][background]' ),
				'value'   => $content,
				'default' => $content,
				'class'   => 'cmb2-colorpicker cmb2-text-small wp-color-picker',
			);

			echo $field_type_object->colorpicker( $colorpicker_atts, '#' );

			?>

		</label>

		<label for="_timeline_styles_container_style_one[.cd-timeline-content]">

			<strong><?php esc_html_e( 'Content Text Color', 'timeline-express-pro' ); ?></strong><br />

			<?php

			$colorpicker_atts = array(
				'name'    => $field_type_object->_name( '[style-one][.cd-timeline-content][color]' ),
				'id'      => $field_type_object->_id( '[style-one][.cd-timeline-content][color]' ),
				'value'   => $content_color,
				'default' => $content_color,
				'class'   => 'cmb2-colorpicker cmb2-text-small wp-color-picker',
			);

			echo $field_type_object->colorpicker( $colorpicker_atts, '#' );

			?>

		</label>

		<span class="cmb2-metabox-description">
			<?php
			printf(
				/* translators: 1. Singular announcement name (eg: announcement). */
				esc_html__( 'Customize "Style One" container styles for this %s.', 'timeline-express-pro' ),
				esc_html( Timeline_Express_Styles::$announcement_singular )
			);
			?>
		</span>

	</p>

	<?php

}
add_action( 'cmb2_render_container_style_one', 'timeline_express_container_style_one', 10, 5 );

function timeline_express_sanitize_container_style_one( $override_value, $value ) {

	global $post;

	$data  = (array) $_POST;
	$value = (array) $data['_timeline_styles_container_style_styles'];
	$style = timeline_express_styles_metabox_values( $post->ID, 'container_style' );

	// unset values not associated with the saved style
	foreach ( $value as $style_name => $style_data ) {

		if ( $style === $style_name ) {

			continue;

		}

		unset( $value[ $style_name ] );

	}

	$final_value = [];

	if ( ! isset( $value[ $style ] ) || empty( $value[ $style ] ) ) {

		return [];

	}

	foreach ( $value[ $style ] as $key => $val ) {

		foreach ( $val as $style => $style_value ) {

			$final_value[ $key . ':' . $style ] = $style_value;

		} // @codingStandardsIgnoreLine

	}

	return $final_value;

}
add_filter( 'cmb2_sanitize_container_style_one', 'timeline_express_sanitize_container_style_one', 10, 2 );
