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
			'',
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
	// this really will just return the sanitized input
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
		$columns['cloudinary-image'] = 'Cloudinary';
		return $columns;
	}

	/**
	* Add the Cloudinary upload links
	*
	* @since 1.0.0
	*/
	public function add_cloudinary_upload($col_name, $media_id) {
		if($col_name == 'cloudinary-image') {
			printf('<a href="#%u">%s</a>', $media_id, CL_UPLOAD_TITLE);
		}
	}

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
	* Query url to Cloudinary
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
		error_log($response['headers']['status']);
		return $is_valid;
	}
}
