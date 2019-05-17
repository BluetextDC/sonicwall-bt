<?php

/**
 * A class for querying associations and associated elements.
 *
 * Arguments:
 *     todo document
 *
 * Usage:
 *
 *     $query = new Toolset_Association_Query( $args );
 *     $results = $query->get_results();
 *
 * Notes:
 *
 *   - For now, it supports only the native associations (they're the only ones we have).
 *   - If you need to query by some parameters that are not supported, either create a feature request about it or
 *     submit a merge request rather than going around the query and touching the database directly.
 *
 *
 * @since m2m
 */
class Toolset_Association_Query extends Toolset_Relationship_Query_Base {

	/** @var string One of the RETURN_* constants determining what kind of output should be provided. */
	private $return;

	/** @var bool */
	protected $dont_count_found_rows;

	const OPTION_USE_CACHED_RESULTS = 'use_cached_results';
	const OPTION_CACHE_RESULTS = 'cache_results';
	const OPTION_RETURN = 'return';
	const OPTION_DONT_COUNT_FOUND_ROWS = 'no_found_rows';

	const QUERY_OFFSET = 'offset';
	const QUERY_LIMIT = 'limit';
	const QUERY_SELECT_FIELDS = 'select_fields';
	const QUERY_RELATIONSHIP_SLUG = 'relationship_slug';
	const QUERY_RELATIONSHIP_ID = 'relationship_id';
	const QUERY_PARENT_ID = 'parent_id';
	const QUERY_CHILD_ID = 'child_id';
	const QUERY_HAS_FIELDS = 'has_fields';
	const QUERY_PARENT_DOMAIN = 'parent_domain';
	const QUERY_PARENT_QUERY = 'parent_query';
	const QUERY_CHILD_DOMAIN = 'child_domain';
	const QUERY_CHILD_QUERY = 'child_query';
	const QUERY_LANGUAGE = 'language';
	const QUERY_HAS_TRASHED_POSTS = 'has_trashed_posts';

	const RETURN_ASSOCIATION_IDS = 'association_ids';
	const RETURN_ASSOCIATIONS = 'associations';
	const RETURN_PARENT_IDS = 'parent_ids';
	const RETURN_CHILD_IDS = 'child_ids';
	const RETURN_PARENTS = 'parents';
	const RETURN_CHILDREN = 'children';

	const LANGUAGE_ALL = 'all';

	const GROUP_CONCAT_SEPARATOR = ',';


	/**
	 * Parse query arguments, store them sanitized as options or in the $query_vars array.
	 *
	 * @param array $query
	 */
	protected function parse_query( $query ) {

		$this->use_cached_results = (bool) toolset_getarr( $query, self::OPTION_USE_CACHED_RESULTS, true );
		$this->cache_results = (bool) toolset_getarr( $query, self::OPTION_CACHE_RESULTS, true );
		$this->return = toolset_getarr( $query, self::OPTION_RETURN, self::RETURN_ASSOCIATIONS, $this->get_return_options() );
		$this->dont_count_found_rows = (bool) toolset_getarr( $query, self::OPTION_DONT_COUNT_FOUND_ROWS, false );

		// Default value of these needs to be null
		$this->parse_query_arg( $query, self::QUERY_RELATIONSHIP_SLUG, 'strval' );
		$this->parse_query_arg( $query, self::QUERY_RELATIONSHIP_ID, 'absint' );
		$this->parse_query_arg( $query, self::QUERY_PARENT_ID, 'absint' );
		$this->parse_query_arg( $query, self::QUERY_CHILD_ID, 'absint' );
		$this->parse_query_arg( $query, self::QUERY_LIMIT, 'absint' );
		$this->parse_query_arg( $query, self::QUERY_OFFSET, 'absint' );
		$this->parse_query_arg( $query, self::QUERY_SELECT_FIELDS, null, array() );
		$this->parse_query_arg( $query, self::QUERY_HAS_FIELDS, 'boolval' );
		$this->parse_query_arg( $query, self::QUERY_PARENT_DOMAIN, null, null, array( Toolset_Field_Utils::DOMAIN_POSTS ) );
		$this->parse_query_arg( $query, self::QUERY_PARENT_QUERY, null ); // todo sanitize?
		$this->parse_query_arg( $query, self::QUERY_CHILD_DOMAIN, null, null, array( Toolset_Field_Utils::DOMAIN_POSTS ) );
		$this->parse_query_arg( $query, self::QUERY_CHILD_QUERY, null ); // todo sanitize?
		$this->parse_query_arg( $query, self::QUERY_HAS_TRASHED_POSTS, 'boolval' );
	}


