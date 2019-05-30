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

// remove any cloudinary_images image metadata
global $wpdb;

$sql = <<<SQL
SELECT post_id, meta_value
FROM wp_postmeta
WHERE meta_key = '_wp_attachment_metadata'
    AND meta_value LIKE '%served_from_cloudinary%'
SQL;

$result = $wpdb->get_results($sql, 'ARRAY_A');

$cloudinary_keys = array_fill_keys([
    'served_from_cloudinary',
    'cloudinary_image_version',
    'cloudinary_image_public_id',
    'cloudinary_image_format'
    ], '');

foreach($result as $row) {
    $meta = unserialize($row['meta_value']);
    $clean_meta = array_diff_key($meta, $cloudinary_keys);
    wp_update_attachment_metadata($row['post_id'], $clean_meta);
}

// Now delete plugin options
delete_option('cloudinary-images');
