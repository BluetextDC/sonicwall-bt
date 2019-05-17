<?php

namespace ACP\Editing;

use AC;
use ACP\Editing;
use LogicException;
use WP_List_Table;

class TableScreen implements AC\Registrable {

	/**
	 * @var AC\Request
	 */
	protected $request;

	/**
	 * @var AC\ListScreen
	 */
	protected $list_screen;

	/**
	 * @var Addon
	 */
	protected $addon;

	/**
	 * @param AC\ListScreen $list_screen
	 * @param AC\Addon      $addon
	 */
	public function __construct( AC\ListScreen $list_screen, AC\Addon $addon ) {
		if ( ! $list_screen instanceof Editing\ListScreen ) {
			throw new LogicException( 'ListScreen should be of type Editing\ListScreen.' );
		}

		$this->list_screen = $list_screen;
		$this->addon = $addon;
	}

	public function register() {
		add_action( 'ac/table_scripts', array( $this, 'scripts' ) );
	}

	public function scripts() {
		/** @var WP_List_Table $wp_list_table */
		global $wp_list_table;

		$total_items = $wp_list_table instanceof WP_List_Table
			? $wp_list_table->get_pagination_arg( 'total_items' )
			: false;

		$editable_columns = $this->get_editable_columns();

		if ( ! $editable_columns ) {
			return;
		}

		$plugin_url = $this->addon->get_url();
		$version = $this->addon->get_version();

		wp_enqueue_script( 'ac-select2' );
		wp_enqueue_style( 'ac-select2' );

		// Main
		wp_register_script( 'acp-editing-table', $plugin_url . 'assets/js/table.js', array( 'jquery' ), $version );
		wp_register_style( 'acp-editing-table', $plugin_url . 'assets/css/table.css', array(), $version );

		// Allow JS to access the column data for this list screen on the edit page
		wp_localize_script( 'acp-editing-table', 'ACP_Editing_Columns', $editable_columns );
		wp_localize_script( 'acp-editing-table', 'ACP_Editing', array(
			'inline_edit' => array(
				'persistent' => $this->is_persistent_editing(),
				'active'     => $this->addon->is_editing_active( $this->list_screen ),
			),
			'bulk_edit'   => array(
				'updated_rows_per_iteration' => $this->get_updated_rows_per_iteration(),
				'total_items'                => $total_items,
			),
			// Translations
			'i18n'        => array(
				'select_author' => __( 'Select author', 'codepress-admin-columns' ),
				'edit'          => __( 'Edit' ),
				'redo'          => __( 'Redo', 'codepress-admin-columns' ),
				'undo'          => __( 'Undo', 'codepress-admin-columns' ),
				'date'          => __( 'Date' ),
				'delete'        => __( 'Delete', 'codepress-admin-columns' ),
				'download'      => __( 'Download', 'codepress-admin-columns' ),
				'errors'        => array(
					'field_required' => __( 'This field is required.', 'codepress-admin-columns' ),
					'invalid_float'  => __( 'Please enter a valid float value.', 'codepress-admin-columns' ),
					'invalid_floats' => __( 'Please enter valid float values.', 'codepress-admin-columns' ),
					'unknown'        => __( 'Something went wrong.', 'codepress-admin-columns' ),
				),
				'inline_edit'   => __( 'Inline Edit', 'codepress-admin-columns' ),
				'media'         => __( 'Media', 'codepress-admin-columns' ),
				'image'         => __( 'Image', 'codepress-admin-columns' ),
				'audio'         => __( 'Audio', 'codepress-admin-columns' ),
				'time'          => __( 'Time', 'codepress-admin-columns' ),
				'update'        => __( 'Update', 'codepress-admin-columns' ),
				'cancel'        => __( 'Cancel', 'codepress-admin-columns' ),
				'done'          => __( 'Done', 'codepress-admin-columns' ),
				'bulk_edit'     => array(
					'selecting' => array(
						'select_all' => __( 'Select all {0} entries', 'codepress-admin-columns' ),
						'selected'   => __( '<strong>{0} entries</strong> selected for Bulk Edit.', 'codepress-admin-columns' ),
					),
					'form'      => array(
						'heads_up'      => __( 'This will update {0} entries.', 'codepress-admin-columns' ),
						'clear_values'  => __( 'You are about to clear {0} entries.', 'codepress-admin-columns' ),
						'update_values' => __( 'You are about to update {0} entries.', 'codepress-admin-columns' ),
						'are_you_sure'  => __( 'Are you sure?', 'codepress-admin-columns' ),
						'yes_update'    => __( 'Yes, Update', 'codepress-admin-columns' ),
					),
					'feedback'  => array(
						'finished'  => __( 'Processed {0} entries', 'codepress-admin-columns' ),
						'updating'  => __( 'Updating entries.', 'codepress-admin-columns' ),
						'processed' => __( 'Processed {0} of {1} entries.', 'codepress-admin-columns' ),
						'failure'   => __( 'Updating failed. Please try again.', 'codepress-admin-columns' ),
						'error'     => __( 'We have found <strong>{0} errors</strong> while processing.', 'codepress-admin-columns' ),
					),
				),
			),
		) );

		// jQuery
		wp_enqueue_script( 'jquery' );

		// Core
		wp_enqueue_script( 'acp-editing-table' );
		wp_enqueue_style( 'acp-editing-bootstrap-editable' );
		wp_enqueue_style( 'acp-editing-table' );

		// WP Media picker
		wp_enqueue_media();
		wp_enqueue_style( 'ac-jquery-ui' );

		// WP Color picker
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );

