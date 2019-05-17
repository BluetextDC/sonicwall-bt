<?php

/**
 * Represents an association between two elements.
 *
 * @since m2m
 */
interface IToolset_Association {


	/**
	 * Unique identifier of the association.
	 *
	 * Depending on the implementation, this may be an association row ID, trid or anything else.
	 * The only guarantee is that each association's UID is unique.
	 *
	 * @return int|string
	 */
	public function get_uid();


	/**
	 * @return Toolset_Relationship_Definition
	 */
	public function get_definition();


	/**
	 * Tell if the association has custom fields.
	 *
	 * Note that this value is based on field definitions, not on the actual values in the database.
	 *
	 * @return bool
	 */
	public function has_fields();


	/**
	 * Check if the association has particular custom field.
	 *
	 * Note that this value is based on field definitions, not on the actual values in the database.
	 *
	 * @param string|Toolset_Field_Definition $field_source Field definition or slug.
	 *
	 * @return bool
	 * @since m2m
	 */
	public function has_field( $field_source );


	/**
	 * Get all association field instances.
	 *
	 * @return Toolset_Field_Instance[]
	 * @since m2m
	 */
	public function get_fields();


	/**
	 * Get a particular association field instance.
	 *
	 * @param string|Toolset_Field_Definition $field_source Field definition or slug.
	 *
	 * @return Toolset_Field_Instance
	 * @throws InvalidArgumentException
	 */
	public function get_field( $field_source );


	/**
	 * Get an association element.
	 *
	 * Instantiates an element from its ID if that hasn't been done yet.
	 *
	 * @param string $element_role
	 *
	 * @return Toolset_Element
	 * @throws InvalidArgumentException
	 * @since m2m
	 */
	public function get_element( $element_role );


	/**
	 * Check that the element role is valid.
	 *
	 * @param string $element_role
	 *
	 * @throws InvalidArgumentException
	 * @since m2m
	 */
	public static function validate_element_role( $element_role );


	/**
	 * Shortcut to the relationship driver.
	 *
	 * @return Toolset_Relationship_Driver_Base
	 */
	public function get_driver();

}