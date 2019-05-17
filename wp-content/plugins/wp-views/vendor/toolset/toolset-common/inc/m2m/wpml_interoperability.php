<?php

/**
 * Keeps the associations table up-to-date with post translations.
 *
 * Encapsulates everything that makes changes in the aforementioned table.
 *
 * Note that the implementation is quite performance-heavy because the of the extreme importancy of its stability.
 * It is open for improvements in the future.
 *
 * fixme this needs complete review
 *
 * @since m2m
 */
class Toolset_Relationship_WPML_Interoperability {

	private static $instance;

	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	private function __construct() {
	}

	private function __clone() {
	}


	const IS_INTEROP_UP_TO_DATE_OPTION = 'toolset_m2m_is_wpml_interop_up_to_date';


	/**
	 * Check for the active WPML interoperability support.
	 *
	 * That means, as this returns true, we're going to try to support association translations.
	 *
	 * @return bool
	 */
	public function is_interop_active() {

		// todo check WPML plugin versions

		//$is_wpml_active = Toolset_Wpml_Utils::is_wpml_active();

		/**
		 * toolset_is_m2m_wpml_interop_active
		 *
		 * Allows to disable m2m-WPML interoperability (updates of the translation table).
		 *
		 * @since m2m
		 */
		return (bool) apply_filters( 'toolset_is_m2m_wpml_interop_active', false );
	}


	/**
	 * Determine whether a full translation view refresh is needed.
	 *
	 * If needed, it updates the underlying WordPress option to properly handle WPML deactivation and re-activation.
	 *
	 * @param null|bool $new_value If a boolean value is provided, it will be used to overwrite the current state.
	 *
	 * @return bool
	 * @since 2.3
	 */
	public function is_full_refresh_needed( $new_value = null ) {

		if ( null === $new_value ) {
			$is_up_to_date = get_option( self::IS_INTEROP_UP_TO_DATE_OPTION, 'no' );
			$is_up_to_date = ( 'yes' == $is_up_to_date );

			$is_interop_active = $this->is_interop_active();

			if ( $is_up_to_date && ! $is_interop_active ) {
				// We still think that we're up-to-date with WPML but it has been deactivated
				// in the meantime.
				//
				// When (or if) it is re-activated, we are going to require full refresh.
				update_option( self::IS_INTEROP_UP_TO_DATE_OPTION, 'no', true );
			}

			return ( $is_up_to_date && $is_interop_active );
		} else {
			update_option( self::IS_INTEROP_UP_TO_DATE_OPTION, ( $new_value ? 'no' : 'yes' ), true );

			return (bool) $new_value;
		}
	}


	/**
	 * An event that should be triggered whenever an association is inserted in the database.
	 *
	 * @param Toolset_Association $association
	 *
	 * @return Toolset_Result
	 */
	public function after_association_insert( $association ) {

		if ( ! $association instanceof Toolset_Association ) {
			// Nothing to do here
			return new Toolset_Result( true );
		}

		if ( ! $this->is_interop_active() ) {
			return new Toolset_Result( true );
		}

		$result = $this->refresh_view_for_single_association(
			$association->get_id(),
			$association->get_definition(),
			$association->get_element( Toolset_Relationship_Role::PARENT )->get_id(),
			$association->get_element( Toolset_Relationship_Role::CHILD )->get_id(),
			$association->get_intermediary_id()
		);

		return $result->aggregate();

	}


	/**
	 * An event that should be triggered before an association is deleted from the database.
	 *
	 * @param Toolset_Association $association
	 *
	 * @return Toolset_Result
	 */
	public function before_association_delete( $association ) {

		if ( ! $association instanceof Toolset_Association ) {
			// Nothing to do here
			return new Toolset_Result( true );
		}

		if ( ! $this->is_interop_active() ) {
			return new Toolset_Result( true );
		}


		return $this->delete_translation_views_for_single_association( $association->get_id() );

	}


	/**
	 * An event that should be triggered when an association is updated in the database (meaning the
	 * record in the associations table).
	 *
	 * @param Toolset_Association $association
	 *
	 * @return Toolset_Result
	 */
	public function after_association_update( $association ) {

		if ( ! $association instanceof Toolset_Association ) {
			// Nothing to do here
			return new Toolset_Result( true );
		}

		if ( ! $this->is_interop_active() ) {
			return new Toolset_Result( true );
		}

		$results = new Toolset_Result_Set();

		// Here we simply reset everything related to an association and then generate it again.
		// It could be probably optimized in the future.
		$results->add(
			$this->before_association_delete( $association )
		);

		$results->add(
			$this->after_association_insert( $association )
		);

		return $results->aggregate();
	}


