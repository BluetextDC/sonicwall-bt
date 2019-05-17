<?php

namespace PixelCaffeine\Admin;


class Response {

	/**
	 * @var bool
	 */
	protected $success;

	/**
	 * @var array
	 */
	protected $data;

	/**
	 * Response constructor.
	 *
	 * @param $success
	 * @param array $data
	 */
	public function __construct( $success, $data = array() ) {
		$this->success = $success;
		$this->data = $data;
	}

	/**
	 * @return bool
	 */
	public function isSuccess() {
		return $this->success;
	}

	/**
	 * @param bool $success
	 */
	public function setSuccess( $success ) {
		$this->success = $success;
	}

	/**
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * @param array $data
	 */
	public function setData( $data ) {
		$this->data = $data;
	}

	/**
	 * Return a key value of data collection
	 *
	 * @param string $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get( $key, $default = false ) {
		return isset( $this->data[ $key ] ) ? $this->data[ $key ] : $default;
	}
}
