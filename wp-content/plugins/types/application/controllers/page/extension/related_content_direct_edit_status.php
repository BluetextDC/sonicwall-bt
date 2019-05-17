<?php

/**
 * Handles if a related content is editable by default for an user.
 *
 * @since m2m
 */
class Types_Page_Extension_Related_Content_Direct_Edit_Status {

	/**
	 * Association ID
	 *
	 * @var int
	 * @since m2m
	 */
	private $association_uid;

	/**
	 * User ID
	 *
	 * @var int
	 * @since m2m
	 */
	private $user_id;


	/**
	 * If it is enabled
	 *
	 * @var boolean
	 * @since m2m
	 */
	private $is_enabled = null;


	/**
	 * Types_Is_Related_Content_Editable constructor.
	 *
	 * @param int|IToolset_Association     $association The association object or its id.
	 * @param null|int                     $user_id The user id.
	 * @param Toolset_Association_Query_V2 $association_query_di Testing purposes.
	 * @since m2m
	 * @throws InvalidArgumentException In case of the association doesn't exist.
	 */
	public function __construct( $association, $user_id = null, Toolset_Association_Query_V2 $association_query_di = null ) {
		if ( ! $user_id ) {
			$this->user_id = get_current_user_id();
		} else {
			$this->user_id = $user_id;
		}

		if ( ! $association instanceof IToolset_Association ) {
			$association_query = $association_query_di
				? $association_query_di
				: new Toolset_Association_Query_V2();
			$association_query->add( $association_query->association_id( (int) $association ) );
			$associations = $association_query->get_results();
			if ( ! $associations ) {
				throw InvalidArgumentException( __( 'Invalid association', 'wpcf' ) );
			}
		}
		$this->association_uid = $associations? $associations[0]->get_uid() : null;
	}


	/**
	 * Gets if it is enabled
	 *
	 * @return boolean
	 * @since m2m
	 */
	public function get() {
		if ( null === $this->is_enabled ) {
			$this->is_enabled = get_transient( 'enable_editing_fields_' . $this->association_uid . '_' . $this->user_id );
		}
		return $this->is_enabled;
	}

	/**
	 * Sets if it is enabled
	 *
	 * @param boolean $enabled If it is enabled.
	 * @since m2m
	 */
	public function set( $enabled ) {
		$this->is_enabled = (bool) $enabled;
		set_transient( 'enable_editing_fields_' . $this->association_uid . '_' . $this->user_id, $this->is_enabled, YEAR_IN_SECONDS );
	}
}