		add_action( 'ac/table/actions', array( $this, 'edit_button' ) );

		do_action( 'ac/table_scripts/editing', $this->list_screen );
	}

	public function edit_button() {
		?>
		<label class="ac-table-button -toggle">
			<span class="ac-toggle">
				<input type="checkbox" value="1" id="acp-enable-editing" <?php checked( $this->addon->is_editing_active( $this->list_screen ) ); ?>>
				<span class="ac-toggle__switch">
					<svg class="ac-toggle__switch__on" width="2" height="6" aria-hidden="true" role="img" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2 6"><path fill="#fff" d="M0 0h2v6H0z"></path></svg>
					<svg class="ac-toggle__switch__off" width="6" height="6" aria-hidden="true" role="img" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 6 6"><path fill="#fff" d="M3 1.5c.8 0 1.5.7 1.5 1.5S3.8 4.5 3 4.5 1.5 3.8 1.5 3 2.2 1.5 3 1.5M3 0C1.3 0 0 1.3 0 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"></path></svg>
					<span class="ac-toggle__switch__track"></span>
				</span>
				<?php _e( 'Inline Edit', 'codepress-admin-columns' ); ?>
			</span>
		</label>
		<?php
	}

	private function is_persistent_editing() {
		return (bool) apply_filters( 'acp/editing/persistent', false, $this->list_screen );
	}

	private function get_updated_rows_per_iteration() {
		return apply_filters( 'acp/editing/bulk/updated_rows_per_iteration', 250, $this->list_screen );
	}

	private function format_js( $list ) {
		$options = array();

		if ( $list ) {
			foreach ( $list as $index => $option ) {
				if ( is_array( $option ) && isset( $option['options'] ) ) {
					$option['options'] = $this->format_js( $option['options'] );
					$options[] = $option;
				} else if ( is_scalar( $option ) ) {
					$options[] = array(
						'value' => $index,
						'label' => html_entity_decode( $option ),
					);
				}
			}
		}

		return $options;
	}

	/**
	 * @return array
	 */
	private function get_editable_columns() {
		$editable_columns = array();

		foreach ( $this->list_screen->get_columns() as $column ) {
			if ( ! $column instanceof Editable ) {
				continue;
			}

			$model = $column->editing();

			if ( ! $model || ! $model->is_active() ) {
				continue;
			}

			$data = $model->get_view_settings();

			/**
			 * @since 4.0
			 *
			 * @param array     $data
			 * @param AC\Column $column
			 */
			$data = apply_filters( 'acp/editing/view_settings', $data, $column );
			$data = apply_filters( 'acp/editing/view_settings/' . $column->get_type(), $data, $column );

			if ( false === $data ) {
				continue;
			}

			if ( isset( $data['options'] ) ) {
				$data['options'] = $this->format_js( $data['options'] );
			}

			$editable_columns[ $column->get_name() ] = array(
				'type'     => $column->get_type(),
				'editable' => $data,
			);
		}

		return $editable_columns;
	}

}