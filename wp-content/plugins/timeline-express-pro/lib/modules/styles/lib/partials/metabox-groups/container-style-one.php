<?php
/**
 * Container Style One Fields
 *
 * @var array Array of fields specific to this container style.
 */

$post_id = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
$style   = $post_id ? timeline_express_styles_metabox_values( $post_id, 'container_style' ) : 'style-default';
$hidden  = ( 'style-one' !== $style ) ? ' hidden' : '';

return [
	[
		'style'    => 'style-one',
		'name'     => __( 'Style One', 'cmb2' ),
		'id'       => $prefix . '_container_style_styles',
		'type'     => 'container_style_one',
		'classes'  => "te-attributes-container style-one-attributes {$hidden}",
		'defaults' => [
			'.cd-timeline-title-container .cd-timeline-item-title' => [
				'background-color' => '#EFEFEF',
			],
			'.cd-timeline-content' => [
				'background' => '#EFEFEF',
				'color'      => '#1a1a1a',
			],
		],
	],
];