	/**
	 * Perform the query and get results.
	 *
	 * Depending on query arguments, the results may be cached.
	 *
	 * @return int[]|IToolset_Element[]|IToolset_Association[] Array of results, depending on query arguments.
	 */
	public function get_results() {
		return parent::get_results();
	}


	protected function get_subject_name_for_cache() {
		return 'associations';
	}


	/**
	 * Build the MySQL statement for querying the data, depending on query variables.
	 *
	 * @return string MySQL query statement.
	 * @since m2m
	 */
	protected function build_sql_statement() {

		global $wpdb;

		/// Condition clauses to be joined with AND.
		$where_clauses = array();

		$groupby_clauses = array();
		$orderby_clauses = array();

		// JOIN statements to be concatenated (they need to start with the JOIN keyword and have padding spaces).
		// The original table to be joined to is the associations one (as 'association').
		$join_clauses = array();

		$having_clauses = array();

		/// Setting this to true will result in joining the relationships table (as 'relationship').
		$join_relationships = false;

		/// If this is set to a column name, that column will be used as an post ID to join the wp_posts table
		/// (as 'wp_posts'). Empty string means that the join is not needed.
		$join_wp_posts_on = '';

		$association_table = Toolset_Relationship_Table_Name::associations();

		// If we have a query that is not specific to a particular language (association translation),
		// we can avoid the self-join on the association table because we'll always have the whole
		// translation group (same trid) in the results, or none of it.
		//
		// For example, when querying for a particular relationship slug, we know it isn't language-specific.
		// On the other hand, querying for a specific parent_id will always get us only a single row for each trid.
		//
		// In that case, we must do the self-join in order to retrieve the information for all the relevant languages.
		$has_language_specific_query = (
			$this->has_query_var( self::QUERY_LANGUAGE )
			&& self::LANGUAGE_ALL != $this->get_query_var( self::QUERY_LANGUAGE )
		);

		// Process individual query arguments.
		//
		//
		if( $this->has_query_var( self::QUERY_RELATIONSHIP_SLUG ) ) {
			$relationship_slug = $this->get_query_var( self::QUERY_RELATIONSHIP_SLUG );
			$relationship = Toolset_Relationship_Utils::get_relationship_definition( $relationship_slug );

			if( null === $relationship ) {
				// This will cause the query to return no results, as there can be no associations
				// for a non-existent relationship.
				$relationship_id = 0;
			} else {
				$relationship_id = $relationship->get_row_id();
			}

			$where_clauses[] = $wpdb->prepare(
				"association.relationship_id = %d",
				$relationship_id
			);
		}

		if( $this->has_query_var( self::QUERY_RELATIONSHIP_ID ) ) {
			$relationship_id = $this->get_query_var( self::QUERY_RELATIONSHIP_ID );

			$where_clauses[] = $wpdb->prepare(
				"association.relationship_id = %d",
				$relationship_id
			);
		}

		if( $this->has_query_var( self::QUERY_PARENT_ID ) ) {
			$where_clauses[] = $wpdb->prepare(
				"parent_id = %d",
				$this->get_query_var( self::QUERY_PARENT_ID )
			);

			$has_language_specific_query = true;
		}

		if( $this->has_query_var( self::QUERY_HAS_TRASHED_POSTS ) ) {
			// Note: This is not very nice from the performance point of view, but it's only a hotfix.
			// The query class will go through a refactoring very soon.
			$join_clauses[] = " JOIN {$wpdb->posts} as wp_parent_post ON ( wp_parent_post.ID = association.parent_id ) ";
			$join_clauses[] = " JOIN {$wpdb->posts} as wp_child_post ON ( wp_child_post.ID = association.child_id ) ";

			$operator = ( $this->get_query_var( self::QUERY_HAS_TRASHED_POSTS ) ? '=' : '!=' );
			$where_clauses[] = "wp_parent_post.post_status $operator 'trash'";
			$where_clauses[] = "wp_child_post.post_status $operator 'trash'";
		}

		if( $this->has_query_var( self::QUERY_CHILD_ID ) ) {
			$where_clauses[] = $wpdb->prepare(
				"child_id = %d",
				$this->get_query_var( self::QUERY_CHILD_ID )
			);

			$has_language_specific_query = true;
		}

		// Query only associations of relationships that have fields (that means they have an intermediary post type).
		// todo we might want to handle a situation when an intermediary post type is modified to have no fields.
		if( $this->has_query_var( self::QUERY_HAS_FIELDS ) ) {
			$join_relationships = true;
			$hasnt_fields_comparison = ( $this->get_query_var( self::QUERY_HAS_FIELDS ) ? 'NOT LIKE' : 'LIKE' );
			$where_clauses[] = "relationship.intermediary_type {$hasnt_fields_comparison} ''";
		}

		if( $this->has_query_var( self::QUERY_PARENT_DOMAIN ) ) {
			$join_relationships = true;
			$where_clauses[] = $wpdb->prepare( 'relationship.parent_domain LIKE %s', $this->get_query_var( self::QUERY_PARENT_DOMAIN ) );
		}

		if( $this->has_query_var( self::QUERY_CHILD_DOMAIN ) ) {
			$join_relationships = true;
			$where_clauses[] = $wpdb->prepare( 'relationship.child_domain LIKE %s', $this->get_query_var( self::QUERY_CHILD_DOMAIN ) );
		}

		// Filter results by a native WordPress query run on child/parent posts.
		//
		// Since currently only post relationships are supported, we're always using WP_Query.
		// We will obtain the list of MySQL clauses and merge them into our statement without actually querying anything yet.
		// See get_wp_query_clauses() for the details.
		$has_parent_query = $this->has_query_var( self::QUERY_PARENT_QUERY );
		$has_child_query = $this->has_query_var( self::QUERY_CHILD_QUERY );

		if( $has_parent_query && $has_child_query ) {
			throw new RuntimeException( 'A assocation query cannot join parent and child on a single query.' );
		}

		if( $has_parent_query || $has_child_query ) {
			$query_role = $has_child_query
				? self::QUERY_CHILD_QUERY
				: self::QUERY_PARENT_QUERY;

			$join_wp_posts_on = $has_child_query
				? 'child_id'
				: 'parent_id';

			$query_role = toolset_ensarr( $this->get_query_var( $query_role ) );

			$clauses = $this->get_wp_query_clauses( $query_role );

			// Include additional clauses to the final query.
			//
			// This should be safe because in WP_Query everything is properly referenced by table names
			// and we're joining only custom Toolset tables and wp_posts.
			//
			// We're ignoring these clauses:
			// - fields: because we have a custom mechanism of selecting them
			// - limits: because those are overridden by offset and limit query args

			// These clauses start with 'AND'
			$where_clauses[] = ' 1 = 1 ' . $clauses['where'];
			if( ! empty( $clauses['groupby'] ) ) {
				$groupby_clauses[] = $clauses['groupby'];
			}

			if( ! empty( $clauses['join'] ) ) {
				$join_clauses[] = $clauses['join'];
			}

			if( ! empty( $clauses['orderby'] ) ) {
				$orderby_clauses[] = $clauses['orderby'];
			}

			// most probably yes
			$has_language_specific_query = true;
		}


		// Determine if we're going to look for translations or not.
		//
		// With WPML inactive, the expected behaviour is to ignore all language-related information.
		//
		// Note that with the "transitional" multilingual mode, we are ignoring language information
		// in the associations table, but it is there, and some association translations might have "holes"
		// in them (missing element IDs where the element is not translated to a particular language).
		// But at this point, we don't mind. It will be handled as a special case in postprocess_results().
		if( Toolset_Relationship_Multilingual_Mode::is_on() ) {

			if( $has_language_specific_query ) {

				// Handle the query for a specific language.
				//
				// We will query for a single row within each translation group, then join the results with the
				// association table again in order to get the information in all the languages we need.
				if( $this->has_query_var( self::QUERY_LANGUAGE ) ) {
					$preferred_language = $this->get_query_var( self::QUERY_LANGUAGE );
				} else {
					$preferred_language = Toolset_Wpml_Utils::get_current_language();
				}

				if( self::LANGUAGE_ALL != $preferred_language ) {

					// Limit the result only for languages we care about.
					//
					// That is: specified language (or the current language), default site language and the
					// original language of the association.

					$language_clauses = array(
						$wpdb->prepare( "translation.lang = %s", $preferred_language ),
						"translation.translation_type IN ('none', 'original')"
					);

					$default_language = Toolset_Wpml_Utils::get_default_language();
					if( $default_language !== $preferred_language ) {
						$language_clauses[] = $wpdb->prepare( "translation.lang = %s", $default_language );
					}

					$language_clauses = sprintf(
						'AND ( %s )',
						implode( ' OR ', $language_clauses )
					);

				} else {
					// This happens when specifically querying for all languages but there's another
					// query argument which is language-specific.
					$language_clauses = '';
				}

				$join_clauses[] = "
					JOIN $association_table AS translation
					ON (
						association.trid = translation.trid
						$language_clauses
					)
				";

				$groupby_table = 'translation';

			} else {

				// We're not doing the self-join (there's no need for it because the query is not language-specific),
				// so we're going to get the data from the associations table directly.
				$groupby_table = 'association';
			}

			$groupby_clauses[] = "$groupby_table.trid";

			// Prepare fields for the SELECT clause.
			//
			// In this case, we'll be concatenating results for all selected languages within a translation group
			// in order to be able to use OFFSET and LIMIT consistently (one row == one actual result).
			$sep = self::GROUP_CONCAT_SEPARATOR;
			$select_fields = array(
				'trid' => "$groupby_table.trid",
				'relationship_id' => "GROUP_CONCAT($groupby_table.relationship_id SEPARATOR '$sep')",
				'association_id' => "GROUP_CONCAT($groupby_table.id SEPARATOR '$sep')",
				'lang' => "GROUP_CONCAT($groupby_table.lang SEPARATOR '$sep')",
				'parent_id' => "GROUP_CONCAT($groupby_table.parent_id SEPARATOR '$sep')",
				'child_id' => "GROUP_CONCAT($groupby_table.child_id SEPARATOR '$sep')",
				'intermediary_id' => "GROUP_CONCAT($groupby_table.intermediary_id SEPARATOR '$sep')",
			);

		} else {

			// The simplest case, there's no need to deal with translations at all.
			$select_fields = array(
				'trid' => 'association.trid',
				'relationship_id' => 'association.relationship_id',
				'association_id' => 'association.id',
				'parent_id' => 'association.parent_id',
				'child_id' => 'association.child_id',
				'intermediary_id' => 'association.intermediary_id'
			);
		}


		// Aggregate the information into parts of the final sql statement.
		//
		//
		$sql_found_rows = ( $this->need_row_count() ? 'SQL_CALC_FOUND_ROWS' : '' );

		array_walk( $select_fields, function( &$value, $key ) {
			$value = $value . ' AS ' . $key;
		});

		// It could be necessary to add more fields to the select statement, for example due to the DISTINCT statement ORDER BY needs the field to be in the SELECT.
		$extra_select_fields = $this->get_query_var( self::QUERY_SELECT_FIELDS );
		if ( ! empty( $extra_select_fields ) ) {
			$select_fields = array_merge( $select_fields, $extra_select_fields );
		}
		$sql_select = implode( ', ', $select_fields );

		$sql_join = '';
		if( $join_relationships ) {
			$sql_join .= ' JOIN ' . Toolset_Relationship_Table_Name::relationships() . ' AS relationship
				ON ( association.relationship_id LIKE relationship.id ) ';
		}
		if( ! empty( $join_wp_posts_on ) ) {
			$sql_join .= " JOIN {$wpdb->posts} ON ( association.{$join_wp_posts_on} = {$wpdb->posts}.ID ) ";
		}
		$sql_join .= implode( $join_clauses );

		$sql_from = "$association_table AS association $sql_join";

		$sql_where = (
			empty( $where_clauses )
				? ' 1=1 '
				: implode( ' AND ', $where_clauses )
		);

		if( $this->has_query_var( self::QUERY_LIMIT ) ) {
			$sql_limits = 'LIMIT ';
			if( $this->has_query_var( self::QUERY_OFFSET ) ) {
				$sql_limits .= $this->get_query_var( self::QUERY_OFFSET ) . ', ';
			}
			$sql_limits .= $this->get_query_var( self::QUERY_LIMIT );
		} else {
			$sql_limits = '';
		}

		$sql_groupby = '';
		if ( ! empty( $groupby_clauses ) ) {
			$sql_groupby = 'GROUP BY ' . join( ', ', $groupby_clauses );
		}

		$sql_orderby = '';
		if ( !empty( $orderby_clauses ) ) {
			$sql_orderby = 'ORDER BY ' . implode( ', ', $orderby_clauses );
		}

		$sql_having = '';
		if( ! empty( $having_clauses ) ) {
			$sql_having = 'HAVING ( ' . implode( ' ) AND ( ', $having_clauses ) . ' ) ';
		}


		// Finally, one query to bind them all...
		//
		//
		$query = "SELECT $sql_found_rows DISTINCT $sql_select FROM $sql_from WHERE $sql_where $sql_groupby $sql_having $sql_orderby $sql_limits";

		return $query;
	}


