<?php
/*
 * Plugin Name: Multiple Columns for Gravity Forms
 * Plugin URI: https://wordpress.org/plugins/gf-form-multicolumn/
 * Description: Allows addition of multiple columns (and multiple rows of multiple columns) to Gravity Forms. <p>GDPR Compliance: this plugin does not collect data.</p>
 * Author: WebHolism
 * Author URI: http://www.webholism.com
 * Version: 3.0.3
 * Text Domain: gfmulticolumn
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl-3.0
 */

    define( 'GF_MULTICOLUMN_VERSION', '3.0.3' );
    define( 'GF_MULTICOLUMN_TITLE', 'Multiple Columns' );
    define( 'GF_MULTICOLUMN_FIELD_GROUP_TITLE', 'Multiple Columns Fields' );

	add_action( 'gform_loaded', [ 'GF_MultiColumn_Bootstrap', 'load' ], 5 );

	class GF_MultiColumn_Bootstrap {

		public static function load() {
			if ( ! method_exists( 'GFForms', 'include_addon_framework' ) ) {
				return;
			}
			require_once 'class-gf-multicolumn.php';
			GFAddOn::register( 'GFMultiColumn' );
		}
	}

	function gf_simple_addon() {
		return GFMultiColumn::get_instance();
	}