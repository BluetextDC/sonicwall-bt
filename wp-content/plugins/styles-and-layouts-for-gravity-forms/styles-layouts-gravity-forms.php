<?php
/*
Plugin Name: Styles & Layouts Gravity Forms
Plugin URI:  http://wpmonks.com/styles-layouts-gravity-forms
Description: Create beautiful styles for your gravity forms
Version:     4.1.4
Author:      Sushil Kumar
Author URI:  http://wpmonks.com/
License:     GPL2License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

define( 'GF_STLA_DIR', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) );
define( 'GF_STLA_URL', plugins_url() . '/' . basename( dirname( __FILE__ ) ) );
define( 'GF_STLA_STORE_URL', 'https://wpmonks.com' );

if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	include_once GF_STLA_DIR . '/admin-menu/EDD_SL_Plugin_Updater.php';
}
include_once 'helpers/customizer-controls/margin-padding.php';
include_once 'helpers/utils/responsive.php';

require_once GF_STLA_DIR . '/admin-menu/licenses.php';
require_once GF_STLA_DIR . '/admin-menu/addons.php';
require_once GF_STLA_DIR . '/admin-menu/welcome-page.php';

// Main class of Styles & layouts Gravity Forms
class Gravity_customizer_admin {

	// public $all_found_forms_ids = array();
	private $trigger;
	private $stla_form_id;
	/**
	 *  method for all hooks
	 *
	 * @since  v1.0
	 * @author Sushil Kumar
	 */
	public function __construct() {
		global $wp_version;
		// $this->all_found_forms_ids = '';
		add_action( 'customize_register', array( $this, 'customize_register' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_controls_enqueue_scripts' ) );
		add_action( 'customize_preview_init', array( $this, 'customize_preview_init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		register_activation_hook( __FILE__, array( $this, 'gf_stla_welcome_screen_activate' ) );
		add_action( 'admin_init', array( $this, 'gf_stla_welcome_screen_do_activation_redirect' ) );
		add_action( 'customize_save_after', array( $this, 'customize_save_after' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_filter( 'gform_toolbar_menu', array( $this, 'gform_toolbar_menu' ), 10, 2 );
		add_action( 'gform_enqueue_scripts', array( $this, 'gform_enqueue_scripts' ), 10, 2 );
		add_action( 'upgrader_process_complete', array( $this, 'stla_upgrade_completed' ), 10, 2 );
		if ( class_exists( 'GFForms' ) ) {
			add_action( 'template_redirect', array( $this, 'gf_stla_preview_template' ) );
			$this->trigger = 'stla-gravity-forms-customizer';
			// only load controls for this plugin
			if ( isset( $_GET[ $this->trigger ] ) ) {
				if ( ! empty( $_GET['stla_form_id'] ) ) {
					$this->stla_form_id = sanitize_text_field( wp_unslash( $_GET['stla_form_id'] ) );
				}
				add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
				//add_filter( 'customize_register', array( $this, 'remove_sections' ), 60 );
				//add_filter( 'customize_control_active', array( $this, 'control_filter' ), 10, 2 );
				// add our custom query vars to the whitelist
				// add_action( 'template_redirect', array( $this, 'load_preview_template' ) );
			}
		}
		// add_filter( 'gform_pre_render', array( $this, 'gf_stla_show_css_frontend' ) );
		//add_action( 'init', array( $this, 'gf_stla_enable_admin_bar' ) );
		//add_action( 'upgrader_process_complete', array( $this, 'set_migrate_transient' ), 10, 2 );
		//add_action( 'init', array( $this, 'migrate_to_4' ) );
	}

	function wp_enqueue_scripts() {
		if(is_customize_preview()){
			wp_enqueue_script( 'stla_frontend_wp', GF_STLA_URL . '/js/frontend.js', array( 'jquery', 'customize-preview' ), '', true );
		}
	}



	function stla_upgrade_completed( $upgrader_object, $options ) {
		//$updated_plugins = array();
		// The path to our plugin's main file
		$our_plugin = plugin_basename( __FILE__ );
		// If an update has taken place and the updated type is plugins and the plugins element exists
		if ( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
			// Iterate through the plugins being updated and check if ours is there
			foreach ( $options['plugins'] as $plugin ) {
				if ( $plugin == $our_plugin ) {
					// Set a transient to record that our plugin has just been updated
					// array_push( $updated_plugins, $plugin );
					if ( class_exists( 'RGFormsModel' ) ) {
						$forms = RGFormsModel::get_forms( null, 'title' );
						$field_names = array( 'padding', 'margin' );
						$field_types = array( 'form-wrapper', 'checkbox-inputs', 'confirmation-message', 'dropdown-fields', 'error-message', 'field-descriptions', 'field-labels', 'field-sub-labels', 'form-description', 'form-header', 'form-title', 'form-wrapper', 'list-field', 'paragraph-textarea', 'placeholders', 'radio-inputs', 'section-break-description', 'section-break-title', 'submit-button', 'text-fields' );
						foreach ( $forms as $form ) {
							$form_id = $form->id;
							$stla_options = get_option( 'gf_stla_form_id_' . $form_id );
							if ( ! empty( $stla_options ) ) {
								foreach ( $field_types as $field_type ) {
									foreach ( $field_names as $field_name ) {
										if ( isset( $stla_options[$field_type][$field_name] ) ) {
											$value = trim( $stla_options[$field_type][$field_name] );
											$values = preg_split( '/[\s]+/', $value );;
											$count = sizeof( $values );
											switch ( $count ) {
											case 4:
												$stla_options[$field_type][$field_name . '-top'] = $values[0];
												$stla_options[$field_type][$field_name . '-right'] = $values[1];
												$stla_options[$field_type][$field_name . '-bottom'] = $values[2];
												$stla_options[$field_type][$field_name . '-left'] = $values[3];
												unset( $stla_options[$field_type][$field_name] );
												update_option( 'gf_stla_form_id_' . $form_id, $stla_options );
												break;

											case 3:
												$stla_options[$field_type][$field_name . '-top'] = $values[0];
												$stla_options[$field_type][$field_name . '-right'] = $values[1];
												$stla_options[$field_type][$field_name . '-bottom'] = $values[2];
												$stla_options[$field_type][$field_name . '-left'] = $values[1];
												unset( $stla_options[$field_type][$field_name] );
												update_option( 'gf_stla_form_id_' . $form_id, $stla_options );
												break;

											case 2:
												$stla_options[$field_type][$field_name . '-top'] = $values[0];
												$stla_options[$field_type][$field_name . '-right'] = $values[1];
												$stla_options[$field_type][$field_name . '-bottom'] = $values[0];
												$stla_options[$field_type][$field_name . '-left'] = $values[1];
												unset( $stla_options[$field_type][$field_name] );
												update_option( 'gf_stla_form_id_' . $form_id, $stla_options );
												break;

											case 1:
												$stla_options[$field_type][$field_name . '-top'] = $values[0];
												$stla_options[$field_type][$field_name . '-right'] = $values[0];
												$stla_options[$field_type][$field_name . '-bottom'] = $values[0];
												$stla_options[$field_type][$field_name . '-left'] = $values[0];
												unset( $stla_options[$field_type][$field_name] );
												update_option( 'gf_stla_form_id_' . $form_id, $stla_options );
												break;
											}
										}
									}
								}
							}
						}
					}

				}
			}
		}
	}

	public function gform_enqueue_scripts( $form, $is_ajax ) {
		if ( is_customize_preview() ) {
			wp_enqueue_style( 'stla_live_preview', GF_STLA_URL . '/css/live-preview.css' );
		}

		$style_current_form = get_option( 'gf_stla_form_id_' . $form['id'] );

		// is_admin doesn't work in gutenberg. used REST_REQUEST for this
		if ( ! is_admin() && ! defined( 'REST_REQUEST' ) ) {

			if( ! empty( $style_current_form ) ) {
				$css_form_id       = $form['id'];
				$main_class_object = $this;
				include 'display/class-styles.php';
			}
		}
		do_action( 'gf_stla_after_post_style_display', $this );

	}

	/**
	 * no longer used since v4.0
	 */

	// public function gf_stla_enable_admin_bar() {
	//  $gf_stla_genreal_options = get_option( 'gf_stla_general_settings' );
	//  $is_admin_bar_enabled    = isset( $gf_stla_genreal_options['admin-bar'] ) ? $gf_stla_genreal_options['admin-bar'] : true;
	//  if ( current_user_can( 'manage_options' ) && $is_admin_bar_enabled ) {
	//   add_filter( 'show_admin_bar', '__return_true', 999 );
	//  }
	// }

	/**
	 *  enqueue js file that autosaves the form selection in database
	 *
	 * @since  v1.0
	 * @author Sushil Kumar
	 * @return null
	 */
	public function customize_controls_enqueue_scripts() {
		wp_enqueue_style( 'stla-customizer-css', GF_STLA_URL . '/css/customizer/stla-customizer-controls.css' );
		wp_enqueue_script( 'gf_stla_auto_save_form', GF_STLA_URL . '/js/customizer-controls/auto-save-form.js', array( 'jquery' ), '', true );
		wp_enqueue_script( 'gf_stla_customize_controls', GF_STLA_URL . '/js/customizer-controls/customizer-controls.js', array( 'jquery' ), '', true );
	}

	/**
	 *  shows live preview of css changes
	 *
	 * @since  v1.0
	 * @author Sushil Kumar
	 * @return null
	 */
	public function customize_preview_init() {
		$current_form_id = get_option( 'gf_stla_select_form_id' );
		wp_enqueue_script( 'gf_stla_show_live_changes', GF_STLA_URL . '/js/live-preview/live-preview-changes.js', array( 'jquery', 'customize-preview' ), '', true );
		wp_enqueue_script( 'gf_stla_customizer_edit_shortcuts', GF_STLA_URL . '/js/live-preview/edit-shortcuts.js', array( 'jquery', 'customize-preview' ), '', true );
		wp_localize_script( 'gf_stla_show_live_changes', 'gf_stla_localize_current_form', array( 'formId' =>  $current_form_id ) );
		wp_localize_script( 'gf_stla_customizer_edit_shortcuts', 'gf_stla_localize_edit_shortcuts', array( 'formId' => $current_form_id ) );
	}

	/**
	 *  Function that adds panels, sections, settings and controls
	 *
	 * @since  v1.0
	 * @author Sushil Kumar
	 * @param main       wp customizer object
	 * @return null
	 */

	public function customize_register( $wp_customize ) {
		if ( isset( $this->stla_form_id ) ) {
			update_option( 'gf_stla_select_form_id', $this->stla_form_id );
		}
		include 'helpers/fonts.php';
		$current_form_id = get_option( 'gf_stla_select_form_id' );
		$border_types    = array(
			// 'inherit' => 'Inherit',
			'solid'   => 'Solid',
			'dotted'  => 'Dotted',
			'dashed'  => 'Dashed',
			'double'  => 'Double',
			'groove'  => 'Groove',
			'ridge'   => 'Ridge',
			'inset'   => 'Inset',
			'outset'  => 'Outset',
		);
		$align_pos       = array(
			'left'    => 'Left',
			'center'  => 'Center',
			'right'   => 'Right',
			'justify' => 'Justify',
		);

		$font_style_choices = array(
			'bold'  => 'Bold',
			'italic' => 'Italic',
			'uppercase' => 'Uppercase',
			'underline' => 'underline'
		);


		$wp_customize->add_panel(
			'gf_stla_panel', array(
				'title'       => __( 'Styles & Layouts Gravity Forms' ),
				'description' => '<p> Craft your Forms</p>', // Include html tags such as <p>.
				'priority'    => 160, // Mixed with top-level-section hierarchy.
			)
		);
		include 'includes/form-select.php';
		if ( ! array_key_exists( 'autofocus', $_GET ) || ( array_key_exists( 'autofocus', $_GET ) && $_GET['autofocus']['panel'] !== 'gf_stla_panel' ) ) {
			// write_log($_GET);
			$wp_customize->add_setting(
				'gf_stla_hidden_field_for_form_id', array(
					'default'   => $current_form_id,
					'transport' => 'postMessage',
					'type'      => 'option',
				)
			);

			$wp_customize->add_control(
				'gf_stla_hidden_field_for_form_id', array(
					'type'        => 'hidden',
					'priority'    => 10, // Within the section.
					'section'     => 'gf_stla_select_form_section', // Required, core or custom.
					'input_attrs' => array(
						'value' => $current_form_id,
						'id'    => 'gf_stla_hidden_field_for_form_id',
					),
				)
			);
		}
		//include 'includes/custom-controls/margin-padding.php';
		include 'helpers/customizer-controls/desktop-text-input.php';
		include 'helpers/customizer-controls/tab-text-input.php';
		include 'helpers/customizer-controls/mobile-text-input.php';
		include 'helpers/customizer-controls/text-alignment.php';
		include 'helpers/customizer-controls/font-style.php';
		include 'helpers/customizer-controls/range-slider.php';
		include 'helpers/customizer-controls/custom-controls.php';
		include 'includes/customizer-addons.php';
		include 'includes/general-settings.php';
		do_action( 'gf_stla_add_theme_section', $wp_customize, $current_form_id );
		include 'includes/form-wrapper.php';
		include 'includes/form-header.php';
		include 'includes/form-title.php';
		include 'includes/form-description.php';
		// //include 'includes/outer-shadow.php';
		// //include 'includes/inner-shadow.php';
		include 'includes/field-labels.php';
		include 'includes/field-sub-labels.php';
		include 'includes/placeholders.php';
		include 'includes/field-descriptions.php';
		include 'includes/text-fields.php';
		include 'includes/dropdown-fields.php';
		include 'includes/radio-inputs.php';
		include 'includes/checkbox-inputs.php';
		include 'includes/paragraph-textarea.php';
		include 'includes/section-break-title.php';
		include 'includes/section-break-description.php';
		include 'includes/list-field.php';
		include 'includes/submit-button.php';
		include 'includes/confirmation-message.php';
		include 'includes/error-message.php';
	} // main customizer function ends here

	public function gf_sb_get_saved_styles( $form_id, $category, $important = '' ) {
		if ( is_customize_preview() ) {
			$important = '';
		}


		$settings = get_option( 'gf_stla_form_id_' . $form_id );
		if ( empty( $settings ) ) {
			return;
		}
		$input_styles = '';
		// if ( isset( $settings[ $category ]['use-outer-shadows'] ) ) {
		//  $input_styles .= empty( $settings[ $category ]['horizontal-offset'] ) ? 'box-shadow: 0px ' : 'box-shadow:' . $settings[ $category ]['outer-horizontal-offset'] . ' ';
		//  $input_styles .= empty( $settings[ $category ]['outer-vertical-offset'] ) ? '0px ' : $settings[ $category ]['outer-vertical-offset'] . ' ';
		//  $input_styles .= empty( $settings[ $category ]['outer-blur-radius'] ) ? '0px ' : $settings[ $category ]['outer-blur-radius'] . ' ';
		//  $input_styles .= empty( $settings[ $category ]['outer-spread-radius'] ) ? '0px ' : $settings[ $category ]['outer-spread-radius'] . ' ';
		//  $input_styles .= empty( $settings[ $category ]['outer-shadow-color'] ) ? ';' : $settings[ $category ]['outer-shadow-color'] . ' ';
		//  if ( isset( $settings[ $category ]['use-inner-shadows'] ) ) {
		//   $input_styles .= empty( $settings[ $category ]['inner-horizontal-offset'] ) ? ', 0px ' : ', ' . $settings[ $category ]['inner-horizontal-offset'] . ' ';
		//   $input_styles .= empty( $settings[ $category ]['inner-vertical-offset'] ) ? '0px ' : $settings[ $category ]['inner-vertical-offset'] . ' ';
		//   $input_styles .= empty( $settings[ $category ]['inner-blur-radius'] ) ? '0px ' : $settings[ $category ]['inner-blur-radius'] . ' ';
		//   $input_styles .= empty( $settings[ $category ]['inner-spread-radius'] ) ? '0px ' : $settings[ $category ]['inner-spread-radius'] . ' ';
		//   $input_styles .= empty( $settings[ $category ]['inner-shadow-color'] ) ? ';' : $settings[ $category ]['inner-shadow-color'] . ' inset; ';
		//  } else {
		//   $input_styles .= ';';
		//  }
		// }

		if ( ! empty( $settings[ $category ]['font-style'] ) ) {
			$font_styles = explode( '|', $settings[ $category ]['font-style'] );

				foreach ( $font_styles as $value ) {
					switch ( $value ) {
					case 'bold':
						$input_styles .= 'font-weight: bold;';
						break;
					case 'italic':
						$input_styles .= 'font-style: italic;';
						break;
					case 'uppercase':
						$input_styles .= 'text-transform: uppercase;';
						break;
					case 'underline':
						$input_styles .= 'text-decoration: underline;';
						break;
					default:
						break;
					}
			}
		}

		$input_styles .= empty( $settings[ $category ]['color'] ) ? '' : 'color:' . $settings[ $category ]['color'] . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['background-color'] ) ? '' : 'background-color:' . $settings[ $category ]['background-color'] . ' ' . $important . ';';
		// Gradient for themes
		$input_styles .= empty( $settings[ $category ]['background-color1'] ) ? '' : 'background:-webkit-linear-gradient(to left,' . $settings[ $category ]['background-color'] . ',' . $settings[ $category ]['background-color1'] . ') ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['background-color1'] ) ? '' : 'background:linear-gradient(to left,' . $settings[ $category ]['background-color'] . ',' . $settings[ $category ]['background-color1'] . ') ' . $important . ';';
		// $input_styles.= empty( $settings[$category]['padding'] )?'':'padding:'. $settings[$category]['padding'].';';
		$input_styles .= empty( $settings[ $category ]['width'] ) ? '' : 'width:' . $settings[ $category ]['width'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['width'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['height'] ) ? '' : 'height:' . $settings[ $category ]['height'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['height'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['title-position'] ) ? '' : 'text-align:' . $settings[ $category ]['title-position'] . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['text-align'] ) ? '' : 'text-align:' . $settings[ $category ]['text-align'] . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['line-height'] ) ? '' : 'line-height:' . $settings[ $category ]['line-height'] . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['error-position'] ) ? '' : 'text-align:' . $settings[ $category ]['error-position'] . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['description-position'] ) ? '' : 'text-align:' . $settings[ $category ]['description-position'] . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['title-color'] ) ? '' : 'color:' . $settings[ $category ]['title-color'] . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['font-color'] ) ? '' : 'color:' . $settings[ $category ]['font-color'] . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['description-color'] ) ? '' : 'color:' . $settings[ $category ]['description-color'] . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['button-color'] ) ? '' : 'background-color:' . $settings[ $category ]['button-color'] . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['description-color'] ) ? '' : 'color:' . $settings[ $category ]['description-color'] . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['font-family'] ) ? '' : 'font-family:' . $settings[ $category ]['font-family'] . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['font-size'] ) ? '' : 'font-size:' . $settings[ $category ]['font-size'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['font-size'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['max-width'] ) ? '' : 'width:' . $settings[ $category ]['max-width'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['max-width'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['maximum-width'] ) ? '' : 'width:' . $settings[ $category ]['maximum-width'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['maximum-width'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['margin'] ) ? '' : 'margin:' . $this->gf_stla_add_px_to_padding_margin( $settings[ $category ]['margin'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['padding'] ) ? '' : 'padding:' . $this->gf_stla_add_px_to_padding_margin( $settings[ $category ]['padding'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['border-size'] ) ? '' : 'border-width:' . $settings[ $category ]['border-size'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['border-size'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['border-color'] ) ? '' : 'border-color:' . $settings[ $category ]['border-color'] . ' ' . $important . ';';
		if( !empty( $settings[ $category ]['border-size'] ) ){
            $input_styles .= empty( $settings[ $category ]['border-type'] ) ? 'border-style:solid;' : 'border-style:' . $settings[ $category ]['border-type'] . ' ' . $important . ';';
        }
		// $input_styles .= empty( $settings[ $category ]['border-type'] ) ? '' : 'border-style:' . $settings[ $category ]['border-type'] . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['border-bottom'] ) ? '' : 'border-bottom-style:' . $settings[ $category ]['border-bottom'] . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['border-bottom-size'] ) ? '' : 'border-bottom-width:' . $settings[ $category ]['border-bottom-size'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['border-bottom-size'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['border-bottom-color'] ) ? '' : 'border-bottom-color:' . $settings[ $category ]['border-bottom-color'] . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['background-image-url'] ) ? '' : 'background: url(' . $settings[ $category ]['background-image-url'] . ') no-repeat ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['border-bottom-color'] ) ? '' : 'border-bottom-color:' . $settings[ $category ]['border-bottom-color'] . ';';
		if ( isset( $settings[ $category ]['display'] ) ) {
			$input_styles .= $settings[ $category ]['display'] ? 'display:none ' . $important . ';' : 'display:inherit ' . $important . ';';
		}
		if ( ! empty( $settings[ $category ]['border-radius'] ) ) {
			$input_styles .= 'border-radius:' . $settings[ $category ]['border-radius'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['border-radius'] ) . ' ' . $important . ';';
			$input_styles .= '-web-border-radius:' . $settings[ $category ]['border-radius'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['border-radius'] ) . ' ' . $important . ';';
			$input_styles .= '-moz-border-radius:' . $settings[ $category ]['border-radius'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['border-radius'] ) . ' ' . $important . ';';
		}
		$input_styles .= empty( $settings[ $category ]['custom-css'] ) ? '' : $settings[ $category ]['custom-css'] . ';';

		$input_styles .= empty( $settings[ $category ]['padding-left'] ) ? '' : 'padding-left:' . $settings[ $category ]['padding-left'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['padding-left'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['padding-right'] ) ? '' : 'padding-right:' . $settings[ $category ]['padding-right'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['padding-right'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['padding-top'] ) ? '' : 'padding-top:' . $settings[ $category ]['padding-top'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['padding-top'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['padding-bottom'] ) ? '' : 'padding-bottom:' . $settings[ $category ]['padding-bottom'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['padding-bottom'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['margin-left'] ) ? '' : 'margin-left:' . $settings[ $category ]['margin-left'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['margin-left'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['margin-right'] ) ? '' : 'margin-right:' . $settings[ $category ]['margin-right'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['margin-right'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['margin-top'] ) ? '' : 'margin-top:' . $settings[ $category ]['margin-top'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['margin-top'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['margin-bottom'] ) ? '' : 'margin-bottom:' . $settings[ $category ]['margin-bottom'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['margin-bottom'] ) . ' ' . $important . ';';

		return $input_styles;
	}

	public function gf_sb_get_saved_styles_tab( $form_id, $category, $important = '' ) {
		$settings = get_option( 'gf_stla_form_id_' . $form_id );
		if ( empty( $settings ) ) {
			return;
		}
		$input_styles  = '';
		$input_styles .= empty( $settings[ $category ]['width-tab'] ) ? '' : 'width:' . $settings[ $category ]['width-tab'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['width-tab'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['max-width-tab'] ) ? '' : 'width:' . $settings[ $category ]['max-width-tab'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['max-width-tab'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['maximum-width-tab'] ) ? '' : 'width:' . $settings[ $category ]['maximum-width-tab'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['maximum-width-tab'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['height-tab'] ) ? '' : 'height:' . $settings[ $category ]['height-tab'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['height-tab'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['font-size-tab'] ) ? '' : 'font-size:' . $settings[ $category ]['font-size-tab'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['font-size-tab'] ) . ' ' . $important . ';';

		$input_styles .= empty( $settings[ $category ]['line-height-tab'] ) ? '' : 'line-height:' . $settings[ $category ]['line-height-tab'] . ' ' . $important . ';';
		return $input_styles;
	}

	public function gf_sb_get_saved_styles_phone( $form_id, $category, $important = '' ) {
		$settings = get_option( 'gf_stla_form_id_' . $form_id );
		if ( empty( $settings ) ) {
			return;
		}
		$input_styles  = '';
		$input_styles .= empty( $settings[ $category ]['width-phone'] ) ? '' : 'width:' . $settings[ $category ]['width-phone'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['width-phone'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['max-width-phone'] ) ? '' : 'width:' . $settings[ $category ]['max-width-phone'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['max-width-phone'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['maximum-width-phone'] ) ? '' : 'width:' . $settings[ $category ]['maximum-width-phone'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['maximum-width-phone'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['height-phone'] ) ? '' : 'height:' . $settings[ $category ]['height-phone'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['height-phone'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['font-size-phone'] ) ? '' : 'font-size:' . $settings[ $category ]['font-size-phone'] . $this->gf_stla_add_px_to_value( $settings[ $category ]['font-size-phone'] ) . ' ' . $important . ';';
		$input_styles .= empty( $settings[ $category ]['line-height-phone'] ) ? '' : 'line-height:' . $settings[ $category ]['line-height-phone'] . ' ' . $important . ';';

		return $input_styles;
	}

	/**
	 * Function to add px if not available (not for padding and margin)
	 */

	public function gf_stla_add_px_to_value( $value ) {
		$int_parsed = (int) $value;
		if ( ctype_digit( $value ) ) {
			$value = 'px';
		} else {
			$value = '';
		}
		return $value;
	}

	/**
	 * Function to add px if not available for padding and margin
	 * [deprecated] No longer used since v4.0
	 */

	public function gf_stla_add_px_to_padding_margin( $value ) {
		$margin_padding     = explode( ' ', $value );
		$new_margin_padding = '';
		foreach ( $margin_padding as $att_value ) {
			if ( ctype_digit( $att_value ) ) {
				$new_margin_padding .= $att_value . 'px ';
			} else {
				$new_margin_padding .= $att_value . ' ';
			}
		}
		return $new_margin_padding;
	}

	/**
	 * Convert HSL colors into RGBA (used to convert gradient colors), Opacity is fetched from database
	 */
	public function hslToRgba( $h, $s, $l, $background_opacity ) {
		$h /= 360;
		$r  = $l;
		$g  = $l;
		$b  = $l;
		$v  = ( $l <= 0.5 ) ? ( $l * ( 1.0 + $s ) ) : ( $l + $s - $l * $s );
		if ( $v > 0 ) {
			$m;
			$sv;
			$sextant;
			$fract;
			$vsf;
			$mid1;
			$mid2;
			$m       = $l + $l - $v;
			$sv      = ( $v - $m ) / $v;
			$h      *= 6.0;
			$sextant = floor( $h );
			$fract   = $h - $sextant;
			$vsf     = $v * $sv * $fract;
			$mid1    = $m + $vsf;
			$mid2    = $v - $vsf;

			switch ( $sextant ) {
			case 0:
				$r = $v;
				$g = $mid1;
				$b = $m;
				break;
			case 1:
				$r = $mid2;
				$g = $v;
				$b = $m;
				break;
			case 2:
				$r = $m;
				$g = $v;
				$b = $mid1;
				break;
			case 3:
				$r = $m;
				$g = $mid2;
				$b = $v;
				break;
			case 4:
				$r = $mid1;
				$g = $m;
				$b = $v;
				break;
			case 5:
				$r = $v;
				$g = $m;
				$b = $mid2;
				break;
			}
		}
		$rgba = 'rgba(' . floor( $r * 255 ) . ',' . floor( $g * 255 ) . ',' . floor( $b * 255 ) . ',' . $background_opacity . ')';
		return $rgba;
	}

	/**
	 * Convert Hex to rgba
	 */
	public function hex_rgba( $hex_code, $background_opacity ) {
		$r                 = '';
		$g                 = '';
		$b                 = '';
		list( $r, $g, $b ) = sscanf( $hex_code, '#%02x%02x%02x' );
		return 'rgba(' . $r . ',' . $g . ',' . $b . ',' . $background_opacity . ')';
	}

	/**
	 * Set Gradient properties for all browsers
	 */
	public function set_gradient_properties( $gradientColor1, $gradientColor2, $direction ) {
		switch ( $direction ) {
		case 'left':
			$gradientDirection         = 'right,';
			$gradientDirectionSafari   = 'left,';
			$gradientDirectionStandard = 'to right,';
			break;
		case 'diagonal':
			$gradientDirection         = 'bottom right,';
			$gradientDirectionSafari   = 'left top,';
			$gradientDirectionStandard = 'to bottom right,';
			break;
		default:
			$gradientDirection         = '';
			$gradientDirectionSafari   = '';
			$gradientDirectionStandard = '';
		}
		$gradient_css  = 'background: linear-gradient(' . "$gradientDirectionStandard" . "$gradientColor1" . ',' . $gradientColor2 . ');';
		$gradient_css .= 'background: -o-linear-gradient(' . "$gradientDirection" . "$gradientColor1" . ',' . $gradientColor2 . ');';
		$gradient_css .= 'background: -moz-linear-gradient(' . "$gradientDirection" . "$gradientColor1" . ',' . $gradientColor2 . ');';
		$gradient_css .= 'background: -webkit-linear-gradient(' . "$gradientDirectionSafari" . "$gradientColor1" . ',' . $gradientColor2 . ');';
		// $gradient_css='apple';
		return $gradient_css;
	}
	public function gf_stla_welcome_screen_activate() {
		set_transient( 'gf_stla_welcome_activation_redirect', true, 30 );
	}


	public function gf_stla_welcome_screen_do_activation_redirect() {
		// Bail if no activation redirect
		if ( ! get_transient( 'gf_stla_welcome_activation_redirect' ) ) {
			return;
		}
		// Delete the redirect transient
		delete_transient( 'gf_stla_welcome_activation_redirect' );
		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}
		// Redirect to welcome about page
		wp_safe_redirect( add_query_arg( array( 'page' => 'stla-documentation' ), admin_url( 'admin.php' ) ) );
	}

	public function customize_save_after() {

		// get name of style to be deleted
		$style_to_be_deleted = get_option( 'gf_stla_general_settings' );
		if ( $style_to_be_deleted['reset-styles'] != -1 || ! empty( $style_to_be_deleted['reset-styles'] ) ) {
			delete_option( 'gf_stla_form_id_' . $style_to_be_deleted['reset-styles'] );
			$style_to_be_deleted['reset-styles'] = -1;
			update_option( 'gf_stla_general_settings', $style_to_be_deleted );
		}
	}

	/*
	 * Check if the form is opened in frontend
	 */

	public function gf_stla_show_css_frontend( $form ) {
		$this->is_this_frontend = true;
		// show css in frontend
		// $style_current_form = get_option( 'gf_stla_form_id_' . $form['id'] );
		// if ( ! empty( $style_current_form ) ) {
		// $css_form_id = $form['id'];
		// $main_class_object = $this;
		// include 'display/class-styles.php';
		// }
		// do_action( 'gf_stla_after_post_style_display', $this );
		return $form;
	}

	public function admin_notices() {
		if ( ! class_exists( 'GFForms' ) ) {
			$class   = 'notice notice-error';
			$message = '<a href="http://www.gravityforms.com/">Gravity Forms</a> not installed. <strong>Styles & Layouts for Gravity Forms</strong> can\'t work without Gravity Forms ';
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
		}
	}

	/**
	 * Adds Styles & Layouts to Toolbar in  Gravity form edit screen
	 */

	public function gform_toolbar_menu( $menu_items, $form_id ) {
		$menu_items['styles-layouts-gravity-forms'] = array(
			'icon'         => '<i class="fa fa-paint-brush fa-lg"></i>',
			'label'        => 'Styles & Layouts', // the text to display on the menu for this link
			'title'        => 'Styles & Layouts', // the text to be displayed in the title attribute for this link
			'url'          => $this->_set_customizer_url( $form_id ), // the URL this link should point to
			'menu_class'   => 'sk-style', // optional, class to apply to menu list item (useful for providing a custom icon)
			'link_class'   => rgget( 'page' ) == 'my_custom_page' ? 'gf_toolbar_active' : '*', // class to apply to link (useful for specifying an active style when this link is the current page)
			'capabilities' => array( 'gravityforms_edit_forms' ), // the capabilities the user should possess in order to access this page
			'priority'     => 500, // optional, use this to specify the order in which this menu item should appear; if no priority is provided, the menu item will be append to end
		);
		return $menu_items;
	}

	/**
	 * Remove any unwanted default conrols.
	 *
	 * @since 1.0.0
	 * @param object  $wp_customize
	 */
	// public function remove_sections( $wp_customize ) {
	//  global $wp_customize;
	//  $wp_customize->remove_section( 'themes' );
	//  return true;
	// }

	/**
	 * Add custom variables to the available query vars
	 *
	 * @since 1.0.0
	 * @param mixed   $vars
	 * @return mixed
	 */
	public function add_query_vars( $vars ) {
		$vars[] = $this->trigger;
		return $vars;
	}

	/**
	 * If the right query var is present load the Gravity Forms preview template
	 *
	 * @since 1.0.0
	 */
	public function gf_stla_preview_template( $wp_query ) {

		// load this conditionally based on the query var
		if ( get_query_var( $this->trigger ) ) {
			wp_head();
			ob_start();
			$form_id = sanitize_text_field( $_GET['stla_form_id'] );
			include GF_STLA_DIR . '/helpers/utils/html-template-preview.php';
			$message = ob_get_clean();
			wp_footer();
			echo $message;
			exit;
		}
		return $wp_query;
	}

	/**
	 * Set the customizer url
	 *
	 * @since 1.0.0
	 */
	private function _set_customizer_url( $form_id ) {
		$url                  = admin_url( 'customize.php' );
		$url                  = add_query_arg( 'stla-gravity-forms-customizer', 'true', $url );
		$url                  = add_query_arg( 'stla_form_id', $form_id, $url );
		$url                  = add_query_arg( 'autofocus[panel]', 'gf_stla_panel', $url );
		$url                  = add_query_arg(
			'url', wp_nonce_url(
				urlencode(
					add_query_arg(
						array(
							'stla_form_id'     => $form_id,
							'stla-gravity-forms-customizer' => 'true',
							'autofocus[panel]' => 'gf_stla_panel',
						), site_url()
					)
				), 'preview-popup'
			), $url
		);
		$url                  = add_query_arg(
			'return', urlencode(
				add_query_arg(
					array(
						'page' => 'gf_edit_forms',
						'id'   => $form_id,
					), admin_url( 'admin.php' )
				)
			), $url
		);
		$this->customizer_url = esc_url_raw( $url );
		return $this->customizer_url;
	}

	/**
	 * Show only our email settings in the preview
	 *
	 * @since 1.0.0
	 */
	// public function control_filter( $active, $control ) {
	//  if ( in_array( $control->section, array( 'gf_stla_select_form_section' ) ) ) {
	//   // write_log($control->section);
	//   return false;
	//  }
	//  return true;
	// }
}//end class
register_activation_hook( __FILE__, 'stla_set_migrate_transient' );

function stla_set_migrate_transient() {
	set_transient( 'stla_updated', 1 );
	// // The path to our plugin's main file
	// $our_plugin = plugin_basename( __FILE__ );
	// // If an update has taken place and the updated type is plugins and the plugins element exists
	// if ( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
	//  // Iterate through the plugins being updated and check if ours is there
	//  foreach ( $options['plugins'] as $plugin ) {
	//   if ( $plugin == $our_plugin ) {
	//    // Set a transient to record that our plugin has just been updated
	//    set_transient( 'stla_updated', 1 );
	//   }
	//  }
	// }
}

add_action( 'plugins_loaded', 'stla_gravity_customizer_admin' );

function stla_gravity_customizer_admin() {
	new Gravity_customizer_admin();

}