	/**
	 * @inheritdoc
	 * @return string
	 */
	protected function get_results_type() {
		return ARRAY_A;
	}


	/**
	 * Process raw output from $wpdb in a way defined by the 'return' query argument.
	 *
	 * @param $rows
	 *
	 * @return int[]|IToolset_Element[]|IToolset_Association[]
	 */
	protected function postprocess_results( $rows ) {

		if( Toolset_Relationship_Multilingual_Mode::is_transitional() ) {
			$rows = $this->preprocess_results_in_transitional_mode( $rows );
		}

		switch( $this->return ) {
			case self::RETURN_ASSOCIATION_IDS:
				return $this->postprocess_association_ids( $rows );
			case self::RETURN_ASSOCIATIONS:
				return $this->postprocess_associations( $rows );
			case self::RETURN_PARENT_IDS:
				return $this->postprocess_element_ids( $rows, Toolset_Relationship_Role::PARENT );
			case self::RETURN_CHILD_IDS:
				return $this->postprocess_element_ids( $rows, Toolset_Relationship_Role::CHILD );
			case self::RETURN_PARENTS:
				return $this->postprocess_elements( $rows, Toolset_Relationship_Role::PARENT );
			case self::RETURN_CHILDREN:
				return $this->postprocess_elements( $rows, Toolset_Relationship_Role::CHILD );
			default:
				// will never happen
				return null;
		}

	}


