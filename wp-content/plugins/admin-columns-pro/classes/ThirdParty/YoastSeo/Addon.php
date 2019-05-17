<?php

namespace ACP\ThirdParty\YoastSeo;

use AC;

final class Addon
	implements AC\Registrable {

	public function register() {
		add_action( 'ac/column_types', array( $this, 'set_columns' ) );
		add_action( 'ac/column_groups', array( $this, 'set_groups' ) );
		add_action( 'ac/admin_footer', array( $this, 'fix_yoast_heading_tooltips' ) );
		add_action( 'ac/table/list_screen', array( $this, 'hide_yoast_filters' ) );
	}

	/**
	 * @param AC\ListScreen $list_screen
	 */
	public function hide_yoast_filters( $list_screen ) {
		global $wpseo_meta_columns;

		if ( ! $this->is_active() && ! $wpseo_meta_columns ) {
			return;
		}

		if ( ! $list_screen->get_column_by_name( 'wpseo-score' ) ) {
			remove_action( 'restrict_manage_posts', array( $wpseo_meta_columns, 'posts_filter_dropdown' ) );
		}

		if ( ! $list_screen->get_column_by_name( 'wpseo-score-readability' ) ) {
			remove_action( 'restrict_manage_posts', array( $wpseo_meta_columns, 'posts_filter_dropdown_readability' ) );
		}

	}

	/**
	 * @param AC\ListScreen $list_screen
	 *
	 * @throws \ReflectionException
	 */
	public function set_columns( $list_screen ) {
		if ( $this->is_active() ) {
			$list_screen->register_column_types_from_dir( __NAMESPACE__ . '\Column' );
		}
	}

	/**
	 * @param AC\Groups $groups
	 */
	public function set_groups( $groups ) {
		$groups->register_group( 'yoast-seo', __( 'Yoast SEO', 'wordpress-seo' ), 25 );
	}

	/**
	 * @return bool
	 */
	private function is_active() {
		return defined( 'WPSEO_VERSION' );
	}

	public function fix_yoast_heading_tooltips() {
		if ( ! $this->is_active() ) {
			return;
		}

		?>
		<style>
			.wp-list-table th > a.yoast-tooltip::before,
			.wp-list-table th > a.yoast-tooltip::after {
				display: none !important;
			}
		</style>
		<?php
	}

}