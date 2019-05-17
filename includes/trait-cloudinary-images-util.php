<?php
namespace CloudinaryImages;

/**
 * Plugin utility mixins
 *
 * @link       https://author.example.com
 * @since      1.0.0
 *
 * @package    Cloudinary_Images
 * @subpackage Cloudinary_Images/includes
 */


trait Cloudinary_Images_Util {
    /**
    * Obtain plugin options fully parsed into associative array
    *
    * @return array Associative array with parsed options
    * @since 1.0.0
    */
    private function get_parsed_options() {
        $options = get_option(PLUGIN_NAME);
        if ($options) {
            preg_match(CLOUDINARY_URL_REGEX, $options['url'], $matches);
            $options['api_key_secret'] = $matches[1];
            $options['api_key'] = $matches[2];
            $options['api_secret'] = $matches[3];
            $options['cloud_name'] = $matches[4];
        }

        return $options;
    }

    /**
    * Build cloudinary admin URL string
    *
    * Build and return the admin URL string for Cloudinary admin api either
    * from provided arguments, or from options
    *
    * @param string $key_secret Optional. API key-secret string
    * @param string $name Opitional. Cloud name
    * @param string $path Path to cloudinary resource
    * @return string Admin URL string
    * @since 1.0.0
    */
    private function cl_admin_url($key_secret = '', $name = '', $path = '') {
        if (empty($key_secret) || empty($name)) {
            $options = $this->get_parsed_options();
            $key_secret = $options['api_key_secret'];
            $name = $options['cloud_name'];
        }

        $url = sprintf(CLOUDINARY_ADMIN_URL, $key_secret, $name, $path);
        return $url;
    }

    /**
    * Get array of all image sizes with dimensions
    *
    * Return an array of all registered images sizes along with their
    * dimensions. Adapted from:
    * @link https://gist.github.com/eduardozulian/6467854
    *
    * @since 1.0.0
    * @return array Array of ALL image sizes with dimensions
    */
    private function get_all_image_sizes() {
        $base_sizes = ['thumbnail', 'medium', 'large'];

        $sizes = [];
        foreach($base_sizes as $size) {
            $sizes[$size]['width'] = intval(get_option("{$size}_size_w"));
            $sizes[$size]['height'] = intval(get_option("{$size}_size_h"));
            $sizes[$size]['crop'] = get_option("{$size}_crop") ?: false;
        }

        return array_merge($sizes, wp_get_additional_image_sizes());
    }
}