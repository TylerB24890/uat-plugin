<?php

/**
 * User Activity Tracking Plugin Bootstrap File
 *
 * @author 	Tyler Bailey
 * @version 1.0
 * @package uat
 * @subpackage uat/includes
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('UAT')) :

	class UAT {

		/**
		 * Plugin initialization functions
		 *
		 * @return 	null
		 * @since    1.0.0
		 */
		public function __construct() {
			$this->set_locale();
			$this->load_dependencies();
			$this->uat_set_ajax_vars();
		}


		/**
		 * Loads all required plugin files and istantiates classes
		 *
		 * @return 	null
		 * @since   1.0.0
		 */
		private function load_dependencies() {
			require_once UAT_GLOBAL_DIR . 'inc/class-uat-database-config.php';
			$uatdb = new UAT_Database_Config();
			$uatdb->check_db_tables();

			require_once UAT_GLOBAL_DIR . 'inc/class-uat-database-management.php';
			require_once UAT_GLOBAL_DIR . 'inc/class-uat-users.php';
			require_once UAT_GLOBAL_DIR . 'inc/class-uat-downloads.php';
			require_once UAT_GLOBAL_DIR . 'inc/class-uat-shortcodes.php';

			if(is_admin())
				require_once UAT_GLOBAL_DIR . 'inc/admin/class-uat-admin.php';

			wp_enqueue_script( 'uat-jquery-cookie', UAT_GLOBAL_URL . 'inc/frontend/assets/jquery.cookie.js', array('jquery'), '2.1.4', true );
			wp_enqueue_script( 'uat-js', UAT_GLOBAL_URL . 'inc/frontend/assets/uat.js', array('jquery', 'uat-jquery-cookie'), UAT_VERSION, true );

			add_filter( 'body_class', array($this, 'uat_add_body_class') );
		}

		/**
		 * Add uat-logged body class if cookie is found
		 *
		 * @return 	array
		 * @since   1.0.0
		 */
		public function uat_add_body_class($classes) {
			if(isset($_COOKIE[UAT_COOKIE])) {
				$classes[] = 'uat-logged';
			}

			return $classes;
		}

		/**
		 * Set plugin JS variables
		 *
		 * @return 	null
		 * @since   1.0.0
		 */
		private function uat_set_ajax_vars() {
			// Localize JS variables
			wp_localize_script('uat-js', 'uat', array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'uat_nonce' => wp_create_nonce('uat_nonce')
			));
		}

		/**
		 * Loads the plugin text-domain for internationalization
		 *
		 * @return 	null
		 * @since   1.0.0
		 */
		private function set_locale() {
			load_plugin_textdomain( UAT_SLUG, false, UAT_GLOBAL_DIR . 'language' );
	    }

	}

endif;
