# Cloudinary-Images
Serve Wordpress image media from [Cloudinary](https://cloudinary.com)

Upload and serve Wordpress registered image types from Cloudinary using named
image transformations and an optional upload preset. Just setup with your Cloudinary
URL and optional preset, and images can be uploaded from the media library

This is intended to be a simpler alternative to the official [Cloudinary plugin](https://wordpress.org/plugins/cloudinary-image-management-and-manipulation-in-the-cloud-cdn/), which is a more full
integration of Cloudinary and Wordpress.

# Requirements
* Wordpress 4.7 or better. 4.9+ recommended
* PHP 5.3 or better

# Installation and Setup
1. Download the zip file and unzip it in the Wordpress plugins directory (`/wp-content/plugins/`)
2. Adjust file permissions as needed. Directories should be: rwxrwxr-x (775) and files: rw-rw-r-- (664)
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to the settings page and input the Cloudinary URL for your account (available on the Cloudinary Dashboard) into the
text field provided
5. Optionally, provide the name of an upload preset that you have defined in your
Cloudinary account
6. Click 'Save Changes.' Configuration will be validated and stored and named transformations
corresponding to Wordpress registered image types will be created
7. You can then upload and serve individual images from the Wordpress media library

# Notes and FAQ
* **Stability:**

  There are no known problems currently. I'm sure some will be found

* **Incompatibility:**

  This plugin is certainly incompatible with the official [Cloudinary
  plugin](https://wordpress.org/plugins/cloudinary-image-management-and-manipulation-in-the-cloud-cdn/), although they will coexist providing that only one is active at a time

* **Compatibility:**

  Cloudinary-Images is compatible with the [Simple Image Sizes](https://wordpress.org/plugins/simple-image-sizes/) plugin

* **I have a lot of images, and don't want to upload them one at a time. Will there be a bulk upload option?**

  Yes. The next step in development will be bulk upload/revert features, although it may be a while

* **Can I still use Wordpress's image editing feature?**

    Yes, but you will need to revert and re-upload from the media library for the change to be reflected on Cloudinary, if the image is already being served from there

* **Will there ever be feature _X_, like in the official plugin?**

    Almost certainly not. Cloudinary-Images is meant to be simple. If you have a more complex use case, then the official plugin might be more suitable

# Changelog
#### 1.0.0-beta
 * Initial release

# To Do
* Implement bulk upload/revert actions