=== Shortcoder ===
Contributors: vaakash
Author URI: https://www.aakashweb.com/
Plugin URI: https://www.aakashweb.com/wordpress-plugins/shortcoder/
Tags: shortcode, html, javascript, shortcodes, short code, posts, pages, widgets, insert, adsense, ads, snippets,
Donate link: https://goo.gl/qMF3iE
License: GPLv2 or later
Requires at least: 4.4.0
Tested up to: 5.0.3
Stable tag: 4.3

Create custom "Shortcodes" easily for HTML, JavaScript snippets and use the shortcodes within posts, pages & widgets.

== Description ==

Shortcoder is a plugin which allows to create a custom shortcode and store HTML, Javascript and other snippets in it. So if that shortcode is used in any post or pages, then the code stored in the shortcode get exceuted in that place.

= Create custom shortcodes easily =
1. Give a name for the shortcode
1. Paste the HTML/Javascript as content
1. Save !
1. Now insert the shortcode `[sc name="my_shortcode"]` in your post.
1. **Voila !** You got the HTML/Javascript in your post.

= Features =

* Create **"custom shortcodes"** easily and use them within WordPress
* Use any kind of **HTML** as Shortcode content.
* Insert: **Custom parameters** in shortcode
* Insert: WordPress parameters in shortcode
* Visual editor for adding shortcode contents.
* Globally disable the shortcode when not needed.
* Can disable the shortcode to admins.

[youtube="https://www.youtube.com/watch?v=GrlRADfvjII"]

= An example usage =

1. Create a shortcode named "adsenseAd" in the Shortcoder admin page.
1. Paste the adsense code in the box given and save it.
1. Use `[sc name="adsenseAd"]` in your posts and pages.
1. Tada !!! the ad appears in the post.

* Using this idea, shortcodes can be created for frequently used snippets.
* You can also add parameters (like `%%id%%`) inside the snippets, and vary it like `[sc name="youtube" id="GrlRADfvjII"]`
* This plugin will be hugely useful to all !!!

= Resources =

