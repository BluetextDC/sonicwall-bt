<?php

namespace PixelCaffeine\ProductCatalog\Helper;

use PixelCaffeine\ProductCatalog\Configuration;
use PixelCaffeine\ProductCatalog\ProductCatalogManager;

/**
 * Class FeedDirectoryHelper
 *
 * Manage the file positions of the feeds
 *
 * @package PixelCaffeine\ProductCatalog\Helper
 */
class FeedDirectoryHelper {

	/**
	 * @var ProductCatalogManager
	 */
	private $product_catalog;

	/**
	 * @var Configuration
	 */
	private $configuration;

	/**
	 * @var string The directory where the feed lives
	 */
	private $directoryPath;

	/**
	 * @var string The directory where the feed lives
	 */
	private $directoryUrl;

	/**
	 * @var string The file name of the feed
	 */
	private $fileName;

	/**
	 * FeedDirectoryHelper constructor.
	 *
	 * @param ProductCatalogManager $product_catalog
	 */
	public function __construct( ProductCatalogManager $product_catalog ) {
		$this->product_catalog = $product_catalog;
		$this->configuration = $this->product_catalog->configuration();

		// Set the default directory path
		$wp_upload_dir = wp_upload_dir();
		$this->setDirectoryPath( $this->configuration->get( Configuration::OPTION_DIRECTORY_PATH, $wp_upload_dir['basedir'] . '/product-catalogs' ) );
		$this->setDirectoryUrl( $this->configuration->get( Configuration::OPTION_DIRECTORY_URL, $wp_upload_dir['baseurl'] . '/product-catalogs' ) );
		$this->setFileName( $this->configuration->get( Configuration::OPTION_FILE_NAME, sprintf( '%s.xml', $this->product_catalog->get_entity()->getId() ) ) );
	}

	/**
	 * Get the absolute path of the feed of the product catalog
	 */
	public function getFeedPath() {
		return untrailingslashit( $this->getDirectoryPath() ) . '/' . $this->getFileName();
	}

	/**
	 * Get the absolute path of the feed of the product catalog
	 */
	public function getFeedPathTmp() {
		return untrailingslashit( $this->getDirectoryPath() ) . '/' . $this->getFileNameTmp();
	}

	/**
	 * Get the URL of the feed
	 *
	 * @return string
	 */
	public function getFeedURL() {
		return untrailingslashit( $this->getDirectoryUrl() ) . '/' . $this->getFileName();
	}

	/**
	 * Get the directory where the feeds live
	 *
	 * @return string
	 */
	protected function getDirectoryPath() {
		return $this->directoryPath;
	}

	/**
	 * Set the directory where the feeds will leave
	 *
	 * @param $directoryPath
	 */
	public function setDirectoryPath( $directoryPath ) {
		$this->directoryPath = $directoryPath;
	}

	/**
	 * @return string
	 */
	public function getDirectoryUrl() {
		return $this->directoryUrl;
	}

	/**
	 * @param string $directoryUrl
	 */
	public function setDirectoryUrl($directoryUrl) {
		$this->directoryUrl = $directoryUrl;
	}

	/**
	 * @return string
	 */
	protected function getFileName() {
		return $this->fileName;
	}

	/**
	 * @return string
	 */
	protected function getFileNameTmp() {
		return $this->fileName . '.tmp';
	}

	/**
	 * @param string $fileName
	 */
	public function setFileName( $fileName ) {
		$this->fileName = $fileName;
	}

	/**
	 * @return bool
	 */
	public function isFeedExisting() {
		return file_exists( $this->getFeedPath() );
	}

}
