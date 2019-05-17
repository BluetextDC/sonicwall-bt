<?php

namespace ACP\Search;

use AC;
use AC\Registrable;
use AC\Request;
use ACP;
use ACP\Asset\Enqueueable;

abstract class TableScreen
	implements Registrable {

	/**
	 * @var Request
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
	 * @var Enqueueable[]
	 */
	protected $assets;

	/**
	 * @param Addon         $addon
	 * @param AC\ListScreen $list_screen
	 * @param Request       $request
	 * @param array         $assets
	 */
	public function __construct( Addon $addon, AC\ListScreen $list_screen, Request $request, array $assets ) {
		$this->addon = $addon;
		$this->list_screen = $list_screen;
		$this->request = $request;
		$this->assets = $assets;
	}

	public function register() {
		add_action( 'ac/table_scripts', array( $this, 'scripts' ) );
		add_action( 'ac/table', array( $this, 'register_segment_button' ) );
		add_action( 'admin_footer', array( $this, 'add_segment_modal' ) );

		$this->register_query();
	}

	public function register_query() {
		$rules = $this->request->filter( 'ac-rules', array(), FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( ! $rules ) {
			return;
		}

		$bindings = array();

		foreach ( $rules as $rule ) {
			$column = $this->list_screen->get_column_by_name( $rule['name'] );

			if ( ! $column ) {
				continue;
			}

			if ( ! $column instanceof Searchable || ! $column->search() ) {
				continue;
			}

			$bindings[] = $column->search()->get_query_bindings(
				$rule['operator'],
				new Value( $rule['value'], $rule['value_type'] )
			);
		}

		QueryFactory::create(
			$this->list_screen->get_meta_type(),
			$bindings
		)->register();
	}

	public function scripts() {
		foreach ( $this->assets as $asset ) {
			$asset->enqueue();
		}

		wp_enqueue_script( 'ac-select2' );
		wp_enqueue_style( 'ac-select2' );

		wp_enqueue_style( 'ac-jquery-ui' );
		wp_enqueue_style( 'wp-pointer' );
	}

	/**
	 * @param AC\Table\Screen $screen
	 */
	public function register_segment_button( AC\Table\Screen $screen ) {
		$segment = $this->request->get( 'ac-segment' );

		$button = new AC\Table\Button( 'edit-columns' );
		$button
			->set_url( '#' )
			->set_label( __( 'Segments', 'codepress-admin-columns' ) )
			->set_text( '
					<span class="ac-table-button__segment__icon cpacicon-segment"></span>
					<span class="ac-table-button__segment__current">' . $segment . '</span>
					<span class="ac-table-button__caret"></span>' )
			->set_attribute( 'class', 'ac-table-button -segments' )
			->set_attribute( 'data-dropdown', '1' );

		$screen->register_button( $button, 9 );
	}

	/**
	 * Display the markup on the current list screen
	 */
	public function filters_markup() {
		?>

		<div id="ac-s"></div>

		<?php
	}

	public function add_segment_modal() {
		?>
		<div class="ac-segments">
			<div class="ac-segments__create">
				<span class="cpac_icons-segment"></span>
				<button class="button button-primary">
					<?php _e( 'Create new Segment', 'codepress-admin-columns' ); ?>
				</button>
			</div>
			<div class="ac-segments__list">
			</div>
			<div class="ac-segments__instructions" rel="pointer-segments">
				<?php _e( 'Instructions', 'codepress-admin-columns' ); ?>
				<div id="ac-segments-instructions" style="display:none;">
					<h3><?php _e( 'Instructions', 'codepress-admin-columns' ); ?></h3>
					<p>
						<?php _e( 'Save a set of custom smart filters for later use.', 'codepress-admin-columns' ); ?>
					</p>
					<p>
						<?php _e( 'This can be useful to group your WordPress content based on different criteria. Click on a segment in the list to load the segmented list.', 'codepress-admin-columns' ); ?>
					</p>
				</div>
			</div>

		</div>
		<div class="ac-modal" id="ac-modal-create-segment">
			<div class="ac-modal__dialog -create-segment">
				<form id="frm_create_segment">
					<div class="ac-modal__dialog__header">
						<?php _e( 'Create New Segment', 'codepress-admin-columns' ); ?>
						<button class="ac-modal__dialog__close">
							<span class="dashicons dashicons-no"></span>
						</button>
					</div>
					<div class="ac-modal__dialog__content">
						<label for="inp_segment_name"><?php _e( 'Name', 'codepress-admin-columns' ); ?></label>
						<input type="text" name="segment_name" id="inp_segment_name" required autocomplete="off">
						<div class="ac-modal__error">
						</div>
					</div>
					<div class="ac-modal__dialog__footer">
						<div class="ac-modal__loading">
							<span class="dashicons dashicons-update"></span>
						</div>
						<button class="button button" data-dismiss="modal"><?php _e( 'Cancel' ); ?></button>
						<button type="submit" class="button button-primary"><?php _e( 'Save segment', 'codepress-admin-columns' ); ?></button>
					</div>
				</form>
			</div>
		</div>
		<?php
	}

}