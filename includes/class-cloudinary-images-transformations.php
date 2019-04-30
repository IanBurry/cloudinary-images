<?php
namespace CloudinaryImages;

/**
 * Create and update image transformations
 *
 * @link       https://author.example.com
 * @since      1.0.0
 *
 * @package    Cloudinary_Images
 * @subpackage Cloudinary_Images/includes
 */

class Cloudinary_Images_Transformations {
    use Cloudinary_Images_Util;

    /**
    * Constructor
    *
    * @access private
    * @since 1.0.0
    */
    private function __counstruct() {}


    /**
    * Create or update cloudinary image transformations
    *
    * Create or update the named image transformations that correspond to
    * the registered Wordpress image types
    *
    * @todo refactor a bit
    * @since 1.0.0
    */
    public static function setup_transformations() {
        $instance = new self;
        $errors = [];
        $sizes = $instance->get_all_image_sizes();
        foreach ($sizes as $name => $size) {
            if (empty($size['width']) && empty($size['height'])) {
                $errors[] = [
                    '400' => sprintf('%s has no size information', $name)
                ];
                continue;
            }

            // cropping
            $crop_string = '';
            if ($size['crop']) {
                if ($name === 'thumbnail') {
                    $crop_string .= 'c_crop,ar_1';
                } elseif (is_array($size['crop']) && count($size['crop']) == 2) {
                    $crop_string .= sprintf('c_crop,ar_%.2f', $size['width']/$size['height']);
                    $crop = $size['crop'];
                    $gravity_key = sprintf('%s-%s', $crop[0], $crop[1]);
                    $crop_string .= sprintf(',%s', CL_WP_CROP[$gravity_key]);
                }
            }

            // image size
            $size_string = $instance->build_size_strings(
                $size['width'], $size['height']
            );

            $trans_string = "$crop_string/$size_string";
            $method = $instance->transform_exists($name);
            $url_path = sprintf(
                'transformations/%s?%s=%s',
                $name,
                ($method == 'POST') ? 'transformation' : 'unsafe_update',
                $trans_string
            );

            $url = $instance->cl_admin_url('', '', $url_path);
            $result = wp_remote_post($url, ['method' => $method]);
            $response = $result['response'];

            if ($response['code'] !== 200) {
                $msg = sprintf(
                    'Error creating/updating transformation for %s: %s',
                    $name,
                    $response['message']
                );
                $errors[] = [ $response['code'] => $msg ];
            }

        }

        return $errors;
    }


    /**
    * Check for existence of named transformation
    *
    * Determine if the named transformation has already been created and
    * return the correct method as a string.
    *
    * @param string $name The transformation's name
    * @return string 'PUT', or 'POST'
    * @since 1.0.0
    */
    private function transform_exists($name) {
        $result = wp_remote_get(
            $this->cl_admin_url(null, null, "transformations/$name")
        );

        $method = $result['response']['code'] === 200 ? 'PUT' : 'POST';
        return $method;
    }

    /**
    * Build dimension strings
    *
    * Build and return array of dimension strings
    *
    * @param integer $width Image width
    * @param integer $height Image height
    * @return string The dimensions string
    * @since  1.0.0
    */
    private function build_size_strings($width, $height) {
        $dimensions = [];
        $dimensions[] = empty($width) ? null : "w_$width";
        $dimensions[] = empty($height) ? null : "h_$height";
        $dim_string = implode(',', array_filter($dimensions));

        return $dim_string;
    }
}



