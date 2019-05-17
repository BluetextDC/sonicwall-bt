<?php
namespace ACP;

use AC\Admin;
use AC\AdminColumns;
use AC\Groups;
use AC\Registrable;

class ListScreens implements Registrable {

	public function register() {
		add_action( 'ac/list_screen_groups', array( $this, 'register_list_screen_groups' ) );
		add_action( 'ac/list_screens', array( $this, 'register_list_screens' ) );
	}

	/**
	 * @param Groups $groups
	 */
	public function register_list_screen_groups( Groups $groups ) {
		$groups->register_group( 'taxonomy', __( 'Taxonomy' ), 15 );
		$groups->register_group( 'network', __( 'Network' ), 5 );
	}

	/**
	 * @return bool
	 */
	private function is_settings_screen() {
		return Admin::PLUGIN_PAGE === filter_input( INPUT_GET, 'page' ) && 'columns' === filter_input( INPUT_GET, 'tab' );
	}

	/**
	 * @since 4.0
	 *
	 * @param AdminColumns $admin_columns
	 */
	public function register_list_screens( $admin_columns ) {
		$list_screens = array();

		// Post types
		foreach ( AC()->get_post_types() as $post_type ) {
			$list_screens[] = new ListScreen\Post( $post_type );
		}

		$list_screens[] = new ListScreen\Media();
		$list_screens[] = new ListScreen\Comment();

		foreach ( $this->get_taxonomies() as $taxonomy ) {
			$list_screens[] = new ListScreen\Taxonomy( $taxonomy );
		}

		$list_screens[] = new ListScreen\User();

		if ( is_multisite() ) {

			// Settings UI
			if ( $this->is_settings_screen() ) {

				// Main site
				if ( is_main_site() ) {
					$list_screens[] = new ListScreen\MSUser();
					$list_screens[] = new ListScreen\MSSite();
				}
			} else {

				// Table screen
				$list_screens[] = new ListScreen\MSUser();
				$list_screens[] = new ListScreen\MSSite();
			}
		}

		foreach ( $list_screens as $list_screen ) {
			$admin_columns->register_list_screen( $list_screen );
		}
	}

	/**
	 * Get a list of taxonomies supported by Admin Columns
	 * @since 1.0
	 * @return array List of taxonomies
	 */
	private function get_taxonomies() {
		$taxonomies = get_taxonomies( array( 'show_ui' => true ) );

		if ( isset( $taxonomies['post_format'] ) ) {
			unset( $taxonomies['post_format'] );
		}

		if ( isset( $taxonomies['link_category'] ) && ! get_option( 'link_manager_enabled' ) ) {
			unset( $taxonomies['link_category'] );
		}

		/**
		 * Filter the post types for which Admin Columns is active
		 * @since 2.0
		 *
		 * @param array $post_types List of active post type names
		 */
		return (array) apply_filters( 'acp/taxonomies', $taxonomies );
	}

}