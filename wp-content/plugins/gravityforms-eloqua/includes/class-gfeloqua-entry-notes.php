<?php
/**
 * GF Eloqua Entry Notes class
 *
 * @package gfeloqua
 */

/**
 * GF Eloqua Entry notes class to create notes for form entries
 */
class GFEloqua_Entry_Notes {

	/**
	 * The GF Entry ID
	 *
	 * @var int
	 */
	private $entry_id;

	/**
	 * The GF Form ID
	 *
	 * @var int
	 */
	private $form_id;

	/**
	 * Notes storage array
	 *
	 * @var array
	 */
	private $notes = array();

	/**
	 * Class constructor
	 *
	 * @param int $entry_id  GF Entry ID.
	 * @param int $form_id   GF Form ID.
	 */
	public function __construct( $entry_id = '', $form_id = '' ) {
		if ( $entry_id ) {
			$this->set_entry_id( $entry_id );
		}
		if ( $form_id ) {
			$this->set_form_id( $form_id );
		}
	}

	/**
	 * Sets the Entry ID for logged notes.
	 *
	 * @param int $entry_id  GF Entry ID.
	 *
	 * @return this  Chainable method.
	 */
	public function set_entry_id( $entry_id ) {
		$this->entry_id = $entry_id;
		return $this;
	}

	/**
	 * Sets the Form ID for logged notes.
	 *
	 * @param int $form_id  GF Form ID.
	 *
	 * @return this  Chainable method.
	 */
	public function set_form_id( $form_id ) {
		$this->form_id = $form_id;
		return $this;
	}

	/**
	 * Adds a note to the entry.
	 *
	 * @param string $note  Note text to add.
	 * @param mixed  $data  Optional element to log with notes.
	 * @param string $type  Type of note (note/error/debug/warning).
	 *
	 * @return bool  If not was logged.
	 */
	public function add( $note, $data = false, $type = 'note' ) {
		if ( ! $this->entry_id ) {
			return false;
		}

		if ( ! isset( $this->notes[ $this->entry_id ] ) ) {
			$this->notes[ $this->entry_id ] = array();
		}

		$new_note = array(
			'note' => $note,
			'timestamp' => current_time( 'timestamp' ),
			'type' => $type,
		);

		if ( $data ) {
			$new_note['data'] = $data;
		}

		$this->notes[ $this->entry_id ][] = $new_note;

		return true;
	}

	/**
	 * Adds an error note
	 *
	 * @param string $note  The note text to log.
	 * @param mixed  $data  Optional data to log with note.
	 *
	 * @return bool  If logged successfully.
	 */
	public function add_error( $note, $data = false ) {
		return $this->add( $note, $data, 'error' );
	}

	/**
	 * Adds a debug note
	 *
	 * @param string $note  The note text to log.
	 * @param mixed  $data  Optional data to log with note.
	 *
	 * @return bool  If logged successfully.
	 */
	public function add_debug( $note, $data = false ) {
		return $this->add( $note, $data, 'debug' );
	}

	/**
	 * Adds a warning note
	 *
	 * @param string $note  The note text to log.
	 * @param mixed  $data  Optional data to log with note.
	 *
	 * @return bool  If logged successfully.
	 */
	public function add_warning( $note, $data = false ) {
		return $this->add( $note, $data, 'warning' );
	}

	/**
	 * Get entry notes.
	 *
	 * @param string $entry_id  GF Entry ID.
	 *
	 * @return mixed  If failed, returns false, otherwise notes for entry.
	 */
	public function get( $entry_id = '' ) {
		if ( $entry_id ) {
			$this->set_entry_id( $entry_id );
		}

		if ( ! $this->entry_id ) {
			return false;
		}

		// Check to see if we have notes we still need to log.
		if ( isset( $this->notes[ $this->entry_id ] ) && count( $this->notes[ $this->entry_id ] ) ) {
			$notes = $this->log( $this->entry_id );
		} else {
			$notes = gform_get_meta( $this->entry_id, GFELOQUA_OPT_PREFIX . 'notes' );
		}

		if ( $notes ) {
			return $notes;
		}

		return array();
	}

	/**
	 * Stores any logged notes into the database.
	 *
	 * @param int $return_id  Entry ID to return notes.
	 *
	 * @return array  Notes of $return_id
	 */
	public function log( $return_id = false ) {
		$return_notes = array();

		foreach ( $this->notes as $entry_id => $new_notes ) {
			do_action( 'gfeloqua_log_entry_notes', $entry_id, $this->form_id );

			if ( ! count( $new_notes ) ) {
				continue;
			}

			$old_notes = gform_get_meta( $entry_id, GFELOQUA_OPT_PREFIX . 'notes' );
			if ( $old_notes && count( $old_notes ) ) {
				$notes = array_merge( $old_notes, $new_notes );
			} else {
				$notes = $new_notes;
			}

			gform_update_meta( $entry_id, GFELOQUA_OPT_PREFIX . 'notes', $notes, $this->form_id );

			if ( $return_id && $return_id === $entry_id ) {
				$return_notes = $notes;
			}

			// Clear notes so they aren't logged again.
			unset( $this->notes[ $entry_id ] );
		}

		return $return_notes;
	}

	/**
	 * Clear Notes for entry
	 *
	 * @param int $entry_id  GF Entry ID.
	 *
	 * @return this  Chainable method.
	 */
	public function clear( $entry_id = '', $form_id = '' ) {
		if ( ! $entry_id ) {
			$entry_id = $this->entry_id;
		}
		if ( ! $form_id ) {
			$form_id = $this->form_id;
		}

		gform_update_meta( $entry_id, GFELOQUA_OPT_PREFIX . 'notes', array(), $form_id );

		return $this;
	}

	/**
	 * Call when ready to store notes.
	 */
	public function done() {
		$this->log();
	}
}
