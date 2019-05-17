<?php

namespace ACP\Editing\Admin;

use AC\Admin\Tooltip;
use AC\Form\Element\Checkbox;
use AC\Settings\Admin;

class CustomFieldEditing extends Admin\General {

	public function __construct() {
		parent::__construct( 'custom_field_editable' );
	}

	/**
	 * @return Tooltip
	 */
	private function get_tooltip() {
		$content = sprintf(
			'<p>%s</p><p>%s</p>',
			__( 'Inline edit will display all the raw values in an editable text field.', 'codepress-admin-columns' ),
			sprintf(
				__( "Please read <a href='%s'>our documentation</a> if you plan to use these fields.", 'codepress-admin-columns' ),
				ac_get_site_utm_url( 'documentation/faq/enable-inline-editing-custom-fields/', 'general-settings' )
			)
		);

		return new Tooltip( $this->name, array( 'content' => $content ) );
	}

	private function get_label() {
		return sprintf( '%s %s %s',
			__( 'Enable inline editing for Custom Fields.', 'codepress-admin-columns' ),
			sprintf( __( "Default is %s.", 'codepress-admin-columns' ), '<code>' . __( 'off', 'codepress-admin-columns' ) . '</code>' ),
			$this->get_tooltip()->get_label()
		);
	}

	protected function get_value() {
		return $this->settings->is_empty() ? '0' : parent::get_value();
	}

	/**
	 * @return bool
	 */
	public function is_enabled() {
		return '1' === $this->get_value();
	}

	public function render() {
		$name = sprintf( '%s[%s]', $this->settings->get_name(), $this->name );

		$checkbox = new Checkbox( $name );

		$checkbox
			->set_options( array(
				'1' => $this->get_label(),
			) )
			->set_value( $this->get_value() );

		return $checkbox->render() . $this->get_tooltip()->get_instructions();
	}

}