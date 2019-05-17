<?php

    GFForms::include_addon_framework();

    class GFMultiColumn extends GFAddOn {

        protected $_version = GF_MULTICOLUMN_VERSION;
        protected $_min_gravityforms_version = '1.9';
        protected $_slug = 'gfmulticolumn';
        protected $_path = 'class-gf-form-multicolumn/class-gf-form-multicolumn.php';
        protected $_full_path = __FILE__;
        protected $_short_title = GF_MULTICOLUMN_TITLE; // Used by Gravity Forms for naming in the admin menu
        protected $_title = 'Multi Columns for Gravity Forms'; // Used by Gravity Forms for the title on the admin page

        private $adminInstance;

        private static $_instance = null;

        protected $rowCount;
        protected $rowNumber;
        protected $columnCount;
        protected $columnNumber;

        protected $rowColumnArray = [];
        protected $cssArray = [];

        /**
         * Get an instance of this class.
         *
         * @return GFMultiColumn
         */
        public static function get_instance() {
            if ( self::$_instance === null ) {
                self::$_instance = new GFMultiColumn();
            }

            return self::$_instance;
        }

        /**
         * Handles loading of fields to be included as additional in the form administration.
         */

        public function pre_init() {
            parent::pre_init();

            if ( $this->is_gravityforms_supported() && class_exists( 'GF_Field' ) ) {
                require_once plugin_dir_path( $this->_full_path ) . 'includes/admin/GF_MultiColumn_Admin.php';

                $this->adminInstance = new GF_MultiColumn_Admin ();

                require_once plugin_dir_path( $this->_full_path ) . 'includes/field/GF_Field_Multicolumn_Group.php';

                require_once plugin_dir_path( $this->_full_path ) . 'includes/field/GF_Field_Column_Start.php';
                require_once plugin_dir_path( $this->_full_path ) . 'includes/field/GF_Field_Column_End.php';
                require_once plugin_dir_path( $this->_full_path ) . 'includes/field/GF_Field_Column_Separator.php';
            }


        }

        /**
         * Handles hooks and loading of language files.
         */
        public function init() {
            parent::init();

            // initialise plugin globals
            $this->rowNumber = 1;

            // Use pre render to get a count of the rows and columns
            add_filter( 'gform_pre_render', [ $this, 'gfmc_preRender' ], 10, 4 );
            // Update element containers based on the row/column values obtained
            // only modify in the case of front end, administrator should ignore this
            $this->modify_field();

            add_action( 'gform_enqueue_scripts', [ $this, 'supporting_files' ] );
            /* DEPRECATED METHOD CALLS */
            add_filter( 'gform_pre_render', [ $this, 'preRender_DEPRECATED' ] );
            add_filter( 'gform_field_container', [ $this, 'fieldContainer_DEPRECATED' ], 10, 6 );
            add_action( 'gform_enqueue_scripts', [ $this, "supporting_files_DEPRECATED" ] );
            /* END OF DEPRECATED METHOD CALLS */
        }

        public function supporting_files() {
            wp_enqueue_style( 'cssStylesheets', plugins_url( '/css/gf-form-multicolumn.css', __FILE__ ), false, '3.0.0', 'all' );
        }

        public function supporting_files_DEPRECATED() {
            wp_enqueue_style( 'cssStylesheetsOld', plugins_url( '/css/gf-form-multicolumn-old.css', __FILE__ ), false, '2.2.0', 'all' );
        }


        // # SCRIPTS & STYLES -----------------------------------------------------------------------------------------------

        /**
         * Return the scripts which should be enqueued.
         *
         * @return array
         */
        public function scripts() {
            $scripts = [];

            return array_merge( parent::scripts(), $scripts );
        }

        // # FRONTEND FUNCTIONS --------------------------------------------------------------------------------------------

        protected function form_submit_button( $button, $form ) {

            return $button;
        }

        // Gather details about the form elements, to allow the form to be split based on the column/row breaks
        public function gfmc_preRender( $form ) {

            if ( $form !== false ) {

                //$this->validate_form( $form );

                // Set row and column counts
                $this->rowCount    = 1;
                $this->rowNumber   = 1;
                $this->columnCount = 0;

                $this->build_counters( $form );
            }

            return $form;
        }

        private function modify_field() {
            if ( ! is_admin() ) {
                $this->rowCount = 1;
                add_filter( 'gform_field_container', [ $this, 'modify_li_field_container' ], 10, 6 );
            }
        }

        public function modify_li_field_container( $field_container, $field, $form, $class_attr, $style, $field_content ) {
            return $this->define_output_elements( $field_container, $field );
        }

        public function column_start_containerisation( $field_container, $field, $form, $class_attr, $style, $field_content ) {
            if ( $field->type == 'column_start' ) {
                $field_container = '<li class="column-start"><ul>';
            }

            return $field_container;
        }

        public function column_break_containerisation( $field_container, $field, $form, $class_attr, $style, $field_content ) {
            if ( $field->type == 'column_break' ) {
                $field_container = '</ul></li><li class="column-split"><ul>';
            }

            return $field_container;
        }

        public function column_end_containerisation( $field_container, $field, $form, $css_class, $style, $field_content ) {
            if ( $field->type == 'column_end' ) {
                $field_container = '</ul></li>';
            }

            return $field_container;
        }

        public function column_start_content( $content, $field, $value, $lead_id, $form_id ) {
            if ( $field->type == 'column_start' ) {
                $content = '<li class="column-start"><div>';
            }

            return $content;
        }

        public function column_break_content( $content, $field, $value, $lead_id, $form_id ) {
            if ( $field->type == 'column_break' ) {
                $content = '</div></li><li class="column-split"><div>';
            }

            return $content;
        }

        public function column_end_content( $content, $field, $value, $lead_id, $form_id ) {
            if ( $field->type == 'column_end' ) {
                $content = '</div></li>';
            }

            return $content;
        }

        public function gform_field_content( $content, $field, $value, $lead_id, $form_id ) {
            return $content;
        }


        // # ADMIN FUNCTIONS -----------------------------------------------------------------------------------------------

        /**
         * Creates a custom page for this add-on.
         */

        public function plugin_page() {
            $this->adminInstance->display_page();
        }

        /**
         * Configures the settings which should be rendered on the add-on settings tab.
         *
         * @return array
         */
        /*public function plugin_settings_fields() {
            return [];
            return $this->adminInstance->display_settings_page();

        }

        public function settings_my_custom_field_type() {
            ?>
            <div>
                My custom field contains a few settings:
            </div>
            <?php
            $customFieldInstance = $this->adminInstance->get_custom_field_types ();

            foreach ( $customFieldInstance as $customField ) {
                $this->settings_text = $customField;
                $this->settings_text(
                    [
                        'label'         => $customField['label'],
                        'name'          => $customField['name'],
                        'default_value' => $customField['default_value'],
                    ]
                );
            }
        }*/

        private function validate_form( $form ) {

            if ( $form !== false ) {

                $columnStartCount = 0;
                $columnEndCount   = 0;
                foreach ( $form['fields'] as &$field ) {
                    if ( $field->type === 'column_start' ) {
                        $columnStartCount ++;
                    }
                    if ( $field->type === 'column_end' ) {
                        $columnEndCount ++;
                    }
                }

                /*
                 * CREATE MESSAGE TO ALERT OF ISSUE - Specified by debug level
                 * CREATE SETTINGS PAGE
                 */
                if ( $columnStartCount > $columnEndCount ) {
                    echo( 'Too many column starts have been added to the form' );
                } elseif ( $columnStartCount < $columnEndCount ) {
                    echo( 'Too many column ends have been added to the form' );
                }
            }
        }

        private function build_counters( $form ) {
            $this->columnCount = 0;
            foreach ( $form as $formElements ) {
                if ( is_array( $formElements ) ) {
                    foreach ( $formElements as $formElement ) {
                        if ( is_a( $formElement, 'GF_Field' ) ) {
                            // Only call on class types that extend from GF_Field class
                            $this->process_element_type( $formElement );
                        }
                    }
                }
            }
            $this->columnCount > 0 ? $rowWidth = 100 / $this->columnCount : $rowWidth = 100;
        }

        private function process_element_type( $formObject ) {
            if ( isset ( $formObject->type ) ) {

                if ( $formObject->type == 'column_start' ) {
                    $this->increment_column_count();
                }
                if ( $formObject->type == 'column_break' ) {
                    $this->increment_column_count();
                }
                if ( $formObject->type == 'column_end' ) {
                    // Add the row and column count to the array
                    $this->rowColumnArray[ $this->rowCount ] = $this->columnCount;
                    $this->columnCount                       = 0;
                    $this->rowCount ++;
                }
                if ( $formObject->type == 'row_break' || $formObject->type == 'page' ) {
                    // Add the row and column count to the array
                    $this->columnCount = 0;
                }
            }
        }

        private function increment_column_count() {
            $this->columnCount ++;
        }

        private function define_output_elements( $field_container, $field ) {
            if ( $field->type == 'column_start' ) {
                $this->increment_column_count();

                return '<li class="gfmc-column gfmc-row-' . $this->rowNumber . '-column gfmc-row-' . $this->rowNumber . '-col-' . $this->columnCount . '-of-' . $this->rowColumnArray[ $this->rowNumber ] . '" style="width: ' . floor( 100 / $this->rowColumnArray[ $this->rowNumber ] ) . '%;"><ul>';
            }
            if ( $field->type == 'column_break' ) {
                $this->increment_column_count();

                return '</ul></li><li class="gfmc-column gfmc-row-' . $this->rowNumber . '-column gfmc-row-' . $this->rowNumber . '-col-' . $this->columnCount . '-of-' . $this->rowColumnArray[ $this->rowNumber ] . '" style="width: ' . floor( 100 / $this->rowColumnArray[ $this->rowNumber ] ) . '%;"><ul>';
            }
            if ( $field->type == 'column_end' ) {
                $this->columnCount = 0;
                $this->rowNumber ++;

                return ( '</ul></li>' );
            }
            if ( $field->type == 'row_break' || $field->type == 'page' ) {
                $this->columnCount = 0;

                return '</ul><ul class="gform_fields top_label form_sublabel_below description_below">';
            }

            return ( $field_container );
        }

        /*
         *
         * OLD FUNCTIONALITY INCLUDED FOR BACKWARD COMPATIBILITY
         *
         */
        function fieldContainer_DEPRECATED( $field_container, $field, $form, $css_class, $style, $field_content ) {
            if ( IS_ADMIN ) {
                return $field_container;
            } // only modify HTML on the front end

            // Variable to specify the width of the column
            $columnWidth = null;

            // Calculate width value based on the number of columns in the row, which has been coded into the cssClass
            $columnCountStartPos = strpos( $field['cssClass'], '-of-' );
            if ( $columnCountStartPos !== false ) {
                $columnTotalForRowTerminator = strpos( substr( $field['cssClass'], $columnCountStartPos + 4 ), ' ' );
                if ( $columnTotalForRowTerminator > 0 ) {
                    $columnTotalForRow = substr( $field['cssClass'], $columnCountStartPos + 4, $columnTotalForRowTerminator );
                } else {
                    $columnTotalForRow = substr( $field['cssClass'], $columnCountStartPos + 4 );
                }
                $columnWidth = ' width: ' . floor( 100 / $columnTotalForRow ) . '%;';
            }

            $columnSpecificCSSTextPos = strpos( $field['cssClass'], 'column-count-' );
            $columnSpecificCSSText    = null;
            if ( $columnSpecificCSSTextPos !== false ) {
                $columnSpecificCSSText = substr( $field['cssClass'], $columnSpecificCSSTextPos );
            }

            // Break the existing cssClass definition to see if the previously set markers for the start and end columns have been set
            if ( $field['type'] == 'dividerStart' && strpos( $field['cssClass'], 'start-divider' ) !== false ) {
                $this->cssArray[] = '<style>.' . $columnSpecificCSSText . ' { ' . $columnWidth . ' }</style>';
                $field_container = '<li class="' . $columnSpecificCSSText . ' divider-list-item multicolumn-start"><div class="multicolumn-wrapper"><ul>';
            } elseif ( $field['type'] == 'dividerStart' ) {
                $this->cssArray[] = '<style>.' . $columnSpecificCSSText . ' { ' . $columnWidth . ' }</style>';
                $field_container = '<li class="' . $columnSpecificCSSText . ' divider-list-item"><div><ul>';
            }
            if ( $field['type'] == 'dividerEnd' && strpos( $field['cssClass'], 'end-divider' ) !== false ) {
                $field_container = '</ul></div></li>';
            } elseif ( $field['type'] == 'dividerEnd' ) {
                $field_container = '</ul>';
            }
            if ( $field['type'] == 'row-divider' ) {
                $field_container = '<div class="row-divider"></div>';
            }

            return $field_container;
        }


        function preRender_DEPRECATED( $form ) {

            if ( $form !== false ) {
                $dividerStartCounter = 0;
                $dividerEndCounter   = 0;

                $columnCount = 0;
                $rowCount    = 1;

                $rowColumnArray = [];

                // Set type of field & CSS if section settings have been dfined as required for column definition: section & split-start/split-end CSS
                foreach ( $form['fields'] as $field ) {
                    if ( $field['type'] == 'section' && strpos( $field['cssClass'], 'split-start' ) !== false ) {
                        // Set row and column details for later break up based on row and column position
                        $field['calculationFormula'] = 'row-' . $rowCount . 'column-' . $columnCount ++;
                        $rowColumnArray[ $rowCount ] = $columnCount;

                        $field['type']       = 'dividerStart';
                        ++$dividerStartCounter;
                        $field['cssClass']   .= ' dividerStart-' . $dividerStartCounter;
                    } elseif ( $field['type'] == 'section' && strpos( $field['cssClass'], 'split-end' ) !== false ) {
                        $field['type']     = 'dividerEnd';
                        ++$dividerEndCounter;
                        $field['cssClass'] .= ' dividerEnd-' . $dividerEndCounter;
                    } elseif ( $field['type'] == 'section' && strpos( $field['cssClass'], 'new-row' ) !== false ) {
                        $field['type'] = 'row-divider';
                        // Reset column counter
                        $columnCount       = 0;
                        $rowCount          += $rowCount;
                        $field['cssClass'] .= ' row-divider';
                    }
                }

                // Loop again through the field list to ensure that the first and last column are identified, based on the numbers defined in the previous loop
                foreach ( $form['fields'] as $field ) {
                    if ( $field['type'] == 'dividerStart' && strpos( $field['cssClass'], 'dividerStart-1' ) ) {
                        $field['cssClass'] .= ' start-divider';
                    } elseif ( $field['type'] == 'dividerEnd' && strpos( $field['cssClass'], 'dividerEnd-' . $dividerEndCounter ) ) {
                        $field['cssClass'] .= ' end-divider';
                    }
                    // Add column count to all dividers to ensure that this can be calculated as the split quantity later
                    if ( $field['type'] == 'dividerStart' ) {
                        // Set cssClass details to allow for break up of rows and columns based on
                        // Variable to hold the value stored in the calculationFormula parameter that relates to the row and column count
                        // Not that this will hold the value for the commencement of the column string, and this defines the end of the row count number
                        $endOfRowCount   = strpos( $field['calculationFormula'], 'column-' );
                        $rowNumberLength = $endOfRowCount - 4;
                        $rowNumber       = substr( $field['calculationFormula'], 4, $rowNumberLength );
                        $columnNumber    = substr( $field['calculationFormula'], $endOfRowCount + 7 ) + 1;
                        if ( strpos( $field['cssClass'], 'column-count-' ) === false ) {
                            $field['cssClass'] .= ' column-count-' . $columnNumber . '-of-' . $rowColumnArray[ $rowNumber ];
                        }
                    }
                }
            }

            return $form;
        }

        /*
         *
         * END OF OLD FUNCTIONALITY INCLUDED FOR BACKWARD COMPATIBILITY
         *
         */

    }