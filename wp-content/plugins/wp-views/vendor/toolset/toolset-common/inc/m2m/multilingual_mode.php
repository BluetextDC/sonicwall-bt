<?php

/**
 * Determine (and cache) the m2m multilingual mode.
 *
 * m2m API needs to recognize three "multilingual modes" internally:
 *
 * 1. off - no support for language filtering (internally: each database row has its own trid and
 *    no other language information is stored)
 * 2. on - with WPML, results are filtered by language and associations are basically made between
 *    translation groups (internally: rows in the associations of translatable relationships are grouped by
 *    trid, the translation_type is correctly set and such rows can have zeros instead of element IDs if
 *    a translation is missing)
 * 3. transitional - after WPML is deactivated (internally: database structure is same as for on,
 *    but the behaviour of association query matches off; new associations are created without
 *    translation information)
 *
 * The transitional mode is required so that the site can keep working when WPML is deactivated
 * (even temporarily). Switching from transitional to off needs to be done manually because it requires
 * a large database update (similar to the migration from legacy post relationships to m2m).
 *
 * Most of the m2m API doesn't need to know about the transitional mode, it's the same as "off" in most cases.
 * The only difference would be Toolset_Association_Query which needs to understand the database structure
 * of pre-existing records after WPML was deactivated.
 *
 * Outside of m2m API, this should never be needed at all.
 *
 * @since m2m
 */
class Toolset_Relationship_Multilingual_Mode {

	private static $instance;

	public static function get_instance() {
		if( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() { }

	private function __clone() { }


	/**
	 * Is m2m in the multilingual mode?
	 *
	 * This is mostly important for the API internals, there should be no need to use this outside.
	 *
	 * @return bool
	 * @since m2m
	 */
	public static function is_on() {
		return ( self::get_instance()->get_multilingual_mode() === self::MODE_ON );
	}


	/** @var null|string Cache for get_multilingual_mode(), 'on'|'off'|'transitional'.  */
	private $current_multilingual_mode = null;


	private $translatable_relationships_exist = null;


	const MULTILINGUAL_MODE_OPTION = 'toolset_m2m_multilingual_mode';
	//const HAS_TRANSLARABLE_RELATIONSHIPS_OPTION = 'toolset_m2m_has_translatable_relationships';

	const MODE_ON = 'on';
	const MODE_OFF = 'off';
	const MODE_TRANSITIONAL = 'transitional';


	private static function allowed_modes() {
		return array( self::MODE_OFF, self::MODE_ON, self::MODE_TRANSITIONAL );
	}


	/**
	 * Flush the cache of current multilingual mode.
	 *
	 * This needs to be done after a relationship definition or post type translation settings are updated
	 * (unless reloading a page immediately after).
	 */
	public static function flush_cache() {
		$instance = self::get_instance();
		$instance->current_multilingual_mode = null;
		$instance->translatable_relationships_exist = null;
	}


	/**
	 * Determine the exact multilingual mode.
	 *
	 * To be used only by the m2m API and only when the transitional mode needs to be handled separately.
	 * Otherwise, is_multilingual_mode_on() is preferred.
	 *
	 * @return string
	 * @since m2m
	 */
	public static function get() {
		return self::get_instance()->get_multilingual_mode();
	}


	/**
	 * Is m2m in the transitional mode?
	 *
	 * @return bool
	 */
	public static function is_transitional() {
		return ( self::get() === self::MODE_TRANSITIONAL );
	}


	private function get_multilingual_mode() {

		// Allways off since this functionality is about to be removed.
		return self::MODE_OFF;

		if( null === $this->current_multilingual_mode ) {

			$stored_mode = $this->load_multilingual_mode_option();
			$current_mode = $this->calculate_multilingual_mode();

			if( $stored_mode !== $current_mode ) {
				update_option( self::MULTILINGUAL_MODE_OPTION, $current_mode, true );
			}

			$this->current_multilingual_mode = $current_mode;

		}

		return $this->current_multilingual_mode;
	}


	private function load_multilingual_mode_option() {

		$stored_mode = get_option( self::MULTILINGUAL_MODE_OPTION );
		if( ! in_array( $stored_mode, self::allowed_modes() ) ) {
			$stored_mode = 'off';
		}

		return $stored_mode;
	}


	/**
	 * Calculate what the current multilingual mode should be, based on WPML state,
	 * existence of translatable relationships and previous mode.
	 *
	 * @return string New multilingual mode.
	 * @since m2m
	 */
	private function calculate_multilingual_mode() {

		$wpml_interop = Toolset_WPML_Compatibility::get_instance();
		$is_wpml_active = $wpml_interop->is_wpml_active_and_configured();

		// Note: This can be true only when WPML is active, otherwise it's not possible
		// to get the translatability status.
		$translatable_relationships_exist = $this->translatable_relationships_exist();

		// https://code2flow.com/0TjdqR
		if ( $is_wpml_active && $translatable_relationships_exist && $this->is_wpml_version_supported() ) {
			$mode = self::MODE_ON;
		} elseif ( self::MODE_OFF !== $this->load_multilingual_mode_option() ) {
			// This means that the previous condition was true before (WPML active + translatable relationships).
			// So we don't need to check for the relationships anymore (which we can't do now anyway) and
			// at the same time we're not risking switching into transitional mode without having
			// translatable relationships.
			$mode = self::MODE_TRANSITIONAL;

			// todo add a notice via Toolset_Admin_Notices_Manager
		} else {
			$mode = self::MODE_OFF;
		}

		return $mode;
	}


	private function is_wpml_version_supported() {
		$wpml_interop = Toolset_WPML_Compatibility::get_instance();
		return version_compare( $wpml_interop->get_wpml_version(), Toolset_Relationship_Controller::MINIMAL_WPML_VERSION, '>=' );
	}


	/**
	 * Check if there are any relationship definitions that are translatable.
	 *
	 * @return bool
	 */
	private function translatable_relationships_exist() {

		if( null === $this->translatable_relationships_exist ) {

			if ( ! Toolset_WPML_Compatibility::get_instance()->is_wpml_active_and_configured() ) {

				$this->translatable_relationships_exist = false;

			} else {

				// In order to do this, we'd need to reset the value when any relationship definition
				// is created, updated or removed, or any post type's translation preferences changed.
				// Seems like an overkill at this point.
				//$this->translatable_relationships_exist = get_option( self::HAS_TRANSLARABLE_RELATIONSHIPS_OPTION, null );

				if ( null === $this->translatable_relationships_exist ) {
					$relationship_query = new Toolset_Relationship_Query( array(
						Toolset_Relationship_Query::QUERY_IS_TRANSLATABLE => true
					) );

					$results = $relationship_query->get_results();

					$this->translatable_relationships_exist = ( ! empty( $results ) );

					//update_option( self::HAS_TRANSLARABLE_RELATIONSHIPS_OPTION, $this->translatable_relationships_exist, true );
				}
			}
		}

		return $this->translatable_relationships_exist;
	}


	public function set_mode( $new_mode ) {
		if( ! in_array( $new_mode, self::allowed_modes() ) ) {
			throw new InvalidArgumentException();
		}

		update_option( self::MULTILINGUAL_MODE_OPTION, $new_mode, true );
	}

}