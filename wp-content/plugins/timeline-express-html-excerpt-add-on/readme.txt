=== Timeline Express HTML Excerpts Add-on ===
Contributors: codeparrots, eherman24
Tags: timeline, express, addon, add, on, html, excerpts, shortcode, parameter, time, line, timeline express
Plugin URI: https://www.wp-timelineexpress.com
Requires at least: WP 4.0 & Timeline Express 1.2
Tested up to: 4.8
Stable tag: 1.1.0
License: GPLv2 or later

Enable a new HTML Excerpt field on Timeline Express announcements, which can be used to replace the default generated excerpts.

== Description ==

The Timeline Express HTML Excerpts add-on will create a new WYSIWYG metabox on Timeline Express announcement posts. This new field can
be used to generate a custom excerpt for your announcements. The new WYSIWYG custom excerpt field allows for HTML to be used in announcement excerpts,
and has no limit to the length.

Features:
* Enable a new 'Announcement Custom Excerpt' field.
* Use HTML markup in your announcements without it being stripped.
* Display images, videos etc. in announcement excerpts on the timeline.
* No limit to the length of the HTML excerpts.

== Screenshots ==
1. The new custom HTML excerpt field on the Timeline Express announcement posts.

== Changelog ==

= 1.1.0 - September 20th, 2016 =
* Ensure that images, videos get parsed in the custom excerpt. (Passing them through `the_content()` filter).

= 1.0.0 - September 20th, 2016 =
* Initial release of the HTML excerpts add-on for Timeline Express.