	/**
	 * Deletes all translation views related to a single association.
	 *
	 * For handling multiple associations at once, delete_translation_views_for_associations() is preferred.
	 *
	 * @param int $association_id
	 *
	 * @return Toolset_Result
	 * @since m2m
	 */
	private function delete_translation_views_for_single_association( $association_id ) {

		global $wpdb;

		$result = $wpdb->delete(
			Toolset_Relationship_Table_Name::association_translations(),
			array( 'association_id' => $association_id ),
			array( '%d ' )
		);

		return new Toolset_Result( $result !== false );
	}


	/**
	 * Delete view rows for specific associations.
	 *
	 * @param int[] $association_ids
	 *
	 * @since m2m
	 */
	private function delete_translation_views_for_associations( $association_ids ) {
		global $wpdb;

		$view_tn = Toolset_Relationship_Table_Name::association_translations();
		$query = "DELETE FROM `{$view_tn}`
			WHERE association_id IN ( " . Toolset_Utils::prepare_mysql_in( $association_ids, '%d' ) . " )";

		$wpdb->query( $query );
	}


	/**
	 * Refresh the association translation MV for a given association between original posts.
	 *
	 * It will scan the icl_translations table for translations of posts (but only where the elements in the associations
	 * actually are posts) and insert one row in the MV for each of them.
	 *
	 * If there are no rows in the icl_translations table for any affected post, which would otherwise result
	 * in doing nothing, we'll insert the row with the original ID manually with empty language code.
	 * That way it will still be possible to query the data.
	 *
	 * @param int $association_id
	 * @param string|Toolset_Relationship_Definition $relationship_source
	 * @param int $original_parent_id
	 * @param int $original_child_id
	 * @param int $original_intermediary_id
	 *
	 * @return Toolset_Result_Set
	 * @since m2m
	 */
	private function refresh_view_for_single_association(
		$association_id, $relationship_source, $original_parent_id, $original_child_id, $original_intermediary_id
	) {
		// Sanitize input
		$element_ids = array(
			Toolset_Relationship_Role::PARENT => (int) $original_parent_id,
			Toolset_Relationship_Role::CHILD => (int) $original_child_id
		);

		$relationship_definition = (
		$relationship_source instanceof Toolset_Relationship_Definition
			? $relationship_source
			: Toolset_Relationship_Definition_Repository::get_instance()->get_definition( $relationship_source )
		);

		// Refresh the view for all posts that take part in the association.
		$results = new Toolset_Result_Set();
		foreach ( Toolset_Relationship_Role::parent_child_role_names() as $element_role ) {
			if ( $relationship_definition->is_post( $element_role ) ) {
				$result = $this->refresh_view_for_post_in_association(
					$element_ids[ $element_role ], $element_role, $relationship_definition->get_slug(), $association_id
				);

				$results->add( $result );
			}
		}

		$result = $this->refresh_view_for_post_in_association(
			$original_intermediary_id, Toolset_Relationship_Role::INTERMEDIARY, $relationship_definition->get_slug(), $association_id
		);

		$results->add( $result );

		return $results;
	}


	/**
	 * Refresh the association translation view for one post playing a specific role in one association.
	 *
	 * @param int $post_id
	 * @param string $role One of the Toolset_Relationship_Utils::ROLE_* constants.
	 * @param string $relationship_slug
	 * @param int $association_id
	 *
	 * @return Toolset_Result_Set|Toolset_Result_Updated
	 * @since m2m
	 */
	private function refresh_view_for_post_in_association( $post_id, $role, $relationship_slug, $association_id ) {

		if ( 0 == $post_id ) {
			return new Toolset_Result_Updated( true, 0 );
		}

		$post_translations = Toolset_Wpml_Utils::get_post_translations_directly( $post_id );

		// Handle missing original post row.
		if ( ! in_array( $post_id, $post_translations ) ) {
			$post_translations[''] = $post_id;
		}

		$insert_format = array( '%d', '%s', '%s', '%d', '%s' );
		$results = new Toolset_Result_Set();

		global $wpdb;

		// Insert one record per translation.
		foreach ( $post_translations as $language_code => $translated_post_id ) {
			$data = array(
				'association_id' => $association_id,
				'relationship' => $relationship_slug,
				'language_code' => $language_code,
				'post_id' => $translated_post_id,
				'role' => $role
			);

			$result = $wpdb->insert( Toolset_Relationship_Table_Name::association_translations(), $data, $insert_format );

			if ( false === $result ) {
				$results->add(
					new Toolset_Result(
						false,
						sprintf( __( 'Unable to insert a row in the %s table.', 'wpcf' ), Toolset_Relationship_Table_Name::association_translations() )
					)
				);
			} else {
				$results->add( new Toolset_Result_Updated( true, $result ) );
			}
		}

		return $results;
	}


