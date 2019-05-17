<?php

	class GF_Field_Multicolumn_Group extends GF_Field {
        public $type = 'multicolumn_group';

        private $button_group = GF_MULTICOLUMN_FIELD_GROUP_TITLE;

        /*
         * Prevent adding a separate button for this field group
         */
        public function get_form_editor_button() {
            return null;
        }

        public function add_button( $field_groups ) {
            $field_groups = $this->add_custom_field_group( $field_groups );

            return parent::add_button( $field_groups );
        }

        public function add_custom_field_group( $field_groups ) {
            foreach ( $field_groups as $field_group ) {
                if ( $field_group['name'] === $this->button_group ) {
                    return $field_groups;
                }
            }

            $field_groups[] = array(
                'name'   => $this->button_group,
                'label'  => __( $this->button_group, 'gfmulticolumn' ),
                'fields' => array()
            );

            return $field_groups;
        }
	}
    GF_Fields::register( new GF_Field_Multicolumn_Group() );