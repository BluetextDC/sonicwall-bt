<?php
/**
 * Extension Settings
 *
 * @package gravityforms-eloqua
 */

if ( $extension['is_active'] ) :
	?>
	<div id="<?php echo $extension['slug']; ?>-settings" style="display: none;">
		<div class="gfeloqua_extension_settings_modal_container">
			<div class="gfeloqua-extension-settings-errors errors"></div>
			<div class="gfeloqua-extension-settings">
				<div class="gfeloqua-settings-fields">
					<?php do_action( 'gfeloqua_extension_settings', $extension, array() ); ?>
				</div>
				<div class="submit-row">
					<input type="hidden" name="<?php echo esc_attr( GFELOQUA_OPT_PREFIX ); ?>extension_slug" value="<?php echo esc_html( $extension['slug'] ); ?>" />
					<input type="button" class="button button-large button-primary gfeloqua-save-extension-settings" value="<?php echo esc_html__( 'Save Settings', 'gfeloqua' ); ?>" tabindex="9002" />
				</div>
			</div>
		</div>
	</div>
	<?php
endif;
