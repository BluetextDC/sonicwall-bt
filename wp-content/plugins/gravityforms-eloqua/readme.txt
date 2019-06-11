=== Gravity Forms Eloqua ===
Tags: gravity forms, forms, gravity, form, crm, gravity form, mail, email, newsletter, Eloqua, Oracle, plugin, sidebar, widget, mailing list, API, email marketing, newsletters
Requires at least: 2.8
Tested up to: 5.1.1
Stable tag: 2.2.5
Contributors: briandichiara
Donate link: https://www.paypal.me/briandichiara
Purchase License: https://briandichiara.com/product/gravityforms-eloqua/

Integrate Eloqua Forms with Gravity Forms

== Description ==

> This plugin requires an <a href="https://eloqua.com/" rel="nofollow">Eloqua</a> account.

= Integrate Eloqua with Gravity Forms =

If you use <strong>Eloqua</strong> email service and the Gravity Forms plugin, you're going to want this plugin!

Integrate your Gravity Forms forms so that when users submit a form entry, the entries get added to Eloqua. Link any field type with Eloqua, including custom fields!

== Installation ==
1. Upload plugin files to your plugins folder, or install using WordPress' built-in Add New Plugin installer.
1. Activate the plugin
1. Go to the plugin settings page (under Forms > Settings > Eloqua)
1. Enter the information requested by the plugin.
1. Click Save Settings.
1. If the settings are correct, it will say so.
1. Follow on-screen instructions for integrating with Eloqua.

== Frequently Asked Questions ==
= Why do I keep getting a disconnect notification? =
When this plugin cannot communicate with Eloqua, there could be a couple of reasons. Most of the time it's due to a timeout when trying to reach Eloqua's servers. Occasionally, the issue is that the credentials stored to connect to Eloqua have become invalid. If this is the case, simply clear your authentication credentials stored on the Eloqua Settings page in Gravity Forms, and re-authenticate your account. If you're still having issues, enable the Disconnect Notification Debugging setting in order to get more information about why the disconnect occurred. Feel free to send this information along to the plugin developer in order to look into a possible fix to the issue.

== Screenshots ==
1. Gravity Forms Eloqua Logo
2. Settings Page
3. Form Feed Setup

== Upgrade Notice ==
= Latest Version (2.2.5) =
* Fixed PHP bug when OAuth token needed to be refreshed.
* Selectively loaded WP_Http only when needed.
* Code clean up and added function documentation.

= Previous Version (2.2.4) =
* Added FormFieldGroup support.

= Recent Version (2.2.3) =
* Hot fix release to solve PHP Fatal error when old OAuth token is used with new OAuth library.

== Changelog ==
= 2.2.5 (2019-05-03) =
* Fixed PHP bug when OAuth token needed to be refreshed.
* Selectively loaded WP_Http only when needed.
* Code clean up and added function documentation.

= 2.2.4 (2019-04-03) =
* Added FormFieldGroup support.

= 2.2.3 (2019-02-01) =
* Hot fix release to solve PHP Fatal error when old OAuth token is used with new OAuth library.

= 2.2.2 (2018-12-19) =
* Fixed a bug where access token wasn't getting restored properly.
* Fixed a bug with legacy auth not able to send HTTP requests.
* Fixed a bug where some legacy code for OAuth refresh tokens got removed, now restored.

= 2.2.1 (2018-12-18) =
* Resolved issue with auto-updater due to changes in Gitlab API.

= 2.2.0 (2018-12-18) =
* New OAuth Library (thephpleague/oauth2-client) is now being used for authentication, form retrieval, and submissions. You'll need to disconnect and re-connect from Eloqua in order for the libary to be used. Hopefully this should help substantially with Eloqua disconnects in the future. Please send any issues you find to support!
* Added Eloqua cookie support - Uses elqCustomerGUID by default. Other cookie values can be filtered with gfeloqua_eloqua_cookies.
* Additional filter added gfeloqua_custom_data for custom data to be sent with request.

= 2.1.7 (2018-10-31) =
* Added support for older versions of Gravity Forms

= 2.1.6 (2018-05-25) =
* Added Compliance with GDPR
* Removed debug logging of personal data.
* Added privacy statements to WordPress Privacy updates of v4.9.6
* Added hooks to remove personal data from stored settings.

= 2.1.5 (2018-05-16) =
* Added a Test Connection button for immediate feedback about Eloqua Connection.
* Fixed issue where object being sent to Eloqua was mistaken for array.
* Fixed an undefined variable bug.
* More logging to help uncover authentication issues.

= 2.1.4 (2018-05-13) =
* Resolved bug where extension license would not save/activate.
* Added some styling to extension settings.

= 2.1.3 (2018-05-11) =
* Quick release to resolve yet another issue with Request Timeout.

= 2.1.2 (2018-05-11) =
* Quick release to resolve issues with Request Timeout setting not being applied to API calls.

= 2.1.1 (2018-05-11) =
* Added better error reporting for Response Code 400: Bad Request - Happens when Eloqua validation fails.
* Fixed a bug when multisite updates didn't work when only activated on subsites.
* Renamed Setting "Connection Timeout" to "Request Timeout" for clarity.
* Added iThemes Security "hide-backend" feature support for Admin URLs.
* Fixed bug where Request Timeout wasn't being applied to some API calls.
* Ensure API calls are using TLS 1.2.

