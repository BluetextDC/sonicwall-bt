=== Wicked Folders ===
Contributors: wickedplugins
Tags: folders, administration, tree view, content management, page organization, custom post type organization, media library folders, media library categories, media library organization
Requires at least: 4.6
Tested up to: 5.1
Stable tag: 2.11.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Organize pages and custom post types into folders.

== Description ==

Wicked Folders is the ultimate tool for managing large numbers of pages and custom post types.  The plugin simplifies content management by allowing you to organize your content into folders.  Wicked Folders is an administration plugin that does not alter your content’s permalinks or hierarchy giving you the complete freedom to organize your pages and/or custom post types any way you want independently of your site’s structure.

= Features =
* Organize pages, posts and custom post types into folders
* Control which post types can be organized using folders
* Create an unlimited number of folders and nest them any way you like
* Tree view of folders
* Drag and drop folders to easily reorganize them
* Drag and drop items to quickly move them into folders
* Bulk move items to folders
* Order items within folders
* Assign items to multiple folders
* Toggle folder pane on or off
* Resizable folder pane
* Dynamic folders (read more below)

= Dynamic Folders =
Dynamic folders let you to filter pages (and custom post types) by things like date or author.  You can even browse pages or custom post types by other categories that are assigned to the post type.  The handy "Unassigned Items" dynamic folder shows you items that haven't been assigned to a folder yet and the "Page Hierarchy" folder lets you browse your pages as if each parent page were a folder.  Dynamic folders are generated on the fly which means you don’t have to do anything; simply install the plugin and enable dynamic folders for the post types you want on the Wicked Folders settings page.  See the screenshots section for an example.

= How the Plugin Works =
Wicked Folders works by leveraging WordPress’s built-in taxonomy API.  When you enable folders for pages or a custom post type, the plugin creates a new taxonomy for that post type called ‘Folders’.  Folders are essentially another type of category and work like blog post categories; the difference is that Wicked Folders allows you to easily browse your content by folder.

This plugin does not alter your page or custom post types’ permalinks, hierarchy, sort order, or anything else; it simply allows you to organize your pages and custom post types into virtual folders so that you can find them more easily.

