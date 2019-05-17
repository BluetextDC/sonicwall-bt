<?php

namespace ACP\Editing\Ajax\EditableRows;

use AC;
use ACP\Editing\Ajax\EditableRows;
use ACP\Editing\Strategy;
use WP_Comment_Query;

final class Comment extends EditableRows {

	/**
	 * @var AC\Ajax\Handler
	 */
	private $handler;

	/**
	 * @param AC\Request $request
	 * @param Strategy   $strategy
	 */
	public function __construct( AC\Request $request, Strategy $strategy ) {
		$handler = new AC\Ajax\Handler( false );
		$handler->set_action( 'parse_comment_query' )
		        ->set_callback( array( $this, 'send_editable_rows' ) )
		        ->set_priority( PHP_INT_MAX - 100 );

		$this->handler = $handler;

		parent::__construct( $request, $strategy );
	}

	public function register() {
		$this->handler->register();
	}

	/**
	 * @param array $args
	 */
	public function send_editable_rows( array $args ) {
		$this->check_nonce();

		$this->handler->deregister();

		$query = new WP_Comment_Query( array_merge( $args, array(
			'fields' => '*',
			'number' => $this->get_editable_rows_per_iteration(),
			'offset' => $this->get_offset(),
		) ) );

		$editable_rows = array();

		foreach ( $query->get_comments() as $comment ) {
			if ( $this->strategy->user_has_write_permission( $comment ) ) {
				$editable_rows[] = $comment->comment_ID;
			}
		}

		$this->success( $editable_rows );
	}

}