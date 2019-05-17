<?php
/**
 * Interface that define the output class for the feed
 */

namespace PixelCaffeine\ProductCatalog\Feed;

/**
 * In order to improve the performance, this interface define the methods to manage the chunks of items to add
 * in the feed. So, any save or edit methods will manage the chunk available and they need to be called in a loop
 * until the chunks are finished.
 */
interface WriterInterface {

	/**
	 * Save next chunk in the feed
	 *
	 * @param string $context An action context where we are during the feed saving
	 *
	 * @return void
	 */
	public function saveChunk( $context );

	/**
	 * Edit all items marked as EDITED, for the limited chunk
	 *
	 * @param string $context An action context where we are during the feed saving
	 *
	 * @return void
	 */
	public function editChunk( $context );

	/**
	 * Make an action to delete the object generated previously
	 *
	 * @param string $context An action context where we are during the feed saving
	 *
	 * @return void
	 */
	public function delete( $context );

	/**
	 * Make an action when the feed saving is starting
	 *
	 * @param string $context An action context where we are during the feed saving
	 *
	 * @return void
	 */
	public function uploadStart( $context );

	/**
	 * Make an action when the feed saving is successfully completed
	 *
	 * @param string $context An action context where we are during the feed saving
	 *
	 * @return void
	 */
	public function uploadSuccess( $context );

	/**
	 * Make an action when the feed saving is failed
	 *
	 * @param string $context An action context where we are during the feed saving
	 *
	 * @return void
	 */
	public function uploadFailure( $context );

	/**
	 * Check if the feed is saving
	 *
	 * @return bool
	 */
	public function isSaving();

}
