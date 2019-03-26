<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://author.example.com
 * @since      1.0.0
 *
 * @package    Cloudinary_Images
 * @subpackage Cloudinary_Images/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap cl-admin">
    <h2><?php echo esc_html(get_admin_page_title()) ?></h2>
    <form action="options.php" method="POST" name="cl_images_options">
        <?php
            $options = get_option($this->plugin_name);
            $cloudinary_url = isset($options['url']) ? $options['url'] : '';
            $cloudinary_preset = isset($options['preset']) ? $options['preset'] : '';
            $do_transforms = isset($options['transforms']) ? $options['transforms'] : 0;
            $configured = isset($options['configured']) ? $options['configured'] : false;

            error_log("Display:");
            error_log(var_export($options, true));

            settings_fields($this->plugin_name);
            do_settings_sections($this->plugin_name);
        ?>
        <input type="hidden" id="configured" name="configured" value="0" />
        <div>
            <?php
                // errors can be used to determine form field highlighting
                $err = get_settings_errors();
            ?>
        </div>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="<?php echo $this->plugin_name ?>-url">
                        <?php esc_attr_e('Cloudinary URL', 'WpAdminStyle') ?>
                    </label>
                </th>
                <td scope="row">
                    <input
                        id="<?php echo $this->plugin_name ?>-url"
                        name="<?php echo $this->plugin_name ?>[url]"
                        type="url"
                        class="large-text"
                        placeholder="cloudinary://API KEY:API SECRET@CLOUD NAME"
                        value="<?= $cloudinary_url ?: '' ?>">
                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?php echo $this->plugin_name ?>-preset">
                        <?php esc_attr_e('Upload Preset', 'WpAdminStyle') ?>
                    </label>
                </th>
                <td>
                    <input
                        id="<?php echo $this->plugin_name ?>-preset"
                        name="<?php echo $this->plugin_name ?>[preset]"
                        type="text"
                        class="regular-text"
                        placeholder="Upload Preset Name (optional)"
                        value="<?= $cloudinary_preset ?: '' ?>">
                </td>
            </tr>
            <tr>
                <th>&nbsp;</th>
                <td>
                    <input
                        id="<?= $this->plugin_name ?>-transforms"
                        name="<?= $this->plugin_name ?>[transforms]"
                        type="checkbox"
                        value="1"
                        <?php echo $configured ? '' : 'checked' ?>>
                    <label for="<?= $this->plugin_name ?>-transforms">
                        <strong>Build/Update Transformations</strong>
                    </label>
                </td>
            </tr>
        </table>
        <?php submit_button("Save Changes", 'primary', 'submit', true) ?>
    </form>
</div>