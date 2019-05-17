<?php

/**
 * Manages the migration from legacy post relationships to m2m data structures.
 *
 * The install_m2m() method is to be called once on TCL upgrade.
 *
 * @since m2m
 */
class Toolset_Relationship_Migration {


	/** @var wpdb */
	private $wpdb;

	/** @var Toolset_Relationship_Database_Operations */
	private $database_operations;

	/** @var Toolset_Relationship_Multilingual_Mode */
	private $multilingual_mode_manager;

	/**
	 * This one needs to be initialized later because it will break when relationship tables don't exist yet.
	 *
	 * @var Toolset_Relationship_Definition_Repository|null
	 */
	private $_relationship_definition_repository;

	/** @var Toolset_Relationship_Migration_Associations|null */
	private $_association_migrator;

	/**
	 * Toolset_Relationship_Migration constructor.
	 *
	 * @param wpdb|null $wpdb_di
	 * @param Toolset_Relationship_Database_Operations|null $database_operations_di
	 * @param Toolset_Relationship_Multilingual_Mode|null $multilingual_mode_di
	 * @param Toolset_Relationship_Definition_Repository|null $relationship_definition_repository_di
	 * @param Toolset_Relationship_Migration_Associations|null $association_migrator_di
	 */
	public function __construct(
		wpdb $wpdb_di = null,
		Toolset_Relationship_Database_Operations $database_operations_di = null,
		Toolset_Relationship_Multilingual_Mode $multilingual_mode_di = null,
		Toolset_Relationship_Definition_Repository $relationship_definition_repository_di = null,
		Toolset_Relationship_Migration_Associations $association_migrator_di = null
	) {

		$this->wpdb = $wpdb_di;
		if( null === $this->wpdb ) {
			global $wpdb;
			$this->wpdb = $wpdb;
		}

		$this->database_operations = (
			null === $database_operations_di
				? new Toolset_Relationship_Database_Operations()
				: $database_operations_di
		);


		$this->multilingual_mode_manager = (
			null === $multilingual_mode_di
				? Toolset_Relationship_Multilingual_Mode::get_instance()
				: $multilingual_mode_di
		);

		$this->_relationship_definition_repository = $relationship_definition_repository_di;

		$this->_association_migrator = $association_migrator_di;
	}


	private function get_relationship_repository() {
		if( null === $this->_relationship_definition_repository ) {
			$this->_relationship_definition_repository = Toolset_Relationship_Definition_Repository::get_instance();
		}

		return $this->_relationship_definition_repository;
	}


	private function get_association_migrator() {
		if( null === $this->_association_migrator ) {
			$this->_association_migrator = new Toolset_Relationship_Migration_Associations( $this->get_relationship_repository() );
		}

		return $this->_association_migrator;
	}


	/**
	 * Update the database to support the native m2m implementation.
	 *
	 * Practically that means creating the wp_toolset_associations table.
	 *
	 * @since m2m
	 *
	 * TODO is it possible to reliably detect dbDelta failure?
	 */
	public function do_native_dbdelta() {
		return $this->database_operations->do_native_dbdelta();
	}


	/**
	 * If it's enabled by filter, drop all m2m-related tables.
	 *
	 * Useful mainly when debugging the migration process.
	 *
	 * @return Toolset_Result|Toolset_Result_Set
	 * @since m2m
	 */
	public function maybe_drop_m2m_tables() {

		/**
		 * toolset_drop_m2m_tables_before_migration
		 *
		 * If this filter returns true, all m2m-related tables will be dropped at the beginning of the
		 * migration process.
		 *
		 * @since m2m
		 */
		$drop_tables = apply_filters( 'toolset_drop_m2m_tables_before_migration', true );
		if( ! $drop_tables ) {
			return new Toolset_Result( true );
		}

		$m2m_tables = array(
			Toolset_Relationship_Table_Name::associations(),
			Toolset_Relationship_Table_Name::relationships(),

			// Obsolete table
			$this->wpdb->prefix . 'toolset_association_translations'
		);

		$results = new Toolset_Result_Set();
		foreach( $m2m_tables as $table_name ) {

			if( ! $this->database_operations->table_exists( $table_name ) ) {
				continue;
			}

			$query_result = $this->wpdb->query( 'DROP TABLE ' . $table_name );
			$table_dropped = ( 1 == $query_result );
			$results->add(
				$table_dropped,
				sprintf(
					$table_dropped ? __( 'Dropped table "%s".', 'toolset' ) : __( 'Error while dropping table "%s".', 'toolset'),
					$table_name
				)
			);
		}

		if( ! $results->has_results() ) {
			$results->add( true, __( 'No tables had to be dropped.', 'wpcf' ) );
		}

		// Disable transitional mode if it was set previously.
		$this->multilingual_mode_manager->set_mode( Toolset_Relationship_Multilingual_Mode::MODE_OFF );

		return $results;
	}


