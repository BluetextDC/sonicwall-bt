=== HandL UTM Grabber ===
Contributors: haktansuren
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SS93TW4NEHHNG
Tags: utm,grabber,shortcodes,gclid,contact form 7,leads,collect,collect leads
Requires at least: 3.6.0
Tested up to: 5.2
Stable tag: 2.7.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

The easiest (we mean it!) way to capture UTMs on your (optin) forms. Supports: Contact Form 7, Gravity Forms, Ninja Forms, Salesforce, ActiveCampaign.

== Description ==

**Capture all the UTM variables (and more)** as soon as user hits your website (ANY page of your WP installation): One great feature of the plugin is the UTM variables are saved in **client's browser (COOKIES)** and even though there is no UTM variables in URL, the variables can still be accessible via shortcode across any page/widget of your website.

> #### Major features in HandL UTM Grabber include:
>
> * Add hidden fields in your forms (incl. Contact Form 7, Gravity Forms, WordPress-to-lead for Salesforce CRM, Ninja Forms, ActiveCampaign, Caldera Forms, WooCommerce and many more.)
> * Pass UTM variables to major marketing tools such as ActiveCampaign, Vero, Aweber, Interspire and many more...
> * Hassle free implementation (no shortcodes). See below...   

[Check out the documentation](https://www.haktansuren.com/handl-utm-grabber/?utm_medium=referral&utm_source=wordpress.org&utm_campaign=HandL+UTM+Grabber+Readme&utm_content=Documentation)

= HandL UTM Grabber Needs Your Support =

It is hard to continue development and support for this free plugin without contributions from users like you. If you enjoy using HandL UTM Grabber, find it useful and want us to develop further, please consider [__making a donation__](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SS93TW4NEHHNG).

Please [review](https://wordpress.org/support/view/plugin-reviews/handl-utm-grabber#postform) if you like the plugin!

Question/Problem/Support join our [slack channel](https://www.haktansuren.com/slack-handlwp/).

**SPECIAL THANKS:** This plugin has been tested on various operating systems and browsers thanks to <a href='https://www.browserstack.com'>BrowserStack!</a> 

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `handl-utm-grabber` folder to the `/wp-content/plugins/` directory via FTP
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

*No question so far :)*

== Screenshots ==

1. It should look like this after install.
1. Gravity Form Support.
1. Salesforce Support.
1. Append UTM variables to all URLs automatically.

== Changelog ==

= 2.7.1 =
* fix for null coalescing operator for PHP < 7.0 compatiblility

= 2.7 =
* Zapier integration added for Contact Form 7, Ninja Form, Gravity Form

= 2.6.6 =
* simple_html_dom.php dependency upgraded to the latest

= 2.6.5 =
* Critical Bug Fix: Possible cross-site request forgery (CSRF) due to add_option, update_option usage

= 2.6.4 =
* Varnish cache and WP Engine workaround fix (JS based COOKIE save)
* 502 error fixed. Possibly caused by printed text before we set the COOKIES. 
* PHP 7.3 related warnings due to simple_html_dom.php fixed

= 2.6.3 =
* BUG FIX: https://wordpress.org/support/topic/php-notice-undefined-index-ninja-php/ & https://wordpress.org/support/topic/php-deprecated-function/

= 2.6.2 =
* NEW FEATURE: append UTM parameters to all the anchor tag (<a>) having class “.utm-out”

= 2.6.1 =
* Absolutely nothing, just trying to fix the version

= 2.6.0 =
* Ninja Form Merge Tags implemented for all the variables used in HandL UTM Grabber (e.g. {handl:utm_campaign})  

= 2.5.13 =
* localhost cookie problem fixed.  

= 2.5.12 =
* Bugfix for [handl_landing_page] and [handl_url]: the URL was not populating on secure sites (https://). Subdomain suport for all the UTM variables and other shortcodes. Special thanks to David W for sponsoring the update. 

= 2.5.11 =
* Security Bugfix: Potential XSS attack using cookies. Special thanks to Robert Tubridy for reporting. 

= 2.5.10 =
* Bugfix: Fix for append UTM variables to all the links: it was adding the UTMs even though the feature is turned off.

= 2.5.9 =
* Bugfix: Initialize SERVER variables and fix nav_menu_link_attributes  

= 2.5.8 =
* Bugfix: Visual Composer Accordion/Tabs fix (Append UTM feature conflict). Thanks [@radasonea](https://wordpress.org/support/topic/accordion-visual-doesnt-work-after-plugin-activates/)   

= 2.5.7 =
* Bug-fix caused by v2.5.7.

= 2.5.6 =
* Append UTM fix for WP menu, new shortcodes: [username] and [email]

= 2.5.5 =
* Fix for the JS in footer for website uses minify JS (Thanks [sylvainww](https://wordpress.org/support/topic/plugin-adds-uncaught-referenceerror-2/))

= 2.5.4 =
* Added CouponHut theme support (Thanks [zizzi17](https://wordpress.org/support/topic/append-to-all-urls-works-only-partially/))

= 2.5.3 =
* WooCommerce support: All parameters (UTM and others) are appended to the corresponding order's meta when available. 

= 2.5 =
* One click to aappend UTM variables to all the links on your site. 

= 2.3 =
* Fix for php close tag at the end of the file. 

= 2.2 =
* New shortcodes added for leads tracking (e.g. Original Referral URL, Referral URL, IP, Landing Page etc.)

= 2.1 =
* Shortcode support for CF7 and Salesforce (Thanks to jenrstretch and wpkmi)

= 2.0 =
* Hassle Free Implementation (No Shortcode)

= 1.4 =
* Gravity Forms support added (Thanks [hashimwarren](https://wordpress.org/support/topic/gravity-forms-45 ))

= 1.3 =
* BugFix for Text Widget (Thanks [eddygbarrett](https://wordpress.org/support/topic/handl-not-working))

= 1.2 =
* BugFix for Contact Form 7 (Thanks [wpkmi](https://wordpress.org/support/topic/contact-form-7-form-submission-hangs-when-utm-grabber-plugin-is-enabled))

= 1.1 =
* Shortcodes changed to support form input
* World's most effective written code :)

= 1.0 =
* Hello World :)


== Upgrade Notice ==

= 1.0 =
HandL UTM Grabber's birthday :)