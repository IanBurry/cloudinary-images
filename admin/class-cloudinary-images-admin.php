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
	* @since 1.0.0
	*/
	public function validate($input) {
		preg_match(self::CLOUDINARY_URL_REGEX, $input['url'], $match);
		if (!empty($match)) {
			$url_check = $this->cl_url($match[1], end($match), 'resources');
			if (!$this->cl_check($url_check)) {
				add_settings_error('url', 'url_error', INVALID_CL_URL_MSG);
			}

			$preset_path = "upload_presets/{$input['preset']}";
			$preset_check = $this->cl_url($match[1], end($match), $preset_path);
			if (!$this->cl_check($preset_check)) {
				add_settings_error('preset', 'preset_error', INVALID_CL_PRESET_MSG);
			}
		} else {
			add_settings_error('url', 'url_error', INVALID_CL_URL_MSG);
		}

		return [
			'url' => esc_url_raw($input['url'], ['cloudinary']),
			'preset' => sanitize_text_field($input['preset'])
		];
	}

	/**
	* Register validate function
	*
	* @since 1.0.0
	*/
	public function options_update() {
		// do validation before registering setting
		register_setting(
			$this->plugin_name, $this->plugin_name, array($this, 'validate')
		);
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
			error_log(var_export($response, true));
			$status = intval(curl_getinfo($ch, CURLINFO_RESPONSE_CODE));
			curl_close($ch);

			// redirect back to library
			$lib_url = wp_get_referer();
			$location = add_query_arg(['status' => $status], $lib_url);
			wp_redirect($location);
		}

	}

	// need to add teh status to the query string and look for it there
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

	/**************************************************************************
	************************** PRIVATE METHODS ********************************
	**************************************************************************/

	/**
	* Construct Cloudinary validation url
	*
	* @since 1.0.0
	*
	* @param String $key API key/secret
	* @param String $name Cloudinary cloud name
	* @param String $path path to be validated
	* @return String URL representation
	*/
	private function cl_url($key, $name, $path) {
		return sprintf(self::CLOUDINARY_URL, $key, $name, $path);
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
	* Verify upload auth and check media ID
	*
	* Checks user permissions and check existence of upload media ID
	*
	* @since 1.0.0
	*/
	private function cl_can_upload() {
		error_log('cl_can_upload');
		// needs to check thoroughly so as not to cause endless redirect loop
		$sendback = wp_get_referer();
		error_log($sendback);
		$location = add_query_arg(array('upload_errors' => 'Upload failed'), $sendback);
		error_log($location);
		// wp_redirect($location);
		// exit();
	}
}
