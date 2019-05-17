=== Simple Custom CSS and JS PRO ===
Created: 06/12/2015
Contributors: diana_burduja
Email: diana@burduja.eu
Tags: CSS, JS, javascript, custom CSS, custom JS, custom style, site css, add style, customize theme, custom code, external css, css3, style, styles, stylesheet, theme, editor, design, admin
Requires at least: 3.0.1
Tested up to: 5.0
Stable tag: 4.10
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires PHP: 5.2.4

Easily add Custom CSS or JS to your website with an awesome editor.

== Description ==

Customize your WordPress site's appearance by easily adding custom CSS and JS code without even having to modify your theme or plugin files. This is perfect for adding custom CSS tweaks to your site.

= Features =
* **Text editor** with syntax highlighting
* Print the code **inline** or included into an **external file**
* Print the code in the **header** or the **footer**
* Add CSS or JS to the **frontend** or the **admin side**
* Add as many codes as you want
* Keep your changes also when you change the theme

= Frequently Asked Questions =
* **Can I recover the codes if I previous uninstalled the plugin?**
No, on the `Custom CSS and JS` plugin's uninstall all the added code will be removed. Before uninstalling make sure you don't need the codes anymore.

* **What if I want to add multiple external CSS codes?**
If you write multiple codes of the same type (for example: two external CSS codes), then all of them will be printed one after another

* **Will this plugin affect the loading time?**
When you click the `Save` button the codes will be cached in files, so there are no tedious database queries.

* **Does the plugin modify the code I write in the editor?**
No, the code is printed exactly as in the editor. It is not modified/checked/validated in any way. You take the full responsability for what is written in there.

* **My code doesn't show on the website**
Try one of the following:
1. If you are using any caching plugin (like "W3 Total Cache" or "WP Fastest Cache"), then don't forget to delete the cache before seing the code printed on the website.
2. Make sure the code is in **Published** state (not **Draft** or **in Trash**).
3. Check if the `wp-content/uploads/custom-css-js` folder exists and is writable

* **Does it work with a Multisite Network?**
Yes.

* **What if I change the theme?**
The CSS and JS are independent of the theme and they will persist through a theme change. This is particularly useful if you apply CSS and JS for modifying a plugin's output.

* **Can I use a CSS preprocesor like LESS or Sass?**
No, for the moment only plain CSS is supported.

* **Can I upload images for use with my CSS?**
Yes. You can upload an image to your Media Library, then refer to it by its direct URL from within the CSS stylesheet. For example:
`div#content {
    background-image: url('http://example.com/wp-content/uploads/2015/12/image.jpg');
}`

* **Can I use CSS rules like @import and @font-face?**
Yes.

