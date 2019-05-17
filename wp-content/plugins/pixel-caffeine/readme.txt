=== Pixel Caffeine ===
Contributors: adespresso, antoscarface, divbyzero, giangian, chiara_09, chusmy, aealeviatore4
Donate link: https://adespresso.com/
Tags: facebook, facebook pixel, facebook ad, facebook insertions, custom audiences, dynamic events, woocommerce
Requires at least: 4.4
Requires PHP: 5.3.9
Tested up to: 5.0.2
Stable tag: 2.0.7
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

The simplest and easiest way to manage your Facebook Pixel needs and create laser focused custom audiences on WordPress.

== Description ==

The easiest, most powerful, **100% free**, WordPress Plugin to manage **Facebook Pixel** and **Facebook Product Catalog**.

Don’t spend money on “pro” plugins while ours is free. Includes full WooCommerce and Easy Digital Downloads support!

Created by AdEspresso, a certified Facebook Marketing Partner and #1 Facebook Ads Partner, Pixel Caffeine is the highest quality Facebook Pixel plug-in available on the WordPress market and we’ll keep it constantly up to date with Facebook latest updates.

Watch our video to see the full range of possibilities:

[youtube http://www.youtube.com/watch?v=zFAszDll_1w]

In just a few simple clicks, you can have Facebook pixel live on your website, track conversions and begin creating custom audiences for almost any parameter you want – whether its web pages visited, products & content viewed, or custom & dynamic events.

Unlike all the other professional plugins available, we have no limitations and no hidden costs or fees.

Welcome to a whole new world of *custom audiences* and *product catalog management*.

### Features:

* **Instant Installation** - get the Facebook pixel site-wide without typing a line of code - just a simple click.

* **Advanced Custom Audiences** - create audiences based on standard/custom events, referring sources (i.e. Twitter, Facebook, Google, etc.), categories/tags of content, specific URL parameters...literally almost anything you’d like!

* **Facebook Dynamic Ads with WooCommerce** - automatically track visitors based on what they viewed (product name, product category and product tags) and then dynamically re-target them with advertisements on Facebook or Instagram

* **Product Catalog Management** - In one click generate a Product catalog for your store and upload it to Facebook or let Pixel Caffeine constantly sync it with Facebook. Advanced filters let you create your product catalog with exactly the products you want to promote. 

### Examples of what you can do with Pixel Caffeine:

* Create “category” audiences for your blog or website and then re-target these visitors with lead generation or direct sale campaigns

* Create audiences of people that viewed specific products and dynamically target them with specific incentives or coupons for exactly the products they viewed

* Create audiences of those that submit particular forms, click on certain buttons, or take certain actions while navigating or searching your website.

* Create product catalog with only products in specific categories with a discounted price and not sold out!

### Videotutorial

[youtube https://www.youtube.com/watch?v=DUn1a291-bA]


== Installation ==

= Minimum Requirements =

* PHP version 5.3.9 or greater
* MySQL version 5.6 or greater or MariaDB version 10.0 or greater

= Automatic installation =

* From your WordPress admin panel, click “Plug-Ins” and then “Add New”
* In the search box, type “Pixel Caffeine”
* Select “Pixel Caffeine” and click “Install”!
* Activate It

= Manual installation =

* Download the plugin from this page (it will download as a zip file)
* Open the WordPress admin panel, go to the "Plugins" and select “Add new”
* Select “Upload” and then  choose the .zip file downloaded from this page
* Select “Install” after the upload is complete
* Activate It

= Video =

Here a brief videotutorial to understand main feature and how their work:

[https://www.youtube.com/watch?v=DUn1a291-bA]

== Frequently Asked Questions ==

= Where do I get my Facebook Pixel ID? =

You can get your Facebook Pixel ID from the [Pixel page of Facebook Ads Manager](https://www.facebook.com/ads/manager/pixel/facebook_pixel). If you don't have a Pixel, you can create a new one by following [these instructions](https://www.facebook.com/business/help/952192354843755?helpref=faq_content#createpixel). Remember: there is only ONE Pixel assigned per ad account.

= Do I need a new Facebook Pixel? =

No, use the pixel from the ad account you want to link to your WordPress website.

= I don't want to login to my Facebook account. Can I put the pixel ID manually without connecting my account? =

No problem! You can manually add the Pixel ID in the settings page instead of connecting your Facebook Account. However, without the Facebook connect, you won't be able to use some of the most advanced features of Pixel Caffeine like our Custom Audience creation.

= Are the custom audiences saved also on my Facebook Ad Account? =

Yes, everything you create in Pixel Caffeine is immediately synced with Facebook and all the audiences will be immediately available to use in Facebook Ads Manager/Power Editor ...or [AdEspresso](https://adespresso.com) if you're using it of course :)

= Is it compatible with WooCommerce? =

YES! We fully support WooCommerce. In the settings page just enable the integration and we'll automatically add all the event tracking! This is also compatible with Dynamic Product Ads and we'll pass Facebook all the advanced settings like product Id, cost, etc.!

= Is it compatible with Easy Digital Downloads =

Absolutely YES! The same of above.

= Can I import my custom audiences I already have in my Ad account into Pixel Caffeine? =

Unfortunately there isn’t any way at the moment to import custom audiences _from_ FB, but it is a feature in our long-term roadmap. With the plugin we want to give extended tools for advanced custom audiences - using WordPress data. This plug-in is NOT a replacement for Business Manager, but it does make it all easier!

== Screenshots ==

1. General Settings
2. Custom audiences manager
3. Special filter for custom audience
4. Conversions events page
5. Dashboard
6. Product Catalog
7. Product Catalog created

== Changelog ==

= 2.0.7 - 2018-12-19 =
* Support - Tested plugin with new WordPress 5.0 version
* Support - Fix issues in feed with newest version of WooCommerce
* Fix - "Products Are Missing From Your Catalogs" error in the DPA pixels for product with variations
* Fix - Fix "&amp;" error on AddToCart event of product single page
* Fix - Fix feed URL for custom configurations
* Fix - Avoid to track multiple Purchase event per each Thank You page visit in WooCommerce and Easy Digital Downloads

= 2.0.6 - 2018-08-14 =
* Fix - Admin modals didn't open after 2.0.5 upgrade

= 2.0.5 - 2018-08-02 =
* Important - **FB API Bracking Change** It's **mandatory** upgrade the plugin in order to have the custom audience working back again because of a change to the custom audience creation API from Facebook, this version of plugin will fix with the new version of FB API
* Add - Enable/Disable advanced matching option
* Fix - Stats chart in dashboard
* Fix - URL in checkout_url, now to product add to cart URL
* Fix - Feed URL when automatic upload is enabled
* Fix - Bad format of price in the pixel when more decimal digits

= 2.0.4 - 2018-04-12 =
* Add - New option to choose a short description as description for the feed item
* Add - New option to choose if price must be including or excluding tax
* Fix - Changed deprecated FB API calls about product catalog. **It's mandatory to upgrade Pixel Caffeine before May 8th 2018 in order to have the product catalog functionality working.**
* Fix - Invalid argument supplied for foreach PHP warning
* Fix - Encoded &amp; detected when a "&" symbol char is present in category or tag
* Fix - Force to absolute URL the image link in the feed

= 2.0.3 - 2018-03-19 =
* Add - Variation ID in the mandatory field error when the item in error is variation
* Add - Get variation description from the parent if it is empty
* Add - Add helpful hook to change allowed standard event parameters
* Add - Option in Advanced Settings to force to use IDs in content_ids parameters even if a product SKU is defined
* Fix - Description or title cannot be empty error during feed generation (for who updates please refresh again the feed)
* Fix - Strip whole SVG tags from content in the product feed
* Fix - Syntax error in Log classes
* Fix - Image link broken inside the feed with some external plugins
* Fix - Error get_plugins does not exist in feed error

= 2.0.2 - 2018-02-01 =
* Add - Useful hooks for the feed items
* Enhancement - Translate shortcodes in the product descriptions inside the feed
* Fix - Invalid characters in feed
* Fix - Strip HTML tags in the product description inside the feed
* Fix - Woo query in the product feed fetched wrong products
* Fix - Use short description/excerpt if no product description
* Fix - Fatal error when only EDD is enabled
* Fix - Include quantity in value parameter of AddToCart
* Fix - NaN in value parameter when add to cart from WooCommerce
* Fix - AddToCart tracking when using [product_page] shortcode
* Fix - AddToCart tracking when using [add_to_cart] shortcode

= 2.0.1 - 2018-01-11 =
* Compatibility - Tested plugin with new WooCommerce 3.3
* Add - New log system: new "Logs" tab is added where are tracked all errors appears across the plugin
* Add - Image size option for the product items of the XML feed
* Add - Option to enable automatic UTM tags tracking
* Add - Option to exclude "value" and "currency" parameters from specific events
* Add - Option to exclude "content_ids", "content_type" and "content_name" parameters from specific events
* Add - Option to choose to save the feed file with a background process (for who has thousands of products in own store)
* Fix - Feed saving process error that causes a "file not existing" error
* Fix - Hide size value in Custom Audiences because of new Facebook policies based on [Advertising Principles](https://newsroom.fb.com/news/2017/11/our-advertising-principles/).
* Fix - Fatal error on google categories value gathering
* Fix - AddToCard not fired when redirect to cart option of Woo is activated
* Fix - Variation ID passed in content_ids when AddToCart event needs to be fired
* Fix - Search event not working without an e-commerce plugin activated
* Fix - Fix "InitiateCheckout" JS error when the "IniziateCheckout" is deactivated
* Fix - Event value parameter is not valid error from Facebook

= 2.0.0 - 2017-12-14 =
* Add - **New Product Catalog** feature. You can now create automatically a XML Product Feed from the Pixel Caffeine admin and push it into your Business Manager account!
* Fix - Pass "value" parameter if price value of product is 0

= 1.2.3 - 2017-09-04 =
* Fix - **IMPORTANT:** Bug in custom audience creation process into facebook account. **It's necessary delete and create again all custom audiences that contain the filters for Blog > Categories/Tags in order to collect right users and prefill again CAs.**
* Fix - Fatal error in WooCommerce checkout page in some cases
* Fix - Fatal error in EDD when add to cart from action and not from AJAX
* Fix - Fatal error in admin when Divi theme is used
* Fix - Fatal error in admin editor when plugin is enabled and there is EDD activated
* Add - Some useful hooks in order to change by code something in the events fired
* Add - Reset FB connection button in advanced settings, useful when the connect is blocked by an error during the connection

= 1.2.2 - 2017-06-21 =
* Support - tested with new 4.8 WordPress version with success
* Add - Option to disable pixel firing when user is logged in as specific roles
* Add - Option to disable use product instead of product_group for content_type parameter
* Enhancement - Enable automatically the main conversions option when one of the ecommerce event option is checked
* Fix - Facebook Pixel isn't fired because of a dynamic language in the Facebook scripts
* Fix - Taxonomy labels in CA filter
* Fix - Admin style conflicts with other plugins that damage admin style of Pixel Caffeine

= 1.2.1 - 2017-04-27 =
* Fix - Box not aligned in general settings in safari browser
* Fix - Fatal error when plugin is disabled and woocommerce plugin is active
* Fix - Permissions error message after plugin activation

= 1.2.0 - 2017-04-03 =
* Feature - *Full support to Easy Digital Downloads* for the dynamic ads events
* Feature - Introduced new hook to add dynamic placeholders in the value arguments of custom conversions
* Tweak - Tested with WooCommerce 3.0.0 RC2, so almost fully compatible with the new version will be released soon
* Tweak - Track "CompleteRegistration" event when a user is registered from woocommerce page
* Fix - Track custom conversions events created by admin even if you set a full URL for page_visit and link_click
* Fix - Shipping cost included in the "value" property of checkout events. Anyway, added also an option to activate it again

= 1.1.0 - 2017-03-16 =
* Feature - Introduced new *delay* options in general settings and in Conversions/Events tab in order to set a delay for the pixel firing
* Feature - Introduced condition dropdown for the URL fields of CA creation/edit form
* Feature - Introduced new advanced settings box in general settings box with delay options and other dev tools
* Fix - Fatal error ‘__DIR__/composer/autoload_real.php’
* Fix - Conversions table layout broken when URL is long in the trigger column
* Fix - HTML tags shown on CA fields error message
* Dev - Introduced new debug mode option, to have a dump of pixel fired in the pages before to fire really
* Dev - Introduced new button to clear the transients used to cache the facebook APi requests, rarely they may cause data not fetched from facebook

= 1.0.2 - 2017-03-09 =
* Fix - Fatal error on AMP pages, using AMP plugin
* Tweak - Increase limit of objects fetched by facebook API request
* Tweak - Increase limit for the posts in CA filters

= 1.0.1 - 2017-02-23 =
* Fix - Remove zero cent from the value amount of ecommerce events
* Fix - change 'and' with 'or' when you set more values for a filter of CA
* Fix - JS error on AddPaymentInfo event
* Fix - Undefined property shown on JS console
* Fix - Fatal error when facebook connection API error occurred and log them
* Tweak - Remove manual hash for advanced matching with the pixel

= 1.0.0 - 2017-02-20 =
* First release
