<?php

namespace ACP\Helper\Select\Formatter;

use ACP\Helper\Select\Entities;
use ACP\Helper\Select\Formatter;
use ACP\Helper\Select\Value;
use WP_Term;

class TermName extends Formatter {

	/**
	 * @var array
	 */
	private $taxonomies;

	public function __construct( Entities $entities, Value $value = null ) {
		if ( ! $value ) {
			$value = new Value\Taxonomy( Value\Taxonomy::ID );
		}

		parent::__construct( $entities, $value );
	}

	/**
	 * @return array
	 */
	private function get_taxonomies() {
		if ( null === $this->taxonomies ) {
			$this->taxonomies = get_taxonomies();
		}

		return $this->taxonomies;
	}

	protected function get_label_unique( $label, $entity ) {
		if ( $entity->parent ) {
			return get_term_by( 'id', $entity->parent, $entity->taxonomy )->name . ' > ' . $label;
		}

		return parent::get_label_unique( $label, $entity );
	}

	/**
	 * @param WP_Term $term
	 *
	 * @return bool
	 */
	private function is_term_post_format( $term ) {
		$slug = str_replace( 'post-format-', '', $term->slug );

		return 0 === strpos( $term->slug, 'post-format-' ) && in_array( $slug, get_post_format_slugs() );
	}

	/**
	 * @param WP_Term $term
	 *
	 * @return string
	 */
	protected function get_label( $term ) {
		// Remove corrupt post formats. There can be post format added to the
		// DB that are not officially registered. Those are skipped.
		if ( 'post_format' === $term->taxonomy && ! $this->is_term_post_format( $term ) ) {
			return '';
		}

		// Extra check if the taxonomy (still) exists
		if ( ! in_array( $term->taxonomy, $this->get_taxonomies() ) ) {
			return '';
		}

		$label = htmlspecialchars_decode( $term->name );

		if ( ! $label ) {
			$label = $term->term_id;
		}

		return $label;
	}

}