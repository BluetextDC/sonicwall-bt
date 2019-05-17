<?php

namespace ACP\ListScreen;

use AC;
use ACP\Column;
use ACP\Editing;
use WP_MS_Sites_List_Table;

class MSSite extends AC\ListScreenWP
	implements Editing\ListScreen {

	public function __construct() {

		$this->set_label( __( 'Network Sites' ) )
		     ->set_singular_label( __( 'Network Site' ) )
		     ->set_key( 'wp-ms_sites' )
		     ->set_screen_id( 'sites-network' )
		     ->set_screen_base( 'sites-network' )
		     ->set_meta_type( 'site' )
		     ->set_group( 'network' )
		     ->set_network_only( true );
	}

	/**
	 * @since 4.0
	 *
	 * @param int $site_id
	 *
	 * @return \WP_Site Site object
	 */
	protected function get_object( $site_id ) {
		return get_site( $site_id );
	}

	/**
	 * @return WP_MS_Sites_List_Table
	 */
	public function get_list_table() {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-ms-sites-list-table.php' );

		return new WP_MS_Sites_List_Table( array( 'screen' => $this->get_screen_id() ) );
	}

	public function set_manage_value_callback() {
		add_action( "manage_sites_custom_column", array( $this, 'manage_value' ), 100, 2 );
	}

	/**
	 * @return string
	 */
	protected function get_admin_url() {
		return network_admin_url( 'sites.php' );
	}

	/**
	 * Settings page is the first subsite instead of one the network page.
	 *
	 * @return string
	 */
	public function get_edit_link() {
		return add_query_arg( array(
			'tab'         => 'columns',
			'page'        => AC\Admin::PLUGIN_PAGE,
			'list_screen' => $this->get_key(),
			'layout_id'   => $this->get_layout_id(),
		), admin_url( 'options-general.php' ) );
	}

	/**
	 * @since 2.4.7
	 *
	 * @param $column_name
	 * @param $blog_id
	 */
	public function manage_value( $column_name, $blog_id ) {
		echo $this->get_display_value_by_column_name( $column_name, $blog_id, null );
	}

	public function get_single_row( $site_id ) {
		return false;
	}

	/**
	 * Register custom columns
	 * @throws \ReflectionException
	 */
	protected function register_column_types() {
		$this->register_column_type( new Column\Actions() );
		$this->register_column_types_from_dir( 'ACP\Column\NetworkSite' );
	}

	public function editing() {
		return new Editing\Strategy\Site();
	}

}