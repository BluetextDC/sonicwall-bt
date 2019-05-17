<?php

/**
 * Handles AJAX calls to get the Content Template block preview.
 * 
 * @since m2m
 */
class Toolset_Ajax_Handler_Get_Content_Template_Block_Preview extends Toolset_Ajax_Handler_Abstract {
	private $constants;

	private $toolset_renderer;

	/**
	 * Toolset_Ajax_Handler_Get_View_Block_Preview constructor.
	 *
	 * @param Toolset_Ajax $ajax_manager
	 * @param Toolset_Constants|null $constants
	 * @param Toolset_Renderer|null $toolset_renderer
	 */
	public function __construct(
		Toolset_Ajax $ajax_manager,
		\Toolset_Constants $constants = null,
		\Toolset_Renderer $toolset_renderer = null
	) {
		parent::__construct( $ajax_manager );

		$this->constants = $constants
			? $constants
			: new \Toolset_Constants();

		$this->toolset_renderer = $toolset_renderer
			? $toolset_renderer
			: \Toolset_Renderer::get_instance();
	}

	/**
	 * @param array $arguments Original action arguments.
	 *
	 * @return void
	 */
	function process_call( $arguments ) {

		$this->ajax_begin(
			array(
				'nonce' => Toolset_Ajax::CALLBACK_GET_CONTENT_TEMPLATE_BLOCK_PREVIEW,
				'is_public' => false,
			)
		);

		$ct_post_name = sanitize_text_field( toolset_getpost( 'ct_post_name', '' ) );

		if ( empty( $ct_post_name ) ) {
			$this->ajax_finish( array( 'message' => __( 'Content Template not set.', 'wpv-views' ) ), false );
		}

		$args = array(
			'name' => $ct_post_name,
			'posts_per_page' => 1,
			'post_type' => 'view-template',
			'post_status' => 'publish',
		);

		$ct = get_posts( $args );

		if (
			null !== $ct
			&& count( $ct ) === 1
		) {
			$ct_post_content = str_replace( "\t", '&nbsp;&nbsp;&nbsp;&nbsp;', str_replace( "\n", '<br />', $ct[0]->post_content ) );

			$ct_post_content .= $this->render_ct_block_overlay( $ct[0]->ID, $ct[0]->post_title );

			$this->ajax_finish( $ct_post_content, true );
		}

		$this->ajax_finish( array( 'message' => sprintf( __( 'Error while retrieving the Content Template preview. The selected Content Template (Slug: "%s") was not found.', 'wpv-views' ), $ct_post_name ) ), false );
	}

	/**
	 * Renders the Toolset Content Template Gutenberg block overlay for the block preview on the editor.
	 *
	 * @param string $ct_id    The ID of the selected Content Template.
	 * @param string $ct_title The title of the selected Content Template.
	 *
	 * @return bool|string
	 */
	public function render_ct_block_overlay( $ct_id, $ct_title ) {
		$renderer = $this->toolset_renderer;
		$template_repository = \Toolset_Output_Template_Repository::get_instance();
		$context = array(
			'module_title' => $ct_title,
			'module_type' => __( 'Content Template', 'wpv-view' ),
			'edit_link' => admin_url( 'admin.php?page=ct-editor&ct_id=' . $ct_id ),
		);
		$html = $renderer->render(
			$template_repository->get( $this->constants->constant( 'Toolset_Output_Template_Repository::PAGE_BUILDER_MODULES_OVERLAY' ) ),
			$context,
			false
		);

		return $html;
	}
}