* **CSS Help.**
If you are just starting with CSS, then here you'll find some resources:
* [codecademy.com - Learn HTML & CSS](https://www.codecademy.com/learn/web)
* [Wordpress.org - Finding Your CSS Styles](https://codex.wordpress.org/Finding_Your_CSS_Styles)

== Installation ==

* From the WP admin panel, click "Plugins" -> "Add new".
* In the browser input box, type "Simple Custom CSS and JS".
* Select the "Simple Custom CSS and JS" plugin and click "Install".
* Activate the plugin.

OR...

* Download the plugin from this page.
* Save the .zip file to a location on your computer.
* Open the WP admin panel, and click "Plugins" -> "Add new".
* Click "upload".. then browse to the .zip file downloaded from this page.
* Click "Install".. and then "Activate plugin".

OR...

* Download the plugin from this page.
* Extract the .zip file to a location on your computer.
* Use either FTP or your hosts cPanel to gain access to your website file directories.
* Browse to the `wp-content/plugins` directory.
* Upload the extracted `custom-css-js` folder to this directory location.
* Open the WP admin panel.. click the "Plugins" page.. and click "Activate" under the newly added "Simple Custom CSS and JS" plugin.

== Frequently Asked Questions ==

= Small incompatibilities =
* If the [qTranslate X](https://wordpress.org/plugins/qtranslate-x/) plugin is adding some `[:]` or `[:en]` characters to your code, then you need to remove the `custom-css-js` post type from the qTranslate settings. Check out [this screenshot](https://www.silkypress.com/wp-content/uploads/2016/08/ccj_qtranslate_compatibility.png) on how to do that.

* The [HTML Editor Syntax Highlighter](https://wordpress.org/plugins/html-editor-syntax-highlighter/) plugin will make the Beautify and Fullscreen editor buttons not work properly.


== Changelog ==

= 4.10 =
* 07/12/2018
* Fix: the Edit Custom Code page was blank for WordPress 5.0 and the Classic Editor enabled

= 4.9 =
* 09/10/2018
* Feature: add the add/edit/delete custom post capabilities to the admin and 'css_js_designer' roles on plugin activation
* Fix: add to the admin the capabilities of the custom-css-js custom post

= 4.8 =
* 04/09/2018
* Fix: rebuild the custom-css-js-tree when activating the pro version, otherwise some HTML codes don't show up in the frontend
* Fix: keep the editor LTR even on RTL websites
* Fix: catch the syntax errors from WP Conditional Tags
* Fix: on multi-site installations any HTML code from the subsite was replaced with one from the main site 

= 4.7 =
* 13/07/2018
* New: allow importing one custom code from another for Less/SASS CSS codes
* Fix: PHP warning at "Apply only on these Pages" if the JSON is not properly formatted
* Fix: the default comment for JS for other locales than "en_" was removing the <scripts> tags
* Tweak: add some missing translations
* Tweak: make the search dialog persistent
* Tweak: use default codemirror.js scrollbar style instead of "simple" in order to avoid conflicts with other plugins
* New: allow shortcodes in a custom HTML code

= 4.6 =
* 15/05/2018
* Tweak: hide the license key on the Settings page
* Tweak: design the custom codes table for screens smaller than 786px
* Fix: check all the functions if exists before declaring them
* Fix: the time accounts also for the timezone

= 4.5 =
* 15/04/2018
* Fix: PHP warning when displaying time for PHP 7.2.x
* Tweak: use the CSS and JS minifier from Matthias Mullie 
* Fix: remove ";" character from the "WP Conditional Tags"
* Tweak: compatibility with WP Quads Pro plugin

= 4.4 =
* 28/03/2018
* Fix: the codes table was squeezed to 70% of the screen width
* Fix: it was impossible to turn off the "Apply network wide" option 
* Change: check the option name against an array of allowed values
* Tweak: tinyMCE menu, add "No shortcodes defined" message
* Tweak: show the network defined shortcodes in the tinyMCE menu
* Tweak: show an "Network wide" icon in the table of custom codes

= 4.3 =
* 10/03/2018
* Fix: deleting a rule from "Apply on these pages" was mistakenly deleting two rule
* Fix: date format on the Custom Codes table list
* Tweak: redesign the Settings page
* Tweak: show more information about the license

= 4.2 =
* 20/02/2018
* Fix: error undefined ccj_editor_theme
* Tweak: show notice to uninstall the free version

= 4.1 =
* 16/02/2018
* Fix: use the `login_init` hook for the login page

= 4.0 =
* 11/02/2018
* Feature: Network-wide custom codes
* Option "Use HTTPS for the main site" for MultiSite installations
* Fix: change syntax highlighting for Less and Sass
* Tweak: for post.php and post-new.php page show code's title in the page title
* Fix: allow admin stylesheets from ACF plugin, otherwise it breaks the post.php page
* Feature: lint on editor

= 3.15 =
* 23/01/2018
* Tweak: "Apply only on these Pages" accepts also absolute URLs
* Tweak: add Editor Shortcuts to the Help area
* Feature: auto close brackets for the editor
* Feature: autocomplete on Ctrl + Space

= 3.14 =
* 15/01/2018
* Feature: add the "Encode the HTML tags" option

= 3.13 =
* 12/01/2018
* Feature: add the "Keep the HTML entities, don't convert to its character" option

= 3.12 =
* 08/01/2018
* Fix: https://wordpress.org/support/topic/footer-code-position-before-external-scripts-is-overridden/
* Fix: https://wordpress.org/support/topic/annoying-bug-in-text-editor/

= 3.11 =
* 03/01/2018
* Feature: add filter by code type 
* Feature: make the 'Modified' column sortable
* Fix: https://wordpress.org/support/topic/broken-layout-of-code-snippet-type-color-tag-css-html-js-on-main-list-table/
* Fix: if the default comment remains in the "Add Custom JS", then there was no 'script' tags added to the code, as the comment contained a 'script' tag

= 3.10 =
* 14/11/2017
* Fix: change the ids of the loaded assets in admin in order to avoid conflicts
* Fix: remove the iframe footer hook in order to avoid conflict with the `HTML Editor Syntax Highlighter` plugin
* Fix: for revisions set the modal with `top` instead of `margin-top`

= 3.9 =
* 19/10/2017
* Declare compatibility with WooCommerce 3.2 (https://woocommerce.wordpress.com/2017/08/28/new-version-check-in-woocommerce-3-2/)
* Fix: avoid conflicts with other plugins that implement the CodeMirror editor
* Update the CodeMirror library to the 5.28 version

= 3.8 =
* 29/09/2017
* Fix: "Apply only on these Pages: First Page" needs to use home_url() instead of get_option('home') for multi-site installations
* Fix: allow the "Please activate the license" meta box

= 3.7 =
* 07/09/2017
* Fix: compatibility with the CSS Plus plugin

= 3.6 =
* 06/08/2017
* Fix: show the preview also when there are no other codes defined
* Tweak: comment about linking an external JS

= 3.5 =
* 27/07/2017
* Feature: prepare the plugin for translation
* Tweak: show date according to the get_option('date_format')
* Fix: the Custom Codes table is responsive for narrower screens
* Fix: initialize the codes with `wp` on frontend and `init` on backend.

= 3.4 =
* 15/07/2017
* Fix: rename the EDD_SL_Plugin_Updater class to avoid conflicts with other plugins that update with this class
* Security fix according to VN: JVN#31459091 / TN: JPCERT#91837758
* Add activate/deactivate link to row actions and in Publish box
* Make the activate/deactivate links work with AJAX
* Feature: option for adding Codes to the Login Page

= 3.3 =
* 13/06/2017
* Fix: compatibility issue with the HTML Editor Syntax Highlighter plugin
* Fix: remove htmlentities in the editor

= 3.2 =
* 04/04/2017
* Fix: allow codes in the backend

= 3.1 =
* 22/12/2016
* Feature: use WP Conditional Tags to restrict code
* Feature: wrap the lines in the editor
* Feature: Beautify Code editor button
* Feature: Fullscreen editor button
* Fix: cURL error because the activation license was done on silkypress.com:80

= 3.0 =
* 07/11/2016
* Feature: shortcodes functionality for the HTML codes
* Check: compatibility with WordPress 4.7

= 2.9 =
* 03/11/2016
* Fix: when Toolset Types plugin was enabled, the editor lost the colors

= 2.8 =
* 16/10/2016
* Fix: add stripslashes before preprocessors or minifiers, not after
* Fix: on activation along the free version there was a warning because both plugins used the same CCJ_VERSION constant

= 2.7 =
* 14/10/2016
* Feature: keep the cursor position after saving
* Fix: for empty allowed_codes() the search_tree() was still printed

= 2.6 =
* 24/08/2016
* Feature: add HTML code
* Feature: choose priority for the codes

= 2.5 =
* 03/08/2016
* Feature: Search functionality for the editor
* Feature: add "Add CSS Code" and "Add JS Code" buttons in order to be consequent with the WP admin interface
* Fix: make the editor wrapper adapt as width to the editor's line numbers gutter
* Fix: adapt the editor wrapper theme to the editor's theme
* Fix: incompatibility with other plugins that load CodeMirror (https://wordpress.org/support/topic/almost-everything-disabled)
* Fix: show warning when qTranslate is activated (https://wordpress.org/support/topic/conflict-with-qtranslate-x-2)
* Compatibility with WP 4.6: the "Modified" column in the Codes listing was empty

= 2.4 =
* 01/07/2016
* Fix: PHP warning for no codes in the $allowed_codes array
* Feature: change the editor's scrollbar so it can be caught with the mouse

= 2.3 =
* 24/06/2016
* Feature: "Debug Info" tab to the settings in order to help debugging
* Fix: when the Allowed Codes is empty, the Search Tree was not filtered

= 2.2 =
* 12/06/2016
* Initial commit

== Upgrade Notice ==

Nothing at the moment
