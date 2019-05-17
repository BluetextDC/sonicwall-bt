<?php

/**
 * Field Term Group, for the purposes of the Custom Fields page, Terms tab.
 *
 * @since 2.3
 */
abstract class Types_Viewmodel_Field_Group extends Toolset_Field_Group {

	/**
	 * Page name for editing
	 *
	 * @since 2.3
	 * @var string
	 */
	const EDIT_PAGE_SLUG = '';


	/**
	 * Page name for deleting
	 *
	 * @since 2.3
	 * @var string
	 */
	const DELETE_PAGE_SLUG = '';


	/**
	 * Gets edit page slug
	 *
	 * @since 2.3
	 * @return string
	 */
	public function get_edit_page_slug() {
		return static::EDIT_PAGE_SLUG;
	}

	/**
	 * Gets delete page slug
	 *
	 * @since 2.3
	 * @return string
	 */
	public function get_delete_page_slug() {
		return static::DELETE_PAGE_SLUG;
	}


	/**
	 * Converts to JSON
	 *
	 * @return array JSON format.
	 */
	public function to_json() {
		$json = array();
		$json['id'] = $this->get_id();
		$json['slug'] = $this->get_slug();
		$json['name'] = $this->get_name();
		$json['description'] = $this->get_description();
		$json['isActive'] = $this->is_active();
		$json['containsRFG'] = $this->contains_repeating_field_group();

		return $json;
	}


	/**
	 * Get the backend edit link.
	 *
	 * @return string
	 * @since 2.3
	 */
	private function get_edit_link() {
		return admin_url() . 'admin.php?page=' . $this->get_edit_page_slug() . '&group_id=' . $this->get_id();
	}


	/**
	 * Gets delete group link.
	 *
	 * @return string
	 * @since 2.3
	 */
	private function get_delete_link() {
		return esc_url(
			add_query_arg(
				array(
					'action' => 'wpcf_ajax',
					'wpcf_action' => $this->get_delete_page_slug(),
					'group_id' => $this->get_id(),
					'wpcf_ajax_update' => 'wpcf_list_ajax_response_' . $this->get_id(),
					'_wpnonce' => wp_create_nonce( 'delete_group' ),
					'wpcf_warning' => rawurlencode( __( 'Are you sure?', 'wpcf' ) ),
				),
				admin_url( 'admin-ajax.php' )
			)
		);
	}


	/**
	 * Add extra JSON data depending on the group
	 *
	 * @param array $json_data JSON data.
	 * @return array
	 * @since 2.3
	 */
	public function add_extra_json_data( $json_data ) {
		$json_data['displayName'] = $json_data['name'];
		$json_data['editLink'] = $this->get_edit_link();
		$json_data['deleteLink'] = $this->get_delete_link();
		$json_data['isActive'] = $json_data['isActive'] ? __( 'Yes', 'wpcf' ):__( 'No', 'wpcf' );

		return $json_data;
	}
}
