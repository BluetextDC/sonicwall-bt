<?php

/**
 * Represents a single, translation-unaware m2m association between two elements in the transitional
 * multilingual mode.
 *
 * The only difference is that in this mode, we get a string instead of a trid, and need to extract the
 * actual trid for the parent constructor, and use the string as an unique identifier.
 *
 * See Toolset_Association_Query::preprocess_results_in_transitional_mode() for more information.
 */
class Toolset_Association_Transitional extends Toolset_Association {


	private $ttrid;

	/**
	 * Unique identifier of the association.
	 *
	 * @return string
	 */
	public function get_uid() {
		return $this->ttrid;
	}


	/**
	 * Toolset_Association_Transitional constructor.
	 *
	 * @param string $ttrid Translation group ID or its string replacement.
	 * @param Toolset_Relationship_Definition $relationship_definition
	 * @param array $element_sources Associative array with both element keys. Each item can be either an ID
	 *     or a matching Toolset_Element instance.
	 * @param int|Toolset_Post $intermediary_source Intermediary post with association fields or its ID. If a
	 *    Toolset_Post instance is provided, it must have the type matching with the relationship definition.
	 *
	 * @since m2m
	 */
	public function __construct(
		$ttrid, Toolset_Relationship_Definition $relationship_definition, $element_sources, $intermediary_source
	) {

		if( ! Toolset_Relationship_Multilingual_Mode::is_transitional() ) {
			throw new RuntimeException( 'Tried to instantiate Toolset_Association_Transitional in a non-transitional mode.' );
		}

		$ttrid_parts = explode( '\\', $ttrid );
		if( count( $ttrid_parts ) !== 4 ) {
			throw new InvalidArgumentException( 'Invalid transitional trid provided.' );
		}

		$this->ttrid = $ttrid;

		// In the extremely rare case someone explictly asks for a trid, they will get it (although it probably
		// won't be unique).
		$trid = (int) $ttrid_parts[2];

		parent::__construct( $trid, $relationship_definition, $element_sources, $intermediary_source );
	}

}