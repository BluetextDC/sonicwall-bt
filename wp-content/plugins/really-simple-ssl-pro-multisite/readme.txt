=== Really Simple SSL pro multisite ===
Contributors: RogierLankhorst
Tags: mixed content, insecure content, secure website, website security, ssl, https, tls, security, secure socket layers, hsts, multisite
Requires at least: 4.4
License: GPL2
Tested up to: 5.0
Stable tag: 2.0.20

Premium support and extra features for Really Simple SSL

== Description ==
Really Simple SSL Pro multisite is Really Simple SSL pro with dedicated multisite features.

= Installation =
* Install the Really Simple SSL plugin, which is needed with this one.
* Go to “plugins” in your Wordpress Dashboard, and click “add new”
* Click “upload”, and select the zip file you downloaded after the purchase.
* Activate
* Navigate to “settings”, “SSL”.
* Click “license”
* Enter your license key, and activate.

For more information: go to the [website](https://www.really-simple-ssl.com/), or
[contact](https://www.really-simple-ssl.com/contact/) me if you have any questions or suggestions.

== Frequently Asked Questions ==

== Changelog ==
= 2.0.20 =
* Fix: fixed a bug in redirect_to_http() function.

= 2.0.19 =
* Added support tab
* Tweak: improved mixed content scan regexes
* Tweak: added a redirect to http:// check before activating SSL

= 2.0.18 =
* Fix: fixed a bug where ignored url's were being added to the ignored url's array, even if they were already present.
* Tweak: the /plugins/, /wp-admin/ and /wp-includes/ directories have been excluded from the scan to increase performance.

= 2.0.17 =
* Tweak: removed HTML from translatable strings

= 2.0.16 =
* Tweak: updated notices to be in line with free notices

= 2.0.15 =
* Fix: limited the datatables Javascript to settings page
* Fix: removed HSTS warning from site dashboard when plugin is per site enabled
* Fix: HSTS link now points to network settings menu

= 2.0.14 =
* Fix: limited enqueue to settings page

= 2.0.13 =
* Added datatables for sites overview

= 2.0.12 =
* Tweak: when the scan is finished it now shows the text Scan Finished and the progress bar turns green
* Fix: improved external css and js detection regex pattern

= 2.0.11 =
* Fix: run javascript only on own settings page
* Fix: ajax url on some functions not correct
* Fix: scan results only outputting after a refresh

= 2.0.10 =
* Tweak: updated the scan cron to prevent it from running without first starting the scan manually

= 2.0.9 =
* fix: remove cookie settings now only executes if there are cookie settings in the wp-config.php

= 2.0.8 =
* Updated the scan functionality with usage of cron

= 2.0.7 =
* Fix: updated secure logged in cookie function
* Fix: removed license tab from subsites

= 2.0.6 =
* Tweak: added a header to force insecure requests over https
* Tweak: updated the scan to also include the image widget
* Tweak: added secure logged in cookie filter

= 2.0.5 =
* Fix: the plugin will no longer scan its own folder for mixed content
* Fix: the scan now contains a number of common false positives in the $safe_domains array to prevent them showing up in the scan

= 2.0.4 =
* Tweak: option to disable flushing of rewrite rules on activation
* Tweak: added warning when using PHP HSTS headers
* Tweak: prefixed icon classnames
* Fix: secure cookie settings are now removed on plugin deactivation

= 2.0.3 =
* Fix: Activation message visible on subsites
* Tweak: added secure cookie settings for networkwide activated SSL setups.
* Tweak: updated the Easy Digital Downloads plugin updater to version 1.6.14

= 2.0.2 =
* Fix: hsts preload option not appearing in network settings page

= 2.0.1 =
* Fix: after activating license, user was redirected to a site settings page, causing the “activate your license” nag not to get dismissed.

= 2.0.0 =
* Fix: moved scan data to transients, so large scan data arrays won't clutter the database
* Updated the 'Scan for issues tab'
* Fix: scan results are now shown in a responsive layout
* Fix: fixed a bug where protocol independent (//) files and/or stylesheet were not being scanned

= 1.0.3 =
* Fix: adjusted the HSTS header so it will also work in three redirects
* Fix: not all hot linked images were matched correctly in the scan

= 1.0.2 =
* Fix: When mixed content fixer is activated, urls are replaced to https, which prevented the scanner from finding these urls. A replace to http fixes this.
* Fix: Regex pattern updated to match the pattern in the free version, to prevent cross elemen matches.
* Fix: Changed priority of main class instantiation to make sure it instantiates after the core plugin

= 1.0.1 =
* Fixed issue where preload HSTS setting wasn't saved when HSTS header was already in place.
* limited .htaccess edit to settings save action.

== Upgrade notice ==

== Screenshots ==
