<?php

class BT_BB_Light_Item {

	private $id;
	private $title;
	
	private $shortcode_tag;

	public function __construct( $post, $shortcode_tag ) {
		$this->id = $post->ID;
		$this->title = $post->post_title;
		$this->shortcode_tag = $shortcode_tag;
	}

	public function id() {
		return $this->id;
	}

	public function title() {
		return $this->title;
	}

	public function shortcode( $args = '' ) {
		return '[' . $this->shortcode_tag . ' id="' . $this->id . '"]';
	}

}