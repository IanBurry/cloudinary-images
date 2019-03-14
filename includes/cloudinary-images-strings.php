<?php
/**
* All long strings as consts ready for internationalization
*/

/**
* Warning for invalid Cloudinary URL. Either does not match correct format, or
* key, cloud name values are invalid
*
* @since 1.0.0
*/
define(
    'INVALID_CL_URL_MSG',
    __('Invalid Cloudinary URL. Check Cloudinary account for correct format and parameter values')
);

/**
* Warning for invalid upload preset name.
*
* @since 1.0.0
*/
define(
    'INVALID_CL_PRESET_MSG',
    __('Preset cannot be found. Check Cloudinary account for correct preset name')
);

/**
* Cloudinary upload link title
*
* @since 1.0.0
*/
define('CL_UPLOAD_TITLE', __('Serve from Cloudinary'));

define('CL_RESPONSE_200', __('Upload to Cloudinary is successful.'));
define('CL_RESPONSE_400', __('Bad Request. Check for correct plugin configuration.'));
define('CL_RESPONSE_403', __('Request not Allowed. Check Cloudinary account.'));
define('CL_RESPONSE_420', __('Enhance your Calm. Cloudinary has rate limited this account.'));
define('CL_RESPONSE_500', __('Server Error. Contact Cloudinary support.'));