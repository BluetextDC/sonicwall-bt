<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

return array(

	'aepc_enable_pixel' => array(
		'type' => 'checkbox',
		'default' => 'no'
	),

	'aepc_pixel_id' => array(
		'type' => 'text',
		'default' => ''
	),

	'aepc_account_id' => array(
		'type' => 'text',
		'default' => ''
	),

	'aepc_pixel_position' => array(
		'type' => 'select',
		'default' => 'head',
		'options' => array(
			'head' => __( 'Head', 'pixel-caffeine' ),
			'footer' => __( 'Footer', 'pixel-caffeine' )
		)
	),

	'aepc_enable_dpa' => array(
		'type' => 'checkbox',
		'default' => 'no'
	),

	'aepc_enable_viewcontent' => array(
		'type' => 'checkbox',
		'default' => 'no'
	),

	'aepc_enable_addtocart' => array(
		'type' => 'checkbox',
		'default' => 'no'
	),

	'aepc_enable_addtowishlist' => array(
		'type' => 'checkbox',
		'default' => 'no'
	),

	'aepc_enable_initiatecheckout' => array(
		'type' => 'checkbox',
		'default' => 'no'
	),

	'aepc_enable_addpaymentinfo' => array(
		'type' => 'checkbox',
		'default' => 'no'
	),

	'aepc_enable_purchase' => array(
		'type' => 'checkbox',
		'default' => 'no'
	),

	'aepc_enable_completeregistration' => array(
		'type' => 'checkbox',
		'default' => 'no'
	),

	'aepc_enable_ca_events' => array(
		'type' => 'checkbox',
		'default' => 'yes'
	),

	'aepc_enable_advanced_events' => array(
		'type' => 'checkbox',
		'default' => 'yes'
	),

	'aepc_enable_taxonomy_events' => array(
		'type' => 'checkbox',
		'default' => 'yes'
	),

	'aepc_enable_utm_tags' => array(
		'type' => 'checkbox',
		'default' => 'yes'
	),

	'aepc_enable_advanced_matching' => array(
		'type' => 'checkbox',
		'default' => 'yes'
	),

	'aepc_custom_fields_event' => array(
		'type' => 'array',
		'default' => array()
	),

	'aepc_force_ids' => array(
		'type' => 'checkbox',
		'default' => 'no'
	),

	'aepc_enable_pixel_delay' => array(
		'type' => 'checkbox',
		'default' => 'no'
	),

	'aepc_general_delay_firing' => array(
		'type' => 'text',
		'default' => 0
	),

	'aepc_enable_advanced_pixel_delay' => array(
		'type' => 'checkbox',
		'default' => 'no'
	),

	'aepc_conversions_no_product_group' => array(
		'type' => 'checkbox',
		'default' => 'no'
	),

	'aepc_track_shipping_costs' => array(
		'type' => 'checkbox',
		'default' => 'no'
	),

	'aepc_no_pixel_when_logged_in' => array(
		'type' => 'checkbox',
		'default' => 'no'
	),

	'aepc_no_pixel_if_user_is' => array(
		'type' => 'array',
		'default' => array()
	),

	'aepc_enable_no_value_parameter' => array(
		'type' => 'checkbox',
		'default' => 'no'
	),

	'aepc_no_value_parameter' => array(
		'type' => 'array',
		'default' => array()
	),

	'aepc_enable_no_content_parameters' => array(
		'type' => 'checkbox',
		'default' => 'no'
	),

	'aepc_no_content_parameters' => array(
		'type' => 'array',
		'default' => array()
	),

	'aepc_advanced_pixel_delay_firing' => array(
		'type' => 'text',
		'default' => 0
	),

	'aepc_enable_debug_mode' => array(
		'type' => 'checkbox',
		'default' => 'no'
	),

);
