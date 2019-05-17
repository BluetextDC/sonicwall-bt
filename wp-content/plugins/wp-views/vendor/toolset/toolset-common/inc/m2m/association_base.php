<?php

/**
 * Represents a single m2m association between two elements.
 *
 * Encapsulates the intermediary post and exposes only the generic API for working with association fields.
 *
 * There are two implementations, one is translation-aware and one is not.
 * Both are to be instantiated exclusively through Toolset_Association_Repository.
 *
 * @since m2m
 */
abstract class Toolset_Association_Base implements IToolset_Association {


	/** @var Toolset_Relationship_Definition */
	private $relationship_definition;

	/** @var Toolset_Element[] Actual elements, loaded on demand. Use self::get_element() to obtain them. */
	protected $elements = array();

	/** @var int Translation group ID. */
	private $trid;

	/** @var null|IToolset_Post */
	protected $intermediary_post = null;



	/**
	 * Toolset_Association_Base constructor.
	 *
	 * Note that no checks about elements with respect to the relationship definition are being performed here.
	 * The caller needs to ensure everything is valid (domains, types, other conditions). This is handled well in the
	 * association factory.
	 *
	 * @param int $trid Association translation ID (acting as an unique identifier).
	 * @param Toolset_Relationship_Definition $relationship_definition
	 * @throws InvalidArgumentException
	 * @since m2m
	 */
	public function __construct( $trid, Toolset_Relationship_Definition $relationship_definition ) {

		if( ! Toolset_Utils::is_natural_numeric( $trid ) ) {
			throw new InvalidArgumentException();
		}

		$this->relationship_definition = $relationship_definition;

		$this->trid = (int) $trid;

	}


	/**
	 * @return Toolset_Relationship_Definition
	 */
	public function get_definition() { return $this->relationship_definition; }


	/**
	 * Get domain of selected association element.
	 *
	 * @param string $element_role
	 *
     * @return string Valid domain name as defined in Toolset_Field_Utils.
	 * @since m2m
	 */
	protected function get_element_domain( $element_role ) {
		$relationship_definition = $this->get_definition();
		$element_type = $relationship_definition->get_element_type( $element_role );
		return $element_type->get_domain();
	}


	/**
	 * Get an association element.
	 *
	 * Instantiates an element if it hasn't been done yet.
	 *
	 * @param string $element_role
	 * @return Toolset_Element
	 * @throws InvalidArgumentException
	 * @since m2m
	 */
	public abstract function get_element( $element_role );


	/**
	 * Check that the element role is valid.
	 *
	 * @param string $element_role
	 *
	 * @throws InvalidArgumentException
	 * @since m2m
	 * todo get rid of this, move to the enum
	 */
	public static function validate_element_role( $element_role ) {
		if( ! in_array( $element_role, Toolset_Relationship_Role::parent_child_role_names() ) ) {
			throw new InvalidArgumentException( 'Invalid element key.' );
		}
	}


	/**
	 * Shortcut to the relationship driver.
	 *
	 * @return Toolset_Relationship_Driver_Base
	 */
	public function get_driver() {
		$relationship_definition = $this->get_definition();
		return $relationship_definition->get_driver();
	}


	/**
	 * Get the unique identifier for the association.
	 *
	 * We can use a trid which is unique per translation group (and per association if WPML is not active).
	 * If there's another implementation of associations in the future, it needs to use a string,
	 * perhaps with some sort of a prefix.
	 *
	 * @return int|string
	 * @since m2m
	 */
	public function get_uid() {
		return $this->trid;
	}


	/**
	 * Get the translation group ID of the association.
	 *
	 * @return int Translation group ID or zero if not supported.
	 */
	public function get_trid() {
		return $this->trid;
	}


	/**
	 * Get the intermediary post if it exists.
	 *
	 * @return null|Toolset_Post
	 */
	protected abstract function get_intermediary_post();



	/**
	 * @inheritdoc
	 *
	 * This needs to be called (internally) before accessing the intermediary post object.
	 *
	 * @return bool
	 * @since m2m
	 */
	public function has_fields() {

		$intermediary_post = $this->get_intermediary_post();

		if ( null === $intermediary_post ) {
			return false;
		}

		return ( $this->intermediary_post->get_field_count() > 0 );
	}


	/**
	 * @inheritdoc
	 *
	 * @param string|Toolset_Field_Definition $field_source
	 * @return bool
	 */
	public function has_field( $field_source ) {
		if( ! $this->has_fields() ) {
			return false;
		}

		return $this->intermediary_post->has_field( $field_source );
	}


	/**
	 * @return bool
	 * @since m2m
	 */
	public function has_intermediary_post() {
		return ( null !== $this->get_intermediary_post() );
	}

}