<?php

    class GF_MultiColumn_Admin {
        protected $supportURL = 'https://wordpress.org/support/plugin/gf-form-multicolumn';

        public function display_page() {
            echo( '<div class="gfmc-content">' );
            echo( '<p>Multiple Columns for Gravity Forms is developed and maintained by <a href="https://www.webholism.com" target="_blank" class="gfmc-developer-name">WebHolism</a></p>' );
            echo( '<p>If you encounter any issues while using the Multiple Columns plugin, please check the <a href="' . $this->supportURL . '" target="_blank">support</a> on the Wordpress Forum for this plugin.  Please note that it is often of great assistance to see the form in question.<br>Please send any exported .json forms through to <a href="mailto:hello@webholism.com?Subject=Gravity Forms Multiple Column Issue" target="_blank">hello@webholism.com</a> .</p>' );
            echo( '<p>See quick usage guides on our associated <a href="">YouTube</a> channel</p>' );
            echo( '</div>' );
        }

        public function display_settings_page() {
            return [];
        }

        public function get_custom_field_types() {
            $fieldTypeList = [];

            return $fieldTypeList;
        }

        protected function create_custom_type ( $label, $name, $default_value ) {
            return [];
        }
    }