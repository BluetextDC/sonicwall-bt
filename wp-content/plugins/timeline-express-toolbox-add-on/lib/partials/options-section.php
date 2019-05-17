<?php
/**
 * Timeline Express No Icons Add-On Settings Section & Options
 *
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {

	exit;

}
?>

<form method="post" action="options.php" name="timeline-express-form" id="timeline-express-form">

	<?php
		/* Do the settings fields */
		settings_fields( 'timeline-express-toolbox-settings' );
		do_settings_sections( 'timeline-express-toolbox-settings' );

		/* Nonce security check :) */
		wp_nonce_field( 'timeline_express_toolbox_save_settings', 'timeline_express_toolbox_settings_nonce' );
	?>

	<table class="form-table timeline-express-form">

		<tbody>

			<!-- Announcement Slug -->
			<tr valign="top">

				<th scope="row">

					<label for="timeline_express_storage[announcement_slug]">
						<?php esc_html_e( 'Announcement Slug', 'timeline-express-toolbox-add-on' ); ?>
					</label>

				</th>

				<td>

					<input name="timeline_express_storage[announcement_slug]" id="timeline_express_storage[announcement_slug]" type="text" placeholder="<?php esc_attr_e( 'announcement', 'timeline-express' ); ?>" value="<?php echo esc_attr( $this->addon_options['announcement_slug'] ); ?>" class="widefat" />

					<p class="description">
						<?php printf( esc_html( 'Set the %s of all announcements on your site.' , 'timeline-express-toolbox-add-on' ), '<a href="https://codex.wordpress.org/Glossary#Post_Slug" title="WordPress Codex Glossary - Post Slug" target="_blank">' . __( 'post slug', 'timeline-express-toolbox-add-on' ) . '</a>' ); ?>
					</p>

					<?php $this->current_announcement_slug_markup(); ?>

				</td>

			</tr>

			<!-- Announcement Singular -->
			<tr valign="top">

				<th scope="row">

					<label for="timeline_express_storage[announcement_singular]">
						<?php esc_html_e( 'Announcement Singular', 'timeline-express-toolbox-add-on' ); ?>
					</label>

				</th>

				<td>

					<input name="timeline_express_storage[announcement_singular]" id="timeline_express_storage[announcement_singular]" type="text" placeholder="<?php esc_attr_e( 'Announcement', 'timeline-express' ); ?>" value="<?php echo esc_attr( $this->addon_options['announcement_singular'] ); ?>" class="widefat" />

					<p class="description">
						<?php esc_html_e( 'Alter all instances of "Announcement" within the plugin on the dashboard.' , 'timeline-express-toolbox-add-on' ); ?>
					</p>

				</td>

			</tr>

			<!-- Announcement Plural -->
			<tr valign="top">

				<th scope="row">

					<label for="timeline_express_storage[announcement_plural]">
						<?php esc_html_e( 'Announcement Plural', 'timeline-express-toolbox-add-on' ); ?>
					</label>

				</th>

				<td>

					<input name="timeline_express_storage[announcement_plural]" id="timeline_express_storage[announcement_plural]" type="text" placeholder="<?php esc_attr_e( 'Announcements', 'timeline-express' ); ?>" value="<?php echo esc_attr( $this->addon_options['announcement_plural'] ); ?>" class="widefat" />

					<p class="description">
						<?php esc_html_e( 'Alter all instances of "Announcements" within the plugin on the dashboard.' , 'timeline-express-toolbox-add-on' ); ?>
					</p>

				</td>

			</tr>

			<!-- Announcement Date String -->
			<tr valign="top">

				<th scope="row">

					<label for="timeline_express_storage[date_string]">
						<?php esc_html_e( 'Announcement Date String', 'timeline-express-toolbox-add-on' ); ?>
					</label>

				</th>

				<td>

					<input name="timeline_express_storage[date_string]" id="timeline_express_storage[date_string]" type="text" placeholder="<?php echo _x( 'Announcement Date: {date}', 'Note: Translate everything but {date}. {date} is the location where the date will appear in the string.', 'timeline-express-toolbox-add-on' ); ?>" value="<?php echo esc_attr( $this->addon_options['date_string'] ); ?>" class="widefat" />

					<p class="description">
						<?php printf( esc_html( 'Alter the announcement date string, allowing you to move the date or change the string altogether. This is the text that appears on the single announcement page eg: "Announcement Date: %1$s". Use %2$s to specify where the date will appear in the string.', 'timeline-express-toolbox-add-on' ), date( $this->addon_options['date_format'], strtotime( 'now' ) ), '<strong>{date}</strong>' ); ?>
					</p>

				</td>

			</tr>

			<!-- Announcement Date Format -->
			<tr valign="top" class="announcement-date">

				<th scope="row">

					<label for="timeline_express_storage[date_format]">
						<?php esc_html_e( 'Announcement Date Format', 'timeline-express-toolbox-add-on' ); ?>
					</label>

				</th>

				<td>

					<?php $this->date_format_option_markup(); ?>

					<p class="description">
						<?php echo sprintf( esc_html( 'Set the date format of the announcements. For additional formats please see %s.' , 'timeline-express-toolbox-add-on' ), '<a href="http://php.net/manual/en/function.date.php" target="_blank" title="' . __( 'PHP Date Documentation', 'timeline-express-toolbox-add-on' ) . '">PHP date documentation</a>' ); ?>
					</p>

				</td>

			</tr>

			<!-- Announcement Capabilities -->
			<tr valign="top" class="announcement-date">

				<th scope="row">

					<label for="timeline_express_storage[date_format]">
						<?php esc_html_e( 'Edit Capabilities', 'timeline-express-toolbox-add-on' ); ?>
					</label>

				</th>

				<td>

					<?php $this->user_levels_option_markup(); ?>

					<p class="description">
						<?php esc_html_e( 'Set the lowest user level who can access the Timeline Express settings.' , 'timeline-express-toolbox-add-on' ); ?>
					</p>

					<p class="description">
						<code><?php esc_html_e( 'Note: Administrators will always have access.' , 'timeline-express-toolbox-add-on' ); ?></code>
					</p>

				</td>

			</tr>

			<!-- Timeline Announcement Image Sizes -->
			<tr valign="top">

				<th scope="row">

					<label for="timeline_express_storage[image_size]">
						<?php esc_html_e( 'Timeline Image Size', 'timeline-express-toolbox-add-on' ); ?>
					</label>

				</th>

				<td>

					<?php $this->image_size_option_markup( 'timeline' ); ?>

					<p class="description">
						<?php printf( __( 'Set the announcement image size on the timeline. To add custom image sizes, please see the following %s.' , 'timeline-express-toolbox-add-on' ), '<a href="https://developer.wordpress.org/reference/functions/add_image_size/" target="_blank">codex article</a>' ); ?>
					</p>

				</td>

			</tr>

			<!-- Timeline Single Page Image Sizes -->
			<tr valign="top">

				<th scope="row">

					<label for="timeline_express_storage[image_size]">
						<?php esc_html_e( 'Single Announcement Image Size', 'timeline-express-toolbox-add-on' ); ?>
					</label>

				</th>

				<td>

					<?php $this->image_size_option_markup( 'single' ); ?>

					<p class="description">
						<?php printf( __( 'Set the announcement image size on the single template. To add custom image sizes, please see the following %s.' , 'timeline-express-toolbox-add-on' ), '<a href="https://developer.wordpress.org/reference/functions/add_image_size/" target="_blank">codex article</a>' ); ?>
					</p>

				</td>

			</tr>

			<!-- Timeline Years Icon Toggle -->

			<tr valign="top">

				<th scope="row">

					<label for="timeline_express_storage[image_size]">
						<?php esc_html_e( 'Year Icons', 'timeline-express-toolbox-add-on' ); ?>
					</label>

				</th>

				<td>

					<input type="checkbox" name="timeline_express_storage[year_icons]" id="timeline_express_storage[year_icons]" value="1" <?php checked( (int) $this->addon_options['year_icons'], 1 ); ?> />

					<p class="description">
						<?php printf( __( 'Display the year of the announcement (instead of the icon) on the timeline. %s' , 'timeline-express-toolbox-add-on' ), '<strong>' . __( 'Note:', 'timeline-express-toolbox-add-on' ) . '</strong> ' . __( 'This will also hide the icon selection on the new/edit announcement screens.', 'timeline-express-toolbox-add-on' ) ); ?>
					</p>

					<?php

					if ( 1 !== (int) $this->addon_options['year_icons'] && ( defined( 'TIMELINE_EXPRESS_YEAR_ICONS' ) && TIMELINE_EXPRESS_YEAR_ICONS ) ) {

						printf(
							'<p class="description" style="color: rgba(255, 77, 77, 0.77);"><small>%1$s</small></p>',
							sprintf(
								esc_html__( 'It looks like you have already defined the constant %1$s. This option will have no affect.', 'timeline-express-toolbox-add-on' ),
								'<code>TIMELINE_EXPRESS_YEAR_ICONS</code>'
							)
						);

					}

					?>

				</td>

			</tr>

			<!-- Submit Button -->
			<tr>

				<td></td>

				<td>

					<input type="hidden" name="save-timeline-express-options" value="true" />

					<input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_attr_e( 'Save Settings', 'timeline-express-toolbox-add-on' ); ?>">

				</td>

			</tr>

		</tbody>

	</table>

</form>