	/**
	 * Hooked into wpml_translation_update, this method refreshes the MV when WPML performs changes
	 * in the icl_translations table.
	 *
	 * Note that if too few information is provided, this might trigger a full refresh of the MV, eventually needing
	 * user's action on very large sites.
	 *
	 * This heavily relies on the fact that a before_delete action is followed by an after_delete one. If WPML
	 * fails to do that, we get a data inconsistency.
	 *
	 * @param array $args Following arguments will be recognized (all of them are optional):
	 * - string $type: insert|update|before_delete|after_delete|element_type_update|reset|before_language_delete,
	 *     default is 'update'
	 * - int $trid
	 * - int $element_id
	 * - string $element_type
	 * - int $translation_id
	 * - string $context: post|tax|...
	 * - int $rows_affected
	 * - string $language: Language code for the before_language_delete event.
	 *
	 * @since m2m
	 */
	public function on_wpml_translation_update( $args ) {

		return; // this is totally obsolete now
		// Not checking is_interop_active() here, since this came from WPML.

		$event_type = toolset_getarr( $args, 'type', 'update' );
		switch ( $event_type ) {

			case 'before_language_delete':
				$this->handle_wpml_language_delete( toolset_getarr( $args, 'language' ) );
				break;

			case 'reset':
			case 'element_type_update':
				$this->handle_big_wpml_change();
				break;

			case 'after_delete':
				$this->after_wpml_translations_deleted();
				break;

			default:
				$this->handle_wpml_change_event( $event_type, $args );
				break;
		}
	}


	/**
	 * Handle the rare but possible situation when a whole language is deleted.
	 *
	 * We will simply delete everything with the given language_code from the MV.
	 *
	 * @param string $language_code
	 *
	 * @since m2m
	 */
	private function handle_wpml_language_delete( $language_code ) {

		global $wpdb;

		$wpdb->delete(
			Toolset_Relationship_Table_Name::association_translations(),
			array( 'language_code' => $language_code ),
			array( '%s' )
		);
	}


	private function handle_big_wpml_change() {
		// todo
	}


	/**
	 * @var int[] Association IDs whose view rows have been deleted between before_delete and after_delete WPML events.
	 * @since m2m
	 */
	private $associations_without_views = array();


	/**
	 * Handle a WPML event that changes icl_translations rows.
	 *
	 * Specifically, this works for: insert, update, before_delete.
	 *
	 * @param $event_type
	 * @param $args
	 *
	 * @since m2m
	 */
	private function handle_wpml_change_event( $event_type, $args ) {

		$affected_posts = $this->query_posts_affected_by_wpml_event( $args );

		if ( ! is_array( $affected_posts ) ) {
			switch ( $affected_posts ) {
				case self::CHANGE_TRIGGERS_FULL_REFRESH:
					$this->handle_big_wpml_change();

					return;
				case self::CHANGE_IRRELEVANT:
					// Nothing to do here.
				default:
					return;
			}
		}

		$affected_associations = $this->query_affected_associations( $affected_posts );

		if ( empty( $affected_associations ) ) {
			return;
		}

		$this->delete_translation_views_for_associations( $affected_associations );

		if ( 'before_delete' == $event_type ) {
			// We won't refresh anything just yet, instead we store the IDs of associations whose views we have just
			// erased, and wait until WPML invokes the after_delete action. Then, we'll do the refresh.
			$this->associations_without_views = array_merge( $this->associations_without_views, $affected_associations );
		} else {
			// Insert or update.
			$this->refresh_views_for_associations( $affected_associations );
		}
	}


	/**
	 * Refresh the view after some WPML translations have been deleted.
	 *
	 * This happens during an after_delete event. In before_delete, we have deleted the view rows for possibly
	 * affected associations and stored association IDs. Now, we will take those IDs and refresh the view for
	 * those associations.
	 *
	 * This couldn't have been done sooner because a refresh before the icl_translations rows are actually deleted
	 * would accomplish nothing.
	 *
	 * @since m2m
	 */
	private function after_wpml_translations_deleted() {
		$this->refresh_views_for_associations( $this->associations_without_views );
		$this->associations_without_views = array();
	}