= Wicked Folders Pro =
Want to organize your media library using folders?  What about your WooCommerce orders, coupons or products?  Our WordPress media library plugin, [Wicked Folders Pro](https://wickedplugins.com/plugins/wicked-folders/?utm_source=readme&utm_campaign=wicked_folders&utm_content=pro_link), allows you to do just that!  Wicked Folders Pro extends the same great folder functionality to the media library so that you can easily organize and browse files by folder.  For those who use WooCommerce, Wicked Folders Pro also adds WooCommerce integration allowing you to organize products, orders and coupons into folders.  [Learn more about Wicked Folders Pro](https://wickedplugins.com/plugins/wicked-folders/?utm_source=readme&utm_campaign=wicked_folders&utm_content=pro_learn_more_link).

= Support =
Please see the [FAQ section]( https://wordpress.org/plugins/wicked-folders/#faq) for common questions, [check out the documentation](https://wickedplugins.com/support/wicked-folders/?utm_source=readme&utm_campaign=wicked_plugins&utm_content=documentation_link) or, [visit the support forum]( https://wordpress.org/support/plugin/wicked-folders) if you have a question or need help.

= About Wicked Plugins =
Wicked Plugins specializes in crafting high-quality, reliable plugins that extend WordPress in powerful ways while being simple and intuitive to use.  We’re full-time developers who know WordPress inside and out and our customer happiness engineers offer friendly support for all our products. [Visit our website](https://wickedplugins.com/??utm_source=readme&utm_campaign=wicked_plugins&utm_content=about_link) to learn more about us.

== Installation ==

1. Upload 'wicked-folders' to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen by searching for 'Wicked Folders'.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Visit Settings > Wicked Folders and enable folders for the desired post types (folder management for pages is enabled by default)

To start organizing your content into folders, go to the Pages screen in your WordPress admin.  Click the "plus" icon in the Folders panel to add your first folder.  To move a page to a folder, hover your mouse over the row of the page you want to move, then click and drag the "move" icon in the first column and drag the page to a folder.  To move multiple pages, check the boxes next to the pages you want to move first.

== Frequently Asked Questions ==

= I installed the plugin, now what? =
The plugin will automatically enable folder management for pages upon activation.  To start organizing your pages into folders, go to the Pages screen in your WordPress admin.  From there, click the "plus" icon in the Folders panel to add your first folder.  Visit the plugin’s settings page at Settings > Wicked Folders to enable folders for custom post types.

= Does this plugin change my page’s or custom post types’ permalinks? =
No, the plugin doesn’t modify pages or custom post types beyond controlling what folders they belong to.

= What happens when I delete a folder? =
Folders work like categories.  When you delete a folder, any pages that were in the folder are simply unassigned from that folder.  The pages are not deleted or modified in any way.

= If I delete a folder will the pages in the folder be deleted? =
No, only the folder is deleted.

= How do I put a page in a folder? =
There are two ways.  The first is to visit the Pages screen, move your mouse over the "move" icon that shows up in the pages list when hovering over a row and drag and drop the page to the desired folder.  Alternatively, you can edit the page and assign folders in the ‘Folders’ meta box in the right sidebar.

= How do I put a page in multiple folders? =
Edit the page and select the desired folders in the ‘Folders’ meta box in the right sidebar.

= How do I remove a page from a folder? =
Edit the page and uncheck the box next to the folder you want to remove it from in the ‘Folders’ meta box in the right sidebar.

= Can the folder pane be hidden? =
Yes, to hide the folder pane, click 'Hide Folder Pane' under the 'Folders' menu item in the admin bar at the top of the screen.

= Can the folder pane be resized? =
Yes, hover your mouse over the vertical grey hairline divider line between the folders and posts and then click and drag to resize.

= Can I drag and drop to reorder pages? =
Pages can be manually ordered within a folder; however, this does not change the menu order of your pages. Note: the ability to reorder pages is currently only available when the 'Enable legacy folder pages' setting is enabled. This setting can be changed by going to Settings > Wicked Folders.

= Why doesn't the sort column show up when viewing "All Folders"? =
Items can only be ordered when you're viewing a specific folder.  The "All Folders" view shows items from all folders and is not actually a folder.

= Can I organize my media library using folders? =
Media library folders is a premium feature available in Wicked Folders Pro.  [Learn more](https://wickedplugins.com/plugins/wicked-folders/?utm_source=readme&utm_campaign=wicked_folders&utm_content=media_faq_link).

= Can I organize WooCommerce products, orders and coupons into folders? =
Yes, the pro version of the plugin, Wicked Folders Pro, adds folders for WooCommerce products, orders and coupons.  [Learn more](https://wickedplugins.com/plugins/wicked-folders/?utm_source=readme&utm_campaign=wicked_folders&utm_content=woocommerce_faq_link).

= How does the "Page Hierarchy" folder work?
The "Page Hierarchy" folder (found under "Dynamic Folders") is a dynamic folder (meaning it's generated on the fly) that lets your browse the hierarchy of your site as if each parent page was a folder. For example, imagine you have two pages ("Child A" and "Child B") that are assigned to a parent page (called "Parent Page"). In that case, expanding the "Page Hierarchy" folder would show a folder labeled "Parent Page" and, clicking on it, would filter the list of pages to show you the pages assigned to that parent (in this case "Child A" and "Child B").

== Screenshots ==

1. Page folders
2. Easily drag and drop folders to rearrange
3. Drag and drop pages to quickly move pages into folders
4. Bulk move pages to folders
5. Use the sort column to order items within a folder
6. Dynamic folders let you quickly filter content by properties like date or author
7. Pro feature: media library folders

== Changelog ==

= 2.11.3 =
* Check if 'hideAssignedItems' property exists when saving state
* Update 'Tested up to' flag to 5.1

= 2.11.2 =
* Revert to 'All Folders' when a previously selected dynamic folder no longer exists
* Correct Post Hierarchy dynamic folder to only display root level pages
* Correct Post Hierarchy folder to respect 'Include items from child folders' setting

= 2.11.1 =
* Fix missing order IDs when dragging WooCommerce orders to folders (Wicked Folders Pro)
* Fix move column width for WooCommerce orders (Wicked Folders Pro)
* Minor correction to readme

= 2.11.0 =
* Add new setting to include items from child folders
* Add folder support for Easy Digital Downloads (Wicked Folders Pro)

= 2.10.2 =
* Fix issue regarding 'Move' column not being returned after using 'Quick Edit' causing table layout to break
* Fix bug regarding posts no longer being draggable after using quick edit
* Extend work-around implemented previously for Polylang plugin for Polylang Pro version

= 2.10.1 =
* Add missing file for post hierarchy dynamic folder class

= 2.10.0 =
* Fix undefined index error when saving settings with no post types selected
* Fix issue regarding 'Move' column always displaying in post lists even when folders are not enabled for the post type
* Add work-around for issue caused by 'Anything Order by Terms' plugin manipulating AJAX requests
* Add new post hierarchy dynamic folder
* Tested for compatibility with WordPress 5.0

= 2.9.4 =
* Fix category dynamic folders to work for taxonomies that are assigned to multiple post types
* Add ability to filter taxonomies for category dynamic folders

= 2.9.3 =
* Enable REST API for folder taxonomies in order to support Gutenberg
* Fix bug regarding user dynamic folder querying non-existent user tables on multisite

= 2.9.2 =
* Add work-around for issue caused by Polylang plugin manipulating AJAX requests on media pages

= 2.9.1 =
* Change prefix for folder taxonomies from 'wicked_' to 'wf_' to ensure folder taxonomy name never exceeds 32 characters
* Fix folder pane to correctly reflect the selected folder when filtering by a folder using the folders column in the posts list table

= 2.9.0 =
* Update folder pane to be responsive
* Add filter to allow folder pane width to be overridden

= 2.8.4 =
* Add folder name to cache key to prevent folder cache from becoming stale after a folder is renamed

= 2.8.3 =
* Fix bug regarding folders not displaying for users who don't already have a folder screen state setting saved

= 2.8.2 =
* Minor adjustment to accommodate custom post types created with Pods plugin

= 2.8.1 =
* Fix bug regarding folder cache not clearing after moving a folder

= 2.8.0 =
* Add multisite support
* Fix bug regarding legacy folder page option incorrectly being enabled for new installs
* Fix bug regarding folder page menu item showing up under Media when legacy folder page option is disabled (Wicked Folders Pro)
* Fix bug causing author dynamic folders to not appear in some instances (Wicked Folders Pro)
* Persist selected folder across all media modal instances on a page (Wicked Folders Pro)
* Add support for folders to media list view (Wicked Folders Pro)
* Add support for plugin icons (Wicked Folders Pro)

= 2.7.3 =
* Implement additional work-arounds to prevent fatal errors caused by themes or plugins that call wp_enqueue_media too early

= 2.7.2 =
* Implement work-around to prevent fatal errors caused by themes or plugins that call wp_enqueue_media too early

= 2.7.1 =
* Add category dynamic folders
* Fix bug regarding date dynamic folders not working for post types that use a custom status
* Add support for ACF (Advanced Custom Fields) field group post types (Wicked Folders Pro)

= 2.7 =
* Integrate folder pane into post list pages
* Extend support to all custom post types that have UI enabled (previously, folders were only available for custom post types that had a top-level menu item in the admin navigation)
* Add option to disabled 'Folders' page
* Add support for WooCommerce product, order and coupon folders (Wicked Folders Pro)

= 2.6.1 =
* Fix admin_body_class filter incorrectly overriding body class and not properly returning body classes

= 2.6.0 =
* New feature! Add ability to order items within a folder
* Fix bug regarding search results not starting on page one when performing a search from subsequent pages

= 2.5.1 =
* Remove extraneous comma accidentally left in Javascript code

= 2.5.0 =
* New feature! Add ability to clone folders
* Add option for syncing upload folder dropdown (Wicked Folders Pro)

= 2.4.4 =
* Improve scroll behavior of folder pane
* Fix folder tree overflowing folder pane bug

= 2.4.3 =
* Fix bug regarding 'quick edit' link showing on folder pages for posts and custom post types
* Update readme file

= 2.4.2 =
* Add various fixes to folder select view to preserve selected state after changes to selection or underlying collection
* Minor CSS change to prevent edges from getting cut off when dragging items to last folder in media grid view  (Wicked Folders Pro)

= 2.4.1 =
* Add callouts for pro version to settings page
* Minor CSS change for pro version

= 2.4.0 =
* Changes to core plugin code to support new features in pro version

= 2.3.6 =
* Load core app Javascript when wp_enqueue_media is called to prevent errors in pro version with front-end editors
* Bug fix for utility function that checks if tax query is an array before manipulating

= 2.3.5 =
* Fix bug regarding folder browser not working for posts

= 2.3.4 =
* Prevent folder pane from being wider than folder browser
* Modify tree view UI to support checkboxes
* Minor bug fixes

= 2.3.3 =
* Minor bug fixes and changes for pro version

= 2.3.2 =
* Fix issue with version numbers

= 2.3.1 =
* Fix indentation level of top-level folders in new folder popup

= 2.3.0 =
* Add dynamic folders feature
* Add settings link to plugin links

= 2.2.2 =
* Update 'tested up to' tag for WordPress 4.8

= 2.2.1 =
* Hide folder tree in media modal when clicking edit link from Advanced Custom Fields image field

= 2.2.0 =
* Add support for posts
* Fix bug regarding folder screen state being overwritten by other folder pages
* Fix minor bug caused by checking for post type in request when saving settings

= 2.1.1 =
* All checked items are now moved when dragging a checked item
* Add "Folders" menu to admin toolbar so that folder actions such as add, edit, etc. can be accessed without having to scroll back up to top of screen

= 2.1.0 =
* Add feature allowing items that have been assigned to a folder to be hidden when viewing the root folder
* Add ability to search items on folder pages

= 2.0.7 =
* Fix version number on WordPress.org

= 2.0.6 =
* Prevent default action when closing folder dialog
* Fix get_terms call for WordPress 4.5 and earlier

= 2.0.5 =
* Change root folder to not be movable
* Replace pseudo element folder icons with span to fix bug regarding move cursor not displaying in IE

= 2.0.4 =
* Various bug fixes

= 2.0.3 =
* Fix display issues in Internet Explorer
* Fix FolderBrowser property not defined as function bug

= 2.0.2 =
* Various bug fixes

= 2.0.1 =
* Enable Backbone emulate HTTP option to support older servers

= 2.0.0 =
* Rebuild folders page as Backbone application
* Various bug fixes

= 1.1.0 =
* Add folder tree navigation to media modal (Wicked Folders Pro)

= 1.0.1 =
* Minor bug fixes

= 1.0.0 =
* Initial release
