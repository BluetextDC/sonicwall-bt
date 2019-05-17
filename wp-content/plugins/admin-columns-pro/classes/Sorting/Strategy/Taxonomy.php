<?php

namespace ACP\Sorting\Strategy;

use ACP;
use WP_Term_Query;

class Taxonomy extends ACP\Sorting\Strategy {

	/**
	 * @var WP_Term_Query
	 */
	private $term_query;

	public function manage_sorting() {
		add_action( 'pre_get_terms', array( $this, 'handle_sorting_request' ) );
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	public function get_results( array $args = array() ) {
		return $this->get_terms( $args );
	}

	/**
	 * @param array $args
	 *
	 * @return int[]
	 */
	protected function get_terms( array $args = array() ) {
		$defaults = array(
			'fields'     => 'ids',
			'taxonomy'   => $this->get_column()->get_taxonomy(),
			'hide_empty' => false,
		);

		$args = array_merge( $defaults, $args );

		$query = new WP_Term_Query( $args );

		return (array) $query->get_terms();
	}

	/**
	 * @return string
	 */
	public function get_order() {
		return $this->get_query_var( 'order' );
	}

	/**
	 * @param WP_Term_Query $term_query
	 */
	private function set_term_query( WP_Term_Query $term_query ) {
		$this->term_query = $term_query;
	}

	/**
	 * return boolean
	 */
	private function is_main_query() {
		if ( ! $this->get_query_var( 'orderby' ) || $this->get_query_var( 'echo' ) || 'all' !== $this->get_query_var( 'fields' ) ) {
			return false;
		}

		$list_screen = $this->get_column()->get_list_screen();

		if ( ! $list_screen instanceof ACP\ListScreen\Taxonomy ) {
			return false;
		}

		$taxonomies = $this->get_query_var( 'taxonomy' );

		if ( empty( $taxonomies ) || ! in_array( $list_screen->get_taxonomy(), $taxonomies ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @param WP_Term_Query $query
	 *
	 * @return void
	 */
	public function handle_sorting_request( WP_Term_Query $query ) {
		$this->set_term_query( $query );

		if ( ! $this->is_main_query() ) {
			return;
		}

		foreach ( $this->model->get_sorting_vars() as $key => $value ) {
			if ( $this->is_universal_id( $key ) ) {
				$key = 'include';
			}

			$query->query_vars[ $key ] = $value;
		}

		// pre-sorting done with an array
		$include = $query->query_vars['include'];

		if ( ! empty( $include ) ) {
			$query->query_vars['orderby'] = 'include';
		}
	}

	/**
	 * @param string $key
	 *
	 * @return null
	 */
	public function get_query_var( $key ) {
		if ( ! $this->term_query instanceof WP_Term_Query || ! isset( $this->term_query->query_vars[ $key ] ) ) {
			return null;
		}

		return $this->term_query->query_vars[ $key ];
	}

}