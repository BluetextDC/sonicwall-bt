=== My Custom Functions PRO ===
Contributors: Arthur Gareginyan
Tags: code, php, function, snippet, custom, execute, edit, editing, editor, functionality plugin, codemirror, syntax highlighting, syntaxhighlighting, syntax highlighter, syntaxhighlighter, syntax,
Donate link: https://www.spacexchimp.com/donate.html
Requires at least: 3.9
Tested up to: 4.9
Stable tag: 2.12
License: GPL3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Easily and safely add your custom functions (PHP code) directly out of your WordPress Admin Area, without the need to have an external editor.


== Description ==

"My Custom Functions PRO" is a premium WordPress plugin that gives you the ability to easily and safely add your custom functions (PHP code) for execution in the WordPress environment directly out of your WordPress Admin Area, without the need to have an external editor. This is a "PRO" version of the plugin "My Custom Functions". More features, more power. Unlimited number of fields, Toggle for temporary disabling the certain fields, and Automatic backup of all functions to a file.

Its purpose is to provide a familiar experience to WordPress users. No need for any more editing of the functions.php file of your theme. Just add your PHP code in the field on the plugin settings page and this plugin will do the rest for you.

It's really useful in case of any theme update, because your custom PHP code would never be overwritten. Your custom PHP code will keep on working, no matter how many times you upgrade or switch your theme.

On the plugin settings page you find the PHP editor powered by CodeMirror. It has syntax highlighting and line numbering options. Also this editor supports a tab indentation.

This is a simple and perfect tool to use as your website's functionality plugin.

**Features**

* Lightweight and fast
* Secure code with using clear coding standards
* Intuitive interface with many settings
* Cross browser compatible (work smooth in any modern browser)
* Compatible with all WordPress themes
* RTL compatible (right to left)
* Translation ready

**Key features include...**

* Checks the entered code for fatal errors
* Ability to temporarily disable all custom functions
* Easy disable option for WSOD
* Syntax highlighting (by CodeMirror)
* Line numbering
* Active line highlighting
* Editor allow for tab indentation
* And much, much more!

**PRO features include...**

* Unlimited number of fields
* Automatic backup of all functions to a file
* Ability to temporarily disable the certain function
* Ability to collapse/expand the certain fields with code
* Well documented

**Coming soon:**

* Reload the settings page at same position after pushing the save button
* Multisite network support

**Translation**

This plugin is ready for translation and has already been translated into several languages.

