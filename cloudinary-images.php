<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://author.example.com
 * @since             1.0.0
 * @package           Cloudinary_Images
 *
 * @wordpress-plugin
 * Plugin Name:       Cloudinary Images
 * Plugin URI:        https://nowhere.example.com
 * Description:       Upload and serve images from Cloudinary
 * Version:           1.0.0
 * Author:            Ian Burry
 * Author URI:        https://author.example.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cloudinary-images
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

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

register_activation_hook( __FILE__, 'activate_cloudinary_images' );
register_deactivation_hook( __FILE__, 'deactivate_cloudinary_images' );

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
