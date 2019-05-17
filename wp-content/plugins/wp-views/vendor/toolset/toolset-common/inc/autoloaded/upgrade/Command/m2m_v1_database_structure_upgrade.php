<?php

/**
 * Transform the m2m table structures and data from the m2m-v1 beta release to be up-to-date with changes
 * implemented in toolsetcommon-305 (aimed for the m2m-v2 beta release).
 *
 * Obviously, the upgrade will run only if m2m is enabled at the time of the upgrade.
 *
 * Note: We're hardcoding a bunch of things here and the code is not really DRY. That's on purpose: This upgrade
 * command aims at a specific database structure and by using hardcoded values (e.g. for table names)
 * we're becoming immune even to unlikely changes like future renaming of tables or columns.
 *
 * Note: This will fail terribly if executed for the second time.
 *
 * @since 2.5.4
 */
class Toolset_Upgrade_Command_M2M_V1_Database_Structure_Upgrade implements IToolset_Upgrade_Command {


	/** @var wpdb */
	private $wpdb;


	/** @var Toolset_Relationship_Database_Operations|null */
	private $_database_operations;


	/**
	 * @var int Next free set_id value for the type set table. We know it starts at 1 because when this command
	 *     runs, the table doesn't exist yet.
	 */
	private $next_type_set_id = 1;


	/**
	 * Toolset_Upgrade_Command_M2M_V1_Database_Structure_Upgrade constructor.
	 *
	 * @param wpdb|null $wpdb_di
	 * @param Toolset_Relationship_Database_Operations|null $relationship_database_operations_di
	 */
	public function __construct(
		wpdb $wpdb_di = null,
		Toolset_Relationship_Database_Operations $relationship_database_operations_di = null
	) {
		if( null === $wpdb_di ) {
			global $wpdb;
			$this->wpdb = $wpdb;
		} else {
			$this->wpdb = $wpdb_di;
		}

		$this->_database_operations = $relationship_database_operations_di;
	}


	/**
	 * Run the command.
	 *
	 * @return Toolset_Result|Toolset_Result_Set
	 */
	public function run() {

		if( ! apply_filters( 'toolset_is_m2m_enabled', false ) ) {
			// Nothing to do here: The tables will be created as soon as m2m is activated for the first time.
			return new Toolset_Result( true );
		}

		if( $this->is_database_already_up_to_date() ) {
			// Nothing to do here: This happens when Types is activated on a fresh site: It creates
			// the tables according to the new structure but runs the upgrade routine at the same time.
			return new Toolset_Result( true );
		}

		error_log( 'The routine Toolset_Upgrade_Command_M2M_V1_Database_Structure_Upgrade::run() is starting' );

		$results = new Toolset_Result_Set();

		$this->create_post_type_set_table();
		$this->transform_post_type_sets();
		$this->change_relationship_type_column_datatypes();

		$this->add_extra_relationship_columns();
		$this->transform_extra_relationship_data();
		$this->drop_relationship_extra_column();

		$this->add_indexes_for_relationships_table();

		$this->add_relationship_id_column_for_associations();
		$this->transform_relationship_references_for_associations();
		$this->remove_relationship_slug_column_for_associations();

		$this->add_indexes_for_associations_table();

		error_log( 'The routine Toolset_Upgrade_Command_M2M_V1_Database_Structure_Upgrade::run() has finished' );

		return $results;
	}


