=== Timeline Express Pro ===
Contributors: codeparrots, eherman24
Tags: timeline, responsive, time, line, vertical, animated, company, history, font awesome, events, calendar, scroll, dates, story, timeline express, milestone, stories
Requires at least: 4.0
Tested up to: 4.9
Stable tag: 2.2.7
License: GPLv2 or later

Timeline Express Pro allows you to create multiple beautiful vertical animated and responsive timelines of posts, without writing a single line of code. Sweet!

== Description ==

Timeline Express Pro allows you to create a vertical animated timeline of announcement posts, without writing a single line of code. You simply create the 'announcement' posts, set the announcement date and publish. The timeline will populate automatically in chronological order, based on the announcement date. Easily limit the announcements displayed to Upcoming announcements, past announcements or simply display all of them.

**Features**

* Load a custom template for single announcements (new)
* Localized date formatting for international users (new)
* Hundreds of Font awesome icons included. Specify a different icon for each announcement
* CSS3 animations on scroll
* Set the color of the announcement
* Specify the length to trim each announcement, or randomize it
* Hide the date of the announcement
* Hide the 'read more' button for each announcement
* Specify an image to display for each announcement
* Delete announcements on uninstallation (so no orphan posts are hanging around in your database)
* Easy to use shortcode to place the timeline wherever your heart desires ( `[timeline-express]` )
* TinyMCE button to generate the shortcode
* Specify Ascending vs Descending display order
* Highly extensible
* Translatable

**Translated**

Timeline express comes ready for translation. I would love to get things translated into as many languages as possible. At the moment the following translations are available for Timeline Express :

* English
* Chinese (zh_CN) - thanks goes to <a href="http://www.vahichen.com" target="_blank">Vahi Chen</a>
* Portuguese (pt_BR) - thanks goes to <a href="http://toborino.com" target="_blank">Gustavo Magalhães</a>
* Polish (pl_PL) - thanks goes to Kanios
* German (de_DE) - thanks goes to <a href="http://www.fairsoft.koeln" target="_blank">Martin Gerlach</a>
* French (fr_FR) - thanks goes to <a href="http://troisplus-et-aeliin-cosplay.fr/" target="_blank">Julien Lambert</a>
* Hungarian (hu_HU) - thanks goes to <a href="http://www.keszites.com/" target="_blank">Zsolt</a>

<em>We're always looking for polyglots to help with the translations. If you enjoy this plugin, speak multiple languages and want to contribute please <a href="http://www.evan-herman.com/contact/" target="_blank">contact me</a> about how you can help translate things so users around the world can benefit from this plugin.</em>

Looking for some advancedd documentation? Check out the <a href="https://wordpress.org/plugins/timeline-express/other_notes/">other notes</a> section.
<br />
<br />
<strong>While the plugins I develop are free, maintaining and supporting them is hard work. If you find this plugin useful, or it helps in anyway, please consider making a <a href="http://www.evan-herman.com/contact/?contact-reason=I%20want%20to%20make%20a%20donation%20for%20all%20your%20hard%20work">donation</a> for its continued development.</strong>

== Installation ==

1. Download the plugin .zip file
2. Log in to yourdomain.com/wp-admin
3. Click Plugins -> Add New -> Upload
4. Activate the plugin
6. On the left hand menu, hover over 'Timeline Express' and click 'New Announcement'
7. Begin populating the timeline with events. (Note: Events will appear in chronological order according to the <strong>announcement date</strong>)
8. Once you have populated the timeline, head over to the settings page (Settings > Timeline Express) to customize your timeline.
9. Create a new page, and enter the shortcode [timeline-express] to display the vertical timeline (Note: Timeline Express displays best on full width pages)

== Frequently Asked Questions ==

= How do I use this plugin? =
Begin by simply installing the plugin. Once the plugin has been installed, go ahead and begin creating announcement posts. You'll find a new menu item just below 'Posts'.
After you have a substantial number of announcements set up, you're ready to display the timeline on the front end of your site.

Timeline express displays best on full width pages, but is not limited to them. Create a new page, and drop the shortcode into the page - `[timeline-express]`.
Publish your page, and view it on the front end the see your new super sweet timeline! (scroll for animation effects!)

= What template is the single announcement post using? Can I customize it at all? I want to do x, y or z. =
The single announcement post is using a custom template file that comes pre-bundled with the plugin. If you want to customize the template for whatever reason
you can do so, by creating a directory in your active theme called 'timeline-express'. Once the directory is created, simply copy the file titled 'single-timeline-express-announcement.php' into
the newly created 'timeline-express' directory in your theme. Timeline express will then automagically pull in the newly created template in your theme root. You can go ahead and customize
it to your hearts desire without fear of losing any changes in future updates!

= Can I create more than one timeline? =
At the moment no, but I will consider adding that into a future update if people show enough interest.

