=== Cloudinary-Images ===
Contributors: Ian Burry
Tags: images, cloudinary
Requires at least: 4.7
Requires PHP: 5.3+
Tested up to: 4.9.8
Stable tag: N/A
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Serve Wordpress registered images types from Cloudinary.

== Description ==
The purpose of Cloudinary-Images is to let you use Wordpress as an "offline" CMS
for statically generated websites, such as those that can be built using NuxtJS.
Named transformations are created on Cloudinary for all Wordpress registered images
types, and Cloudinary URLs are generated on the fly.

This is not a replacement for the official Cloudinary plugin [URL here], which is a more full
integration of Cloudinary and Wordpress. CloudinaryImages simply serves the basic,
registered Wordpress images from Cloudinary

== Installation ==
1. Download the zip file and uzip it in the Wordpress plugins directory (`/wp-content/plugins/`)
2. Activate the plugin through the 'Plugins' menu in WordPress

== Setup and Usage ==
1. Input the Cloudinary URL for your account (available on the Cloudinary Dashboard) into the
text field provided
2. Optionally, provide the name of an upload preset that you have defined in your
Cloudinary account
3. Click 'Save Changes.' Configuration will be validated and stored and named transformations
corresponding to Wordpress registered image types will be created
4. You can then upload and serve individual images from the media library

== Changelog ==
= 1.0.0-beta =
* Initial release

== TODO ==
1. Implement bulk actions
