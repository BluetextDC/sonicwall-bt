<?php

namespace ACP\Helper\Select\Formatter;

use ACP\Helper\Select\Formatter;
use DateTime;
use WP_Comment;

class CommentSummary extends Formatter {

	/**
	 * @param WP_Comment $comment
	 * @param bool       $is_duplicate
	 *
	 * @return string
	 */
	public function get_label( $comment, $is_duplicate = false ) {
		$date = new DateTime( $comment->comment_date );

		$value = array_filter( array(
			$comment->comment_author_email,
			$date->format( 'M j, Y H:i' ),
		) );

		return $comment->comment_ID . ' - ' . implode( ' / ', $value );
	}

}