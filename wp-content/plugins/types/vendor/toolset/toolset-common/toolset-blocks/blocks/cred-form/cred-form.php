<?php

/**
 * Handles the creation of the Toolset Toolset Form Gutenberg block to allow Toolset Form embedding inside the Gutenberg editor.
 *
 * @since 2.6.0
 */
class Toolset_Blocks_CRED_Form extends Toolset_Gutenberg_Block {

	const BLOCK_NAME = 'toolset/cred-form';

	public function init_hooks() {
		add_action( 'init', array( $this, 'register_block_editor_assets' ) );

		add_action( 'init', array( $this, 'register_block_type' ) );

		// Hook scripts function into block editor hook
		add_action( 'enqueue_block_editor_assets', array( $this, 'blocks_editor_scripts' ) );
	}

	/**
	 * Register the needed assets for the Toolset Gutenberg blocks
	 *
	 * @since 2.6.0
	 */
	public function register_block_editor_assets() {
		$this->toolset_assets_manager->register_script(
			'toolset-cred-form-block-js',
			$this->constants->constant( 'TOOLSET_COMMON_URL' ) . '/toolset-blocks/assets/js/cred.form.block.editor.js',
			array( 'wp-editor' ),
			$this->constants->constant( 'TOOLSET_COMMON_VERSION' )
		);

		$cred_forms_posts_domain = class_exists( 'CRED_Form_Domain' ) ? CRED_Form_Domain::POSTS : 'posts';
		$cred_forms_users_domain = class_exists( 'CRED_Form_Domain' ) ? CRED_Form_Domain::USERS : 'users';
		$cred_forms_relationships_domain = class_exists( 'CRED_Form_Domain' ) ? CRED_Form_Domain::ASSOCIATIONS : 'relationships';
		wp_localize_script(
			'toolset-cred-form-block-js',
			'toolset_cred_form_block_strings',
			array(
				'isCREDActive' => $this->cred_active->is_met(),
				'blockCategory' => Toolset_Blocks::TOOLSET_GUTENBERG_BLOCKS_CATEGORY_SLUG,
				'blockName' => self::BLOCK_NAME,
				'publishedForms' => array(
					'postForms' => apply_filters( 'cred_get_available_forms', array(), $cred_forms_posts_domain ),
					'userForms' => apply_filters( 'cred_get_available_forms', array(), $cred_forms_users_domain ),
					'relationshipForms' => array(
						'new' => apply_filters( 'cred_get_available_forms', array(), $cred_forms_relationships_domain ),
					),
				),
				'wpnonce' => wp_create_nonce( Toolset_Ajax::CALLBACK_GET_CRED_FORM_BLOCK_PREVIEW ),
				'association' => array(
					'action' => 'cred_' . $this->constants->constant( 'CRED_Ajax::CALLBACK_GET_ASSOCIATION_FORM_DATA' ),
					'wpnonce' => wp_create_nonce( $this->constants->constant( 'CRED_Ajax::CALLBACK_GET_ASSOCIATION_FORM_DATA' ) ),
				),
				'actionName' => $this->toolset_ajax_manager->get_action_js_name( Toolset_Ajax::CALLBACK_GET_CRED_FORM_BLOCK_PREVIEW ),
			)
		);

		$this->toolset_assets_manager->register_style(
			'toolset-cred-form-block-editor-css',
			$this->constants->constant( 'TOOLSET_COMMON_URL' ) . '/toolset-blocks/assets/css/cred.form.block.editor.css',
			array(),
			$this->constants->constant( 'TOOLSET_COMMON_VERSION' )
		);
	}

	/**
	 * Register block type. We can use this method to register the editor & frontend scripts as well as the render callback.
	 *
	 * @note For now the scripts registration is disabled as it creates console errors on the classic editor.
	 *
	 * @since 2.6.0
	 */
	public function register_block_type() {
		register_block_type(
			self::BLOCK_NAME,
			array(
				'attributes' => array(
					'form' => array(
						'type' => 'string',
						'default' => '',
					),
					'formType' => array(
						'type' => 'string',
						'default' => '',
					),
					'formAction' => array(
						'type' => 'string',
						'default' => '',
					),
					'postToEdit' => array(
						'type' => 'string',
						'default' => 'current',
					),
					'anotherPostToEdit' => array(
						'type' => 'object',
						'default' => '',
					),
					'userToEdit' => array(
						'type' => 'string',
						'default' => 'current',
					),
					'anotherUserToEdit' => array(
						'type' => 'object',
						'default' => '',
					),
					'relationshipFormType' => array(
						'type' => 'string',
						'default' => 'create',
					),
					'relationshipParentItem' => array(
						'type' => 'string',
						'default' => '$current',
					),
					'relationshipChildItem' => array(
						'type' => 'string',
						'default' => '$current',
					),
					'relationshipParentItemSpecific' => array(
						'type' => 'object',
						'default' => '',
					),
					'relationshipChildItemSpecific' => array(
						'type' => 'object',
						'default' => '',
					),
				),
				'editor_script' => 'toolset-cred-form-block-js', // Editor script.
				'editor_style' => 'toolset-cred-form-block-editor-css', // Editor style.
			)
		);
	}

	/**
	 * Enqueue assets, needed on the editor side, for the Toolset Gutenberg blocks
	 *
	 * @since 2.6.0
	 */
	public function blocks_editor_scripts() {
		do_action( 'toolset_enqueue_styles', array( 'toolset-blocks-react-select-css' ) );
	}
}
