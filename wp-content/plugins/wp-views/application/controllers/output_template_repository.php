<?php

/**
 * Repository for templates in Views.
 *
 * See Toolset_Renderer for a detailed usage instructions.
 *
 * @since m2m
 */
class WPV_Output_Template_Repository extends Toolset_Output_Template_Repository_Abstract {

	const VIEWS_LISTING_PAGE_HELP = '/admin/help/views-listing-page-help.phtml';
	const VIEWS_EDIT_PAGE_HELP = '/admin/help/views-edit-page-help.phtml';
	const CONTENT_TEMPLATES_LISTING_PAGE_HELP = '/admin/help/content-templates-listing-page-help.phtml';
	const CONTENT_TEMPLATES_EDIT_PAGE_HELP = '/admin/help/content-templates-edit-page-help.phtml';
	const WORDPRESS_ARCHIVES_LISTING_PAGE_HELP = '/admin/help/wordpress-archives-listing-page-help.phtml';
	const WORDPRESS_ARCHIVES_EDIT_PAGE_HELP = '/admin/help/wordpress-archives-edit-page-help.phtml';

	const ADMIN_FILTERS_POST_RELATIONSHIP_ANCESTOR_NODE = '/admin/filters/post/relationship/ancestor_node.phtml';

	const VIEWS_EDITOR_VIEW_WRAPPER_SECTION = '/admin/views-editor/sections/view-wrapper.phtml';

	const VIEWS_EDITOR_VIEW_WRAPPER_DISABLE_FOR_SEPARATORS_LIST = '/admin/views-editor/pointers/view-wrapper-disable-for-separators-list.phtml';

	// Views Settings
	const VIEWS_SETTINGS_PAGE_BUILDER_OPTIONS = '/admin/settings/page-builder-options.phtml';

	const MCE_VIEW_WPV_POST_BODY = '/mce/wpv-post-body.phtml';
	const MCE_VIEW_WPV_VIEW = '/mce/wpv-view.phtml';

	/**
	 * @var array|null Template definition cache.
	 */
	private $templates;


	/** @var Toolset_Output_Template_Repository */
	private static $instance;


	/**
	 * @return Toolset_Output_Template_Repository
	 */
	public static function get_instance() {
		if( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * @inheritdoc
	 * @return string
	 */
	protected function get_default_base_path() {
		return $this->constants->constant( 'WPV_TEMPLATES' );
	}


	/**
	 * Get the array with template definitions.
	 *
	 * @return array
	 */
	protected function get_templates() {
		if( null === $this->templates ) {
			$this->templates = array(
				self::VIEWS_LISTING_PAGE_HELP => array(
					'namespaces' => array()
				),
				self::VIEWS_EDIT_PAGE_HELP => array(
					'namespaces' => array()
				),
				self::CONTENT_TEMPLATES_LISTING_PAGE_HELP => array(
					'namespaces' => array()
				),
				self::CONTENT_TEMPLATES_EDIT_PAGE_HELP => array(
					'namespaces' => array()
				),
				self::WORDPRESS_ARCHIVES_LISTING_PAGE_HELP => array(
					'namespaces' => array()
				),
				self::WORDPRESS_ARCHIVES_EDIT_PAGE_HELP => array(
					'namespaces' => array()
				),

				self::ADMIN_FILTERS_POST_RELATIONSHIP_ANCESTOR_NODE => array(
					'namespaces' => array()
				),

				self::VIEWS_EDITOR_VIEW_WRAPPER_SECTION => array(
					'namespaces' => array(),
				),

				self::VIEWS_EDITOR_VIEW_WRAPPER_DISABLE_FOR_SEPARATORS_LIST => array(
					'namespaces' => array(),
				),

				// Views Settings
				self::VIEWS_SETTINGS_PAGE_BUILDER_OPTIONS => array(
					'namespaces' => array(),
				),
				self::MCE_VIEW_WPV_POST_BODY => array(
					'namespaces' => array(),
				),
				self::MCE_VIEW_WPV_VIEW => array(
					'namespaces' => array(),
				),
			);
		}
		return $this->templates;
	}

}
