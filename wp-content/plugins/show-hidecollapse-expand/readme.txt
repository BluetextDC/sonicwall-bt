=== Show-Hide / Collapse-Expand ===
Contributors: buntegiraffe
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VTAHA24DSYL5Y
Tags: hide content, show content, collapse, expand, faq, questionnaire, question answer, show widget, hide widget, save space, organize content, collapse content, expand content, seo friendly collapse content
Requires at least: 4.2
Tested up to: 5.0.3
Stable tag: 1.2.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Save space on your pages, posts, sidebars. Hide the content before user clicks to see it. Collapse long lists, create FAQs & more.

== Description ==

**Live Demo:**
See live demo of our plugin at [showhide.bunte-giraffe.de](http://showhide.bunte-giraffe.de "http://showhide.bunte-giraffe.de"). User: demo, password: demo.

Show-Hide / Collapse-Expand allows you to easily manage the amount of content shown to user upon entering your website. It will free your pages from clutter and give your website a stylish minimalistic look and feel. You will find it handy for grouping content, composing FAQs, collapsing long lists and expanding them on click, providing hidden answers to questions, organizing your widgets more efficiently, and lots more. Inserting a shortcode is done via a handy tinyMCE button in your WordPress Editor. You will configure the look of your button/hyperlink on the fly and will be able to change it later using the provided shortcode parameters. Why don't you give it a try?

Please feel free to post your questions in the support threads of this plugin, we will be glad to help you with any issues.

> #### **Main features**

> * Show/Hide ANY content*
> * Collapse/Expand using jQuery Effects (blind, fold, highlight, slide)
> * Set custom animation speed
> * Add your custom JS functions to onclick events 
> * Collapse/Expand lists
> * Easily add FAQs
> * Add hidden answers to quizzes (answer opens on button click)
> * Collapse widgets to declutter sidebars
> * Save space on pages by grouping the content under expandable headings
> * Use hyperlinks or stylish pure-css buttons
> * Style every link/button individually
> * Make button stick to the end of the expanded content
> * Use icons for your buttons or in front of your links
> * Nest up to 3 levels of collapsible content
> * Easily customize the settings and insert the shortcode with a TinyMCE button
> * Use in sidebars by inserting the shortcode with needed parameters
> * Regular updates
> * Great support

* We tested our plugin thoroughly, but if you have issues with any type of content, please let us know in the support thread.

== Installation ==

1. Upload the plugin files to the '/wp-content/plugins/show-hidecollapse-expand' directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to Tools->ShowHide/CollapseExpand screen to customize plugin settings (animation effect and animation speed).
4. Go to the post/page you need to add the shortcode to and click the Collapse-Expand button in your TinyMCE Editor.
5. Customize the needed settings: select type of control with which you want the user to expand/collapse the content, select the color of your hyperlink or button text, type in your labels for expanding/collapsing the content, select the icon for your button/link, etc.
6. If needed, go to Tools->ShowHide/CollapseExpand screen and switch to the Help tab to see the full list of shortcode parameters.

== Frequently Asked Questions ==

= Can I see a demo of your plugin before installing it? =

See live demo of our plugin at [showhide.bunte-giraffe.de](http://showhide.bunte-giraffe.de "http://showhide.bunte-giraffe.de"). User: demo, password: demo.

= How can I insert the shortcode?  =

Go to the post or page you wish to edit and switch to Visual tab. You will see the Collapse-Expand button in your Editor. Please click it and customize the needed parameters. When you click OK, the shortcode will be inserted.

= Can I insert the same shortcode into a post and a sidebar widget? =

Yes, you can use the same shortcode multiple times on the same page.

= How do I nest shortcodes inside one another? =

For inserting level 2 shortcode, please check the "This shortcode should be nested in another shortcode" checkbox or use [bg_collapse_level2] shortcode. For level 3, please use [bg_collapse_level3] shortcode.

= Will my other shortcodes work inside your shortcode? =

Yes, we did our best to make them work. Some shortcodes (like Google Maps plugins shortcodes) might behave strangely, we recommend using our Google Maps - Simple Pins plugin if you need to hide your Google maps. If you have any issues, please let us know in the support thread.

= I don't want to use animation, can I turn it off? =

Sure. Go to Tools->ShowHide/CollapseExpand screen (Settings tab), clear the Enable jQuery UI effects checkbox and set Custom animation speed (ms) to 0.

== Screenshots ==

1. Insert your shortcode on button click.
2. Use one of our predefined button styles.
3. Collapse long lists.
4. Create an FAQ.
5. Hide answers and show them on click.
6. Nest shortcodes inside one another.
7. Collapse widgets in sidebar.

== Changelog ==

= 1.2 =
* Fixed icons on buttons

= 1.2 =
* Added the 'stick-to-bottom' option for links/buttons
* Fixed onclick event for buttons
* Fixed bug preventing working with plugins like Content View
* Fixed bug interfering with css classes in some themes 

= 1.1 =
* Added inline_css shortcode attribute to allow styling every control individually
* Added onclick shortcode attribute to allow adding custom JS functions to controls

= 1.0 =
* Added custom animation speed selection.
* Added support for jQuery UI Effects (blind, fold, highlight, slide).
* Fixed issues with expandable lists.

= 0.1 =
Initial release.

== Upgrade Notice ==

= 1.1 =
Added ability to style every control individually, e.g. change font-size of a single link. Added ability to assign your own custom JS functions onclick, e.g. link will be expanded, but also an alert will be shown to user

= 1.0 =
Added custom animation speed selection. Added support for jQuery UI Effects (blind, fold, highlight, slide). Fixed issues with expandable lists.

== Credits ==

We used Best CSS Button Generator [http://www.bestcssbuttongenerator.com](http://www.bestcssbuttongenerator.com "http://www.bestcssbuttongenerator.com") for our sleek pure-css buttons.