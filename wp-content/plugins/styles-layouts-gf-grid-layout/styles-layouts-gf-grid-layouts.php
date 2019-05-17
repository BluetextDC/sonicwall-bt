<?php

/*
 * Plugin Name: Grid Layout Gravity Forms
 * Plugin URI: https://wpmonks.com/downloads/grid-layout
 * Description: Make Grid Layout of Gravity Form fields
 * Author: Sushil Kumar
 * Author URI: https://wpmonks.com
 * Version: 2.1
 * License: GPLv2
 */
// don't load directly
if ( !defined( 'ABSPATH' ) ) die( '-1' );
//file to send automatic updates
include_once WP_PLUGIN_DIR . "/" . basename( dirname( __FILE__ ) ).'/update.php';
//set constants for plugin directory and plugin url
define( "GF_STLA_GRID_LAYOUT_DIR", WP_PLUGIN_DIR . "/" . basename( dirname( __FILE__ ) ) );
define( "GF_STLA_GRID_LAYOUT_URL", plugins_url() . "/" . basename( dirname( __FILE__ ) ) );

Class Gravity_Grid_Layout{
	public $all_found_forms_ids = array();

	function __construct(){

		//add_action( 'wp', array( $this, 'gf_stla_get_forms_shortcode' ) );
		$this->selected_form_id = get_option('gf_stla_select_form_id' );
		add_action('gf_stla_add_theme_section', array($this,'gf_stla_add_grid_layout_section'),13, 2 );
		add_filter( 'gform_field_css_class', array($this,'gf_stla_grid_layout_divide_colums'), 10, 3 );
		if ( class_exists( 'RGFormsModel' ) ) {
		$this->get_form_fields = RGFormsModel::get_form_meta( $this->selected_form_id );
	}
		$this->all_found_forms_ids = '';
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'gf_stla_grid_layout_js' ) );
	} //constructor ends here

function gf_stla_grid_layout_js(){

	wp_enqueue_script('hide_grid_layout_section', GF_STLA_GRID_LAYOUT_URL. '/js/hide-grid-layout.js', array( 'jquery' ), '', true );
}



function gf_stla_add_grid_layout_section($wp_customize, $current_form_id){

	$column_list = array('stla_default' => 'Default Layout','gf_left_half'=> 'Left (2 column layout)', 'gf_right_half'=> 'Right (2 column layout)', 'gf_left_third' => 'Left (3 column layout)', 'gf_middle_third' => 'Middle (3 column layout)', 'gf_right_third' => 'Right (3 column layout)');
	$column_list_checkboxes = array('stla_default' => 'Default Layout','gf_left_half'=> 'Left (2 column layout)', 'gf_right_half'=> 'Right (2 column layout)', 'gf_left_third' => 'Left (3 column layout)', 'gf_middle_third' => 'Middle (3 column layout)', 'gf_right_third' => 'Right (3 column layout)', 'gf_list_2col' => 'Divide in 2 Columns', 'gf_list_3col' => 'Divide in 3 Columns', 'gf_list_4col' => 'Divide in 4 Columns', 'gf_list_5col' => 'Divide in 5 Columns', 'gf_list_inline' => 'Inline List');
	$field_count = count($this->get_form_fields["fields"]);
	

	//Add Field Icons section
		$wp_customize->add_section( 'gf_stla_form_id_grid_layout' , array(
			'title' => 'Grid Layout',
			'panel' => 'gf_stla_panel',
		  ) );

		
		for( $i=0; $i<$field_count; $i++){
			$field_object = $this->get_form_fields["fields"][$i];
			if ( $field_object->type == 'checkbox' || $field_object->type == 'radio' ) {
					$wp_customize->add_setting( 'gf_stla_form_id_grid_layout_'.$current_form_id.'['.$field_object->type.']['.$field_object->id.']' , array(
				  'default'     => 'stla_default',
				  //'transport'   => 'postMessage',
				  'type' => 'option'
			  ) );

				  $wp_customize->add_control('gf_stla_form_id_grid_layout_'.$current_form_id.'['.$field_object->type.']['.$field_object->id.']',   array(
					'type' => 'select',
					'priority' => 10, // Within the section.
					'section' => 'gf_stla_form_id_grid_layout', // Required, core or custom.
					'label' => __( $field_object->label." Position"),
					'choices'        => $column_list_checkboxes,
			  )	);	



			} else {
			
				//add field icons settings and controls 
				
				$wp_customize->add_setting( 'gf_stla_form_id_grid_layout_'.$current_form_id.'['.$field_object->type.']['.$field_object->id.']' , array(
				  'default'     => 'stla_default',
				  //'transport'   => 'postMessage',
				  'type' => 'option'
			  ) );

				  $wp_customize->add_control('gf_stla_form_id_grid_layout_'.$current_form_id.'['.$field_object->type.']['.$field_object->id.']',   array(
					'type' => 'select',
					'priority' => 10, // Within the section.
					'section' => 'gf_stla_form_id_grid_layout', // Required, core or custom.
					'label' => __( $field_object->label." Position"),
					'choices'        => $column_list,
			  )	);	
}
 
   }
			
	}



function gf_stla_grid_layout_divide_colums( $classes, $field, $form ) {


	$column_layout_data = get_option( 'gf_stla_form_id_grid_layout_'.$form['id'] );
	if ( !empty( $column_layout_data ) ) {
		 if ( array_key_exists( $field->type, $column_layout_data ) ) {
		 		$column_layout_data_fields = $column_layout_data[ $field->type ];
		 		if ( array_key_exists( $field->id, $column_layout_data_fields ) ) {
						$classes .= ' '.$column_layout_data_fields[ $field->id ];
				}
		}
	}

	return $classes;
	
}			

} //class ends here

add_action( 'plugins_loaded', 'sk_gravity_grid_layout');

function sk_gravity_grid_layout(){
	new Gravity_Grid_Layout();
}
