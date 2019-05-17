<?php

/**
 * @return ACP\AdminColumnsPro
 */
function ACP() {
	return ACP\AdminColumnsPro::instance();
}

/**
 * Editing instance
 * @since 4.0
 * @return ACP\Editing\Addon
 */
function acp_editing() {
	return new ACP\Editing\Addon();
}

/**
 * Filtering instance
 * @since 4.0
 * @return ACP\Filtering\Addon
 */
function acp_filtering() {
	return new ACP\Filtering\Addon();
}

/**
 * @since 4.2
 * @return ACP\Filtering\Helper
 */
function acp_filtering_helper() {
	return acp_filtering()->helper();
}

/**
 * @return ACP\Sorting\Addon
 */
function acp_sorting() {
	return new ACP\Sorting\Addon();
}

/**
 * @return ACP\Export\Addon
 */
function ac_addon_export() {
	return new ACP\Export\Addon();
}

/**
 * @return ACP\Search\Addon
 */
function ac_addon_search() {
	return new ACP\Search\Addon();
}

/**
 * @since 4.4
 * @return string
 */
function acp_support_email() {
	return 'support@admincolumns.com';
}

/**
 * Check if an addon is compatible or not
 *
 * @param string $namespace
 * @param string $version
 *
 * @return bool
 */
function acp_is_addon_compatible( $namespace, $version ) {
	$addons = array(
		'ACA\ACF'   => '2.4',
		'ACA\BP'    => '1.3.2',
		'ACA\EC'    => '1.2.3',
		'ACA\NF'    => '1.2.1',
		'ACA\Pods'  => '1.2.1',
		'ACA\Types' => '1.3.3',
		'ACA\WC'    => '3.2',
	);

	$namespace = rtrim( $namespace, '\\' );

	if ( ! array_key_exists( $namespace, $addons ) ) {

		return true;
	}

	return version_compare( $addons[ $namespace ], $version, '<=' );
}

/**
 * @deprecated 4.5
 * @return \ACP\Editing\Helper
 */
function acp_editing_helper() {
	_deprecated_function( __FUNCTION__, '4.5' );

	return ACP()->editing()->helper();
}