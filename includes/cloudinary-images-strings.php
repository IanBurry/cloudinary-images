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