	/**
	 * Split single field's concatenated values by language.
	 *
	 * A database row from query results may contain more than one language version of the association,
	 * in which case its values are going to be concatenated in individual fields.
	 *
	 * This method converts a selected field into an associative array of values where keys are language codes.
	 * An empty string is used when there is no language defined.
	 *
	 * @param $row
	 * @param string $field_name
	 * @param null|string[] $language_codes Array of language codes if already extracted before. Must be an exact value.
	 * @param bool $skip_zeros If true, (numeric) zero field values will be skipped.
	 *
	 * @return array Field values with language codes as keys.
	 * @since m2m
	 */
	private function get_field_by_language( $row, $field_name, $language_codes = null, $skip_zeros = false ) {

		if( null === $language_codes ) {
			$language_codes = explode( self::GROUP_CONCAT_SEPARATOR, toolset_getarr( $row, 'lang' ) );
		}

		$result_count = count( $language_codes );
		$field_values = explode( self::GROUP_CONCAT_SEPARATOR, toolset_getarr( $row, $field_name ) );

		$results = array();
		for( $i = 0; $i < $result_count; ++$i ) {
			$field_value = $field_values[ $i ];

			if( is_numeric( $field_value ) && 0 == $field_value && $skip_zeros ) {
				continue;
			}

			$results[ $language_codes[ $i ] ] = $field_value;
		}

		return $results;
	}


