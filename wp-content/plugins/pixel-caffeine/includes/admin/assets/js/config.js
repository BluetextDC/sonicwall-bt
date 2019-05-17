/**
 * UI scripts for admin settings page
 */

const Config = {

	fragments: {
		'fb_pixel_box': '.panel.panel-settings-set-fb-px',
		'ca_list': '.panel.panel-ca-list',
		'conversions_list': '.panel.panel-ce-tracking',
		'logs_list': '.panel.panel-log-list',
		'sidebar': '.plugin-sidebar',
		'product_feed_status': '.js-product-feed-info',
		'product_feed_schedule': '.js-schedule-options.schedule-update'
	},

	loaders: [
		{ action: 'get_user_roles', dropdown: 'input.user-roles' },
		{ action: 'get_standard_events', dropdown: 'input.standard-events' },
		{ action: 'get_custom_fields', dropdown: 'input.custom-fields' },
		{ action: 'get_languages', dropdown: '#conditions_language' },
		{ action: 'get_device_types', dropdown: '#conditions_device_types' },
		{ action: 'get_categories', dropdown: '' },
		{ action: 'get_tags', dropdown: '' },
		{ action: 'get_posts', dropdown: '' },
		{ action: 'get_dpa_params', dropdown: '' },
		{ action: 'get_currencies', dropdown: '' },
	]

};

export default Config;
