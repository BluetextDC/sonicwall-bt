<?php

class WPV_Renderer {
	protected $constants;

	const VIEWS_LISTING_PAGE_HELP = '/application/views/admin/help/views-listing-page-help.phtml';
	const VIEWS_EDIT_PAGE_HELP = '/application/views/admin/help/views-edit-page-help.phtml';
	const CONTENT_TEMPLATES_LISTING_PAGE_HELP = '/application/views/admin/help/content-templates-listing-page-help.phtml';
	const CONTENT_TEMPLATES_EDIT_PAGE_HELP = '/application/views/admin/help/content-templates-edit-page-help.phtml';
	const WORDPRESS_ARCHIVES_LISTING_PAGE_HELP = '/application/views/admin/help/wordpress-archives-listing-page-help.phtml';
	const WORDPRESS_ARCHIVES_EDIT_PAGE_HELP = '/application/views/admin/help/wordpress-archives-edit-page-help.phtml';

	public function __construct( Toolset_Constants $constants = null ) {
		if ( null === $constants ) {
			$constants = new Toolset_Constants();
		}
		$this->constants = $constants;
	}

	/**
	 * Render the given template.
	 *
	 * @param  string   $template_path   The path of the template to render.
	 * @param  array    $context         The context of the template for passing some additional data to the template, if needed.
	 *
	 * @return string   The template output.
	 */
	public function render( $template_path, $context = null ) {
		$template_output = '';
		if ( is_file( $this->constants->constant( 'WPV_PATH' ) . $template_path ) ) {
			ob_start();
			include( $this->constants->constant( 'WPV_PATH' ) . $template_path );
			$template_output = ob_get_clean();
		}
		return $template_output;
	}

}