<?php

namespace PixelCaffeine\Logs;


use PixelCaffeine\Logs\Entity\Log;

interface LogRepositoryInterface {

	/**
	 * Save the object into the DB, updating it if the $log has an ID set
	 *
	 * @param Log $log
	 */
	public function save( Log &$log );

	/**
	 * Remove an Log from the DB
	 *
	 * @param int $log_id
	 */
	public function remove( $log_id );

	/**
	 * Find a Log by the ID
	 *
	 * @param $id
	 *
	 * @return Log
	 */
	public function findByID( $id );

	/**
	 * Find all Logs for the page specified
	 *
	 * @param array $orderBy
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return Log[]
	 *
	 */
	public function findAll( array $orderBy = null, $limit = null, $offset = null );

	/**
	 * Find all Logs filtered by Exception for the page specified
	 *
	 * @param string $exception
	 * @param array $orderBy
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return Log[]
	 */
	public function findByException( $exception, array $orderBy = null, $limit = null, $offset = null );

	/**
	 * Return the count of all logs saved
	 *
	 * @return int
	 */
	public function getCountAll();

}
