<?php
namespace CloudinaryImages;

/**
 * Fired during plugin deactivation
 *
 * @link       https://author.example.com
 * @since      1.0.0
 *
 * @package    Cloudinary_Images
 * @subpackage Cloudinary_Images/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Cloudinary_Images
 * @subpackage Cloudinary_Images/includes
 * @author     Ian Burry <iburry@aol.com>
 */
class Cloudinary_Images_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
     * @todo Remove the delete option code
	 */
	public static function deactivate() {
        // if we are debugging...
        error_log('Deactivating...');
        if(WP_DEBUG) {
            delete_option('cloudinary-images');
        }

	}

}
