<?php

namespace ACP\Helper\Select\Entities;

use ACP\Helper\Select;
use ACP\Helper\Select\Value;
use WP_Term_Query;

class Taxonomy extends Select\Entities
	implements Select\Paginated {

	/**
	 * @var WP_Term_Query
	 */
	protected $query;

	/** @var array */
	private $args;

	/**
	 * @param array $args
	 * @param Value $value
	 */
	public function __construct( array $args = array(), Value $value = null ) {
		if ( null === $value ) {
			$value = new Value\Taxonomy();
		}

		$args = array_merge( array(
			'page'       => 1,
			'number'     => 30,
			'search'     => '',
			'hide_empty' => 0,
			'taxonomy'   => null,
		), $args );

		// calculate offset
		$args['offset'] = ( $args['page'] - 1 ) * $args['number'];

		$this->args = $args;

		$this->query = new WP_Term_Query( $args );

		parent::__construct( $this->query->get_terms(), $value );
	}

	/**
	 * @inheritDoc
	 */
	public function get_total_pages() {
		$taxonomy = $this->query->query_vars['taxonomy'][0];

		return absint( ceil( wp_count_terms( $taxonomy, $this->query->query_vars ) / $this->query->query_vars['number'] ) );
	}

	/**
	 * @inheritDoc
	 */
	public function get_page() {
		return $this->query->query_vars['page'];
	}

	/**
	 * @inheritDoc
	 */
	public function is_last_page() {
		return $this->get_total_pages() <= $this->get_page();
	}

}