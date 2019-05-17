<?php

/**
 * Factory for viewmodels of custom fields groups, for the purposes of the Custom Fields page.
 *
 * @since 2.3
 */
class Types_Viewmodel_Field_Group_Factory extends Toolset_Field_Group_Factory {

	/**
	 * Constructor
	 *
	 * @since 2.3
	 */
	protected function __construct() {
		parent::__construct();
	}


	/**
	 * Returns post type
	 *
	 * @return string Post type that holds information about this field group type.
	 */
	public function get_post_type() {
		return '';
	}


	/**
	 * Returns the class name of the field group
	 *
	 * @return string
	 * @since 2.3
	 */
	protected function get_field_group_class_name() {
		return '';
	}


	/**
	 * For a given field domain, return the appropriate field group factory instance.
	 *
	 * @param string $domain Valid field domain.
	 *
	 * @return Toolset_Field_Group_Factory
	 * @throws InvalidArgumentException When the domains is incorrect.
	 * @since 2.1
	 */
	public static function get_factory_by_domain( $domain ) {
		switch ( $domain ) {
			case Toolset_Field_Utils::DOMAIN_POSTS:
				return Types_Viewmodel_Field_Group_Post_Factory::get_instance();
			case Toolset_Field_Utils::DOMAIN_USERS:
				return Types_Viewmodel_Field_Group_User_Factory::get_instance();
			case Toolset_Field_Utils::DOMAIN_TERMS:
				return Types_Viewmodel_Field_Group_Term_Factory::get_instance();
			default:
				throw new InvalidArgumentException( 'Invalid field domain.' );
		}
	}
}
