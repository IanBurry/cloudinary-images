<?php

/**
 * Fired when the plugin is uninstalled.
 *
 *
 * @link       https://author.example.com
 * @since      1.0.0
 *
 * @package    Cloudinary_Images
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// check for correct plugin name in $_REQUEST
if ( ! preg_match('/cloudinary-images/', $_REQUEST['plugin']) ) {
    echo 'Incorrect plugin name in request.';
    exit;
}

// check for logged in user
if ( ! is_user_logged_in() ) {
    echo 'No logged in user.';
    exit;
}

// verify that user is authorized
if ( ! current_user_can('delete_plugins') ) {
    echo 'Current user is not authorized to delete plugins';
    exit;
}

// Now delete plugin options
delete_option('cloudinary-images');
