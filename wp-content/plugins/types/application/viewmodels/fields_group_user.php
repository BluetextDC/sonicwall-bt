<?php

/**
 * Field User Group, for the purposes of the Custom Fields page, Users tab.
 *
 * @since 2.3
 */
class Types_Viewmodel_Field_Group_User extends Types_Viewmodel_Field_Group {

	/**
	 * Page name for editing
	 *
	 * @since 2.3
	 * @var string
	 */
	const EDIT_PAGE_SLUG = 'wpcf-edit-usermeta';


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
	const POST_TYPE = 'wp-types-user-group';


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
		return Toolset_Field_Definition_Factory_Term::get_instance();
	}



	/**
	 * Gets 'Available for'
	 *
	 * @return string
	 * @since 2.3
	 */
	public function get_available_for() {
		$show_for = get_post_meta( $this->get_id(), '_wp_types_group_showfor', true );
		if ( empty( $show_for ) || 'all' === $show_for ) {
			$show_for = array();
		} else {
			$show_for = explode( ',', trim( $show_for, ',' ) );
		}

		if ( function_exists( 'wpcf_access_register_caps' ) ) {
			$show_for = __( 'This groups visibility is also controlled by the Access plugin.', 'wpcf' );
		} else {
			$show_for = ( 0 === count( $show_for ) ) ?
				__( 'Displayed for all users roles', 'wpcf' ) :
				ucwords( implode( $show_for, ', ' ) );
		}
		return $show_for;
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

		$json_data['availableFor'] = $this->get_available_for();

		return $json_data;
	}
}
