<?php

/**
 * Helper class for migrating a single legacy association between two posts into m2m.
 *
 * Not to be used outside the m2m API.
 *
 * @since m2m
 */
class Toolset_Relationship_Migration_Associations {

	/** @var Toolset_Relationship_Definition_Repository */
	private $definition_repository;

	/** @var Toolset_Element_Factory */
	private $element_factory;


	/**
	 * Toolset_Relationship_Migration_Associations constructor.
	 *
	 * @param Toolset_Relationship_Definition_Repository $definition_repository
	 * @param Toolset_Element_Factory|null $element_factory_di
	 */
	public function __construct(
		Toolset_Relationship_Definition_Repository $definition_repository,
		Toolset_Element_Factory $element_factory_di = null
	) {

		$this->definition_repository = $definition_repository;

		$this->element_factory = (
			null === $element_factory_di
				? new Toolset_Element_Factory()
				: $element_factory_di
		);
	}


	/**
	 * @param int $parent_id
	 * @param int $child_id
	 * @param int $relationship_slug
	 *
	 * @return Toolset_Result
	 */
	public function migrate_association( $parent_id, $child_id, $relationship_slug ) {

		$relationship_definition = $this->definition_repository->get_definition( $relationship_slug );

		if( null == $relationship_definition ) {
			return new Toolset_Result( false, sprintf( __( 'Relationship definition "%s" not found.', 'wpcf' ), $relationship_slug ) );
		}

		try {
			// We specifically require posts (element_factory->get_post()) and not translation sets (which we might
			// get when using element_factory->get_element()).
			//
			// Here, we create an association between two specific posts. When the WPML/m2m interop is fully
			// implemented, this will be enough to track associations between all translations of these posts.
			$parent = $this->element_factory->get_post( $parent_id );
			$child = $this->element_factory->get_post( $child_id );
		} catch( Exception $e ) {
			$display_message = sprintf(
				__( 'Unable to migrate an association from post #%d to #%d to a relationship "%s"', 'wpcf'),
				$parent_id,
				$child_id,
				$relationship_slug
			);
			return new Toolset_Result( $e, $display_message );
		}

		$potential_association_query_factory = new Toolset_Potential_Association_Query_Factory();
		$potential_association = $potential_association_query_factory->create(
			$relationship_definition,
			new Toolset_Relationship_Role_Child(),
			$parent
		);

		if( ! $potential_association->check_single_element( $child ) ) {
			return new Toolset_Result(
				false,
				sprintf(
					__( 'The association between posts %d and %d is not allowed (maybe it already exists).', 'wpcf' ),
					$parent->get_id(),
					$child->get_id()
				)
			);
		}

		try {
			$association = Toolset_Relationship_Database_Operations::create_association(
				$relationship_slug,
				$parent_id,
				$child_id,
				0 // no intermediary post
			);
		} catch( Exception $e ) {
			return new Toolset_Result( $e );
		}

		if( $association instanceof Toolset_Result ) {
			return new Toolset_Result(
				false,
				sprintf( "%s\n\t%s",
					__( 'Error while saving an association to database.', 'wpcf' ),
					print_r(
						array( 'parent_id' => $parent_id, 'child_id' => $child_id, 'relationship_slug' => $relationship_slug ),
						true
					)
				)
			);
		} else {
			return new Toolset_Result( true );
		}

	}

}