	/**
	 * From raw query results, extract association IDs.
	 *
	 * If the associations are translated, the ID of the best translation is returned.
	 *
	 * @param $rows
	 *
	 * @return int[]
	 * @since m2m
	 */
	private function postprocess_association_ids( $rows ) {

		$selected_ids = array();

		foreach( $rows as $row ) {

			$association_ids_by_language = $this->get_field_by_language( $row, 'association_id' );

			$available_translations = array_keys( array_keys( $association_ids_by_language ) );

			// Here we asking to always return any value but the result should make sense - only
			// relevant languages should be queried by design (unless requesting 'all' languages,
			// but in that case returning a random translation makes sense).
			$selected_language = Toolset_Wpml_Utils::choose_best_translation( $available_translations, true );

			$selected_ids[] = (int) toolset_getarr( $association_ids_by_language[ $selected_language ], 'id', 0 );
		}

		return array_unique( $selected_ids );
	}


	/**
	 * From raw query results, extract element IDs of chosen role.
	 *
	 * If the elements are translated, the ID of the best translation is returned for each of them.
	 *
	 * @param array $rows
	 * @param string $role Toolset_Relationship_Role value.
	 *
	 * @return int[] Element IDs.
	 * @since m2m
	 */
	private function postprocess_element_ids( $rows, $role ) {

		$column_name = Toolset_Relationship_Database_Operations::get_instance()->role_to_column( $role );

		if( Toolset_Relationship_Multilingual_Mode::is_on() ) {
			$results = array();
			foreach( $rows as $row ) {
				$element_ids = $this->get_field_by_language( $row, $column_name, null, true );
				// todo handle empty result (which should never happen)
				$available_translations = array_keys( $element_ids );
				$selected_language = Toolset_Wpml_Utils::choose_best_translation( $available_translations, true );
				$results[] = $element_ids[ $selected_language ];
			}
			return $results;
		} else {
			return array_unique( wp_list_pluck( $rows, $column_name ) );
		}

	}


