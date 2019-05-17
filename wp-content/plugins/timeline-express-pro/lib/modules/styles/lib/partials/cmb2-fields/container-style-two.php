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
function timeline_express_container_style_two( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {

	global $post;

	$defaults = isset( $post->ID ) ? timeline_express_styles_metabox_values( $post->ID, 'container_style_styles' ) : false;

	$width = (string) isset( $defaults['.timeline-express-read-more-link:width'] ) ? $defaults['.timeline-express-read-more-link:width'] : $field->args['defaults']['.timeline-express-read-more-link']['width'];
	$align = (string) isset( $defaults['.timeline-express-read-more-link:text-align'] ) ? $defaults['.timeline-express-read-more-link:text-align'] : $field->args['defaults']['.timeline-express-read-more-link']['text-align'];

	?>

	<p class="container-styles-holder">

		<label for="_timeline_styles_container_style_two[.cd-timeline-title-container]">

			<strong>
				<?php esc_html_e( 'Read More Width (%)', 'timeline-express-pro' ); ?>
				<span class="dashicons dashicons-editor-help te-tooltip" title="<?php esc_html_e( 'Note: Set this value to 0 to inherit the width from the current theme.', 'timeline-express-pro' ); ?>"></span>
			</strong><br />

			<?php

			$input_atts = array(
				'type'    => 'number',
				'name'    => '_timeline_styles_container_style_styles[style-two][.timeline-express-read-more-link][width]',
				'id'      => '_timeline_styles_container_style_styles[style-two][.timeline-express-read-more-link][width]',
				'value'   => $width,
				'default' => $width,
				'class'   => 'widefat',
				'max'     => '100',
				'min'     => '0',
			);

			echo $field_type_object->input( $input_atts, '#' );

			?>

		</label>

		<label for="_timeline_styles_container_style_two[.cd-timeline-title-container]">

			<strong>
				<?php esc_html_e( 'Read More Text Align', 'timeline-express-pro' ); ?>
				<span class="dashicons dashicons-editor-help te-tooltip" title="<?php esc_html_e( 'Note: Set this value to 0 to inherit the read more width from the current theme.', 'timeline-express-pro' ); ?>"></span>
			</strong><br />

			<select name="_timeline_styles_container_style_styles[style-two][.timeline-express-read-more-link][text-align]" id="_timeline_styles_container_style_styles[style-two][.timeline-express-read-more-link][text-align]" class="widefat">

				<?php

				$text_aligns = [
					'default' => esc_html__( 'Inherit', 'timeline-express-pro' ),
					'left'    => esc_html__( 'Left', 'timeline-express-pro' ),
					'right'   => esc_html__( 'Right', 'timeline-express-pro' ),
					'center'  => esc_html__( 'Center', 'timeline-express-pro' ),
				];

				foreach ( $text_aligns as $slug => $text ) {

					printf(
						'<option value="%1$s"%2$s>%3$s</option>',
						esc_attr( $slug ),
						selected( $slug, $align, false ),
						esc_html( $text )
					);

				}

				?>

			</select>

		</label>

		<span class="cmb2-metabox-description">
			<?php
			printf(
				/* translators: 1. Singular announcement name (eg: announcement). */
				esc_html__( 'Customize "Style Two" container styles for this %s.', 'timeline-express-pro' ),
				esc_html( Timeline_Express_Styles::$announcement_singular )
			);
			?>
		</span>

	</p>

	<?php

}
add_action( 'cmb2_render_container_style_two', 'timeline_express_container_style_two', 10, 5 );
