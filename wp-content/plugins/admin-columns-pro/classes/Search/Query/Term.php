<?php

namespace ACP\Search\Query;

use ACP\Search\Query;
use WP_Term_Query;

class Term extends Query {

	public function register() {
		add_action( 'pre_get_terms', array( $this, 'callback_meta_query' ), 1 );
	}

	/**
	 * @param WP_Term_Query $query
	 *
	 * @return void
	 */
	public function callback_meta_query( WP_Term_Query $query ) {
		if ( ! $this->is_main_query( $query ) ) {
			return;
		}

		$meta_query = $this->get_meta_query();

		if ( ! $meta_query ) {
			return;
		}

		if ( $query->query_vars['meta_query'] ) {
			$meta_query[] = $query->query_vars['meta_query'];
		}

		$query->query_vars['meta_query'] = $meta_query;
	}

	/**
	 * @param WP_Term_Query $query
	 *
	 * @return bool
	 */
	private function is_main_query( WP_Term_Query $query ) {
		return ! isset( $query->query_vars['echo'] ) && 'all' === $query->query_vars['fields'];
	}

}