	/**
	 * Read legacy post relationship settings and convert them into one-to-many relationship definitions.
	 *
	 * Relationship slugs will be {$parent_post_type}_{$child_post_type}. Overwrites existing definitions.
	 *
	 * @return Toolset_Result_Set
	 * @since m2m
	 */
	public function migrate_relationship_definitions() {

		$relationships = $this->get_legacy_relationship_post_type_pairs();

		$results = new Toolset_Result_Set();

		// Handle empty input (report success)
		if( empty( $relationships ) ) {
			$results->add( new Toolset_Result( true, __( 'No relationships to migrate.', 'wpcf' ) ) );
		}

		foreach( $relationships as $post_type_pair ) {
			$parent_post_type = $post_type_pair['parent'];
			$child_post_type = $post_type_pair['child'];
			$relationship_slug = $post_type_pair['slug'];

			$result = $this->create_relationship_definition( $parent_post_type, $child_post_type, $relationship_slug );
			$results->add( $result );
		}

		// Now we need to persist everything
		$this->get_relationship_repository()->save_definitions();

		return $results;
	}


	/**
	 * Read the legacy relationships data stored in an option and transform it into array that can be
	 * processed more easily.
	 *
	 * @return array[] Each item is an array with 'parent' and 'child' post type, and also with a proposed 'slug'
	 *     for the relationship definition.
	 * @since m2m
	 */
	public function get_legacy_relationship_post_type_pairs() {

		// Get the legacy relationships definition.
		//
		// It looks somehow like this:
		//
		// array(
		//     “parent_type” => array(
		//         “child_type” => array( /* display options */ ),
		//          ...
		//     ),
		//     ...
		// )
		//
		$relationships = toolset_ensarr( get_option( 'wpcf_post_relationship', array() ) );

		$results = array();

		foreach ( $relationships as $parent_post_type => $relationships_per_post_type ) {

			$relationships_per_post_type = toolset_ensarr( $relationships_per_post_type );

			foreach ( $relationships_per_post_type as $child_post_type => $temporarily_ignored ) {
				$results[] = array(
					'parent' => $parent_post_type,
					'child' => $child_post_type,
					'slug' => $this->derive_relationship_slug( $parent_post_type, $child_post_type )
				);
			}
		}

		return $results;
	}


	/**
	 * Create an one-to-many relationship definitions for two provided post types.
	 *
	 * Doesn't persist anything.
	 *
	 * @param string $parent_post_type
	 * @param string $child_post_type
	 * @param string $relationship_slug
	 *
	 * @return Toolset_Result
	 * @since m2m
	 */
	private function create_relationship_definition( $parent_post_type, $child_post_type, $relationship_slug ) {

		$factory = $this->get_relationship_repository();

		// Overwrite the definition if it already exists.
		if( $factory->definition_exists( $relationship_slug ) ) {
			$factory->remove_definition( $relationship_slug );
		}

		try {
			$parent_type = Toolset_Relationship_Element_Type::build_for_post_type( $parent_post_type );
			$child_type = Toolset_Relationship_Element_Type::build_for_post_type( $child_post_type );

			$definition = $factory->create_definition( $relationship_slug, $parent_type, $child_type );

			// All legacy relationships are one-to-many
			$cardinality = new Toolset_Relationship_Cardinality( 1, Toolset_Relationship_Cardinality::INFINITY );
			$definition->set_cardinality( $cardinality );

			// All legacy relationships are distinct by definition.
			$definition->is_distinct( true );

			// All legacy relationships need extra backward compatibility support
			$definition->set_legacy_support_requirement( true );

		} catch ( Exception $e ) {
			return new Toolset_Result(
				false,
				sprintf(
					__( 'Could not create relationship definition because an error happened: %s', 'wpcf' ),
					$e->getMessage()
				)
			);
		}

		return new Toolset_Result( true );

	}


