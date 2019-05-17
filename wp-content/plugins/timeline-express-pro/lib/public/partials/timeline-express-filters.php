<?php
/**
 * Timeline Express Filters section
 * Copy your theme root in a '/timeline-express/' directory (not recommended, but can be done)
 * For help, please see: https://www.wp-timelineexpress.com/documentation/customize-announcement-container/
 * @since 1.2
 */
global $post;

/**
 * Check for Categories
 */
if ( isset( $_GET['timeline-category'] ) ) {

	$cats = $_GET['timeline-category'];

	if ( explode( ',', $_GET['timeline-category'] ) ) {

		$cats = explode( ',', $_GET['timeline-category'] );

	} // @codingStandardsIgnoreLine

} else {

	$cats = '';

}

/**
 * Check for timeline query arg for filtering
 */
$timeline_filter_array = ( isset( $_GET['timeline'] ) ) ? explode( ',', $_GET['timeline'] ) : array();

/**
 * Display the dropdown only if the timeline to query was not specified, and the filter was set to 1
 */
if ( ! empty( $atts['timeline'] ) ) {

	$dropdown_array = array();

	$timelines = timeline_express_tax_array( 'timelines', $atts['timeline'] );

	foreach ( $timelines as $timeline_id ) {

		$term_array = get_term_by( 'id', $timeline_id, 'timeline', ARRAY_A );

		if ( $term_array ) {

			$dropdown_array[] = array(
				'name'    => $term_array['name'],
				'term_id' => $term_array['term_id'],
			);

		}

		$dropdown = '<select name="timelines" id="timelines" class="postform">';

		if ( count( $dropdown_array ) > 1 ) {

			$dropdown .= '<option class="level-0" value="-1">' . __( 'All Timelines', 'timeline-express-pro' ) . '</option>';

		}

		foreach ( $dropdown_array as $dropdown_option ) {

			$chosen_one = ( in_array( $dropdown_option['term_id'], $timeline_filter_array ) ) ? 'selected' : '';

			$dropdown .= '<option class="level-0" value="' . $dropdown_option['term_id'] . '" ' . $chosen_one . '>' . $dropdown_option['name'] . '</option>';

		}

		$dropdown .= '</select>';

	} // @codingStandardsIgnoreLine

} // End if().

$dropdown_active = ( ! empty( $atts['timeline'] ) && ( isset( $timelines ) && count( $timelines ) > 1 ) ) ? true : false;

/**
 * If the user has timelines set
 */
?>
<section class="timeline-express-filters">

	<div class="timeline-express-filter-group">

		<!-- action -->
		<input type="hidden" name="action" value="filter-timeline">

		<!-- redirect URL -->
		<input type="hidden" name="filter-redirect-url" value="<?php echo esc_url( get_the_permalink( $post->ID ) ); ?>">

		<?php
		if ( $dropdown_active ) {

			if ( isset( $dropdown ) ) {

				?>

				<div class="timeline-express-filter-dropdown timeline-express-filter-left">

					<?php
						echo apply_filters( 'timeline_express_timeline_filter_label', '<span style="display:block;width:100%;">' . __( 'Filter by Timeline', 'timeline-express-pro' ) . '</span>' );
						echo $dropdown;
					?>

				</div>

				<?php

			} // @codingStandardsIgnoreLine

		}

		$categories = empty( $atts['categories'] ) ? false : timeline_express_tax_array( 'categories', $atts['categories'] );

		if ( $categories && count( $categories ) > 1 ) {

			$style = empty( $atts['categories'] ) ? 'display: none;' : 'margin-left: 0 !important;';

			?>
			<!-- Categories Checkboxes -->
			<div class="timeline-express-filter-checkbox <?php echo ( $dropdown_active ) ? 'timeline-express-filter-right' : 'timeline-express-filter-left'; ?>" style="<?php echo esc_attr( $style ); ?>">

				<?php echo apply_filters( 'timeline_express_timeline_filter_label', '<span style="display: block; width: 100%;">' . esc_html__( 'Filter by Category', 'timeline-express-pro' ) . '</span>' ); ?>

				<ul class="cat-checkbox-container" style="margin-left: 0 !important;padding-left:0 !important;">
					<?php

					$checkbox_array = array();

					foreach ( $categories as $term_id ) {

						$term_array = get_term_by( 'id', $term_id, 'timeline_express_categories', ARRAY_A );

						if ( $term_array ) {

							$checkbox_array[] = array(
								'name'    => $term_array['name'],
								'term_id' => $term_array['term_id'],
							);

						} // @codingStandardsIgnoreLine

					}

					foreach ( $checkbox_array as $term_array ) {

						if ( is_array( $cats ) ) {

							$checked = in_array( $term_array['term_id'], $cats ) ? 'checked="checked"' : '';

						} else {

							$checked = ( $term_array['term_id'] == $cats ) ? 'checked="checked"' : '';

						}

						?>

						<li>
							<label for="<?php echo $term_array['name']; ?>">
								<input type="checkbox" name="timeline-express-category" id="<?php echo esc_attr( $term_array['name'] ); ?>" value="<?php echo esc_attr( $term_array['term_id'] ); ?>" title="<?php echo esc_attr( $term_array['name'] ); ?>" <?php echo $checked; ?>>
								<?php echo esc_attr( $term_array['name'] ); ?>
							</label>
						</li>

						<?php

					}
					?>
				</ul>

			</div>

			<?php

		}

		?>

	</div>

	<input class="timeline-express-filter-submit" type="submit" class="<?php echo esc_attr( apply_filters( 'timeline-express-filter-button-classes', 'timeline-express-filter' ) ); ?>" title="<?php esc_attr_e( 'Filter Timeline', 'timeline-express-pro' ); ?>" value="<?php echo esc_attr( apply_filters( 'timeline_express_filter_button_text', __( 'Filter Timeline', 'timeline-express-pro' ) ) ); ?>">

</section>

<?php
