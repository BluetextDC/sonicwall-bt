<?php

namespace ACP\Helper\Select\Entities;

use ACP\Helper\Select;
use ACP\Helper\Select\Value;
use WP_Comment_Query;

class Comment extends Select\Entities
	implements Select\Paginated {

	/**
	 * @var WP_Comment_Query
	 */
	protected $query;

	/**
	 * @param array $args
	 * @param Value $value
	 */
	public function __construct( array $args = array(), Value $value = null ) {
		if ( null === $value ) {
			$value = new Value\Comment();
		}

		$args = array_merge( array(
			'number'        => 30,
			'fields'        => 'ID',
			'orderby'       => 'comment_date_gmt',
			'paged'         => 1,
			'search'        => null,
			'no_found_rows' => false,
		), $args );

		$args['offset'] = ( $args['paged'] - 1 ) * $args['number'];

		$this->query = new WP_Comment_Query( $args );

		parent::__construct( $this->query->get_comments(), $value );
	}

	/**
	 * @inheritDoc
	 */
	public function get_total_pages() {
		return $this->query->max_num_pages;
	}

	/**
	 * @inheritDoc
	 */
	public function get_page() {
		return $this->query->query_vars['paged'];
	}

	/**
	 * @inheritDoc
	 */
	public function is_last_page() {
		return $this->get_total_pages() <= $this->get_page();
	}

}