	// Return values for query_posts_affected_by_wpml_event().
	const CHANGE_IRRELEVANT = 'irrelevant';

	const CHANGE_TRIGGERS_FULL_REFRESH = 'full_refresh';


	/**
	 * From arguments provided by WPML, try to understand what happened and which specific posts (and their
	 * translations) might be affected.
	 *
	 * Note: This can be further optimized, for example by filtering out post types that don't participate in
	 * any relationships (when element_type is provided/will be queried), etc.
	 *
	 * @param array $args
	 *
	 * @return string|int[] An array of post IDs that are affected by the update, CHANGE_IRRELEVANT if no posts
	 *     have been affected or CHANGE_TRIGGERS_FULL_REFRESH if the change is too big or unspecific to decide.
	 * @since m2m
	 */
	private function query_posts_affected_by_wpml_event( $args ) {

		if ( ! $this->could_be_wpml_post_translation_change( $args ) ) {
			return self::CHANGE_IRRELEVANT;
		}

		$trid = $this->get_trid_affected_by_wpml_event( $args );

		if ( 0 == $trid ) {
			return self::CHANGE_TRIGGERS_FULL_REFRESH;
		}

		return Toolset_Wpml_Utils::query_elements_by_trid( $trid );
	}


	/**
	 * From arguments provided by WPML, get trid of everything that might be affected.
	 *
	 * See query_posts_affected_by_wpml_event() for details.
	 *
	 * @param array $args
	 *
	 * @return int trid or zero if it can't be determined.
	 */
	private function get_trid_affected_by_wpml_event( $args ) {
		$trid = (int) toolset_getarr( $args, 'trid', 0 );

		// Less expensive query first.
		if ( 0 == $trid ) {
			$translation_id = (int) toolset_getarr( $args, 'translation_id', 0 );
			$trid = Toolset_Wpml_Utils::get_trid_from_translation_id( $translation_id );
		}

		if ( 0 == $trid ) {
			$element_id = (int) toolset_getarr( $args, 'element_id', 0 );
			$element_type = toolset_getarr( $args, 'element_type', 'post%' );
			$trid = Toolset_Wpml_Utils::get_trid_from_element_id( $element_id, $element_type );
		}

		return $trid;
	}


	/**
	 * From arguments provided by WPML, decide if the event *could* affect post translations.
	 *
	 * @param array $args
	 *
	 * @return bool
	 * @since m2m
	 */
	private function could_be_wpml_post_translation_change( $args ) {

		$context = toolset_getarr( $args, 'context' );
		$element_type = toolset_getarr( $args, 'element_type' );
		$has_element_type = ( ! empty( $element_type ) );

		// Theoretically, we still might have the element_type even if no context was given.
		if ( empty( $context ) && $has_element_type ) {
			$context = explode( '_', $element_type );
			$context = $context[0];
		}

		$has_context = ( ! empty( $context ) );

		if ( $has_context && 'post' != $context ) {
			return false;
		} else {
			return true;
		}
	}


	/**
	 * Query associations (through the view table) where given posts have a role.
	 *
	 * todo move this to the associations query
	 *
	 * @param int[] $post_ids
	 *
	 * @return int[] Association IDs
	 * @since m2m
	 */
	private function query_affected_associations( $post_ids ) {
		global $wpdb;

		$view_tn = Toolset_Relationship_Table_Name::association_translations();

		$query = "
			SELECT DISTINCT association_id 
			FROM `{$view_tn}` 
			WHERE post_id IN (" . Toolset_Utils::prepare_mysql_in( $post_ids, '%d' ) . ")";

		$association_ids = $wpdb->get_col( $query );

		return $association_ids;
	}


	/**
	 * Refresh views for a set of association IDs.
	 *
	 * Gets all the association info in one query and then performs per-association refreshes.
	 *
	 * @param $association_ids
	 *
	 * @since m2m
	 */
	private function refresh_views_for_associations( $association_ids ) {

		if ( empty( $association_ids ) ) {
			return;
		}

		global $wpdb;

		$associations_tn = Toolset_Relationship_Table_Name::associations();
		$query = "
			SELECT * 
			FROM `{$associations_tn}` 
			WHERE id IN (" . Toolset_Utils::prepare_mysql_in( $association_ids, '%d' ) . ")";

		$associations = $wpdb->get_results( $query );

		foreach ( $associations as $association ) {
			$this->refresh_view_for_single_association(
				$association->id,
				$association->relationship,
				$association->parent_id,
				$association->child_id,
				$association->intermediary_id
			);
		}
	}
}