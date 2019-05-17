<?php

/**
 * Field Term Group, for the purposes of the Custom Fields page, Terms tab.
 *
 * @since 2.3
 */
class Types_Viewmodel_Field_Group_Term extends Types_Viewmodel_Field_Group {

	/**
	 * Page name for editing
	 *
	 * @since 2.3
	 * @var string
	 */
	const EDIT_PAGE_SLUG = 'wpcf-termmeta-edit';


	/**
	 * Page name for deleting
	 *
	 * @since 2.3
	 * @var string
	 */
	const DELETE_PAGE_SLUG = 'delete_group';


	/**
	 * Post type
	 *
	 * @since 2.3
	 * @var string
	 */
	const POST_TYPE = 'wp-types-term-group';


	/**
	 * Constructor
	 *
	 * @param WP_Post $field_group_post Post object representing a post field group.
	 * @throws InvalidArgumentException Incorrect post type.
	 */
	public function __construct( $field_group_post ) {
		parent::__construct( $field_group_post );
		if ( self::POST_TYPE !== $field_group_post->post_type ) {
			throw new InvalidArgumentException( 'incorrect post type' );
		}
	}


	/**
	 * Returns the field definition factory for this domain
	 *
	 * @return Toolset_Field_Definition_Factory Field definition factory of the correct type.
	 * @since 2.3
	 */
	protected function get_field_definition_factory() {
		return Toolset_Field_Definition_Factory_User::get_instance();
	}


	/**
	 * Returns the post type
	 *
	 * @return string
	 * @since 2.3
	 */
	public function get_post_type() {
		return Toolset_Field_Group_Term::POST_TYPE;
	}


	/**
	 * Get taxonomies that are associated with this field group.
	 *
	 * @return string[] Taxonomy slugs. Empty array means that this group should be displayed with all taxonomies.
	 * @since 2.3
	 */
	public function get_associated_taxonomies() {
		$postmeta = get_post_meta( $this->get_id(), Toolset_Field_Group_Term::POSTMETA_ASSOCIATED_TAXONOMY, false );

		// Survive empty or whitespace taxonomy slugs (skip them). They are invalid values but
		// if we have only them, we need to return an empty array to keep the group displayed everywhere.
		foreach ( $postmeta as $index => $taxonomy_slug ) {
			$taxonomy_slug = trim( $taxonomy_slug );
			if ( empty( $taxonomy_slug ) ) {
				unset( $postmeta[ $index ] );
			}
		}

		$postmeta = array_filter( $postmeta, array( $this, 'is_not_blank' ) );
		return toolset_ensarr( $postmeta );
	}


	/**
	 * Filter if is not blank
	 *
	 * @param string $value The text to filter.
	 * @return boolean
	 * @since 2.3
	 */
	private function is_not_blank( $value ) {
		$value = trim( $value );
		return ( ! empty( $value ) );
	}


	/**
	 * Converts to JSON
	 *
	 * @return array JSON format.
	 */
	public function to_json() {
		$json = parent::to_json();
		return $this->add_extra_json_data( $json );
	}


	/**
	 * Add extra JSON data depending on the group
	 *
	 * @param array $json_data JSON data.
	 * @return Array
	 * @since 2.3
	 */
	public function add_extra_json_data( $json_data ) {
		$json_data = parent::add_extra_json_data( $json_data );

		$taxonomies = $this->get_associated_taxonomies();
		if ( empty( $taxonomies ) ) {
			$json_data['taxonomies'] = __( 'Any', 'wpcf' );
		} else {
			$taxonomy_labels = array();
			foreach ( $taxonomies as $taxonomy_slug ) {
				$taxonomy_labels[] = Types_Utils::taxonomy_slug_to_label( $taxonomy_slug );
			}
			$json_data['taxonomies'] = implode( ', ', $taxonomy_labels );
		}

		return $json_data;
	}
}
