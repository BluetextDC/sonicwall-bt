<?php

/**
 * Interface for post elements.
 *
 * @since m2m
 */
interface IToolset_Post extends IToolset_Element {


	/**
	 * @return string Post type slug.
	 * @since m2m
	 */
	public function get_type();


	/**
	 * @return IToolset_Post_Type|null
	 * @since 2.5.10
	 */
	public function get_type_object();


	/**
	 * @param string $title New post title
	 *
	 * @return void
	 * @since m2m
	 */
	public function set_title( $title );


	/**
	 * @return string Post slug
	 * @since m2m
	 */
	public function get_slug();


	/**
	 * @return bool
	 * @since 2.5.10
	 */
	public function is_revision();


	/**
	 * @return int ID of the post author.
	 * @since 2.5.11
	 */
	public function get_author();


	/**
	 * @return int The trid of the translation set if WPML is active and the post is part of one, zero otherwise.
	 * @since 2.5.11
	 */
	public function get_trid();


	/**
	 * @return string Post status
	 * @since Types 3.2
	 */
	public function get_status();


	/**
	 * Preferred editor mode for the current post. Relevant only in the "per post" editor mode of the post type.
	 *
	 * @return string
	 * @since Types 3.2.2
	 */
	public function get_editor_mode();


	/**
	 * @return string Raw post content
	 * @since Types 3.2.2
	 */
	public function get_content();
}
