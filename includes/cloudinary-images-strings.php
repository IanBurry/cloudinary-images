<?php
namespace CloudinaryImages;

/**
* Strings ready for internationalizaton and constants
*/

/**
* Warning for invalid Cloudinary URL. Either does not match correct format, or
* key, cloud name values are invalid
*
* @since 1.0.0
*/
define(
    __NAMESPACE__ . '\INVALID_CL_URL_MSG',
    __('Invalid Cloudinary URL. Check Cloudinary account for correct format and parameter values', 'cloudinary-images')
);

/**
* Warning for invalid upload preset name.
*
* @since 1.0.0
*/
define(
    __NAMESPACE__ . '\INVALID_CL_PRESET_MSG',
    __('Preset cannot be found. Check Cloudinary account for correct preset name', 'cloudinary-images')
);

/**
* Cloudinary upload link title
*
* @since 1.0.0
*/
define(__NAMESPACE__ . '\CL_UPLOAD_TITLE', __('Serve from Cloudinary', 'cloudinary-images'));

/**
* Http status notification messages
*
* @since 1.0.0
*/
define(__NAMESPACE__ . '\CL_RESPONSE_200', __('Upload to Cloudinary is successful.', 'cloudinary-images'));
define(__NAMESPACE__ . '\CL_RESPONSE_400', __('Bad Request. Check for correct plugin configuration.', 'cloudinary-images'));
define(__NAMESPACE__ . '\CL_RESPONSE_403', __('Request not Allowed. Check Cloudinary account.', 'cloudinary-images'));
define(__NAMESPACE__ . '\CL_RESPONSE_420', __('Enhance your Calm. Cloudinary has rate limited this account.', 'cloudinary-images'));
define(__NAMESPACE__ . '\CL_RESPONSE_500', __('Server Error. Contact Cloudinary support.', 'cloudinary-images'));

/**
* Transformations error messages
* @deprecated ?
* @since 1.0.0
*/
define(__NAMESPACE__ . '\CL_TRANSFORM_ERR', __('Failed to create the following named transformation: %s', 'cloudinary-images'));

/**
* Cloudinary image src url template
*
* @since 1.0.0
*/
const CLOUDINARY_URL_REGEX = '/\Acloudinary:\/\/((\d{15}):(\w{27}))@(\w+)\z/';
const CLOUDINARY_ADMIN_URL = 'https://%s@api.cloudinary.com/v1_1/%s/%s';
const CL_IMAGE_SRC_URL = 'https://res.cloudinary.com/%s/image/upload/%s/v%s/%s.%s';
const CL_SERVED = 'served_from_cloudinary';
const CL_IMG_VERSION = 'cloudinary_image_version';
const CL_IMG_PUB_ID = 'cloudinary_image_public_id';
const CL_IMG_FORMAT = 'cloudinary_image_format';

