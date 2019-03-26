<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://author.example.com
 * @since      1.0.0
 *
 * @package    Cloudinary_Images
 * @subpackage Cloudinary_Images/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cloudinary_Images
 * @subpackage Cloudinary_Images/admin
 * @author     Ian Burry <iburry@aol.com>
 */
class Cloudinary_Images_Admin {

	/**
	* @todo Tidy up const names. Maybe move them into strings file
	*/
	const CLOUDINARY_URL = 'https://%s@api.cloudinary.com/v1_1/%s/%s';
	const CLOUDINARY_UPLOAD_URL = 'https://api.cloudinary.com/v1_1/%s/%s/upload';
	const CLOUDINARY_URL_REGEX = '/\Acloudinary:\/\/((\d{15}):(\w{27}))@(\w+)\z/';

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	* The Cloudinary account info
	*
	* @since 1.0.0
	* @access private
	* @var array $cl_config
	*/
	private $cl_config = [
		'full_url' => '',
		'key_secret' => '',
		'api_key' => '',
		'secret' => '',
		'cloud_name' => ''
	];

	/**
	* @todo Is this needed? Can we zap it?
	*/
	private $response_status = 0;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$options = get_option($this->plugin_name);
		if ($options['configured']) {
			preg_match(self::CLOUDINARY_URL_REGEX, $options['url'], $matches);
			$this->cl_config = array_combine(
				array_keys($this->cl_config), $matches
			);
		}
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/cloudinary-images-admin.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/cloudinary-images-admin.js',
			array( 'jquery' ),
			$this->version,
			false
		);
	}

	/**
	* Register admin menu
	*
	* @since 0.1.0
	*/
	public function add_plugin_admin_menu() {
		add_options_page(
			'Cloudinary Images Settings',
			'Cloudinary Images',
			'manage_options',
			$this->plugin_name,
			array($this, 'display_plugin_setup_page')
		);
	}

	/**
	* Add action links (whatever those are)
	*
	* @since 1.0.0
	*/
	public function add_action_links($links) {
		$url = admin_url('options-general.php?page=' . $this->plugin_name);
		$anchor_text = __('Settings', $this->plugin_name);
		$settings_link = array(
	    	sprintf('<a href="%s">%s</a>', $url, $anchor_text),
	    );

		return array_merge($settings_link, $links);
	}

	/**
	* Render the settings page
	*
	* @since 1.0.0
	*/
	public function display_plugin_setup_page() {
		include_once('partials/cloudinary-images-admin-display.php');
	}

	/**
	* validate form fields
	*
	* @todo Restructure this so that no preset error is added if there is a
	* preceding url error. That is, don't check preset if the url is already
	* known to be bad
	*
	* @since 1.0.0
	*/
	public function validate($input) {
		preg_match(self::CLOUDINARY_URL_REGEX, $input['url'], $match);
		if (!empty($match)) {
			$url_check = $this->cl_admin_url($match[1], end($match), 'resources');
			if (!$this->cl_check($url_check)) {
				add_settings_error($this->plugin_name, 'url_error', INVALID_CL_URL_MSG);
			}

			$preset_path = "upload_presets/{$input['preset']}";
			$preset_check = $this->cl_admin_url($match[1], end($match), $preset_path);
			if (!$this->cl_check($preset_check)) {
				add_settings_error($this->plugin_name, 'preset_error', INVALID_CL_PRESET_MSG);
			}
		} else {
			add_settings_error($this->plugin_name, 'url_error', INVALID_CL_URL_MSG);
		}

		$valid = [
			'configured' => empty(get_settings_errors($this->plugin_name)),
			'url' => esc_url_raw($input['url'], ['cloudinary']),
			'preset' => sanitize_text_field($input['preset']),
			'transforms' => empty($input['transforms']) ? 0 : 1
		];

		return $valid;
	}

	/**
	* Register validate function
	*
	* @since 1.0.0
	*/
	public function options_update() {
		if (isset($_POST['option_page']) && $_POST['option_page'] == 'cloudinary-images') {

			register_setting(
				$this->plugin_name, $this->plugin_name, array($this, 'validate')
			);

			$options = get_option($this->plugin_name);
			// we don't really want the option anyway, so use the checkbox value
			// from the POST. This works correctly
			if ($_POST[$this->plugin_name]['transforms']) {
				$sizes = $this->get_all_image_sizes();

				foreach ($sizes as $name => $size) {
					if ($size['width'] > 0 || $size['height'] > 0) {
						$width = "w_{$size['width']}";
						$height = "h_{$size['height']}";
						$trans = sprintf('transformations/%s?transformation=%s,%s', $name, $width, $height);
						$url = $this->cl_admin_url(null, null, $trans);

						$method = 'POST';
						$check_result = wp_remote_get($this->cl_admin_url(null, null, "transformations/$name"));
						if ($check_result['response']['code'] === 200) {
							$method = 'PUT';
						}

						$result = wp_remote_post($url, ['method' => $method]);

						if ($result['response'] === 200) {
							error_log("Transformation updated");
						}
						error_log("And so on...");
					}
				}

				// $options['transforms'] = 0;
				// $fred = update_option($this->plugin_name, $options, true);
			}
		}
	}

	/**
	* Add cloudinary column in media table view
	*
	* @since 1.0.0
	*/
	public function add_cloudinary_column($columns) {
		if (current_user_can('upload_files')) {
			$columns['cloudinary-image'] = 'Cloudinary Image';
		}

		return $columns;
	}

	/**
	* Add the Cloudinary upload links
	*
	* @since 1.0.0
	*/
	public function add_cloudinary_upload($col_name, $media_id) {
		if($col_name == 'cloudinary-image' && current_user_can('upload_files')) {
			printf('<a href="?cloudinary_upload=%u">%s</a>', $media_id, CL_UPLOAD_TITLE);
		}
	}

	/**
	* Upload image file to cloudinary from wp library
	*
	* This will probably be off-loaded to a class cause it'll get messy here
	* @todo Need to do some validations for file existence, permissions, etc.
	*
	* @since 1.0.0
	*/
	public function upload_to_cloudinary() {

		global $_wp_additional_image_sizes;


		add_action('admin_notices', [$this, 'upload_notice']);
		if (isset($_GET['cloudinary_upload']) && intval($_GET['cloudinary_upload']) > 0) {
			$img_path = get_attached_file($_GET['cloudinary_upload']);

			// now get the needed bits from options
			$options = get_option($this->plugin_name);
			preg_match(self::CLOUDINARY_URL_REGEX, $options['url'], $matches);
			list($url, $key_sec, $api_key, $api_secret, $cloud_name) = $matches;
			$preset = $options['preset'];

			// build cloudinary upload url
			$upload_url = sprintf(self::CLOUDINARY_UPLOAD_URL, $cloud_name, 'image');

			// build siggy part of payload
			$sig_params = ['timestamp' => time(), 'upload_preset' => $preset];
			$sig = sha1(http_build_query($sig_params) . $api_secret);

			// add api_key, file, and sig to sig_params
			$file = curl_file_create($img_path);
			$post_body = array_merge(
				$sig_params,
				['api_key' => $api_key, 'file' => $file, 'signature' => $sig]
			);

			// ok, wp_remote_post doesn't work with files... so curl it is
			$ch = curl_init($upload_url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$response = json_decode(curl_exec($ch), true);
			// error_log(var_export($response, true));
			$status = intval(curl_getinfo($ch, CURLINFO_RESPONSE_CODE));
			curl_close($ch);

			// update image (attachment) info
			if ($status === 200) {
				$this->update_image($_GET['cloudinary_upload'], $response);
			}

			// redirect back to library
			$lib_url = wp_get_referer();
			$location = add_query_arg(['status' => $status], $lib_url);
			wp_redirect($location);
		}

	}

	/**
	* Handle upload success and error notifications
	*
	* @since 1.0.0
	*/
	public function upload_notice() {
		if (isset($_GET['status'])) {
			$status = $_GET['status'];
			$type = 'error';

			switch ($status) {
				case 400:
				case 401:
				case 404:
					$message = CL_RESPONSE_400;
					break;
				case 403:
					$message = CL_RESPONSE_403;
					break;
				case 420:
					$message = CL_RESPONSE_420;
					break;
				case 500:
					$message = CL_RESPONSE_500;
					break;
				default:
					$message = CL_RESPONSE_200;
					$type = 'updated';
			}

			printf(
				'<div class="%s notice is-dismissible cl-images-upload"><p>%s</p></div>',
				$type,
				$message
			);
		}
	}

	/**
	* Return cloudinary url if image is served from cloudinary
	*
	* @param array 			$image 	   		Array of image data: url, width, height
	* @param integer 		$attachment_id 	Integer image attachment ID
	* @param string/array 	$size 	    	Dimension array, or string ('large', 'thumbnail', ...)
	*/
	public function get_cl_image_info($image, $attachment_id, $size, $icon) {
		if (is_string($size) && !empty($image)) {
			$meta = wp_get_attachment_metadata($attachment_id);
			// error_log(var_export($meta, true));
			if (isset($meta[CL_SERVED]) && $meta[CL_SERVED] === true) {
				$image[0] = sprintf(
					CL_IMAGE_SRC_URL,
					'dipuqn6zk', // this will be in config at some point
					sprintf('w_%u,h_%u', $image[1], $image[2]),
					$meta[CL_IMG_VERSION],
					$meta[CL_IMG_PUB_ID],
					$meta[CL_IMG_FORMAT]
				);
			}
		}

		return empty($image) ? false : $image;
	}

// https://res.cloudinary.com/dipuqn6zk/image/upload/w_200,h_200/v1553030637/blognog/sandiego.jpg
	/**************************************************************************
	************************** PRIVATE METHODS ********************************
	**************************************************************************/

	/**
	* Add cloudinary data to image meta-data
	*
	* Add served-by-cloudinary flag and cloudinary path data to image meta-data
	*
	* @param integer 	$image_id 		Integer ID for uploaded image attachment
	* @param array 		$cl_response 	Upload response data from cloudinary
	* @return ? 0 or error code ?
	*
	* @todo Figure out return value and error handling for it
	*
	* @since 1.0.0
	*/
	private function update_image($image_id, $cl_response) {
		// first get meta data
		$meta = wp_get_attachment_metadata($image_id, true);

		// add uploaded flag
		if (!is_array($meta) || empty($meta)) {
			// what to do? Perhaps return some error indicator?
			//
		}

		$meta[CL_SERVED] = true;
		$meta[CL_IMG_VERSION] = $cl_response['version'];
		$meta[CL_IMG_PUB_ID] = $cl_response['public_id'];
		$meta[CL_IMG_FORMAT] = $cl_response['format'];


		// update meta data
		$result = wp_update_attachment_metadata($image_id, $meta);
		if ($result === false) {
			// do something to notify

		}
		// all done
		return 0; // or some error code?
	}

	/**
	* Construct Cloudinary validation url
	*
	* Builds Cloudinary admin url from given args, or from cl_config if
	* any args are empty
	*
	* @since 1.0.0
	*
	* @param String $key Optional. API key/secret
	* @param String $name Optional. Cloudinary cloud name
	* @param String $path Optional path to be validated
	* @return String URL representation
	*/
	private function cl_admin_url($key_secret = '', $name = '', $path = '') {
		if (empty($key_secret) || empty($name)) {
			$key_secret = $this->cl_config['key_secret'];
			$name = $this->cl_config['cloud_name'];
		}
		return sprintf(self::CLOUDINARY_URL, $key_secret, $name, $path);
	}

	/**
	* Validate url to Cloudinary
	*
	* @since 1.0.0
	* @param String $check_url The validation URL
	* @return Integer status code
	*/
	private function cl_check($check_url) {
		$is_valid = false;
		$response = wp_remote_get($check_url);
		if (is_array($response)) {
			$is_valid = intval($response['headers']['status']) === 200;
		}
		if (WP_DEBUG_LOG) error_log($response['headers']['status']);
		return $is_valid;
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
