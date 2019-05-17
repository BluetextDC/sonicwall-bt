<?php

namespace PixelCaffeine\ProductCatalog\Entity;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The product catalog entity class
 *
 * @class ProductCatalog
 */
class ProductCatalog {

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $format;

	/**
	 * @var array
	 */
	private $config;

	/**
	 * @var int
	 */
	private $productsCount;

	/**
	 * @var \DateTime
	 */
	private $lastUpdateDate;

	/**
	 * @var string
	 */
	private $lastErrorMessage;

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId( $id ) {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getFormat() {
		return $this->format;
	}

	/**
	 * @param string $format
	 */
	public function setFormat( $format ) {
		$this->format = $format;
	}

	/**
	 * @return array
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * @param array $config
	 */
	public function setConfig( $config ) {
		$this->config = $config;
	}

	/**
	 * @return int
	 */
	public function getProductsCount() {
		return $this->productsCount;
	}

	/**
	 * @param int $productsCount
	 */
	public function setProductsCount( $productsCount ) {
		$this->productsCount = $productsCount;
	}

	/**
	 * @param int $count
	 */
	public function incrementProductsCounter( $count ) {
		$this->productsCount += $count;
	}

	/**
	 * @return \DateTime
	 */
	public function getLastUpdateDate() {
		return $this->lastUpdateDate;
	}

	/**
	 * @param \DateTime $lastUpdateDate
	 */
	public function setLastUpdateDate( $lastUpdateDate ) {
		$this->lastUpdateDate = $lastUpdateDate;
	}

	/**
	 * @return string
	 */
	public function getLastErrorMessage() {
		return $this->lastErrorMessage;
	}

	/**
	 * @param string $lastErrorMessage
	 */
	public function setLastErrorMessage( $lastErrorMessage ) {
		$this->lastErrorMessage = $lastErrorMessage;
	}

	/**
	 * Clear the last error message
	 */
	public function clearLastErrorMessage() {
		$this->lastErrorMessage = '';
	}

}
