<?php

namespace ACP\Export;

use AC;
use ACP\Asset\Location;
use ACP\Asset\Script;
use ACP\Asset\Style;
use ACP\Export\Asset;
use ACP\Export\Asset\Script\Table;

class Addon implements AC\Registrable {

	/**
	 * @var string
	 */
	public $plugin_file;

	public function __construct() {
		$this->plugin_file = ACP_FILE;
	}

	public function register() {
		new Admin();

		$table_screen = $this->table_screen();
		$table_screen->register();

		$this->register_table_screen_options();

		add_action( 'ac/table/list_screen', array( $this, 'load_list_screen' ) );
	}

	public function table_screen() {
		return new TableScreen( array(
			new Style( 'acp-export-listscreen', $this->get_location()->with_suffix( 'assets/export/css/listscreen.css' ) ),
			new Table( 'acp-export-listscreen', $this->get_location()->with_suffix( 'assets/export/js/listscreen.js' ) ),
		) );
	}

	public function register_table_screen_options() {
		new TableScreenOptions( array(
			new Script( 'acp-export-table-screen-options', $this->get_location()->with_suffix( 'assets/export/js/table-screen-options.js' ) ),
		) );
	}

	private function get_location() {
		return new Location\Absolute(
			plugin_dir_url( $this->plugin_file ),
			plugin_dir_path( $this->plugin_file )
		);
	}

	/**
	 * Get the path and URL to the directory used for uploading
	 * @since 1.0
	 * @return array Two-dimensional associative array with keys "path" and "url", containing the
	 *   full path and the full URL to the export files directory, respectively
	 */
	public function get_export_dir() {
		// Base directory for uploads
		$upload_dir = wp_upload_dir();

		// Paths for exported files
		$suffix = 'admin-columns/export/';
		$export_path = trailingslashit( $upload_dir['basedir'] ) . $suffix;
		$export_url = trailingslashit( $upload_dir['baseurl'] ) . $suffix;
		$export_path_exists = true;

		// Maybe create export directory
		if ( ! is_dir( $export_path ) ) {
			$export_path_exists = wp_mkdir_p( $export_path );
		}

		return array(
			'path'  => $export_path,
			'url'   => $export_url,
			'error' => $export_path_exists ? '' : __( 'Creation of Admin Columns export directory failed. Please make sure that your uploads folder is writable.', 'codepress-admin-columns' ),
		);
	}

	/**
	 * Load a list screen and potentially attach the proper exporting information to it
	 * @since 1.0
	 *
	 * @param AC\ListScreen $list_screen List screen for current table screen
	 */
	public function load_list_screen( $list_screen ) {
		if ( $list_screen instanceof ListScreen ) {
			$list_screen->export()->attach();
		}
	}

}