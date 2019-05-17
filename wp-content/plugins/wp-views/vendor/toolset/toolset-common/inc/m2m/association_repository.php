<?php

/**
 * Repository for associations.
 *
 * Outside m2m API, only this object should be used to obtain instances of the IToolset_Association.
 * Use it as a singleton in production code.
 *
 * @since m2m
 */
class Toolset_Association_Repository {

	private static $instance;

	/** @var Toolset_Relationship_Database_Operations|null */
	private $_database_operations;

	/** @var IToolset_Association[] */
	private $associations_by_uid = array();


	public static function get_instance() {
		if( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	public function __construct( Toolset_Relationship_Database_Operations $database_operations_di = null ) {
		$this->_database_operations = $database_operations_di;
	}


	private function __clone() { }


	/**
	 * Get an association by UID.
	 *
	 * Load it from database if needed.
	 *
	 * @param $association_uid
	 *
	 * @return null|IToolset_Association
	 */
	public function get( $association_uid ) {
		if( $this->in_cache( $association_uid ) ) {
			return $this->from_cache( $association_uid );
		}

		/**
		 * toolset_get_m2m_association_by_uid
		 *
		 * Allow for loading associations by UID by custom means.
		 *
		 * Note: To be enabled when actually needed.
		 *
		 * @param null $association Default value.
		 * @param int|string $association_uid UID of the association.
		 * @since m2m
		 */
		// $association = apply_filters( 'toolset_get_m2m_association_by_uid', null, $association_uid );
		// if( ! $association instanceof IToolset_Association ) {
		 	$association = $this->load_association_by_uid( $association_uid );
		// }

		$this->to_cache( $association_uid, $association );

		return $association;
	}


	private function in_cache( $association_uid ) {
		return array_key_exists( $association_uid, $this->associations_by_uid );
	}


	private function from_cache( $association_uid ) {
		return $this->associations_by_uid[ $association_uid ];
	}


	private function to_cache( $association_uid, $association ) {
		$this->associations_by_uid[ $association_uid ] = $association;
	}


	/**
	 * Load a native association from the database.
	 *
	 * @param int $association_uid Association UID.
	 *
	 * @return null|Toolset_Association The association instance or null if it couln't have been loaded.
	 *
	 * todo actually test this - maybe better use the query
	 */
	private function load_association_by_uid( $association_uid ) {
		global $wpdb;

		$associations_tn = Toolset_Relationship_Table_Name::associations();

		$query = $wpdb->prepare( "SELECT * FROM {$associations_tn} WHERE trid = %d", $association_uid );
		$row = $wpdb->get_row( $query );

		if ( ! $row ) {
			return null;
		}

		$relationship_definition = Toolset_Relationship_Definition_Repository::get_instance()
			->get_definition_by_row_id( $row->relationship_id );

		if ( null === $relationship_definition ) {
			return null;
		}

		try {
			$association = new Toolset_Association(
				$row->trid,
				$relationship_definition,
				array(
					Toolset_Relationship_Role::PARENT => $row->parent_id,
					Toolset_Relationship_Role::CHILD => $row->child_id
				),
				$row->intermediary_id
			);

		} catch( Exception $e ) {
			$association = null;
		}

		return $association;

	}


	/**
	 * Create an association instance from provided values.
	 *
	 * @param int|string|IToolset_Relationship_Definition $relationship_definition_source
	 * @param int|string $association_trid
	 * @param array $element_sources Elements indexed by role names - either Toolset_Element instances or ids (can be mixed).
	 *
	 * @return IToolset_Association
	 * @since m2m
	 */
	public function instantiate( $relationship_definition_source, $association_trid, $element_sources ) {

		if( $this->in_cache( $association_trid ) ) {
			return $this->from_cache( $association_trid );
		}

		$relationship_definition = Toolset_Relationship_Utils::get_relationship_definition( $relationship_definition_source );
		if ( ! $relationship_definition instanceof Toolset_Relationship_Definition ) {
			throw new InvalidArgumentException();
		}

		// todo Consider moving this part to the relationship driver.
		// todo     That would allow for adding other drivers in the future, and the instantiation
		// todo     and caching within this repository would work out of the box.
		if(
			Toolset_Relationship_Multilingual_Mode::is_on()
		    && $relationship_definition->is_translatable() ) {

			$association = new Toolset_Association_Translation_Set(
				$association_trid,
				$relationship_definition,
				$element_sources
			);

		} elseif ( Toolset_Relationship_Multilingual_Mode::is_transitional() ) {

			$association = new Toolset_Association_Transitional(
				$association_trid,
				$relationship_definition,
				$element_sources,
				toolset_getarr( $element_sources, Toolset_Relationship_Role::INTERMEDIARY, 0 )
			);

		} else {

			$association = new Toolset_Association(
				$association_trid,
				$relationship_definition,
				$element_sources,
				toolset_getarr( $element_sources, Toolset_Relationship_Role::INTERMEDIARY, 0 )
			);

		}

		$this->to_cache( $association->get_uid(), $association );

		return $association;
	}


	/**
	 * @return Toolset_Relationship_Database_Operations
	 */
	private function get_database_operations() {
		if( null === $this->_database_operations ) {
			$this->_database_operations = new Toolset_Relationship_Database_Operations();
		}

		return $this->_database_operations;
	}


	/**
	 * Delete all associations from given relationship.
	 *
	 * @param IToolset_Relationship_Definition $relationship_definition
	 *
	 * @return Toolset_Result_Updated
	 */
	public function remove_by_relationship( IToolset_Relationship_Definition $relationship_definition ) {

		foreach( $this->associations_by_uid as $association_uid => $association ) {
			if( $association->get_definition()->get_slug() === $relationship_definition->get_slug() ) {
				unset( $this->associations_by_uid[ $association_uid ] );
			}
		}

		/** @var Toolset_Relationship_Definition $relationship_definition */
		return $this->get_database_operations()->delete_associations_by_relationship( $relationship_definition->get_row_id() );
	}


	/**
	 * @param IToolset_Element $element
	 */
	public function delete_associations_involving_element( $element ) {

		$query_parent = new Toolset_Association_Query( array(
			Toolset_Association_Query::QUERY_PARENT_DOMAIN => $element->get_domain(),
			Toolset_Association_Query::QUERY_PARENT_ID => $element->get_id(),
			Toolset_Association_Query::OPTION_RETURN => Toolset_Association_Query::RETURN_ASSOCIATIONS
		) );

		$associations = $query_parent->get_results();

		$query_child = new Toolset_Association_Query( array(
			Toolset_Association_Query::QUERY_PARENT_DOMAIN => $element->get_domain(),
			Toolset_Association_Query::QUERY_PARENT_ID => $element->get_id(),
			Toolset_Association_Query::OPTION_RETURN => Toolset_Association_Query::RETURN_ASSOCIATIONS
		) );

		/** @var Toolset_Association[] $associations */
		$associations = array_merge( $associations, $query_child->get_results() );

		foreach( $associations as $association ) {
			$definition = $association->get_definition();
			$driver = $definition->get_driver();
			$driver->delete_association( $association );
		}

	}

}
