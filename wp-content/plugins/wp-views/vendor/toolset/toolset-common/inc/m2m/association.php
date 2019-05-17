<?php

/**
 * Translation-unaware m2m association between two elements.
 *
 * This can be used only when the multilingual mode is off/transitional
 *
 * Not to be used directly outside of the m2m API.
 *
 * @since m2m
 */
class Toolset_Association extends Toolset_Association_Base {


	/** @var int[] IDs of elements, always complete. */
	private $element_ids = array();

	/** @var int */
	private $intermediary_id;


	/**
	 * Toolset_Association constructor.
	 *
	 * See superclass for parameter description.
	 *
	 * It is assumed that the relationship definition uses the Toolset_Relationship_Driver driver.
	 *
	 * @param int $trid Translation group ID.
	 * @param Toolset_Relationship_Definition $relationship_definition
	 * @param array $element_sources Associative array with both element keys. Each item can be either an ID
	 *     or a matching Toolset_Element instance.
	 * @param int|Toolset_Post $intermediary_source Intermediary post with association fields or its ID. If a
	 *    Toolset_Post instance is provided, it must have the type matching with the relationship definition.
	 *
	 * @since m2m
	 */
	public function __construct(
		$trid, Toolset_Relationship_Definition $relationship_definition, $element_sources, $intermediary_source
	) {

		parent::__construct( $trid, $relationship_definition );

		foreach( Toolset_Relationship_Role::parent_child_role_names() as $element_role ) {
			$element_source = toolset_getarr( $element_sources, $element_role, null );

			if( is_array( $element_source ) && 1 === count( $element_source ) ) {
				$element_source = array_pop( $element_source );
			}

			if( $element_source instanceof Toolset_Element ) {
				$this->element_ids[ $element_role ] = $element_source->get_id();
				$this->elements[ $element_role ] = $element_source;
			} elseif( Toolset_Utils::is_natural_numeric( $element_source ) ) {
				$this->element_ids[ $element_role ] = $element_source;
			} else {
				throw new InvalidArgumentException( 'Invalid or missing element source.' );
			}
		}

		if( is_array( $intermediary_source ) && 1 === count( $intermediary_source ) ) {
			$intermediary_source = array_pop( $intermediary_source );
		}

		// Intermediary posts needs special care, as always.
		if( Toolset_Utils::is_nonnegative_integer( $intermediary_source ) ) {
			$this->intermediary_id = (int) $intermediary_source;
		} elseif( $intermediary_source instanceof Toolset_Post ) {
			/** @var Toolset_Relationship_Driver $driver */
			$driver = $this->get_definition()->get_driver();
			if( $intermediary_source->get_type() != $driver->get_intermediary_post_type() ) {
				throw new InvalidArgumentException( 'Invalid post type of the intermediary post.');
			}

			$this->intermediary_id = $intermediary_source->get_id();
			$this->intermediary_post = $intermediary_source;
		} else {
			throw new InvalidArgumentException( 'Invalid intermediary post source.' );
		}
	}


	/**
	 * Get an ID of an element in the associaton.
	 *
	 * @param string $element_role Must be a valid role.
	 *
	 * @return int
	 * @since m2m
	 */
	protected function get_element_id( $element_role ) {
		return $this->element_ids[ $element_role ];
	}


	/**
	 * Get an association element.
	 *
	 * Instantiates an element from its ID if that hasn't been done yet.
	 *
	 * @param string $element_role
	 * @return Toolset_Element
	 * @throws InvalidArgumentException
	 * @since m2m
	 */
	public function get_element( $element_role ) {
		self::validate_element_role( $element_role );
		if( ! array_key_exists( $element_role, $this->elements ) ) {
			$this->elements[ $element_role ] = Toolset_Element::get_instance(
				$this->get_element_domain( $element_role ),
				$this->get_element_id( $element_role )
			);
		}

		return $this->elements[ $element_role ];
	}


	/**
	 * @inheritdoc
	 * @return null|Toolset_Post
	 */
	protected function get_intermediary_post() {
		if( 0 === $this->intermediary_id ) {
			return null;
		}

		if( null === $this->intermediary_post ) {
			try {
				$this->intermediary_post = Toolset_Element::get_instance( Toolset_Field_Utils::DOMAIN_POSTS, $this->intermediary_id );
			} catch( Exception $e ) {
				// We couldn't load the post, it probably doesn't exist. Reset the ID to avoid checking again.
				$this->intermediary_id = 0;
			}
		}

		return $this->intermediary_post;
	}


	/**
	 * @inheritdoc
	 * @return bool|Toolset_Field_Instance[]
	 */
	public function get_fields() {
		if( ! $this->has_fields() ) {
			return false;
		}

		return $this->intermediary_post->get_fields();
	}


	/**
	 * @inheritdoc
	 * @param string|Toolset_Field_Definition $field_source
	 * @return bool|Toolset_Field_Instance
	 */
	public function get_field( $field_source ) {
		if( ! $this->has_fields() ) {
			return false;
		}

		return $this->intermediary_post->get_field( $field_source );
	}


	/**
	 * Get the ID of the intermediary post with association fields.
	 *
	 * Required for the [types] shortcode, but use with consideration.
	 *
	 * @return int Post ID or zero if no post exists.
	 * @since m2m
	 */
	public function get_intermediary_id() {
		return $this->intermediary_id;
	}


	/**
	 * @return bool
	 * @since m2m
	 */
	public function has_intermediary_post() {
		return ( 0 != $this->get_intermediary_id() );
	}



}