<?php
/**
 * Container Style One Fields
 *
 * @var array Array of fields specific to this container style.
 */

$post_id = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_NUMBER_INT );
$style   = $post_id ? timeline_express_styles_metabox_values( $post_id, 'container_style' ) : 'style-default';
$hidden  = ( 'style-two' !== $style ) ? ' hidden' : '';

return [
	[
		'style'    => 'style-two',
		'name'     => __( 'Style Two', 'timeline-express-pro' ),
		'id'       => $prefix . '_container_style_styles_two',
		'type'     => 'container_style_two',
		'classes'  => "te-attributes-container style-two-attributes {$hidden}",
		'defaults' => [
			'.timeline-express-read-more-link' => [
				'width'      => '100',
				'text-align' => 'center',
			],
		],
	],
];
