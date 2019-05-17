<?php

namespace PixelCaffeine\Logs;


use PixelCaffeine\Logs\Entity\Log;
use PixelCaffeine\Logs\Exception\LogNotExistingException;

class LogRepository implements LogRepositoryInterface {

	const DB_TABLE_NAME = 'aepc_logs';

	/**
	 * Save the object into the DB, updating it if the $log has an ID set
	 *
	 * @param Log $log
	 */
	public function save( Log &$log ) {
		if ( $log->getId() ) {
			$this->update( $log );
		} else {
			$this->persist( $log );
		}
	}

	/**
	 * Persists new Log object into the DB
	 *
	 * @param Log $log
	 */
	protected function persist( Log &$log ) {
		global $wpdb;

		$wpdb->insert( $wpdb->prefix . self::DB_TABLE_NAME, array(
			'exception' => $log->getException(),
			'message' => $log->getMessage(),
			'date' => $log->getDate()->format(\DateTime::ISO8601 ),
			'context' => serialize( $log->getContext() ),
		) );
		$log->setId( $wpdb->insert_id );
	}

	/**
	 * Update a Log object into the DB
	 *
	 * @param Log $log
	 *
	 * @throws LogNotExistingException
	 */
	protected function update( Log &$log ) {
		if ( ! $this->findByID( $log->getId() ) ) {
			throw new LogNotExistingException();
		}

		global $wpdb;

		$wpdb->update( $wpdb->prefix . self::DB_TABLE_NAME, array(
			'exception' => $log->getException(),
			'message' => $log->getMessage(),
			'date' => $log->getDate()->format( \DateTime::ISO8601 ),
			'context' => serialize( $log->getContext() ),
		), array( 'ID' => $log->getId() ) );
	}

	/**
	 * Remove an Log from the DB
	 *
	 * @param int $log_id
	 *
	 * @throws LogNotExistingException
	 */
	public function remove( $log_id ) {
		if ( ! $this->findByID( $log_id ) ) {
			throw new LogNotExistingException();
		}

		global $wpdb;
		$wpdb->delete( $wpdb->prefix . self::DB_TABLE_NAME, array( 'ID' => $log_id ) );
	}

	/**
	 * Remove all logs from the DB
	 */
	public function removeAll() {
		$logs = $this->findAll();
		foreach ( array_filter( (array) $logs ) as $log ) {
			$this->remove( $log->getId() );
		}
	}

	/**
	 * Get the criteria SQL clauses
	 *
	 * @param array $criteria
	 *
	 * @return string
	 */
	protected function getCriteriaSql( array $criteria ) {
		$sql = '';

		if ( $criteria ) {
			global $wpdb;
			foreach ( $criteria as $field => &$value ) {
				$format = is_int( $value ) ? '%d' : '%s';
				$value  = $wpdb->prepare( "{$field} = {$format}", $value );
			}

			$sql .= sprintf( " WHERE %s", implode( ' AND ', array_values( $criteria ) ) );
		}

		return $sql;
	}

	/**
	 * Find by a field defined in $criteria
	 *
	 * @param array $criteria
	 * @param array $orderBy
	 * @param null $limit
	 * @param null $offset
	 *
	 * @return bool|Log|Log[]
	 */
	protected function findBy( array $criteria, array $orderBy = null, $limit = null, $offset = null ) {
		global $wpdb;

		$logs = array();
		$table_name = $wpdb->prefix . self::DB_TABLE_NAME;
		$sql = "SELECT * FROM {$table_name}" . $this->getCriteriaSql( $criteria );

		if ( $orderBy ) {
			global $wpdb;
			foreach ( $orderBy as $field => &$order ) {
				$order = "{$field} {$order}";
			}

			$sql .= sprintf( " ORDER BY %s", implode( ', ', array_values( $orderBy ) ) );
		}

		if ( $limit ) {
			$limitSql = " LIMIT {$limit}";
			if ( $offset ) {
				$limitSql .= " OFFSET {$offset}";
			}
			$sql .= $limitSql;
		}
		$raw_logs = $wpdb->get_results( $sql );

		if ( empty( $raw_logs ) ) {
			return false;
		}

		foreach ( $raw_logs as $raw_log ) {
			$log = new Log(
				$raw_log->exception,
				$raw_log->message,
				new \DateTime( $raw_log->date ),
				maybe_unserialize( $raw_log->context )
			);
			$log->setId( $raw_log->ID );

			$logs[] = $log;
		}

		return $limit === 1 ? $logs[0] : $logs;
	}

	/**
	 * Find a Log by the ID
	 *
	 * @param $id
	 *
	 * @return Log|bool
	 */
	public function findByID( $id ) {
		return $this->findBy( array( 'ID' => $id ), array(), 1 );
	}

	/**
	 * Find all Logs for the page specified
	 *
	 * @param array $orderBy
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return Log[]
	 */
	public function findAll( array $orderBy = null, $limit = null, $offset = null ) {
		return $this->findBy( array(), $orderBy, $limit, $offset );
	}

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
	public function findByException( $exception, array $orderBy = null, $limit = null, $offset = null ) {
		return $this->findBy( array( 'exception' => $exception ), $orderBy, $limit, $offset );
	}

	/**
	 * Return the count of all logs saved
	 *
	 * @param array $criteria
	 *
	 * @return int
	 */
	protected function getCount( array $criteria ) {
		global $wpdb;

		$table_name = $wpdb->prefix . self::DB_TABLE_NAME;
		$sql = "SELECT COUNT(*) FROM {$table_name}" . $this->getCriteriaSql( $criteria );

		return intval( $wpdb->get_var( $sql ) );
	}

	/**
	 * Return the count of all logs saved
	 *
	 * @return int
	 */
	public function getCountAll() {
		return $this->getCount( array() );
	}
}
