=== Plugin Name ===
Contributors: webholism
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JHGDAKZ2YLFLN
Tags: gravityforms, gravity forms, multiple columns, multicolumn, multicolumns, multi column, multi columns, responsive, gravity forms multi column, multi row, multirow, multiple rows
Requires at least: 4.6
Tested up to: 5.0.3
Stable tag: 3.0.3
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add multiple columns (and multiple rows of multiple columns) to Gravity Forms.


== Description ==

<blockquote>
  <p>This plugin requires the <a href="http://www.webholism.com/gravity-forms/" rel="nofollow">Gravity Forms plugin</a>.  <strong>Don't use Gravity Forms yet?  <a href="https://rocketgenius.pxf.io/c/1211218/445235/7938" rel="nofollow">Buy the plugin</a></strong> and see your Wordpress life take form!</p>
  <p>Instructions on usage can be found at <a href ="https://youtu.be/Am6gB9cIvG0" target="_blank">YouTube</a></p>
</blockquote>

This responsive-design plugin allows you to add multiple columns into your Gravity Forms form.  Once this plugin is installed, you can create columns by using three new buttons - Column Start, Column Break, and Column End.


== Installation ==

1. In your WordPress admin panel, go to <em>Plugins > New Plugin</em>, search for “Multi Column” for WordPress, find the “Gravity Forms Multi Column” plugin and click “<em>Install now</em>”.
2. Alternatively download the zip file, unzip, and upload the gf-form-multicolumn folder (and files) to your plugins directory, which usually is `/wp-content/plugins/`.
3. Activate the plugin through your <em>Plugins</em> area.
4. Create a new row of separated columns by selecting the Column Start button in the Multiple Columns Field box on the right-hand side of the Gravity Forms form page.
5. Add the fields that you want in the column.
6. Create a Column Break to separate to the next column, or Column End to end the column division and the field row.
7. Repeat 4-6 as necessary.
8. Click the Update button to save the changes.


== Frequently Asked Questions ==

= How many columns can I make? =

We’ve tested 2, 3, 4 and 5 columns.  Theoretically you can have more, although this will depend on your theme and the amount of screen space you have.

= Could you give an example of how I would create 3 columns on a single row? =

Add a Column Start field.
Add the field/s that are to be contained in the first column.
Add a Column Break field.
Add the field/s that are to be contained in the second column.
Add a Column Break field.
Add the field/s that are to be contained in the third column.
Add a Column End field

= Could you give an example of how I would create 1 row with 2 columns, then a second row with 3 columns? =

Add a Column Start field.
Add the field/s that are to be contained in the first column.
Add a Column Break field.
Add the field/s that are to be contained in the second column.
Add a Column End field
Add a Column Start field.
Add the field/s that are to be contained in the first column.
Add a Column Break field.
Add the field/s that are to be contained in the second column.
Add a Column Break field.
Add the field/s that are to be contained in the third column.
Add a Column End field

= How do I ensure that a new row will occur at the end of my columns? =

A row will be concluded with each Column End field.

= Can I use this plugin with multisite? =

From version 2.1.0 multisite is supported.

= Which version of Gravity Forms is this plugin compatible with? =

It has been tested with version 2.0.7 and above, but please do contact us if you’re experiencing a problem with your version of Gravity Forms.


== Screenshots ==

1. New Gravity Form with Multiple Columns Fields floating panel (collapsed).
2. Multiple Columns Fields floating panel close up (expanded).
3. Form showing new fields (Column Start, Column Break, Column End) added to form.
4. Example of a completed form, composed of Multiple Columns Fields and generic Gravity Form fields.


== Upgrade Notice ==

= 3.0.3 =
Fix: Removed echo commands as these were causing update issues from within Gutenberg pages.
Improvement: Altered CSS to be more specific with class naming implementation.

= 3.0.2 =
Re-release of version 3.0.0.

= 3.0.1 =
Restored 2.1.1 as version 3.0.0 appeared to generate an Internal Server Error.

= 3.0.0 =
Restructured way that columns and rows are added to forms; native UI buttons are now integrated into the Gravity Forms interface.
Resolved a few issues that had been highlighted in previous versions:
 Displaying multiple forms on a single page
 Correct error handling when form id not present in shortcode
 CSS enhancements to align list elements

= 2.1.1 =
This version removed code that had been used for testing multisite in 2.1.0.

= 2.1.0 =
This version resolves issues around the plugin providing network only functionality on multisite installations.  This plugin will now allow admins to activate or deactivate on individual network sites.
A new CSS style has been introduced to remove the left padding and left margin from the first column of each row of the created form, to allow for a form to line up elements as expected.  This is achieved with the style: `li[class*="column-count-1"] > div > ul`
Please note that with this version, the title of this plugin has changed and will now likely appear in a new location in your plugin list.  Do not be alarmed!  This is simply a new naming convention to align with Wordpress recommendations.

= 2.0.1 =
* Code altered to account for web servers with PHP version < 5.4.

= 2.0.0 =
Introduced new feature to allow for multiple rows.  Individual rows will split the columns they contain evenly.

= 1.0.1 =
Altered details related to the supporting files.  No functional alterations.  Upgrade optional.

= 1.0.0 =
Initial Release. Trumpets sound!


== Changelog ==

= 3.0.3 =
Fix: Removed echo commands as these were causing update issues from within Gutenberg pages.
Improvement: Altered CSS to be more specific with class naming implementation.

= 3.0.2 =
Re-release of version 3.0.0.

= 3.0.1 =
Restored 2.1.1 as version 3.0.0 appeared to generate an Internal Server Error.

= 3.0.0 =
Restructured way that columns and rows are added to forms; native UI buttons are now integrated into the Gravity Forms interface.
Resolved a few issues that had been highlighted in previous versions:
 Displaying multiple forms on a single page
 Correct error handling when form id not present in shortcode
 CSS enhancements to align list elements

= 2.1.1 =
* Fix: Removed inaccurate output code that had been used for testing multisite functionality.

= 2.1.0 =
* Fix: Removed Network: True value to allow activation on individual multisite sites.
* Improvement: Introduced new CSS style: li[class*="column-count-1"] > div > ul to remove left margin and padding rules.
* Improvement: Improved readability of the readme.txt file instructions for setup.
* Improvement: Added Plugin URI value to reduce chances of conflict with other plugins of similar naming convention.
* Improvement: Changed name of plugin to align with recommendations provided by Wordpress.

= 2.0.1 =
* Fix: Altered primary file as array syntax [] was not functioning on sites with PHP version < 5.4.

= 2.0.0 =
* Improvement: Introduced row functionality. Changes to primary php file and CSS definitions.

= 1.0.1 =
* Improvement: Alterations to readme.txt
* Improvement: Description altered in primary file.

= 1.0.0 =
* Initial Release