* English (default)
* Russian (translation by [Milena Kiseleva](https://www.instagram.com/milava_kiseleva/))
* German (translation by Michael)
* Chinese-Taiwan (translation by Gordon Yu)
* Spanish (translation by Ramiro Garcés and Patricio Toledo)
* French (translation by Theophil Bethel)

If you want to help translate this plugin then please visit the [translation page](https://translate.wordpress.org/projects/wp-plugins/my-custom-functions).

**Minimum system requirements:**

* [PHP](https://php.net) version **5.2** or higher.
* [MySQL](https://www.mysql.com) version **5.0** or higher.

**Recommended system requirements:**

* [PHP](https://php.net) version **7.0** or higher.
* [MySQL](https://www.mysql.com) version **5.6** or higher.


== Installation ==

Install "My Custom Functions PRO" just as you would any other WordPress Plugin.

Upload via WordPress:

1. Log in to Admin Area of your WordPress website.
2. Go to "`Plugins`" -> "`Add New`".
3. Click "`Upload Plugin`", and browse the ZIP file with plugin.
4. Activate this plugin through the "`Plugins`" tab.

Upload via FTP:

1. Unzip the ZIP file with plugin.
2. Upload the unzipped catalog to your website's plugin directory (`/wp-content/plugins/`).
3. Log in to Admin Area of your WordPress website.
4. Activate this plugin through the "`Plugins`" tab.

After installation and activation, the "`PHP Inserter PRO`" menu item will appear in the "`Settings`" section of Admin Area. Click on it in order to view the plugin settings page.

[More help installing plugins](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins "WordPress Codex: Installing Plugins")


== Frequently Asked Questions ==

= Q. Will this plugin work on my WordPress.COM website? =
A. Sorry, this plugin is available for use only on self-hosted (WordPress.ORG) websites.

= Q. Can I use this plugin on my language? =
A. Yes. This plugin is ready for translation and has already been translated into several languages. But If your language is not available then you can make one. The POT file is included and placed in the "`languages`" folder. Just [send the PO file to us](https://www.spacexchimp.com/contact.html) and we will include this translation within the next plugin update. Many of plugin users would be delighted if you share your translation with the community. Thanks for your contribution!

= Q. How does it work? =
A. Simply go to the plugin settings page, place your PHP code in the field, switch the toggle to the "ON" position and click the "Save changes" button. Enjoy the result of applying your PHP code. It's that simple!
You can find the plugin settings page at "`WordPress Admin Area`" -> "`Settings`" -> "`PHP Inserter PRO`".

= Q. Can I use HTML/CSS/JS code integrated in PHP code? =
A. Yes. But you need to do it properly, like this:

`function NameOfYourFunction {

    echo "<script>
		// Your JS code
	  </script>";

}`

= Q. How much of PHP code (characters) I can enter in the text field? =
A. We don't limit the number of characters.

= Q. On the plugin settings page, an error message appears. What could be wrong? =
A. Here are a few of the most likely causes of the error message:

1. You make a syntax error in the code that you have entered. Check the syntax of your code and try again.
2. You entered two functions with the same name. Use a unique names for your functions.
3. You have entered function with a name that is already occupied by another function. Use a unique name for your function.
4. You are trying to overwrite an existing function (of WordPress, theme, or plugin). Instead, use filters and hooks.

= Q. Does this plugin requires any modification of the theme? =
A. Absolutely not. This plugin is configurable entirely from the plugin settings page that you can find in the Admin Area of your WordPress website.

= Q. Does this require any knowledge of HTML or CSS? =
A. This plugin can be configured with no knowledge of HTML or CSS, using an easy-to-use plugin settings page. But you need to know the HTML or CSS in order to add/remove/modify the HTML or CSS code by using this plugin.

= Q. It's not working. What could be wrong? =
A. As with every plugin, it's possible that things don't work. The most common reason for this is a web browser's cache. Every web browser stores a cache of the websites you visit (pages, images, and etc.) to reduce bandwidth usage and server load. This is called the browser's cache.​ Clearing your browser's cache may solve the problem.

It's impossible to tell what could be wrong exactly, but if you post a support request in the plugin's support forum on WordPress.org, we'd be happy to give it a look and try to help out. Please include as much information as possible, including a link to your website where the problem can be seen.

= Q. What to do if this plugin crashed the website? =
A. This plugin has a built-in functions for checking the custom code for syntax errors, duplicate functions names, and etc. But plugin is not perfect, so there are times when the entered custom code causes the error and white screen (WSOD). This is due to the fact that your custom code has a syntax error that this plugin could not detect. When this happens with you, please perform the following steps.

1. Access your server via FTP or SFTP. If you aren’t sure how usually your web hosting provider will have instructions somewhere on their website.
2. Browse to the directory `wp-content/plugins/my-custom-functions-pro/`. Please contact your web hosting company to get help if you can't find this folder.
3. Rename the file `START` to `STOP`. This will stop the execution of your custom code.
4. Log in to Admin Area of your WordPress website.
5. Go to the plugin settings page `Settings` -> `PHP Inserter PRO`.
6. Edit/fix your custom code that you entered before the crash.
7. Return to the plugin folder and rename the file `STOP` to `START` and you're done!

This plugin stored you entered code in the database of your website. For getting your code, you also can go to the `Database` -> Table "`wp_options`" -> Option "`spacexchimp_p011_settings`" -> "`option_value`".

= Q. The last WordPress update is preventing me from editing my website that is using this plugin. Why is this? =
A. This plugin can not cause such problem. More likely, the problem are related to the settings of the website. It could just be a cache, so please try to clear your website's cache (may be you using a caching plugin, or some web service such as the CloudFlare) and then the cache of your web browser. Also please try to re-login to the website, this too can help.

= Q. Where to report bug if found? =
A. Bug reports are very welcome! Please visit [our contact page](https://www.spacexchimp.com/contact.html) and report. Thank you!

= Q. Where to share any ideas or suggestions to make the plugin better? =
A. Any suggestions are very welcome! Please visit [our contact page](https://www.spacexchimp.com/contact.html) and share. Thank you!

= Q. I love this plugin! Can I help somehow? =
A. Yes, any contributions are very welcome! Please visit [our donation page](https://www.spacexchimp.com/donate.html). Thank you!


== Screenshots ==


== Other Notes ==

****

"My Custom Functions PRO" is one of the own software projects of [Space X-Chimp](https://www.spacexchimp.com).

**License**

All PHP code, and PDF/POT/PO/MO files is released under the [GNU General Public License version 3 (GPLv3)](http://www.gnu.org/licenses/gpl-3.0.html).
All HTML/CSS/JAVASCRIPT code, and PNG files is released under the restrictive proprietary license.

**Credits**

* The icon of plugin is a copyrighted image created by the [Space X-Chimp](https://www.spacexchimp.com) team. (C) All rights reserved.
* The banner of plugin is a copyrighted image created by the [Space X-Chimp](https://www.spacexchimp.com) team. (C) All rights reserved.
* [CodeMirror](https://codemirror.net/) is an open-source project shared under the [MIT license](https://codemirror.net/LICENSE).
* [Bootstrap](http://getbootstrap.com) by Twitter, Inc. released under the [MIT license](https://github.com/twbs/bootstrap/blob/master/LICENSE).

**Links**

* [Developer website](https://www.spacexchimp.com)


== Changelog ==

= 2.12 - 2018-07-01 =
* The changelog is improved. Added release dates.
* Fixed: CodeMirror addon 'autorefresh.js' was added to one of the previous versions of the plugin, but it was not enabled.

= 2.11 - 2018-06-30 =
* Settings for the CodeMirror editor are moved to a separate file 'codemirror-settings.js'.
* Added the addon 'placeholder.js' to the CodeMirror editor. Added a placeholder for code fields.
* Texts on the plugin settings page are updated. Translations are updated.

= 2.10 - 2018-06-26 =
* To the file 'inline-js.php' added code to prevent direct access.
* Updated the method of loading the addons of the CodeMirror library.
* Added the addon 'autorefresh.js' to the CodeMirror editor. The code for manual refreshing the CodeMirror editor is deleted.
* Texts on the plugin settings page are updated. Translations are updated.

= 2.9 - 2018-06-12 =
* CodeMirror library updated to the latest version v5.38.0. The directory structure is changed (files are better organized). Added a test files for the CodeMirror modes.
* Updated the method of loading the modes and addons of the CodeMirror library.

= 2.8 - 2018-06-04 =
* Fixed a bug due to which the plugin data that stored in the database to not be deleted during the uninstallation of the plugin.
* The contents of the file 'uninstall.php' is moved to the file 'core.php'. The file 'uninstall.php' is deleted.
* Some texts are corrected.

= 2.7 - 2018-05-21 =
* Added a file "controls.php", which contains functions that render controls on the settings page.
* Added the function "_control_help" that renders help text under the control elements.
* Added the function "_control_license" that renders the control "License Key".
* Registered a new setting group "'_settings_group_info'" and setting "_info".
* A new tab called "License" is added to the plugin settings page. To render the "License" tab added a separate file named "license.php". Added option for entering (and saving in the database) the license key.
* Translation files updated.

= 2.6 - 2018-05-20 =
* Added new constant "_FILE".
* Added a function that runs during the plugin activation. Now the date of the first activation of the plugin is recorded in the database.

= 2.5 - 2018-05-20 =
* Added auto-versioning of the CSS and JavaScript files to avoid cache issues.
* CSS code in the file 'admin.css' is optimized.

= 2.4 - 2018-04-22 =
* The link to the documentation page replaced with a new one. The documentation updated.
* An additional link to the plugin documentation page added to the plugin's meta row on the "Plugins" page.
* An additional link to the plugin donation page removed from the plugin's meta row on the "Plugins" page.
* Fixed the link "Settings", located in the plugin's meta row on the "Plugins" page. The suffix ".php" was deleted.
* Some texts updated, and typos corrected.
* All translation files updated.
* Fixed information stored in the header of the translation files.
* The information about the author of the plugin (including name, links, copyright, etc.) was changed due to the fact that the plugin became the property of SpaceXChimp.
* The human.txt file updated.

= 2.3 - 2018-01-25 =
* German translation added. (Thanks to Michael)
* Fixed an issue where the "Hello" message could not be hidden.
* CSS code improved.
* The plugin is fully tested for compatibility with WordPress version 4.9.
* Texts updated.
* The year in the copyright text is updated.
* Translation files updated.

= 2.2 - 2017-09-23 =
* At the request of some users, plugin settings page moved to the submenu item in the top-level menu item "Settings", like before.

= 2.1 - 2017-09-19 =
* Fixed the issue due to which the 'Space X-Chimp' sub menu item in the brand menu item was displayed.

= 2.0 - 2017-09-18 =
* The design of the plugin settings page is completely redone.
* The header on the settings page of plugin is redesigned.
* Added the top level menu item of the brand.
* The submenu item of the plugin has moved to the menu item of the brand.
* The menu item of the plugin is renamed.
* Added tab navigation menu for the settings page.
* Added an additional save button that fixed in the upper left corner.
* The ON/OFF switch replaced with new.
* My Unicode signature added to the main file.
* Compatibility with PHP version 5.2 improved.
* The Bootstrap framework integrated.
* The "Font Awesome" library is integrated for use on the plugin settings page.
* The 'bootstrap-checkbox.js' JavaScript plugin added.
* All PHP, JS, HTML and CSS code is better formatted.
* Code commenting improved.
* Code of the 'admin.css' file improved and better commented.
* Prefixes of the PHP functions changed to 'spacexchimp_p000_'.
* Prefixes of the PHP constants changed to 'SPACEXCHIMP_P000_'.
* The "functions.php" file renamed to "inline-js.php".
* The "LICENSE.txt" file renamed to "license.txt".
* The "humans.txt" file added.
* The "_service_info" setting added to the data-base.
* Added function for managing information about the version number of the plugin.
* Added the "Hello" message that show when the plugin is just installed.
* Added function for generating the plugin constants.
* Some constants now get the value from the plugin header data.
* All references to the plugin name, slug, prefix are replaced with constants.
* Added function that render checkboxes and fields for saving plugin settings to database.
* Added file "upgrade.php" for future upgrades.
* To the plugin settings page added information about the plugin version number.
* Options from the settings page moved to a separate file.
* Messages from the plugin settings page moved to a separate file "messages.php".
* Added Spanish translation. (Thanks Patricio Toledo)
* The POT file updated.
* Translations updated.

= 1.3 =
* User's PHP code displayed on settings page are escaped for output by `htmlentities()` for prevent converting characters to HTML entities.
* Added global constant for plugin text-domain.
* POT file updated.
* Russian translation improved.

= 1.2 =
* Added option for easy disable the custom code for cases of WSOD.
* Added prefixes to the stylesheet and script names when using wp_enqueue_style() and wp_enqueue_script().
* Added constant for storing the plugin version number.
* The "styles.css" renamed to "admin.css".
* The "functions.js" renamed to "admin.js".
* Style sheet of settings page improved and better commented.
* JS code improved.
* Plugin URI changed to https://www.spacexchimp.com/plugins/my-custom-functions-pro.html.
* Documentation improved.

= 1.1 =
* Added function to check for duplicate function names. Compares the names of all functions (internal, user). The _duplicates function added.
* Preparation of user entered code moved to separate function. Function _exec replaced by two new functions _prepare and _exec.
* Added automatic backup of all functions to file.
* Added active-line add-on to CodeMirror.
* Removed the default message about successful saving.
* Added the custom message about successful saving.
* Added function of automatic remove the "successful" message after 3 seconds.
* Image "btn_donateCC_LG.gif" is now located in the "img" directory.

= 1.0 =
* Initial release.
* Added a toggle for temporarily disable the certain functions.
* Added a message about the not saved changes.

= 0.3 =
* Release candidate.
* New design of settings page (new layout).
* Constants variables added.
* Text domain changed to "my-custom-functions-pro".
* Added compatibility with the translate.wordpress.org.
* All images are moved to the directory "images".
* Prefixes changed to "MCFunctionsPRO".
* Deleted editor.js file.
* Created functions.js file with all JS functions.
* .pot file added.
* Russian translation updated.

= 0.2 =
* Beta version.

= 0.1 =
* Alpha version.


== Upgrade Notice ==

= 2.0 =
Please update to new release!

= 1.0 =
Please update to first stable release!
