<?php
/**
 * Wider Admin Menu > Settings > Form
 */
?>
<form method="post" action="options.php">

	<?php
	// version-based CSS classes
	if ( version_compare( $wp_version, '3.8', '>=' ) ) {
		$version_class = 'ver38';
		$reset_class = 'dashicons dashicons-undo';
	} else {
		$version_class = 'pre-ver38';
		$reset_class = 'undo';
	}

	$default_width = 160;
	settings_fields( 'wpmwam_settings_group' );
	$wpmwam_options = get_option( 'wpmwam_options' );
	$wpmwam_width = $wpmwam_options['wpmwam_width'];
	if ( ! $wpmwam_width )
		$wpmwam_width = $default_width;
	?>

	<input type="hidden" name="wp_version" value="<?php echo $wp_version; ?>">

	<table class="form-table wpmwam" style="width: auto;">
		<tr>
			<td><?php _e( 'New', 'wider-admin-menu' ); ?></td>
			<td class="input">
				<input id="wpmwam_width" type="text" name="wpmwam_options[wpmwam_width]"
						value="<?php esc_attr_e( $wpmwam_width ); ?>"
						size="8" maxlength="8" tabindex="1">px
			</td>
			<td class="slider">
				<div id="nouislider-wrap">
					<div id="nouislider"></div>
				</div>
			</td>
		</tr>
		<tr>
			<td><?php _e( 'Current', 'wider-admin-menu' ); ?></td>
			<td><span id="wpmwam_current" class="box <?php echo $version_class; ?>"><?php esc_attr_e( $wpmwam_width ); ?></span>px</td>
			<td class="lefted">
				<a id="reset-current" class="<?php echo $reset_class; ?>" href="#" title="<?php _e( 'revert to the current width', 'wider-admin-menu' ); ?>"></a>
			</td>
		</tr>
		<tr>
			<td><?php _e( 'Default', 'wider-admin-menu' ); ?></td>
			<td><span id="wpmwam_default" class="box <?php echo $version_class; ?>"><?php echo $default_width; ?></span>px</td>
			<td class="lefted">
					<a id="reset-default" class="<?php echo $reset_class; ?>" href="#" title="<?php _e( 'restore default width', 'wider-admin-menu' ); ?>"></a>
			</td>
		</tr>
	</table>

	<table class="form-table" style="width: auto;">
		<tr>
			<th scope="row">
				<label class="lnt" for="wpmwam_lnt">Leave No Trace</label>
			</th>
			<td>
				<div id="leave-no-trace">
					<select id="wpmwam_lnt" name="wpmwam_options[wpmwam_lnt]">
						<option value="1" <?php selected( $wpmwam_options['wpmwam_lnt'], 1 ); ?>>
							<?php _e( 'Yes - Deleting this plugin will also delete these settings.', 'wider-admin-menu' ); ?>
						</option>
						<option value="0" <?php selected( $wpmwam_options['wpmwam_lnt'], 0 ); ?>>
							<?php _e( 'No - These settings will remain after deleting this plugin.', 'wider-admin-menu' ); ?>
						</option>
					</select>

					<p class="help">
						<?php _e( 'Deactivating this plugin will not delete anything.', 'wider-admin-menu' ); ?>
					</p>
				</div>
			</td>
		</tr>
	</table>

	<?php submit_button(); ?>

</form>