= At what width are the breakpoints set? =
Breakpoints are set at 822px. The timeline will shift/re-adjust automatically using masonry based on the height of each announcement container.

= How can I translate this plugin? =
The text-domain for all gettext functions is `timeline-express`.

If you enjoy this plugin and want to contribute, I'm always looking for people to help translate the plugin into any of the following languages, credit will be given where credit is due :

* Arabic
* English
* Greek
* Hebrew
* Hindi
* Hong Kong
* Italian
* Japanese
* Korean
* Persian
* Portuguese (European)
* Romanian
* Russian
* Spanish
* Swedish
* Taiwanese
* Tamil
* Urdu
* Vietnamese
* Welsh

Read the Codex article "[I18n for WordPress Developers]"(http://codex.wordpress.org/I18n_for_WordPress_Developers) for more information.

== Other Notes ==

Have an idea for a future release feature? I love hearing about new ideas! You can get in contact with me through the contact form on my website, <a href="http://www.evan-herman.com/contact/" target="_blank">Evan-Herman.com</a>.

<hr />

<strong>Developer Documentation</strong>

**Hooks + Filters**

**Use custom images instead of Font Awesome icons (props <a href="https://github.com/petenelson">Petenelson</a>)**

Timeline express expects a font-awsome icon to be selected and used for the timeline. You can now specify custom images using the following filter:

Example:
For example usage , see the following <a href="https://gist.github.com/EvanHerman/6bbc8de82f34b4cb3c5c">Gist</a>.

Original Pull Request: https://github.com/EvanHerman/Timeline-Express/pull/7

**Use Alternate Image Size For Announcements (New v1.1.5.5)**

By default Timeline Express generates a custom image size to use within the timeline. If you would like to use another image size, you can use the following filter.

Example:
<code>
function change_timeline_express_announcement_image_size( $image_size ) {
	$image_size = 'full';
	return $image_size;
}
add_filter( 'timeline-express-announcement-img-size' , 'change_timeline_express_announcement_image_size' );
</code>

**Define your own custom fields to use in Announcement posts (New v1.1.5)**

Users can now add custom fields to Timeline Express announcement posts. This allows for greater control over the announcements and the front end display. Using this hook in conjunction with a custom single announcement template will give you the greatest control.

Example:
<code>
function add_custom_timeline_express_field( $custom_fields ) {
	$custom_fields = array(
		array(
			'name' => __( 'Example Text Field', 'timeline-express-pro' ),
			'desc' => __( 'this is an example user defined text field.', 'timeline-express-pro' ),
			'id'   => 'announcement_user_defined_text',
			'type' => 'text_medium',
		),
		array(
			'name' => __( 'Example WYSIWYG', 'timeline-express-pro' ),
			'desc' => __( 'this is an example wysiwyg field.', 'timeline-express-pro' ),
			'id'   => 'announcement_user_defined_wysiwyg',
			'type' => 'wysiwyg',
		),
		array(
			'name' => __( 'Example Email Field', 'timeline-express-pro' ),
			'desc' => __( 'this is an example user defined email field.', 'timeline-express-pro' ),
			'id'   => 'announcement_user_defined_money',
			'type' => 'text_email',
		)
	);
	return $custom_fields;
}
add_filter( 'timeline_express_custom_fields' , 'add_custom_timeline_express_field' );
</code>

This example would add 3 new fields below the 'Announcement Image' field on the announcement post.

The first field is a simple text field. The second field is an example WYSIWYG, and the third is an email field.

Note: You can add as many fields as you would like, and display them on the front end using the <a href="http://codex.wordpress.org/Function_Reference/get_post_meta" target="_blank" title="WordPress Codex: get_post_meta()">get_post_meta()</a> function.

**Customize the 'announcement' slug (New v1.1.4)**

Users can now define their own slug for announcement posts using the provided filter `'timeline-express-slug'`. This alters the URL structure of the announcement, possibly for SEO purposes. You would enter the following code into your active themes functions.php file.

After you enter the code into the functions.php file, you'll want to refresh your permalinks. You can do so by going to 'Settings > Permalinks' and simply clicking save. That will prevent the 404 page you may see upon altering the slug.

Example:
<code>
// alter '/announcement/' to be '/event/'
function timeline_express_change_announcement_slug( $slug ) {
    $slug = 'event';
    return $slug;
}
add_filter('timeline-express-slug', 'timeline_express_change_announcement_slug' );
</code>

This example would change the default `/announcement/` slug, to `/event/`.

**Alter the 'Read More' button text (New v1.1.3.1)**

Users can now alter the 'Read More' button text using the provided gettext filter and the 'timeline-express' text domain.

Example:
<code>
// alter 'Read more' to say 'View Announcement'
function timeline_express_change_readmore_text( $translated_text, $untranslated_text, $domain ) {
    switch( $untranslated_text ) {
        case 'Read more':
          $translated_text = __( 'View Announcement','timeline-express-pro' );
        break;
     }
   return $translated_text;
}
add_filter('gettext', 'timeline_express_change_readmore_text', 20, 3);
</code>

This example would alter 'Read more' to say 'View Announcement'.

**Add custom classes to the 'Read More' button (New v1.1.3.1)**

Users can now add custom classes to the 'Read More' announcement button. This allows for greater control in fitting the Timeline into your currently active theme.

Parameters :

$button_classes = default button classes assigned to the 'Read More' button

Example:
<code>
// add a custom class to the timeline express readmore link
function timeline_express_custom_readmore_class( $button_classes ) {
	return $button_classes . 'custom-class-name';
}
add_filter( 'timeline-express-read-more-class' , 'timeline_express_custom_readmore_class' );
</code>

This example would print the following 'Read More' button HTML onto the page :

`<a href="http://site.com/link-to-announcement" class="cd-read-more btn btn-primary custom-class-name">Read more</a>`

**Setup a custom date format for front end display (New v1.0.9)**

New in version 1.0.9 is the localization of dates on the front end. The date format is now controlled by your date settings inside of 'General > Settings'.

If, for one reason or another, you'd like to specify a different date format than provided by WordPress core you can use the provided filter `timeline_express_custom_date_format`.

The one parameter you need to pass into your function is $date_format, which is (as it sounds) the format of the date.

Some formatting examples:

* `m.d.Y` - 11.19.2014
* `d-m-y` - 11-19-14
* `d M y` - 19 Nov 2014
* `D j/n/Y` - Wed 11/19/2014
* `l jS \of\ F` - Wednesday 19th of November

Example:
<code>
function custom_te_date_format( $date_format ) {
	$date_format = "M d , Y"; // will print the date as Nov 19 , 2014
	return $date_format;
}
add_filter( 'timeline_express_custom_date_format' , 'custom_te_date_format' , 10 );
</code>

* d - Numeric representation of a day, with leading zeros 01 through 31.
* m - Numeric representation of a month, with leading zeros 01 through 12.
* y - Numeric representation of a year, two digits.

* D - Textual representation of a day, three letters Mon through Sun.
* j - Numeric representation of a day, without leading zeros 1 through 31.
* n - Numeric representation of a month, without leading zeros 1 through 12.
* Y - Numeric representation of a year, four digits.

* S - English ordinal suffix for the day of the month. Consist of 2 characters st, nd, rd or th.
* F - Textual representation of a month, January through December.

* M - Textual representation of a month, three letters Jan through Dec.


<em>[view more date formatting parameters](http://php.net/manual/en/function.date.php)</em>


**Load Your Own Single Announcement Template File (New v1.0.8)**

By default all single announcements will try and load a single.php template file. If that can't be found, we've done our best to implement a template for you. If your unhappy with the template file we've provided you have two options. Your first option is to copy over the single-announcement-template directory contained within the plugin into your active themes root. This will trigger the plugin to load that file instead. You can then customize this file to your hearts content without fear of losing any of your changes in the next update.

Your next option is to use our new filter for loading your own custom template file. If for whatever reason you've designed or developed your own single.php file which you would rather use, or you just want to use your themes page.php template instead, you can use the provided filter to change the loaded template. Here is an example ( you want to drop this code into your active theme's functions.php file ) :

Example:
<code>
// By default Timeline Express uses single.php for announcements
// you can load page.php instead
// just change page.php to whatever your template file is named
// keep in mind, this is looking in your active themes root for the template
function custom_timeline_express_template_file( $template_file ) {
	$template_file = 'page.php';
	return $template_file;
}
add_filter( 'timeline_express_custom_template' , 'custom_timeline_express_template_file' , 10 );
</code>

**Alter the Arguments for the Timeline Express Announcement Query (New v1.1.6.8)**

If you want to perform a more advanced query for arguments, alter the order the announcements display in on a specific page, change what announcements get displayed or any other alteration to the query - you can use the following filter.

The following example will check the current page, and if on page with ID 1190 the announcements will be displayed in descending order.

Example:
<code>
/*
* Alter Timeline Express Announcement Query
* Parameters:
*		$query_args - default query arguments
*		$post_data - the global post data which contains all data stored in global $post (page id, page title etc.)
*/
function alter_query_args_for_te_express( $query_args, $post_data ) {
	// confirm we are on page 1190
	if( $post_data->ID == 1190 ) {
		$query_args['order'] = 'DESC';
	}
	// return the arguments
	return $query_args;
}
add_filter( 'timeline_express_announcement_query_args', 'alter_query_args_for_te_express', 10, 2 );
</code>

== Screenshots ==

1. Timeline Express announcement post creation screen
2. Timeline Express announcement management on the 'Edit Announcements' page
3. Timeline Express sample timeline with multiple icons/colors
4. Timeline Express responsive (mobile version)
5. Timeline Express full settings page

== Changelog ==

= 2.2.7 - November 3rd, 2018 =
* New: Introduce `timeline_express_transient_name` filter, to allow the transient name to be filtered for custom caching solutions.
* Tweak: Update the settings page logo.

= 2.2.6 - October 25th, 2018 =
* New: Introduced new JavaScript events at certain points in execution (`timelineLayoutStart`, `timelineLayoutComplete`, `announcementAnimateIn`) and prepared plugin new Timeline Express - Sliders Add-On.
* New: Introduced filter `timeline-express-announcement-img` to shortcircuit the output of the announcement image.
* Tweak: Adjusted certain styles on the announcement timeline container that were not set to `display: block;` causing layout issues on some themes.
* Tweak: Fix PHP warnings when announcements were saved as drafts.

= 2.2.5 - September 21st, 2018 =
* New: Introduced a new toggle on the settings page to enable/disable built-in cache. (option: `timeline_express_cache_enabled`)
* Fix: Fix incorrect announcement container arrow when the background container was styled on the announcement edit screen.
* Tweak: Update styles of announcement color pickers.
* Tweak: Fix incorrect syntax in CSS gradient definition (top → to bottom).
* Tweak: Upgrade our CircleCI config file to 2.0.
* Tweak: Delete Bitbucket pipeline config.
* Tweak: Update admin menu unit test.
* Tweak: Exclude `wpcs` directory in phpcs scan.
* Tweak: Add whitelist to phpunit config, to enable coverage report in circleci.

= 2.2.4 - September 11th, 2018 =
* Tweak: Ensure filters work with horizontal timelines.
* Tweak: strtolower the timeline/categories name so ALL/All/aLL etc. work.
* Tweak: Update color picker styles in styles metabox.
* Tweak: Add `year-icon` class to the announcement container array.

= 2.2.3 - July 31st, 2018 =
* New: Introduced new filter `timeline_express_disable_cache` to allow users to bypass timeline caching. (Default: false)
* New: Introduced new filter `timeline_express_fade_in_timeout` to allow users to filter the length of time it takes for the timeline to fade in. (Default: 800 (ms))
* Tweak: Banner popup scripts and styles adjusted.
* Tweak: Tweak when and where year icons show up when custom icon sizes are being used (ie: no icon, square, rotated square etc.).
* Tweak: Tweak the announcement banner script and styles to work around any warnings being thrown.
* Tweak: Disable caching when announcement time frame is set to display 'Future' or 'Past'.

= 2.2.2 - May 26th, 2018 =
* Tweak: Enhancements and updates to ensure GDPR compliance.
* Tweak: Font Awesome is now loaded locally first, with the option to override and load it from a CDN. (`define( 'TIMELINE_EXPRESS_FONT_AWESOME_LOCAL', false );`)
* Tweak: Support email address updated.
* Tweak: 'Read More' is now removed from the timeline-express text domain. Translations are now handled by WordPress core.
* New: Added new cache flush functionality on the settings page.

= 2.2.1 - April 21st, 2018 =
* New: Introduced <a href="https://wordpress.org/plugins/qtranslate-x/" target="_blank">qtranslate</a> compatbility file, so translations are loaded properly.
* Tweak: Fix PHP warning in `timeline-express-filters.php` with incorrect `count()` syntax.
* Tweak: Update add-ons page so the AJAX Limits Add-On is listed properly.

= 2.2.0 - February 25th, 2018 =
* Tweak: Update sort order to account for announcment date and fallback to post date when dates are the same.
* Tweak: Do not reference the timeline-express-query- transients when WP_DEBUG is enabled. Re-run the queries.
* Tweak: Reference proper css/js files when `SCRIPT_DEBUG` is enabled.
* Tweak: Tweak Add-Ons page.
* Tweak: Check the post_type inside of `timeline_express_save_styles()` to avoid conflicts with other plugins and themes also using CMB2.
* Tweak: Check that `$screen->post_type` is set before referencing it inside of `setup_admin_scripts()`.

= 2.1.1 - January 15th, 2018 =
* Tweak: Tweak Timeline Express js files to reference js objects and not previously declared variables. Fixes YOAST SEO conflict.
* Tweak: Pass all field parameters to `$announcement_metabox->add_field()` in `metaboxes.announcements.php`.

= 2.1.0 - December 23rd, 2017 =
* Tweak: Updated the templating engine to reference single.php of the active theme. Users can still override the default template by following the documentation at https://www.wp-timelineexpress.com/documentation/customize-single-announcement-template/.
* Tweak: Updated the front end styles for single announcements.
* Tweak: Updated RTL styles for RTL languages.
* New: Added an option to toggle on/off the Timeline Express sidebar. When disabled, the default page sidebar will be displayed on single announcement templates.
* New: Introduced `TIMELINE_EXPRESS_LEGACY_SINGLE_TEMPLATE` constant to load the old single template file instead of inheriting the themes.
* New: Introduced `timeline_express_sidebar_id` filter to filter which sidebar the Timeline Express sidebar takes place of.

= 2.0.5 - December 9th, 2017 =
* Tweak: Adjust the horizontal timeline scripts.
* Fix: Fix horizontal timeline conflict with Avada and Fusion plugins.

= 2.0.4 - December 6th, 2017 =
* New: Introduced conditional read more links. If the announcement excerpt lengths do not meet the value set for 'Excerpt Length' on the settings page, no read more links are visible.
* New: Introduced `timeline_express_conditional_readmore` filter to disable conditional read more links. eg: `add_filter( 'timeline_express_conditional_readmore', '__return_false' );`
* Tweak: Prevent Timeline Express color picker from loading on non-announcement pages. (Prevents conflicts with Avada theme)

= 2.0.3 - December 3rd, 2017 =
* New: Added new 'ids' shortcode parameter, to display specific announcements (or post types). eg: `[timeline-express ids="1, 2, 3"]`
* New: Added new Extra Content module (`extra_content`), to allow content to be added above/below each announcement in the timeline.
* New: Added new action hooks for the extra content module. Allows content to be added before/after each announcement on the timeline. `timeline-express-before-announcement-block` & `timeline-express-after-announcement-block`.
* Tweak: Removed overlap class. Relies on javascript to prevent overlaps, attempts to resolve resize overlaps.

= 2.0.2 - September 28th, 2017 =
* Fix: Resolve undefined warning displayed on plugin listing page.
* Fix: Styles module not saving values properly.
* Fix: Styles module not loading and displaying when editing an existing announcement.
* Tweak: Update remote updater class.

= 2.0.1 - September 11th, 2017 =
* New: Introduced `timeline_express_all_tax_get_term_args` filter to filter the get_terms() array run.
* Fix: Load CMB2 tab assets on timeline express post type pages only.
* Fix: Close out the open <style> tag in the admin header.
* Tweak: Set get_terms() arguments when timelines/categries="all" to hide taxonomies with no announcements assigned.
* Tweak: `timeline_express_timeline_filter_label` filter and split into two filters, `timeline_express_filter_by_timeline_label` & `timeline_express_filter_by_category_label`
* Tweak: filter name from `timeline-express-filter-button-text` to `timeline_express_filter_button_text`.

= 2.0.0 - July 2017 =
* New: All New modules added - Styles Module, Announcement Banner Popups, Custom Icons, Side Navigation.
* New: Added integration with <a href="https://wordpress.org/plugins/svg-vector-icon-plugin/" target="_blank">WP SVG Icons</a> to allow users to use custom icons on the timeline.
* New: Introduced styles tweaks allowing users to adjust individual container & icon styles and animations.
* New: Introduced new filters & actions for the styles and custom icons modules.
* New: Ability to toggle read more links on/off per announcement.
* New: Introduced `timeline_epxress_jqueryui_date_format` to let users customize he jQuery UI date picker format.
* New: Introduced `timeline_express_jqueryui_acceptable_formats` for deafult date formats in jQuery UI date picker.
* Fix: Updated the date formats/date sanitize functions. Remove functions no longer needed.
* Enhancement: Refactored Timeline Express initialization scripts, to better prevent container overlaps.
* Enhancement: Refactored large parts of the plugin for performance.
* Enhancement: Refactored the shortcode generator.
* Enhancement: Major UI enhancements.
* Enhancement: The timeline is now hidden on initial load and will fade in after initialization, to prevent FOUC.
* Enhancement: 'Announcement Image' string is now 'Announcement Banner'.
* Enhancement: Video headers are now enabled on all announcements. Use video headers the same way images are used.
* Enhancement: Updated translations throughout the plugin.
* Enhancement: Updated the text-domain to now be 'timeline-express-pro' to match the plugin.
* Enhancement: Re-generated .po/.mo files, in preparation for translations.
* Enhancement: Re-wrote the shortcode generator, and re-styled it to properly follow WordPress coding standards.
* Enhancement: Font Awesome now loads locally first, to prevent timeouts when the CDN is not active. Added 10 second timeout from external servers.
* Enhancement: Banner popups scripts load locally.
* Enhancement: Removed js scrollTo when linking to a specific item on the timeline. This removed the `scroll_to`, `scroll_to_speed` and `scroll_to_animation` localized script data, and the `timeline_express_scroll_to`, `timeline_express_scroll_to_speed` and `timeline_express_scroll_to_animation` filters.

= 1.4.0 - April 20th, 2017 =
* Refactors.

= 1.3.9 - March 15th, 2017 =
* Introduce `timeline_express_frontend_year_icons` filter to fix historical dates year icons bug.

= 1.3.8 - March 14th, 2017 =
* Allow for multiple horizontal sliders to be on the same page.
* Clean up css/js files.
* Update on resize function.
* Refactor slider/scrolling horizontal timeline.
* Tweak public styles to ensure compatibility with <a href="https://thrivethemes.com/" target="_blank">Thrive plugins</a>.
* Remove old usage tracking class, introduce our own custom class.
* Introduce hotfix for i10n month names.

= 1.3.7 - February 12th, 2017 =
* Fix issue with multiple horizontal timelines on a single page.
* Tweak horizontal slider scripts & styles.
* Refactor a few functions, filters.
* Introduced <code>timeline_express_container_classes</code> filter to add additional classes to the parent Timeline Express container <code>#cd-timeline</code>.
* Set the default horizontal timeline to initialize as a slider.
* Remove excess css/js file banners.

= 1.3.6 - February 11th, 2017 =
* Introduce Horizontal timelines. Add <code>horizontal="1"</code> to the <code>[timeline-express]</code> shortcode to generate a horizontal timeline. See the documentation for additional parameters.

<strong>1.3.5 - February 8th, 2017 =
* Remove filter from date sanitize function, causing issues when users were storing 'Y' date formats.
* Prepping for Pop-ups add-on
* Refactored functions
* Tweaked styles

= 1.3.4 - January 28th, 2017 =
* Fix incompatability with toolbox add-on
* Fix image issue
* Update i18n functions
* Update version
* Fix grunt file and tasks
* Introduce $link parameter to helper function
* Add toolbox add-on to add-on page
* Updated EDD_SL_Plugin_Updater to v1.6.9

= 1.3.3 - December 15th, 2016 =
* Bumped font awesome version to 4.7.0.
* Built in support for Twenty Seventeen theme inside of page wrapper templates.
* Tweaked front end icon container box shadow and margin on hover.
* Introduced the `` filter. (see <a href="/documentation/alter-announcement-date-string/">documentation</a> for example usage.)
* Fix php warning after support ticket has been submitted.

= 1.3.2 - Nov. 25th, 2016 =
* Adjusted image styles.
* Added *new* pagination, to split lengthy timelines - usage `[timeline-express limit="5" pagination="1"]`
* Added new styles to control the pagination appearance
* Included new filter `timeline_express_pagination_disable_additional_links` to disable the Next/Previous links on the pagination. - usage add_filter( 'timeline_express_pagination_disable_additional_links', '__return_true' );
* Refactored a few functions.

= 1.3.1 - Nov. 15th, 2016 =
* Refactored the Timeline Express JavaScript.
* Removed old collision detection - which caused performance issues for some users.
* Removed `include_once` on our custom time stamp field, so it can be added to the announcements more than once.
* Set announcement years to `color: #fff;` (when displayed instead of icons).

= 1.3.0 =
* Added new hooks to the options page.
* Introduced `timeline_express_announcement_query` filter, to filter the final WP_Query object (including results).
* Introduced `timeline_express_announcement_permalink` filter, to filter the read more link URL.
* Introduced new helper functions: `get_timeline_express_add_ons()`, `add_timeline_express_add_on`, `remove_timeline_express_add_on`, `timeline_express_generate_options_header`, `timeline_express_generate_options_tabs`
* Added new styles for the options page header/tabs.
* Added missing local font awesome icons.
* Define new constant to load font awesome icons ( `define( 'TIMELINE_EXPRESS_FONT_AWESOME_LOCAL', true );` - added to theme functions.php will load font awesome icons locally instead of Font Awesome CDN).
* Bumped version numbers.

= 1.2.9 =
* Update shortcode filter from `timeline-express-pro` to `timeline-express`, to map add-ons properly.
* Added usage tracking class.
* Cleaned up a few functions.
* Added new site options to delete on plugin uninstall.
* Fixed undefined variable in admin table (orderby).
* Cleaned up all i18n strings, and related functions.
* Regenerated translation files, with new string functions.
* Updated Grunt file with new translation tasks.

= 1.2.8 =
* Re-laid out/Re-styled the welcome page.
* Fixed icon placement on the timeline when the read more links are hidden.
* Updated to the latest stable version of CMB2.
* Refactored the Timeline Express JavaScript file.
* Adjusted the 'collision detection' scripts.
* Tweak front end timeline layout styles.
* Update text-domain from `timeline-express` to `timeline-express-pro`.
* Added missing languages folder.
* Update strings, add new .mo files.
* Included po2mo in grunt file.

= 1.2.7 =
* Tweaked the functions used when `TIMELINE_EXPRESS_YEAR_ICONS` is defined in the theme.
* Introduced new helper function `timeline_express_get_announcement_date_timestamp()`, to retrieve the UNIX time stamp date of the announcement (unformatted).
* Tweaked styles on the options page.
* Introduced the templating engine, to better fit announcements into themes.
* Added WPML config file.

= 1.2.6.6 - August 14th, 2016 =
* Prepping for post types add-on.
* Tweaked the shortcode generator script.
* Re-minified scripts/styles and bumped version and banner files.

= 1.2.6.5 - July 21st, 2016 =
* Fixed the 'Read More' link wrapping the icon when disabled.
* Tweaked responsive styles, set containers to 100% on mobile.

= 1.2.6.4 - July 3rd, 2016 =
* Updated the licensing save function, as it was unclear what was happening.
* Updated certain links throughout the plugin.
* Added new single container content wrappers, to help integrate with existing themes better.
* Bundled new `content-wrapper-start.php/content-wrapper-end.php` templtes which can be overridden.

= 1.2.6.3 - June 18th, 2016 =
* Re-factored shortcode generator .js to work with checkboxes

= 1.2.6.2 - June 15th, 2016 =
* Removed un-used `single.timeline-express.php` template file.
* Cleaned up the filtering functions. At times they were incorrectly hidden.
* Re-factored the WP_Query filters, to take into account a single category/timeline set in the shortcode (eg: `[timeline-express categories="1" timeline="2,3" filters="1"]` will not show the filters for category 1, but will limit the timeline to those announcements.)
* Bumped version number.

= 1.2.6.1 - June 6th, 2016 =
* Repaired announcement custom post type parameters (public, exclude_from_search & publicly_queryable)
* Single announcements should now be viewable, without re-directing back to the homepage.

= 1.2.6 - June 5th, 2016 =
* Bump version up.
* Removed all RSS feed dependencies.
* Added missing 'announcement-banner-image' to the announcement image on single templates.
* Repaired single template check, so the template is loaded in it's appropriate location. (swapped out && for || inside `timeline_express_announcement_single_page_template()``)
* Fixed announcements displaying in site searches setting.
* Fixed issue where `TIMELINE_EXPRESS_YEAR_ICONS` constant caused media gallery grid layout to break.

= 1.2.5 - May 24th, 2016 =
* Tweak how the plugin handles uninstalls.
* Bump version numbers.
* Tweak front end styles.
* Use srcset attribute for responsive images on single announcements.
* Timeline now adjusts to 95% width on screens below 822px.

= 1.2.4 - May 18th, 2106 =
* Multiple timelines now initialize and layout properly on the same page.
* Updated the templating engine.
* Updates to styles.
* Bumped version number.
* New templates.
* Refactored functions.
* Added new helpers, new filters etc.

= 1.2.3 - May 13th, 2106 =
* Added a new conditional to load the proper template. (Fixed wrong template loaded when displaying the timeline inside a post)
* Repaired the bootstrap selector - any missing icons should now be present.

= 1.2.2 - May 10th, 2016 =
* Re-added the 'Read more' button to <em>all</em> announcements, regardless of length
* Removed the 'Read More' text/link after the ellipses
* Fixed theme overriding timeline excerpt lengths, added priority 999 and a post_type() conditional
* Fixed weird admin responsive issue due to hard coded widths on the columns
* Localized the date picker to honor the date format setting inside of 'Settings > General > Date Format'
* Fixed the 'Read More' toggle setting not properly removing the links from the icons

= 1.2.1 - May 9th, 2016 =
* Repair `timeline_express_frontend_excerpt` filter.
* Used wp_kses_post() instead of esc_attr() when printing the excerpts.

= 1.2 - May 8th, 2016 =
* Refactored plugin base to improve stability - split code base into classes and re-wrote many functions.
* Plugin now more extendable, and much easier to style and customize.
* New templating system in place, to allow for users to override, for help please see our [customization articles](https://www.wp-timelineexpress.com/?s=customize&post_type=kbe_knowledgebase).
* Plugin WordPress compatible, following all standards.
* Transients setup for front end caching (page transient caching to allow for different timelines on different pages).
* Flush re-write rules now properly setup, to flush when needed (saving a page with the shortcode on it, saving the settings, creating/updating an announcement).
* Started writing unit tests for improved future proofing.
* Tweaked front end styles a bit, to improve consistency between themes.
* Documentation site being built out, to help with questions. (https://www.wp-timelineexpress.com/documentation/)
* Added additional filters and hooks to help with customizations.
* Wrapped every string in the plugin in the appropriate gettext filters.
* Added new options to disable animations and prevent scroll/fade in animations.
* Tested migration from 1.1.8.2 to 1.2 - including new options.
* Added new optional shortcode parameters `limit` (integer, limiting the number of announcements to display), `display` ('Future', 'Past' or 'All' to set which announcements will display on the timeline) and `sort` ('DESC' or 'ASC' to set the order of the timeline).
* Frontend inline styles are now more reliable in overriding the appropriate elements.

= 1.1.7.8 =
* Added new hooks/filters (prep for AJAX loader add-on)
* New parameters to existing hooks/filters

= 1.1.7.7 =
- Resolved issue when filtering multiple timelines/categories was only displaying a single category.
- Now using the WordPress local time to dictate the current date for query comparisons (future vs past).

= 1.1.7.6 =
- Updated filtering scripts.
- Resolved filtering leading to a 404 page - due to query strings being incorrect.

= 1.1.7.5 =
- Re-worked the support page, gearing up for the historical dates add-on (http://www.wp-timelineexpress.com/products/timeline-express-historical-dates-add-on/) and others.
- Built in hooks for add-ons to make use of.

= 1.1.7.4 =
- Fixed reversed `s_ssl()` checks.

= 1.1.7.3 =
- Cleaned up a few styles.
- Re-factored some code.
- Updated referrer URL on single announcements.

= 1.1.7.2 =
- Cleaned up weird URL displaying on the front end.
- Resolved undefined variable in category filter.
- Resolved duplicate dates displaying.
- Built in RTL support.

= 1.1.7.1 =
- Built in filters for additional add-ons to be compatible with the Timeline Express pro.
- Altered date formats for international folks.
- Removed filters from `<form>` element, to prevent issues with the back button.
- Added query args to the back button, so when users head back the filtering options remain the same.

= 1.1.7 =
- Localized dates now working for date pickers (eg: dd/mm/yy or d/m/y) - see https://github.com/WebDevStudios/CMB2-Snippet-Library/blob/master/filters-and-actions/localize-date-format.php
- Wrapped single announcement date in the appropriate `timeline_express_custom_date_format` filter, to allow the dates to change in the single template as well as in the timeline.
- Fixed issue with icons stacking when multiple timelines were used on a single page.
- Fixed typo on the support page form button
- Fixed link back to support page
- Added link to Timeline Express documentation page

= 1.1.6.9 =
- Altered a few styles, and removed the .js class additions on page load. (Timelines should remain full width in their container)
- Stored cross license check data in a transient to prevent heavy load times and queries on every page load.
- Added assigned timelines and assigned category slugs to the `.cd-timeline-block` container so you can now style containers and it's descendants based on assigned classes/timelines.

= 1.1.6.8 =
- Added a new filter to allow users to alter the query arguments how they need. `timeline_express_announcement_query_args` (see readme for usage example)

= 1.1.6.7 =
- Removed limitation of announcement exceprt.

= 1.1.6.6 =
- Repaired the way the announcement dates were being stored in the database (this caused improper query results on the front end)

= 1.1.6.5 =
- Re-worked the way images are retrieved from the database both on the front end and in the announcement post list table
- Enabled multiple timelines to be displayed on the same page
- Cleared up .js error thrown on announcement post table
- Adjusted thumbnail sizes to better fit in with the announcement post table

= 1.1.6.4 =
- Updated the entire filtering process - setting 'Timelines' and/or 'Cateogires' will display those announcements only.
- Timeline and Category filters now only display items you have specified in your shortcode parameters. To display all announcements + all filtering options, add all timelines and categories to your shortcode and set the filter parameter to 1 (filter="1").
- A few style adjustments to the filtering options

= 1.1.6.3 =
- Swap date time function to allow for dates pre-1970 to be properly stored and called.
- Fixed broken filtering (both categories and dropdown)

= 1.1.6.2 =
- Resolve conflict with 'Give' plugin

= 1.1.6.1 =
- Fix issue with line, background containers and arrow not changing when settings changed.
- Fix undefined variables and errors thrown inside modal since CMB2 change

= 1.1.6 =
- Resolved bootstrap script conflicts on a few themes, renamed bootstrap script name to bootstrap.min from bootstrap-min
- Upgraded CMB to CMB2, updated all associated functionality, scripts and styles.

= 1.1.5 =
- Packaged Hungarian translation (hu_HU) - thanks goes to <a href="http://www.keszites.com/" target="_blank">Zsolt</a>

= 1.1.4 =
- Fixed style breakpoint on ipads
- Set full width images (caused style issues on some devices)
- Added Hungrain translation - props [Zsolt](http://www.keszites.com/)

= 1.1.3 =
- Fixed remote activation/deactivation/support requests (License page)

= 1.1.2 =
- Fixed issue where users could not enter license key into the support page
- Fixed issue where users could not submit support requests via the support page

= 1.1.1 =
- Added new .pot translation file to plugin directory. All translations can now be updated with newly added strings.

= 1.1.0 =
- Added a GUI/shortcode generator to select categories and timelines from when adding your shortcode to a post or page
- Fixed post metabox re-arrangement on posts other than our announcement custom post type
- Fixed minor style issue on filter check boxes when no timeline drop down was displayed

= 1.0 - April 11th, 2015 =
* Enhancement: Added support for multiple timelines
* Enhancement: New parameter 'timeline' to specify which timelines posts you want to display on a given timeline (defaults to all)
