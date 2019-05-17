<?php

namespace PixelCaffeine\Logs\Entity;


class Log {

	/**
	 * @var int
	 */
	protected $id = 0;

	/**
	 * @var string
	 */
	protected $exception;

	/**
	 * @var string
	 */
	protected $message;

	/**
	 * @var \DateTime
	 */
	protected $date;

	/**
	 * @var array
	 */
	protected $context = array();

	/**
	 * Pass the mandatory arguments
	 *
	 * @param $exception
	 * @param $message
	 * @param \DateTime $date
	 * @param array $context
	 */
	public function __construct( $exception, $message, \DateTime $date = null, $context = array() ) {
		$this->exception = $exception;
		$this->message = $message;
		$this->date = $date ?: new \DateTime();
		$this->context = $context;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return (int) $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId( $id ) {
		$this->id = (int) $id;
	}

	/**
	 * @return string
	 */
	public function getException() {
		return $this->exception;
	}

	/**
	 * @param string $exception
	 */
	public function setException( $exception ) {
		$this->exception = $exception;
	}

	/**
	 * @return string
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * @param string $message
	 */
	public function setMessage( $message ) {
		$this->message = $message;
	}

	/**
	 * @return \DateTime
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * @param \DateTime $date
	 */
	public function setDate( $date ) {
		$this->date = $date;
	}

	/**
	 * @return array
	 */
	public function getContext() {
		return $this->context;
	}

	/**
	 * @param array $context
	 */
	public function setContext( $context ) {
		$this->context = $context;
	}

}
