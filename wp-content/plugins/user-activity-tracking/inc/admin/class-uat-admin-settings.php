<?php

/**
* User Activity Tracking Administration
*
* @author 	Tyler Bailey
* @version 1.0
* @package uat
* @subpackage uat/includes/admin
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('UAT_Admin_Settings')) :

	class UAT_Admin_Settings {

		/**
		* Executed on class istantiation.
		*
		* Constructs parent object
		*
		* @since    1.0.0
		*/
		public function __construct() {

			add_action("admin_init", array($this, 'uat_display_plugin_fields'));
		}

		public function uat_display_license_field() {
			$cur_val = get_option('uat_license');
			$editor_args = array(
				'textarea_name' => 'uat_license'
			);

			wp_editor($cur_val, 'uat_license', $editor_args);
		}

		public function uat_include_styles_field() {
			$cur_val = get_option('uat_styles');

			echo '<input type="checkbox" name="uat_styles" value="1" ' . ($cur_val && $cur_val == 1 ? 'checked' : '') . '/>';
			echo ' <small>Will include Twitter Bootstrap CSS & JS files only while the login window is present.</small>';
		}

		public function uat_display_plugin_fields() {
			register_setting('uat_options', 'uat_license');
			register_setting('uat_options', 'uat_styles');
			add_settings_section("uat-form", "User Activity Login Form", null, "uat");
			add_settings_field("uat_styles", "Include Default UAT Styles", array($this, 'uat_include_styles_field'), 'uat', 'uat-form');
			add_settings_field("uat_license", "License Agreement Text", array($this, 'uat_display_license_field'), 'uat', 'uat-form');
		}

		public function uat_options_validate() {

		}
	}

	new UAT_Admin_Settings();
endif;