* [Documentation](https://www.aakashweb.com/wordpress-plugins/shortcoder/)
* [FAQs](https://www.aakashweb.com/faqs/wordpress-plugins/shortcoder/)
* [Support](https://www.aakashweb.com/forum/)
* [Report Bugs](https://www.aakashweb.com/forum/)

== Installation ==

1. Extract the zipped file and upload the folder `Shortcoder` to to `/wp-content/plugins/` directory.
1. Activate the plugin through the `Plugins` menu in WordPress.
1. Go to the "Shortcoder" admin page. Admin page is under the "Settings" menu.
1. Enter a shortcode name.
1. Paste some code in it.
1. Then use the shortcode `[sc name="name of the shortcode"]` in your post. ex: If "youtube" is the shortcode name, then just use `[sc name="youtube"]` in your posts
1. That's all ! 

You can also insert some parameters within the post. Check this page to [learn more](https://www.aakashweb.com/wordpress-plugins/shortcoder/).

== Frequently Asked Questions ==

Please visit the [Plugin homepage](https://www.aakashweb.com/wordpress-plugins/shortcoder/) for lots of FAQ's. Selected are given here.

= I've created a shortcode, how to use it ? =

For example, consider you made a shortcode "advertisement". Then you should use the shortcode `[sc name="advertisement"]` in your post.

= How to temporarily disable a shortcode ? =

Just check the "Temporarily disable this shortcode" in the shortcode admin page to disable it. 
Note: When you disable a shortcode, the shortcode will not be executed in the page.

[More FAQs](https://www.aakashweb.com/docs/shortcoder-doc/)

== Screenshots ==

1. Shortcoder admin page.
2. Editing a shortcode.
3. Popup to select and insert shortcode into posts.
4. A shortcode inserted into a post.
5. The shortcode executed in the post.

[More Screenshots](https://www.aakashweb.com/wordpress-plugins/shortcoder/)

== Changelog ==

= 4.3 =
* New: Edit shortcode name after creating.
* New: Post modified date parameter added.
* Fix: Date parameters now display in site language.

= 4.2 =
* Fix: Some plugins fail to fire onload JS event since it was overwritten by shortcoder.
* Fix: Javascript in insert shortcode popup not working in IE 11.
* Fix: Missing parenthesis while calling `is_year`.
* Fix: Widgets page not loading insert shortcode popup.
* Fix: Removed settings emoji icon from plugin actions list.
* Fix: Load latest version 5.42.0 of codemirror.
* Fix: Updated minimum required WordPress version.

= 4.1.9 =
* Fix: Minor UI refinements for better experience.
* Fix: Import error where some exported JSON files have 0 as EOF.

= 4.1.8 =
* New: Insert custom fields in shortcode content.
* Fix: Removed comments in shortcode output

= 4.1.7 =
* New: Categorize, search and filter shortcodes using "tags".
* New: Last used shortcode editor will be saved along with shortcode.
* New: Enclosed shortcode content can now be used as shortcode parameter.
* New: Active line highlight has been enabled for code editor.
* Fix: Codemirror has been updated to latest version.
* Fix: Minor admin interface enhancements.

= 4.1.6 =
* New: Date variables can noe be added into shortcode content.
* Fix: Error "trying to get property of non-object" is handled.

= 4.1.5 =
* New: Bloginfo variables can now be added into shortcode content.

= 4.1.4 =
* New: Codemirror powered syntax highlighted shortcode content code editor (beta).

= 4.1.3 =
* Fix: Shortcode names with not-allowed characters cannot be edited/deleted.
* New: Shortcode imports made can now be fresh or overwritten.
* New: Only users with `manage_options` capability will see "edit shortcode" option in insert window.
* Fix: Import failure with UTF-8 characters.
* Fix: Case sensitive search in admin pages.
* Fix: Minor admin interface changes.

= 4.1.2 =
* New: Search box for shortcodes in admin page.

= 4.1.1 =
* Fix: HTTP 500 error because of syntax error in import module.

= 4.1 =
* New: Import/export feature for shortcodes.
* Fix: Visual editor is now disabled by default.
* Fix: Added instructions in admin page.

= 4.0.3 =
* New: Added feature to sort created shortcodes list.
* Fix: HTML errors in admin page

= 4.0.2 =
* Fix: Sometimes `get_current_screen()` was called early in some setups. 

= 4.0.1 =
* Fix: Servers with PHP version < 5.5 were facing HTTP 500 error because of misuse of PHP language construct in code.

= 4.0 =
* New: Plugin rewritten from scratch.
* New: Brand new administration page
* New: Shortcode vissibility settings, show/hide in desktop/mobile devices
* New: Insert WordPress information into shortcode content.
* Fix: Insert shortcode window is not loading.
* Fix: Unable to delete the shortcodes

= 3.4.1 =
* Fixed Shortcoder not working in WordPress 4.4
* Changed the shortcoder syntax from `[sc:the_name]` to `[sc name="the_name"]` permanently in effect of WordPress 4.4 changes.

= 3.4 =
* New feature: Embedded/Nested shortcodes is now supported.
* New feature: Full fledged native WordPress editor for adding shortcode content with media buttons.
* Bug fix: "duplicate percentage" in content on plugin reactivate.
* Updated admin UI with fixed errors.
* Updated "insert shortcode" interface is revised and some issues are fixed.
* Updated with translatable texts in admin page.
* Minor code revision and changes.

= 3.3 =
* Fixed bug in loops using `foreach`.
* Fixed several PHP notices.

= 3.2 =
* Moved the shortcoder admin page to the "Settings" menu.
* Some admin page issues are fixed.

= 3.1 =
* Changed the "Custom parameter" syntax from %param% to %%param%%
* Code revision.

= 3.0.1 = 
* Added license tag to the readme file.

= 3.0 =
* Plugin code rewritten from scratch.
* Shortcode syntax is changed.
* Supports any custom parameters.
* Admin interface is redesigned and easy to use.
* Added a tinyMCE button to the editing toolbar for inserting the shortcodes in the post.
* Inbuilt shortcodes are removed.

= 2.3 =
* Can disable the shortcode to Administrators.
* Admin interface changed.

= 1.3.1 =
* Changed the folder name's case and some minor bugs.
* Code revision.

= 1.3 =
* Initial Version with 5 inbuilt shortcodes.

(Pre made versions are not released)

== Upgrade Notice ==

4.0 is a major upgrade. Entire code is rewritten from scratch.