<?php

/**
 * Factory for viewmodels of custom fields groups terms, for the purposes of the Custom Fields page, Users tab.
 *
 * @since 2.3
 */
class Types_Viewmodel_Field_Group_User_Factory extends Types_Viewmodel_Field_Group_Factory {

	/**
	 * Returns the class name of the field group
	 *
	 * @return string
	 * @since 2.3
	 */
	protected function get_field_group_class_name() {
		return 'Types_Viewmodel_Field_Group_User';
	}

	/**
	 * Returns the post type
	 *
	 * @return string
	 * @since 2.3
	 */
	public function get_post_type() {
		return Toolset_Field_Group_User::POST_TYPE;
	}


	/**
	 * Load a field group instance.
	 *
	 * @param int|string|WP_Post $field_group Post ID of the field group, it's name or a WP_Post object.
	 *
	 * @return null|Types_Viewmodel_Field_Group_User Field group or null if it can't be loaded.
	 */
	public static function load( $field_group ) {
		$factory = Types_Viewmodel_Field_Group_User_Factory::get_instance();
		return $factory->load_field_group( $field_group );
	}
}
