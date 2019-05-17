<?php
/**
 * Extensions!
 *
 * apply_filters( 'gfeloqua_extension_fields', '' );
 *
 * @package gfeloqua
 */
?>
<div class="wrap gfeloqua-extensions">
	<?php foreach ( $extensions as $extension ) : ?>
		<div class="gfeloqua-extension">
			<h4><?php echo $extension['name']; ?></h4>
			<p class="gfeloqua-extension-description"><?php echo $extension['description']; ?></p>
			<div class="gfeloqua-extension-actions">
				<?php if ( $extension['is_active'] ) : ?>
					<?php do_action( 'gfeloqua_extension_actions_before', $extension ); ?>
					<a href="<?php echo esc_attr( $extension['settings_url'] ); ?>" class="button-primary gfeloqua-extension-settings thickbox" title="<?php echo esc_attr( $extension['name'] ); ?> <?php _e( 'Settings', 'gfeloqua' ); ?>"><?php _e( 'Settings', 'gfeloqua' ); ?></a>
					<?php if ( $extension['deactivate_url'] ) : ?>
						<a href="<?php echo esc_url( $extension['deactivate_url'] ); ?>" class="button-secondary gfeloqua-extension-deactivate"><?php _e( 'Deactivate', 'gfeloqua' ); ?></a>
					<?php else : ?>
						<span class="gfeloqua-extension-status status-active"><?php _e( 'Active' ); ?></span>
					<?php endif; ?>
					<?php do_action( 'gfeloqua_extension_actions_after', $extension ); ?>
				<?php elseif ( ! current_user_can( 'activate_plugin', $extension['plugin'] ) ) : ?>
					<span class="gfeloqua-extension-status status-inactive"><?php _e( 'Inactive' ); ?></span>
				<?php elseif ( $extension['activate_url'] && file_exists( trailingslashit( WP_PLUGIN_DIR ) . $extension['plugin'] ) ) : ?>
					<a href="<?php echo esc_url( $extension['activate_url'] ); ?>" class="button-primary gfeloqua-extension-activate"><?php _e( 'Activate', 'gfeloqua' ); ?></a>
				<?php else : ?>
					<a href="<?php echo esc_url( $extension['purchase_url'] ); ?>" target="_blank" rel="noopener" class="button-primary gfeloqua-extension-purchase"><?php _e( 'Purchase', 'gfeloqua' ); ?></a>
				<?php endif; ?>
			</div>

			<?php include( apply_filters( 'gfeloqua_extension_settings_view', GFELOQUA_PATH . 'views/extension-settings.php' ) ); ?>

		</div>
	<?php endforeach; ?>

	<div class="gfeloqua-extension">
		<h4><?php _e( 'More Coming Soon!', 'gfeloqua' ); ?></h4>
		<p class="gfeloqua-extension-description"><?php _e( 'Keep checking back for new extensions!', 'gfeloqua' ); ?></p>
	</div>
</div>
