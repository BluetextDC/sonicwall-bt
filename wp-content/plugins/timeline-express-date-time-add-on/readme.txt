=== Timeline Express - Date - Time Add-On ===
Contributors: codeparrots, eherman24
Tags: timeline, express, addon, add-on, date, timepicker, announcement
Plugin URI: https://www.wp-timelineexpress.com
Requires at least: WP 4.0 & Timeline Express 1.2
Tested up to: 4.9
Requires PHP: 5.6
Stable tag: 1.0.1
License: GPLv2 or later

Assign and display times alongside the announcement dates in Timeline Express announcements.

== Description ==

When active, the Timeline Express - Date - Time Add-On will hide the default announcement date field, and generate a date and time field for you to use.

Multiple announcements that have the same date & time will fallback to use the published date to dictate order on the timeline.

== Installation ==
1. Upload the entire `timeline-express-date-time-add-on` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. If you previously had announcements setup, follow the migration steps.
4. Create a new announcement set a date & time and set the date format for each announcement.

== Frequently Asked Questions ==

= What if multiple announcements have the same dates? =
If multiple announcements use the same date and time, then the announcement 'published' date will be used to dictate the order. The published date can be adjusted just above the 'Publish' button in the right hand sidebar on the announcement creation/edit screen in the dashboard.

= Can I set the display format? I don't want to display the time on some announcements. =
Yes! For each announcement you have the ability to choose how the dates are displayed. Out of the box you can display the dates in the following formats:

- Full Date (ie: 02/10/2018 1:00 PM)
- Year Only (ie: 2018)
- Date Only (ie: 02/10/2018)
- Time Only (ie: 1:00 PM)

== Screenshots ==
1. Announcement Date & Time Selector
2. Front End Date & Time on the Timeline

== Changelog ==

= 1.0.1 - February 26th, 2018 =
* Tweak: Ensure plugin works with Timeline Express free/pro.
* Tweak: Allow translation files to be loaded from theme root (see i18n/how-to.txt).
* Tweak: Hide migration notice when plugin is activated without Timeline Express free/pro.
* Tweak: Update admin notice styles on activation.

= 1.0.0 - February 11th, 2018 =
* Initial release.

== Developers ==

Filters:
<strong>timeline_express_date_time_formats</strong> - Add your own date formats to the announcement.

**Example:**
<pre>
/**
 * Assign a custom date format to the announcements.
 *
 * @param array $date_formats The original date formats array.
 */
function timeline_express_demo_custom_date_format( $date_formats ) {

	$date_formats['custom'] = 'Y-m-d'; // eg: 2018-10-02

}
add_filter( 'timeline_express_date_time_formats', 'timeline_express_demo_custom_date_format' );
</pre>

<strong>timeline_express_date_time_query_args</strong> - Filter the query run for the date time add-on.

**Example:**
<pre>
/**
 * Filter the announcement date time add-on query.
 * Fall back to post titles instead of published date when announcements contain the same date-time values.
 *
 * @param array $query_args The original date time add-on query arguments.
 */
function timeline_express_demo_filter_query_args( $query_args ) {

	unset( $query_args['orderby'] );

	$query_args['orderby'] = 'meta_value_num title';

	return $query_args;

}
add_filter( 'timeline_express_date_time_query_args', 'timeline_express_demo_filter_query_args' );
</pre>