	/**
	 * Generate a relationship slug from two post type slugs.
	 *
	 * @param string $parent_post_type
	 * @param string $child_post_type
	 *
	 * @return string
	 * @since m2m
	 */
	private function derive_relationship_slug( $parent_post_type, $child_post_type ) {

		$relationship_slug = sprintf(
			'%s_%s',
			sanitize_title( $parent_post_type ),
			sanitize_title( $child_post_type )
		);

		return $relationship_slug;
	}


	/**
	 * Migrate post relationship data from the old Types post relationships to the native m2m.
	 *
	 * @since m2m
	 *
	 * @param int $offset
	 * @param int $limit
	 *
	 * @return Toolset_Result_Updated|Toolset_Result_Set
	 */
	public function migrate_associations( $offset, $limit ) {

		$associations_to_migrate = $this->get_associations_to_migrate( $offset, $limit );

		// Indicate success if there are no more items to process.
		if( empty( $associations_to_migrate ) ) {
			return new Toolset_Result_Updated( true, 0 );
		}
		
		$results = new Toolset_Result_Set();
		
		foreach( $associations_to_migrate as $association_to_migrate ) {
			$result = $this->get_association_migrator()->migrate_association(
				$association_to_migrate['parent_id'],
				$association_to_migrate['child_id'],
				$association_to_migrate['relationship_slug']
			);
			$results->add( $result );
		}
		
		if( $results->is_complete_success() ) {
			return new Toolset_Result_Updated( true, count( $associations_to_migrate ) );
		} else {
			return $results;
		}

	}


	/**
	 * Read a batch of legacy association data and prepare it for migration.
	 *
	 * @param int $offset
	 * @param int $limit
	 *
	 * @return array[] Each element has 'parent_id', 'child_id' and a 'relationship_slug'.
	 * @since m2m
	 */
	public function get_associations_to_migrate( $offset, $limit ) {
		$postmeta_records = $this->get_association_postmeta_records( $offset, $limit );

		$results = array();
		foreach( $postmeta_records as $association_postmeta ) {
			$matches = array();
			preg_match( '/_wpcf_belongs_(.*)_id/', $association_postmeta->relationship_meta_key, $matches );
			$parent_post_type = toolset_getarr( $matches, 1 );

			$results[] = array(
				'parent_id' => (int) $association_postmeta->parent_id,
				'child_id' => (int) $association_postmeta->child_id,
				'relationship_slug' => $this->derive_relationship_slug( $parent_post_type, $association_postmeta->post_type )
			);
		}

		return $results;
	}


	/**
	 * Retrieve postmeta records with legacy post relatioships.
	 *
	 * @param int $offset
	 * @param int $limit
	 *
	 * @return object[] Each result has these fields: parent_id, child_id, post_type, relationship_meta_key.
	 *     post_type is related to the child post, whereas relationship_meta_key is
	 *     a string "_wpcf_belongs_{$parent_post_type}_id".
	 *
	 * @since m2m
	 */
	private function get_association_postmeta_records( $offset, $limit ) {

		$query = $this->wpdb->prepare(
			"SELECT post.ID AS child_id, 
		  		postmeta.meta_key AS relationship_meta_key,
				postmeta.meta_value AS parent_id,
				post.post_type AS post_type
			FROM {$this->wpdb->postmeta} AS postmeta JOIN {$this->wpdb->posts} AS post ON (postmeta.post_id = post.ID)
			WHERE postmeta.meta_key LIKE %s
			LIMIT %d, %d",
			'\_wpcf\_belongs\_%\_id',
			$offset,
			$limit
		);

		return $this->wpdb->get_results( $query );
	}


	/**
	 * Final migration step.
	 *
	 * @since m2m
	 */
	public function finish() {

	    update_option( Toolset_Relationship_Controller::IS_M2M_ENABLED_OPTION, 'yes', true );

        // todo this needs a review
        // There is no need to update the translation view because we've just properly imported everything.
		//$wpml_interop = Toolset_Relationship_WPML_Interoperability::get_instance();
		//$wpml_interop->is_full_refresh_needed( false );
	}

}

