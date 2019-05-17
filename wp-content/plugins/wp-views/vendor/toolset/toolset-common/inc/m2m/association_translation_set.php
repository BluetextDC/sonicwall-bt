<?php

/**
 * Represents a translation set of an association between posts.
 *
 * Benefits:
 *     - allows for specifying the preferred language in the rare cases it might be needed
 *     - allows for storing element sources (IDs) with the complete language information but
 *       without instantiating the elements immediately - that improves performance when
 *       instantiating and prevents from a WPML interaction/database queries in the future
 *
 * You should never need to use this class directly outside of the m2m API.
 *
 * @since m2m
 */
class Toolset_Association_Translation_Set extends Toolset_Association_Base {

	/**
	 * @var array For each relationship role, there is an element source (something that will be
	 *     accepted by Toolset_Element::get_instance()).
	 */
	private $element_sources = array();


	/**
	 * Toolset_Association_Translation_Set constructor.
	 *
	 * @param int $trid
	 * @param Toolset_Relationship_Definition $relationship_definition
	 * @param array $element_sources
	 */
	public function __construct(
		$trid, Toolset_Relationship_Definition $relationship_definition, $element_sources
	) {

		if ( ! Toolset_Relationship_Multilingual_Mode::is_on() ) {
			throw new RuntimeException( 'Attempted to use an association translation set while WPML was inactive' );
		}

		parent::__construct( $trid, $relationship_definition );

		foreach( Toolset_Relationship_Role::all_role_names() as $role ) {
			$this->element_sources[ $role ] = toolset_getarr( $element_sources, $role, null );
		}
	}


	/**
	 * Get an association element.
	 *
	 * Instantiates an element if it hasn't been done yet.
	 *
	 * @param string $element_role
	 *
	 * @return Toolset_Element|null
	 * @throws InvalidArgumentException
	 * @since m2m
	 */
	public function get_element( $element_role ) {

		Toolset_Relationship_Role::validate( $element_role );

		if( ! array_key_exists( $element_role, $this->elements ) ) {
			$element_source = toolset_getarr( $this->element_sources, $element_role, null );

			try {
				$this->elements[ $element_role ] = Toolset_Element::get_instance(
					$this->get_element_domain( $element_role ),
					$element_source
				);
			} catch( Exception $e ) {
				$this->elements[ $element_role ] = null;
			}
		}

		return $this->elements[ $element_role ];
	}


	/**
	 * @inheritdoc
	 * @return null|Toolset_Post|Toolset_Post_Translation_Set
	 */
	protected function get_intermediary_post() {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->get_element( Toolset_Relationship_Role::INTERMEDIARY );
	}


	/**
	 * Determine whether the intermediary post exists and supports translations.
	 *
	 * @return bool
	 */
	private function is_intermediary_post_translation_set() {
		return ( $this->has_fields() && $this->get_intermediary_post() instanceof Toolset_Post_Translation_Set );
	}


	/**
	 * @inheritdoc
	 *
	 * @return bool|Toolset_Field_Instance[]
	 */
	public function get_fields( $language_code = null ) {
		if( ! $this->has_fields() ) {
			return false;
		}

		if( $this->is_intermediary_post_translation_set() ) {
			return $this->get_intermediary_post()->get_fields( $language_code );
		}

		return $this->get_intermediary_post()->get_fields();
	}


	/**
	 * @inheritdoc
	 * @param string|Toolset_Field_Definition $field_source
	 * @return bool|Toolset_Field_Instance
	 */
	public function get_field( $field_source, $language_code = null ) {
		if( ! $this->has_fields() ) {
			return false;
		}

		if( $this->is_intermediary_post_translation_set() ) {
			return $this->get_intermediary_post()->get_field( $field_source, $language_code );
		}

		return $this->get_intermediary_post()->get_field( $field_source );
	}

}