	/**
	 * From raw query results, select elements with a certain role (parent or child are supported) and return them
	 * as Toolset_Element instances.
	 *
	 * Posts will also get information about translations if it is part of the results.
	 *
	 * @param array $rows Query results from wpdb.
	 * @param string $role ROLE_PARENT or ROLE_CHILD.
	 *
	 * @return Toolset_Element[]
	 */
	private function postprocess_elements( $rows, $role ) {

		$column_name = Toolset_Relationship_Database_Operations::get_instance()->role_to_column( $role );

		$results = array();
		foreach( $rows as $row ) {

			if( Toolset_Relationship_Multilingual_Mode::is_on() ) {
				$relationship_slug = $this->get_field_by_language( $row, 'relationship' );
				$relationship_slug = array_pop( $relationship_slug );
				$element_source = $this->get_field_by_language( $row, $column_name );
			} else {
				$relationship_slug = toolset_getarr( $row, 'relationship' );
				$element_source = (int) toolset_getarr( $row, $column_name );
			}

			// We need the relationship definition for determining the element domain.
			$relationship_definition = Toolset_Relationship_Definition_Repository::get_instance()->get_definition( $relationship_slug );

			// todo handle errors
			try {
				$resuts[] = Toolset_Element::get_instance(
					$relationship_definition->get_domain( $role ),
					$element_source
				);
			} catch(Exception $e) {
				// skip missing post for now
			}
		}

		return $results;
	}


	/**
	 * Use raw query results to return instances of Toolset_Association_Base.
	 *
	 * Even if multiple language variants are present, the associations will always be returned in an original
	 * version.
	 *
	 * @param array $rows
	 *
	 * @return IToolset_Association[]
	 */
	private function postprocess_associations( $rows ) {

		$association_repository = Toolset_Association_Repository::get_instance();

		$associations = array();

		foreach( $rows as $row ) {

			if( Toolset_Relationship_Multilingual_Mode::is_on() ) {

				// Prepare language codes to optimize field reading below.
				//
				// Note that the language codes need to be in the exact order as in the results
				// even with duplications.
				$language_codes = explode( self::GROUP_CONCAT_SEPARATOR, toolset_getarr( $row, 'lang' ) );

				$parent_source = $this->get_field_by_language( $row, 'parent_id', $language_codes, true );
				$child_source = $this->get_field_by_language( $row, 'child_id', $language_codes, true );

				$intermediary_source = $this->get_field_by_language( $row, 'intermediary_id', $language_codes, true );
				if( empty( $intermediary_source ) ) {
					$intermediary_source = 0;
				}

				// All relationship slugs should be the same, we just grab one of them.
				$relationship_ids = $this->get_field_by_language( $row, 'relationship_id', $language_codes, true );
				$relationship_id = (int) array_pop( $relationship_ids );

			} else {

				// Simple scenario, no GROUPBY_CONCAT results.
				$parent_source = (int) toolset_getarr( $row, 'parent_id' );
				$child_source = (int) toolset_getarr( $row, 'child_id' );
				$intermediary_source = (int) toolset_getarr( $row, 'intermediary_id' );
				$relationship_id = (int) toolset_getarr( $row, 'relationship_id' );

			}

			// todo sanitize, can be string
			$association_trid = toolset_getarr( $row, 'trid' );

			// Add the association to results.
			try {
				$association = $association_repository->instantiate(
					$relationship_id,
					$association_trid,
					array(
						Toolset_Relationship_Role::PARENT => $parent_source,
						Toolset_Relationship_Role::CHILD => $child_source,
						Toolset_Relationship_Role::INTERMEDIARY => $intermediary_source
					)
				);

				$associations[] = $association;

			} catch( Exception $e ) {
				// The association couldn't have been loaded for some reason, we just skip it.
				continue;
			}
		}

		return $associations;
	}