= 2.1.0 (2018-04-26) =
* Added Multisite Support. Subdomains still count as separate domains, but subdirectory multisites count as the same site.
* Switched to frontend OAuth code collection to avoid admin issues.
* Minor bug fixes.

= 2.0.4 (2018-04-25) =
* Fixed a bug where field mapping validation was not working
* Added better feedback when changing forms on Edit Feed page.
* Restored Entry Notes feature to display log of notes on entry detail page.

= 2.0.3 (2018-04-20) =
* Addresses an issue where connection to Eloqua may be lost when retrieving an OAuth refresh token
* Added "Last authenticated" date to settings page Authentication tooltip.
* Some code refactoring to remove redundant code.

= 2.0.2 (2018-04-18) =
* Fixed an issue with re-activating a the license on the same domain
* Resolve issue where license not getting saved properly
* Fixed issue where license key was getting cleared after saving settings page
* Checked for timeouts to prevent frequent Eloqua disconnections.
* Added more debugging notes throughout

= 2.0.1 (2018-04-04) =
* Added support for an Extensions library
* More consistent settings naming (may reset some of your settings)
* Added Remote request Timeout setting and filter

= 2.0.0 (2018-03-31) =
* Updated to use includes WordPress coding standards syntax
* Restructured Javascript to use GFEloqua object (namespace)
* Another OAuth adjustment to support changes to Eloqua OAuth API
* Changed License to GPL v3
* Updated to Select2 version 4.0.6-rc.1
* Fixed js bug with select2 script handle
* Added support to call resubmit_entry without AJAX
* Switched all debugging logging to use Gravity Forms built in logging
* Added plugin license and auto-updater
* Fixed PHP notice related to resubmitting failed entries

= 1.6.0-delta =
* Fixed an OAuth issue where spaces was causing OAuth problems.
* Additional Debugging data stored
* Adds Authentication Timestamp

= 1.5.3-beta =
* Fixed a PHP warning which would occur when the entry meta was being processed during submission.
* Added new hook for custom error logging `gfeloqua_log_entry_notes`
* Bug Fix related to retry_failed_submissions
* Some admin styling adjustments
* Added Github Updater support back in (which may or may not have ever been taken out)

= 1.5.2 =
* Some admin styling adjustments
* Added disconnect debugging setting

= 1.5.1 =
* Fixed a bug when refreshing OAuth token
* FIxed a bug with admin notification

= 1.5.0 =
* Completely revamped Debugger/Entry Notes with a Custom Debugger Class
* Added additional debugging info to various places
* Attempted to fix false positives
* Added a button to reset the entry status in the case of a false positive, so a resubmission can be done and debug notes can be reviewed.
* Removed some duplicate debugging comments
* Fixed a few typos
* Added visual queue of unlimited retries.

= 1.4.2 =
* Removed Github Access Token

= 1.4.1 =
* Added automatic re-submission of failed entries
* Added display count to show retry attempts.
* Added private Github Updater Token, but I don't think it's working.

= 1.4.0 =
* Restructured repository for Github Updater support

= 1.3.3 =
* Added "Retry Submission" button on failed submissions to Eloqua
* Added "Sent to Eloqua?" meta column to display submissions status on Entries View
* Added additional debug detail when submissions fail to be received by Eloqua

= 1.3.2 =
* Fixed bug where form list from Eloqua wouldn't refresh with latest forms

= 1.3.1 =
* Fixed PHP Notice when inserting version data throws notice about non-object
* Added GitHub Updater plugin support (More Info: https://github.com/afragen/github-updater)
* Added filter `gfeloqua_validate_response` to validate_response in GFEloqua API Class
* Added entry note/error logging and display in admin
* Updated select2 to version 4.0.3

= 1.3.0 =
* Fixed bug where only 1000 records are displayed. (needs testing)
* Fixed bug where multi-checkbox values are not being stored.
* Added feature to show Forms grouped by folder name
* Added ability to specify count and page parameters to get_forms() method
* Added Admin Notice when Eloqua is disconnected
* A few minor tweaks

= 1.2.4 =
* fixed a bug keeping you from disabling the notification
* added some documentation

= 1.2.3 =
* added feature to alert you if Eloqua is disconnected

= 1.2.2 =
* added better OAuth setup, no longer needs code copy/paste
* added better error message when can't connect to Eloqua

= 1.2.1 =
* added select2 to find Eloqua forms easier
* fixed javascript spinner bug

= 1.2.0 =
* NOTE: Changed plugin slug to fix Issue #4. Your settings may need to be reset.
* added OAuth support
* added credential validation to settings page
* fixed Issue #4 Gravity Forms Registration Warning
* fixed Issue #5 Error "This add-on needs to be updated. Please contact the developer."

= 1.1.0 =
* setup securely stored auth string
* fixed bug with clearing transients
* minor bug fixes

= 1.0.1 =
* Added refresh buttons to clear transients

= 1.0 =
* Launched plugin

