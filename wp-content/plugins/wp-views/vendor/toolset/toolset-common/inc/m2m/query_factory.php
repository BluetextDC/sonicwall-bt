<?php

/**
 * Factory for instantiating query classes.
 *
 * Should be extendended for association query and all others within the m2m project.
 *
 * @since m2m
 */
class Toolset_Relationship_Query_Factory {

	/**
	 * @param $args
	 *
	 * @return Toolset_Relationship_Query
	 * @deprecated
	 */
	public function relationships( $args ) {
		return new Toolset_Relationship_Query( $args );
	}


	/**
	 * @return Toolset_Relationship_Query_V2
	 *
	 * @return Toolset_Relationship_Query_V2
	 */
	public function relationships_v2() {
		return new Toolset_Relationship_Query_V2();
	}


	/**
	 * @param IToolset_Relationship_Definition $relationship
	 * @param IToolset_Relationship_Role_Parent_Child $target_role Target role of the relationships (future role of
	 *     the posts that are being queried)
	 * @param int $for_element_id ID of the element to check against.
	 * @param Toolset_Relationship_Table_Name|null $table_names_di
	 * @param wpdb|null $wpdb_di
	 *
	 * @return Toolset_Relationship_Distinct_Post_Query
	 */
	public function distinct_relationship_posts(
		IToolset_Relationship_Definition $relationship,
		IToolset_Relationship_Role_Parent_Child $target_role,
		$for_element_id,
		Toolset_Relationship_Table_Name $table_names_di = null,
		wpdb $wpdb_di = null
	) {
		return new Toolset_Relationship_Distinct_Post_Query(
			$relationship,
			$target_role,
			$for_element_id,
			$table_names_di,
			$wpdb_di
		);
	}


	/**
	 * @param array $args Query arguments.
	 *
	 * @return WP_Query
	 */
	public function wp_query( $args ) {
		return new WP_Query( $args );
	}


	/**
	 * @param $args
	 *
	 * @return Toolset_Association_Query
	 */
	public function associations( $args ) {
		return new Toolset_Association_Query( $args );
	}

}