	/**
	 * @return string[] Possible values for the 'return' query argument.
	 */
	private function get_return_options() {
		return array(
			self::RETURN_ASSOCIATIONS,
			self::RETURN_ASSOCIATION_IDS,
			self::RETURN_CHILD_IDS,
			self::RETURN_CHILDREN,
			self::RETURN_PARENT_IDS,
			self::RETURN_PARENTS
		);
	}


	/**
	 * Determine if SQL_CALC_FOUND_ROWS should be part of the MySQL statement.
	 *
	 * @return bool
	 */
	private function need_row_count() {
		return ( ! $this->dont_count_found_rows && $this->has_query_var( self::QUERY_LIMIT ) );
	}


	/**
	 * Fool WP_Query into generating MySQL query clauses for given query arguments without actually executing the query.
	 *
	 * It also prevents WPML from modifying the query because filtering by language is handled elsewhere.
	 * It doesn't support sticky posts, filter suppressing and probably some complex use-cases.
	 *
	 * @param array $query_args Arguments for WP_Query.
	 * @return string[] MySQL clauses, for details see the posts_clauses filter.
	 * @since m2m
	 */
	private function get_wp_query_clauses( $query_args ) {

		// Sticky posts are handled in a special way after the query takes place, so they would have no
		// effect in any case. This is a performance optimalization.
		$query_args['ignore_sticky_posts'] = true;

		// Without this, we won't be able to get the clauses because the posts_clauses filter would not be applied.
		$query_args['suppress_filters'] = false;

		if( ! isset( $query_args['post_type'] ) ) {
			// Get all associated post_types if none is defined
			$query_args['post_type'] = 'any';
		}

		// This will hold the mysql clauses after WP_Query pushes them through the posts_pre_query filter.
		$clauses_out = array();

		$catch_clauses = function( $clauses_in ) use( &$clauses_out ) {
			$clauses_out = $clauses_in;
		};

		// Filter priority
		$very_late = 10000;

		add_filter( 'posts_clauses', $catch_clauses, $very_late );

		// Returning a non-null value on the posts_pre_query filter (since WP 4.6) causes that no actual
		// mysql query takes place in WP_Query::get_posts().
		$dont_query_anyting = function() { return array(); };
		add_filter( 'posts_pre_query', $dont_query_anyting, $very_late );

		// Avoid WPML messing with the results because we already know in which language we want to query
		//$current_language = apply_filters( 'wpml_current_language', '' );
		//do_action( 'wpml_switch_language', 'all' );

		// This will immediately run the query.
		new WP_Query( $query_args );

		// Switch back to the current language so that we don't break anything else down the road.
		//do_action( 'wpml_switch_language', $current_language );

		// Clean up
		remove_filter( 'posts_clauses', $catch_clauses, $very_late );
		remove_filter( 'posts_pre_query', $dont_query_anyting, $very_late );

		return $clauses_out;
	}


