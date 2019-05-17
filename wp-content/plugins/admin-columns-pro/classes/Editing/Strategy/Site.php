<?php

namespace ACP\Editing\Strategy;

use ACP\Editing\Strategy;
use WP_Site;

class Site implements Strategy {

	/**
	 * @param  $site
	 *
	 * @return bool|int
	 */
	public function user_has_write_permission( $site ) {
		if ( ! current_user_can( 'manage_sites' ) ) {
			return false;
		}

		if ( ! $site instanceof WP_Site ) {
			$site = get_site( $site );

			if ( ! $site instanceof WP_Site ) {
				return false;
			}
		}

		return true;
	}

}