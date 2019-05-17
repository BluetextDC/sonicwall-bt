=== Plugin Name ===
Contributors: martythornley
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=11225299
Tags: imsanity, image, images, automatic scale, automatic resize, image resizer, image scaler, automatic image resizer, auto image resize, auto image resizer, space saver, image shrinker, image skruncher, image cruncher
Requires at least: 3.5
Tested up to: 4.0
Stable tag: trunk

Bulk Resize Media automatically resizes huge image uploads and makes sure the maximum upload size is respected for all uploaded images.

== Description ==

WordPress creates multiple image sizes, but keeps the original image, no matter how big it is. So you still get the massive image on your server and can include it in posts, which can drastically slow down your site.

Works with Multisite to allow site admins to control the image uploads. 

Can be run before exporting to help images make the trip during import.

Based on work in the "Imsanity" plugin by Jason Hinkle
http://verysimple.com/products/imsanity/
http://verysimple.com/

= Features =

* Automatically resizes large image uploads, keeping the maximum size and removing the original huge image.
* Bulk-resize feature to selectively resize all existing attachments. 
* Allows configuration of max width/height and jpg quality
* In MultiSite, network admins can control image sizes for entire network.
* Optionally converts BMP files to JPG.

== Installation ==

Automatic Installation:

1. Go to Admin - Plugins - Add New and search for "Bulk Resize Media"
2. Click the Install Button
3. Click 'Activate'

Manual Installation:

1. Download bulk-resize-media.zip
2. Unzip and upload the 'bulk-resize-media' folder to your '/wp-content/plugins/' directory
3. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==


== Frequently Asked Questions ==

== Changelog ==

= 1.1 =

Fixed a bug that was preventing it from finding images in some cases.