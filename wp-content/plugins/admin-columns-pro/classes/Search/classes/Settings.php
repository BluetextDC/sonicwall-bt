<?php

namespace ACP\Search;

use AC;
use AC\Registrable;

class Settings
	implements Registrable {

	/**
	 * @var Addon
	 */
	protected $addon;

	public function __construct( Addon $addon ) {
		$this->addon = $addon;
	}

	public function register() {
		add_action( 'ac/column/settings', array( $this, 'column_settings' ) );
		add_action( 'ac/settings/scripts', array( $this, 'admin_scripts' ) );
	}

	public function column_settings( AC\Column $column ) {
		if ( ! $column instanceof Searchable ) {
			return;
		}

		$setting = new Settings\Column( $column );
		$setting->set_default( 'on' );

		$column->add_setting( $setting );
	}

	public function admin_scripts() {
		wp_enqueue_style( 'acp-search-admin', $this->addon->get_url() . 'assets/css/admin.css', array(), $this->addon->get_version() );
	}

}