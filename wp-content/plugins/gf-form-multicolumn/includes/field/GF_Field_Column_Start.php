<?php

	class GF_Field_Column_Start extends GF_Field {
        public $type = 'column_start';

		public function get_field_label( $force_frontend_label, $value ) {
			$field_label = esc_html__( 'Column Start', 'gf-form-multicolumn' );

			return $field_label;
		}

		/*
		 * Add button into the multicolumn field group
		 */
        public function get_form_editor_button() {
            return array(
                'group' => GF_MULTICOLUMN_FIELD_GROUP_TITLE,
                'text'  => $this->get_form_editor_field_title(),
            );
        }

        public function get_form_editor_field_title() {
            return esc_attr__( 'Column Start', 'gfmulticolumn' );
        }
	}
	GF_Fields::register( new GF_Field_Column_Start() );