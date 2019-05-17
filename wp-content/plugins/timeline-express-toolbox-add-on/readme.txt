=== Timeline Express - Toolbox Add-On ===
Contributors: codeparrots, eherman24
Tags: timeline, express, time, line, timeline express, add, on, add-on
Plugin URI: https://www.wp-timelineexpress.com
Requires at least: WP 4.0 & Timeline Express 1.2
Tested up to: 4.8
Stable tag: 1.1.0
License: GPLv2 or later

This add-on enables additional settings to allow users to customize the date format, image sizes, announcement slug and more.

== Description ==

This add-on enables additional settings to allow users to customize the date format, image sizes, announcement slug and more.

<h3>Per Announcement Date Formats</h3>

Since version 1.1.0 users can now set a 'Per announcement date format', allowing them to set the date formats for each announcement, instead of globally.

Add the following to your theme to enable the custom date format selector on each announcement.

`defined( 'TIMELINE_EXPRESS_ANNOUNCEMENT_DATE_FORMATS', true );`

== Changelog ==

= 1.1.0 - July 2017 =
* Tweak: Enabled a 'Per Announcement Date Format' option, by defining a `TIMELINE_EXPRESS_ANNOUNCEMENT_DATE_FORMATS` constant in your `functions.php` file. Example: `defined( 'TIMELINE_EXPRESS_ANNOUNCEMENT_DATE_FORMATS', true );`
* Tweak: Timeline Express Pro v2 compatibility updates.
* Tweak: Bump tested up to version.
* Tweak: Updates to our grunt task.

= 1.0.2 - March 8th, 2017 =
* Update constant, Gruntfile.js - fix bug with endless 'Update' loop.

= 1.0.1 - February 3rd, 2017 =
* Tweaked strings for compatibility with Timeline Express free.

= 1.0.0 - January 17th, 2017 =
* Initial release.
