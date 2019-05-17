<?php

namespace PixelCaffeine\ProductCatalog;


interface FeedSaverInterface {

	/**
	 * Run the save process of the feed
	 *
	 * @param string $context
	 *
	 * @return mixed
	 */
	public function save( $context );

}
