<?php
/**
 * GF Eloqua Bootstrap plugin class
 *
 * @package gfeloqua
 */

/**
 * Main GF Eloqua Class to hook into Gravity Forms
 */
class GFEloqua_Bootstrap {

	public static function load() {

		if ( ! class_exists( 'GFForms' ) || ! class_exists( 'GFAddOn' ) ) {
			return;
		}

		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			return;
		}

		GFForms::include_feed_addon_framework();

		require_once( GFELOQUA_PATH . '/includes/helpers.php' );
		require_once( GFELOQUA_PATH . '/api/class-eloqua-api.php' );
		require_once( GFELOQUA_PATH . '/includes/class-gfeloqua-entry-notes.php' );
		require_once( GFELOQUA_PATH . '/includes/class-gfeloqua.php' );
		require_once( GFELOQUA_PATH . '/includes/class-bd-license-manager.php' );
		require_once( GFELOQUA_PATH . '/includes/class-gfeloqua-extensions.php' );

		GFAddOn::register( 'GFEloqua' );

		// Init License Manager for GFEloqua.
		global $gfeloqua_license_manager;
		$gfeloqua_license_manager = bd_license_manager();

		// Setup plugin details.
		$gfeloqua_license_manager->setup_plugin( array(
			'path' => GFELOQUA_PATH,
			'slug' => 'gravityforms-eloqua',
			'filename' => 'gravityforms-eloqua.plugin.php',
			'sku' => 'GFELQ',
		) );

		// Init GFEloqua Extensions.
		global $gfeloqua_extensions;
		$gfeloqua_extensions = gfeloqua_extensions();
	}
}
