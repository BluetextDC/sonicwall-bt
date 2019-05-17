<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @class AEPC_Admin_Notices
 */
class AEPC_Admin_Notices {

	/** @var array Save all notices occur in the admin pages */
	protected static $notices = array(
		'error' => array(),
		'success' => array(),
		'warning' => array(),
		'info' => array()
	);

	/**
	 * Add useful hooks for initialization
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'get_notices_from_user_meta' ) );
		add_action( 'shutdown', array( __CLASS__, 'save_notices_in_user_meta' ) );
	}

	/**
	 * Add the notice, by $type and $id
	 *
	 * @param $type
	 * @param $id
	 * @param $message
	 * @param string $dismiss_action
	 */
	public static function add_notice( $type, $id, $message, $dismiss_action = '' ) {
		if ( ! isset( self::$notices[ $type ][ $id ] ) ) {
			self::$notices[ $type ][ $id ] = array();
		}

		// Add the notice
		self::$notices[ $type ][ $id ][] = array(
			'text' => $message,
			'dismiss_action' => $dismiss_action
		);
	}

	/**
	 * Check if there is some error for the type and ID
	 *
	 * @param string $type
	 * @param string $id
	 *
	 * @return bool
	 */
	public static function has_notice( $type = '', $id = '' ) {
		if ( 'any' === $type ) {
			$type = '';
		}

		// Check for all
		if ( empty( $type ) && empty( $id ) ) {
			foreach( array_keys( self::$notices ) as $type ) {
				if ( ! empty( self::$notices[ $type ] ) ) {
					return true;
				}
			}
		}

		// Check for ID of any type
		elseif ( empty( $type ) && ! empty( $id ) ) {
			foreach( array_keys( self::$notices ) as $type ) {
				if ( ! empty( self::$notices[ $type ][ $id ] ) ) {
					return true;
				}
			}
		}

		// Check any ID of specific type
		elseif ( ! empty( $type ) && empty( $id ) ) {
			return ! empty( self::$notices[ $type ] );
		}

		// Check specific ID of specific type
		elseif ( ! empty( $type ) && ! empty( $id ) ) {
			return ! empty( self::$notices[ $type ][ $id ] );
		}

		return false;
	}

	/**
	 * Return the notices, by $type and $id
	 *
	 * @param string $type
	 * @param string $id
	 *
	 * @return array|mixed
	 */
	public static function get_notices( $type = '', $id = '' ) {
		if ( 'any' === $type ) {
			$type = '';
		}

		// Init array to return with all empty keys
		$notices = array_map( '__return_empty_array', array_flip( array_keys( self::$notices ) ) );

		// Check for all
		if ( empty( $type ) && empty( $id ) ) {
			foreach( array_keys( self::$notices ) as $type ) {
				if ( ! empty( self::$notices[ $type ] ) ) {
					$notices[ $type ] = self::$notices[ $type ];
				}
			}
		}

		// Check for ID of any type
		elseif ( empty( $type ) && ! empty( $id ) ) {
			foreach( array_keys( self::$notices ) as $type ) {
				if ( ! empty( self::$notices[ $type ][ $id ] ) ) {
					$notices[ $type ][ $id ] = self::$notices[ $type ][ $id ];
				}
			}
		}

		// Check any ID of specific type
		elseif ( ! empty( $type ) && empty( $id ) && ! empty( self::$notices[ $type ] ) ) {
			return self::$notices[ $type ];
		}

		// Check specific ID of specific type
		elseif ( ! empty( $type ) && ! empty( $id ) && ! empty( self::$notices[ $type ][ $id ] ) ) {
			return self::$notices[ $type ][ $id ];
		}

		return array_filter( $notices );
	}

	/**
	 * Remove the notices, by defining $type and $id, both optional
	 *
	 * @param string $type
	 * @param string $id
	 */
	public static function remove_notices( $type = '', $id = '' ) {
		if ( 'any' === $type ) {
			$type = '';
		}

		// Check for all
		if ( empty( $type ) && empty( $id ) ) {
			self::$notices = array_map( '__return_empty_array', self::$notices );
		}

		// Check for ID of any type
		elseif ( empty( $type ) && ! empty( $id ) ) {
			foreach( array_keys( self::$notices ) as $type ) {
				unset( self::$notices[ $type ][ $id ] );
			}
		}

		// Check any ID of specific type
		elseif ( ! empty( $type ) && empty( $id ) && ! empty( self::$notices[ $type ] ) ) {
			self::$notices[ $type ] = array();
		}

		// Check specific ID of specific type
		elseif ( ! empty( $type ) && ! empty( $id ) && ! empty( self::$notices[ $type ][ $id ] ) ) {
			unset( self::$notices[ $type ][ $id ] );
		}
	}

	/**
	 * Get the notices from user meta, saved on php shutdown
	 */
	public static function get_notices_from_user_meta() {
		if ( $saved_notices = get_user_meta( get_current_user_id(), 'aepc_admin_notices', true ) ) {
			self::$notices = $saved_notices;
			delete_user_meta( get_current_user_id(), 'aepc_admin_notices' );
		}
	}

	/**
	 * This method is triggered on php shutdown, because if some notice remains, it will be shown on frontend
	 * as soon as possible
	 */
	public static function save_notices_in_user_meta() {
		if ( ! empty( self::$notices ) ) {
			update_user_meta( get_current_user_id(), 'aepc_admin_notices', self::$notices );
		}
	}

	/**
	 * Performs an action for each ID of notice dismissed
	 *
	 * @param $id
	 */
	public static function dismiss_notice( $id ) {
		switch ( $id ) {

			case 'ca_bug_warning' :
				update_option( 'aepc_show_warning_ca_bug', false );
				break;

		}
	}

}
