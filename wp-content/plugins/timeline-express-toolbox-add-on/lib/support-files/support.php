<?php
/**
 * Licensing Functions
 *
 * @author Code Parrots <info@codeparrots.com>
 */

// Timeline Express Free
if ( is_plugin_active( 'timeline-express/timeline-express.php' ) ) {

	add_action( 'admin_menu', function() {

		remove_submenu_page( 'edit.php?post_type=te_announcements', 'timeline-express-support' );

		add_submenu_page(
			'edit.php?post_type=te_announcements',
			__( 'Toolbox License', 'timeline-express-toolbox-add-on' ),
			__( 'Toolbox License', 'timeline-express-toolbox-add-on' ),
			'manage_options',
			'toolbox-license',
			'generate_toolbox_support_page'
		);

	}, 999 );

	/**
	 * Generate the support section
	 *
	 * @since 1.0.0
	 */
	function generate_toolbox_support_page() {

		include_once( TIMELINE_EXPRESS_TOOLBOX_PATH . 'lib/partials/support-section.php' );

	}
}

// Timeline Express Pro
if ( is_plugin_active( 'timeline-express-pro/timeline-express-pro.php' ) ) {

	/**
	* Generate the Timeline Express Toolbox Add-on Support Tab
	*
	* @param  string $active_tab The active tab
	*
	* @since 1.0.0
	*/
	add_action( 'timeline-express-support-tabs', function( $active_tab ) {

		?>

			<a class="nav-tab<?php if ( 'toolbox-addon' === $active_tab ) { ?> nav-tab-active<?php } ?>" href="<?php echo esc_url( admin_url( 'edit.php?post_type=te_announcements&page=timeline-express-license&tab=toolbox-addon' ) ); ?>">Toolbox Add-On</a>

		<?php

	} );

	/**
	 * Generate the support section
	 *
	 * @param  string $active_tab The active tab
	 *
	 * @return mixed
	 */
	add_action( 'timeline-express-support-sections', function( $active_tab ) {

		if ( 'toolbox-addon' !== $active_tab ) {

			return;

		}

		include_once( TIMELINE_EXPRESS_TOOLBOX_PATH . 'lib/partials/support-section.php' );

	} );
}