	/**
	 * If the type set table exists, it means that we're dealing with a more recent database structure than this
	 * command aims to improve.
	 *
	 * @return bool
	 */
	private function is_database_already_up_to_date() {
		$table_name = $this->get_type_set_table_name();
		$query = $this->wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name );
		$type_set_table_already_exists = ( $this->wpdb->get_var( $query ) == $table_name );
		return $type_set_table_already_exists;
	}


	private function create_post_type_set_table() {

		// Note that dbDelta is very sensitive about details, almost nothing here is arbitrary.
		$query = "CREATE TABLE {$this->get_type_set_table_name()} (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    			set_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
    			type VARCHAR(20) NOT NULL DEFAULT '',
    			PRIMARY KEY  id (id),
			    KEY set_id (set_id),
			    KEY type (type)
			) {$this->wpdb->get_charset_collate()};";

		self::dbdelta( $query );
	}


	private function transform_post_type_sets() {
		$relationships = $this->wpdb->get_results(
			"SELECT id, parent_types, child_types FROM {$this->get_relationships_table_name()}"
		);

		foreach( $relationships as $relationship ) {
			$parent_types = $this->save_post_type_set( maybe_unserialize( $relationship->parent_types ) );
			$child_types = $this->save_post_type_set( maybe_unserialize( $relationship->child_types ) );

			$this->wpdb->update(
				$this->get_relationships_table_name(),
				array(
					'parent_types' => $parent_types,
					'child_types' => $child_types,
				),
				array(
					'id' => (int) $relationship->id,
				),
				'%s',
				'%d'
			);
		}
	}


	private function change_relationship_type_column_datatypes() {
		$this->wpdb->query(
			"ALTER TABLE {$this->get_relationships_table_name()}
			MODIFY parent_types bigint(20) UNSIGNED NOT NULL DEFAULT 0,
			MODIFY child_types bigint(20) UNSIGNED NOT NULL DEFAULT 0"
		);
	}


	/**
	 * Execute a dbDelta() query, ensuring that the function is available.
	 *
	 * @param string $query MySQL query.
	 *
	 * @return array dbDelta return value.
	 */
	private static function dbdelta( $query ) {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		return dbDelta( $query );
	}


	private function get_type_set_table_name() {
		return $this->wpdb->prefix . 'toolset_type_sets';
	}


	private function get_relationships_table_name() {
		return $this->wpdb->prefix . 'toolset_relationships';
	}


	private function get_associations_table_name() {
		return $this->wpdb->prefix . 'toolset_associations';
	}


	private function save_post_type_set( $post_types ) {
		$set_id = $this->next_type_set_id++;

		foreach( $post_types as $post_type ) {
			$this->wpdb->insert(
				$this->get_type_set_table_name(),
				array(
					'set_id' => $set_id,
					'type' => $post_type,
				),
				array( '%d', '%s' )
			);
		}

		return $set_id;
	}


	private function add_extra_relationship_columns() {
		$this->wpdb->query(
			"ALTER TABLE {$this->get_relationships_table_name()}
			CHANGE COLUMN display_name display_name_plural varchar(255) NOT NULL DEFAULT '',
			ADD COLUMN display_name_singular varchar(255) NOT NULL DEFAULT '',
			ADD COLUMN role_name_parent varchar(255) NOT NULL DEFAULT '',
    		ADD COLUMN role_name_child varchar(255) NOT NULL DEFAULT '',
    		ADD COLUMN role_name_intermediary varchar(255) NOT NULL DEFAULT '',
    		ADD COLUMN needs_legacy_support tinyint(1) NOT NULL DEFAULT 0,
    		ADD COLUMN is_active tinyint(1) NOT NULL DEFAULT 0"
		);
	}


	private function transform_extra_relationship_data() {
		$relationships = $this->wpdb->get_results(
			"SELECT id, extra FROM {$this->get_relationships_table_name()}"
		);

		foreach( $relationships as $relationship ) {
			$extra_data = maybe_unserialize( $relationship->extra );

			$this->wpdb->update(
				$this->get_relationships_table_name(),
				array(
					'role_name_parent' => 'parent',
					'role_name_child' => 'child',
					'role_name_intermediary' => 'association',
					'needs_legacy_support' => ( toolset_getarr( $extra_data, 'needs_legacy_support', 0 ) ? 1 : 0 ),
					'is_active' => ( toolset_getarr( $extra_data, 'is_active', 1 ) ? 1 : 0 ),
					'display_name_singular' => toolset_getarr( $extra_data, 'display_name_singular', '' ),
				),
				array( 'id' => (int) $relationship->id ),
				array( '%s', '%s', '%s', '%d', '%d', '%s' ),
				'%d'
			);
		}
	}


	private function drop_relationship_extra_column() {
		$this->wpdb->query(
			"ALTER TABLE {$this->get_relationships_table_name()}
			DROP COLUMN extra"
		);
	}


	private function add_indexes_for_relationships_table() {
		$this->wpdb->query(
			"ALTER TABLE {$this->get_relationships_table_name()}
			ADD INDEX is_active (is_active),
			ADD INDEX needs_legacy_support (needs_legacy_support),
			ADD INDEX parent_type (parent_domain, parent_types),
			ADD INDEX child_type (child_domain, child_types)"
		);
	}


	private function add_relationship_id_column_for_associations() {
		$this->wpdb->query(
			"ALTER TABLE {$this->get_associations_table_name()}
			ADD COLUMN relationship_id bigint(20) UNSIGNED NOT NULL"
		);
	}


	private function transform_relationship_references_for_associations() {
		$relationships = $this->wpdb->get_results(
			"SELECT id, slug FROM {$this->get_relationships_table_name()}"
		);

		foreach( $relationships as $relationship ) {
			$this->wpdb->update(
				$this->get_associations_table_name(),
				array( 'relationship_id' => $relationship->id ),
				array( 'relationship' => $relationship->slug ),
				'%d',
				'%s'
			);
		}
	}


	private function remove_relationship_slug_column_for_associations() {
		$this->wpdb->query(
			"ALTER TABLE {$this->get_associations_table_name()}
			DROP COLUMN relationship"
		);
	}


	private function add_indexes_for_associations_table() {
		$this->wpdb->query(
			"ALTER TABLE {$this->get_associations_table_name()}
			ADD INDEX relationship_id (relationship_id),
			ADD INDEX parent_id (parent_id, relationship_id),
			ADD INDEX child_id (child_id, relationship_id)"
		);
	}
}