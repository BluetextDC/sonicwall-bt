<?php

/**
 * Field Post Group, for the purposes of the Custom Fields page, Posts tab.
 *
 * @since 2.3
 */
class Types_Viewmodel_Field_Group_Post extends Types_Viewmodel_Field_Group {

	/**
	 * Page name for editing
	 *
	 * @since 2.3
	 * @var string
	 */
	const EDIT_PAGE_SLUG = 'wpcf-edit';


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
	const POST_TYPE = 'wp-types-group';


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
		return Toolset_Field_Definition_Factory_Post::get_instance();
	}



	/**
	 * Get taxonomies related to the group
	 *
	 * @return string
	 * @since 2.3
	 */
	private function get_associated_taxonomies() {
		global $wp_taxonomies;
		$taxonomies = wpcf_admin_get_taxonomies_by_group( $this->get_id() );
		$data_taxonomies = '';
		if ( empty( $taxonomies ) ) {
			$data_taxonomies = __( 'None', 'wpcf' );
		} else {
			foreach ( $taxonomies as $taxonomy => $terms ) {
				$data_taxonomies .= isset( $wp_taxonomies[ $taxonomy ]->labels->singular_name )
					? '<em>' . $wp_taxonomies[ $taxonomy ]->labels->singular_name . '</em>: '
					: '<em>' . $taxonomy . '</em>: ';
				$terms_output = array();
				foreach ( $terms as $term_id => $term ) {
					$terms_output[] = $term['name'];
				}
				$data_taxonomies .= implode( ', ', $terms_output ) . '<br />';
			}
		}
		return $data_taxonomies;
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

		// Post types.
		global $wp_post_types;
		$post_types = wpcf_admin_get_post_types_by_group( $json_data['id'] );
		$supports = array();
		if ( ! empty( $post_types ) ) {
			foreach ( $post_types as $key => $post_type_slug ) {
				if ( isset( $wp_post_types[ $post_type_slug ]->labels->singular_name ) ) {
					$supports[] = $wp_post_types[ $post_type_slug ]->labels->singular_name;
				} else {
					$supports[] = $post_type_slug;
				}
			}
		}

		$json_data['postTypes'] = empty( $post_types ) ? __( 'All post types', 'wpcf' ) : implode( ', ', $supports );
		$json_data['taxonomies'] = $this->get_associated_taxonomies();

		return $json_data;
	}
}
