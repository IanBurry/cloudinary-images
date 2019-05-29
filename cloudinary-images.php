<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/IanBurry/cloudinary-images
 * @since             1.0.0
 * @package           Cloudinary_Images
 *
 * @wordpress-plugin
 * Plugin Name:       Cloudinary Images
 * Plugin URI:        https://github.com/IanBurry/cloudinary-images
 * Description:       Upload and serve images from Cloudinary
 * Version:           1.0.0
 * Author:            Ian Burry
 * Author URI:        https://github.com/IanBurry
 * Requires PHP:      5.3+
 * Text Domain:       cloudinary-images
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * Copyright 2019 Ian Burry (iburry@aol.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

namespace CloudinaryImages;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
const PLUGIN_NAME_VERSION = '1.0.0';

/**
* Plugin Name
*/
const PLUGIN_NAME = 'cloudinary-images';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cloudinary-images-activator.php
 */
function activate_cloudinary_images() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cloudinary-images-activator.php';
	Cloudinary_Images_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cloudinary-images-deactivator.php
 */
function deactivate_cloudinary_images() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cloudinary-images-deactivator.php';
	Cloudinary_Images_Deactivator::deactivate();
}

register_activation_hook( __FILE__, __NAMESPACE__ . '\activate_cloudinary_images' );
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\deactivate_cloudinary_images' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cloudinary-images.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cloudinary_images() {

	$plugin = new Cloudinary_Images();
	$plugin->run();

}
run_cloudinary_images();