	/**
	 * Make sure that the query results contain the complete information and data integrity is maintained.
	 *
	 * When the multilingual mode is transitional (between switching WPML off and updating the associations
	 * table), we need to behave like it's off (no language information is taken into account) but with
	 * a database where we may have zero element IDs in any place (when a particular element translation
	 * didn't exist).
	 *
	 * In order to fill these holes, we do another query to get the original translation and the row with
	 * untranslatable element IDs (translation type "none"), merge this information and use it as a replacement
	 * when a "hole" is encountered in the original results.
	 *
	 * Furthermore, trid is supposed to be unique for each association, but the transitional mode breaks
	 * this invariant. In order to salvage it, we're going to construct a faux "trid" value which is unique.
	 * This value can be broken down to the original trid and association_id.
	 * Toolset_Association_Transitional was created to handle this rare difference.
	 *
	 * Finally, completing holes in results may lead to having duplicate results (with exactly the same
	 * relationship, (original) trid and triplets of element IDs). We need to get rid of those.
	 *
	 * This approach is rather performance-heavy, but I believe that's acceptable because the transitional
	 * mode is really supposed to be transitional, temporary, just a measure to avoid the site from
	 * breaking when WPML is deactivated.
	 *
	 * @param array $rows Raw output from database.
	 *
	 * @return array Updated and valid results.
	 *
	 * @since m2m
	 */
	private function preprocess_results_in_transitional_mode( $rows ) {

		// Get all trids that appear within results
		//
		//
		$trids = array();
		foreach( $rows as $row ) {
			$trids[] = (int) toolset_getarr( $row, 'trid' );
		}
		$trids = implode( ', ', array_unique( $trids ) );

		// Get original translations and untranslated content for each trid
		//
		//
		global $wpdb;
		$associations_table = Toolset_Relationship_Table_Name::associations();
		$trid_original_results = $wpdb->get_results(
			"SELECT trid, parent_id, child_id, intermediary_id FROM {$associations_table} AS association
			WHERE association.trid IN ({$trids})
			    AND association.translation_type IN ('original', 'none')",
			ARRAY_A
		);

		// Merge nonzero element IDs from those translations
		//
		//
		$trid_completion = array();
		foreach( $trid_original_results as $row ) {
			$trid = (int) toolset_getarr( $row, 'trid' );
			$trid_completion[ $trid ] = $this->merge_rows(
				toolset_ensarr( toolset_getarr( $trid_completion, $trid ) ),
				$row
			);
		}

		// Fill holes in query results
		//
		//
		$results = array();
		foreach( $rows as $row ) {
			$trid = (int) toolset_getarr( $row, 'trid' );
			$row = $this->merge_rows( $row, $trid_completion[ $trid ] );

			// Avoid duplicating the same row (same meaning equal relationship, trid and element IDs)
			$row_hash = $this->calculate_row_hash( $row );
			if( array_key_exists( $row_hash, $results ) ) {
				continue;
			}

			// Generate an unique trid for the row since this is not guaranteed in the transitional mode.
			$id = (int) toolset_getarr( $row, 'association_id' );
			$row['trid'] = sprintf( '\transitional\%d\%d', $trid, $id );

			$results[ $row_hash ] = $row;
		}

		return $results;
	}


	/**
	 * Merge element IDs from completion row into the main one if the ID in the main one is zero.
	 *
	 * @param $main
	 * @param $completion
	 *
	 * @return array Main row, updated.
	 */
	private function merge_rows( $main, $completion ) {
		foreach( Toolset_Relationship_Role::all_role_names() as $role ) {
			$column_name = Toolset_Relationship_Database_Operations::get_instance()->role_to_column( $role );
			$main_value = (int) toolset_getarr( $main, $column_name );
			if( 0 === $main_value ) {
				$main[ $column_name ] = (int) toolset_getarr( $completion, $column_name );
			}
		}

		return $main;
	}


	/**
	 * Calculate a md5 hash of a database row, using relationship, trid and element IDs.
	 *
	 * @param $row
	 * @return string md5 hash value.
	 */
	private function calculate_row_hash( $row ) {
		$md5_source = sprintf(
			"%s_%d_%d_%d_%d",
			toolset_getarr( $row, 'relationship' ),
			(int) toolset_getarr( $row, 'trid' ),
			(int) toolset_getarr( $row, 'parent_id' ),
			(int) toolset_getarr( $row, 'child_id' ),
			(int) toolset_getarr( $row, 'intermediary_id' )
		);

		return md5( $md5_source );
	}

}
