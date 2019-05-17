=== Right Answers ===
Contributors: russell@irondogmedia.com
Donate link: 
Tags: 
Requires at least: 3.0.1
Tested up to: 4.9.8
Requires PHP: 5.2.4
Stable tag: 3.2.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin provides support information for SonicWall products.

== Description ==

This plugin pulls information from Upland Right Answers, Brightcove Video, and Salesforce to create a support section for the Sonicwall.com website.

== Changelog ==


 = 3.2.5 =

 Added transient caching for knowledgebase searches

 = 3.2.4 =

 Modified the tech-docs-requests.php file to include a call to get PDF only books from the tech docs site. 

 = 3.2.3 =

 Fixed several bugs reported in Jira - still need to work on better ways to clear cache so updates made on external web services are updated immediately on the sonicwall site.

 = 3.2.0 =

 Added new shortcode template to display a single video with related videos below it. 

 Moved BC_API class extender SW_VIDS to new video-admin.php file so that it could be extended further to support the single video display functions.

 Fixed bug on single video display. Fixed bug with related videos showing all the same videos over and over. 

 = 3.1.2 =

 Added two additional selectors to js file to prevent image overlay from native theme.

 = 3.1.1 =

 Adjusted CSS to remove white border around video player

 Adjusted js to remove theme image overlay from video page images as it interferes with operation of video pop-up


 = 3.1.0 =

 Added video category drop down to video tutorials page to filter videos by different categories

 Improved video overlay to keep video centered on screen and included close button 

 Added styling to the PLC tables as well as tool tips to help explain different columns of data on each of the tables.


 = 3.0.0 =

Continued refinements to the Right Answers display to match specifications provided by marketing team. 

Continued refinements to the video display to match specs provided by marketing. 

Continued refinements to the Salesforce Product Lifecycle Tables (PLC) display to match specs provided by marketing.

Removed [ra-search-form ] shortcode in favor of using theme layouts now that pages are configured differently

Added mobile style for video tutorials and also added video overlay to display single videos at larger size


 = 2.0.0 =

Integration of Brightcove Video via their API and Salesforce Product Lifecycle Tables via their API. Both presentations are rudimentary and will require updates to display as desired. 

Improvements to Right Answers functionality, requiring te creation of several additional Wordpress pages to match URL structure of original Sonicwall.com site as best as possible. Some htaccess redirects still needed to make everything work.

Removed several Ajax calls and went back to direct page loads except where appropriate to better serve navigation of the support site via the back button.



 = 1.0.0 =

 Initial development focused on integrating the Right Answers API calls into the plugin and establising data flow beween Right Anwswers server and Wordpress. Rudimentary design coonsiderations. No video and no Salsesforce data. Mulitiple experiments with using Ajax versus straight page reloads to try to get the most efficient presentation of data. Majority of page loads focused on Ajax calls.