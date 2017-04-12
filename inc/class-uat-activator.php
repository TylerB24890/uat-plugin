<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @author 	Tyler Bailey
 * @version 1.0.0
 * @package uat
 * @subpackage uat/includes
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('UAT_Activator')) :

	class UAT_Activator {

		/**
		 * Fired upon plugin activation
		 *
		 * Checks system requirements
		 *
		 * Runs database configuration
		 *
		 * @since    1.0.0
		 */
		public static function activate() {
			self::uat_system_requirements_met();
			self::run_database_config();
		}

		/**
		 * Checks if the system requirements are met
		 *
		 * @since	1.0.0
		 * @return 	bool True if system requirements are met, die() message if not
		 */
		private static function uat_system_requirements_met() {
			global $wp_version;

			if ( version_compare( PHP_VERSION, UAT_REQUIRED_PHP_VERSION, '<' ) ) {
				wp_die(__("PHP 5.3 is required to run this plugin.", UAT_SLUG), __('Incompatible PHP Version', UAT_SLUG));
			}
			if ( version_compare( $wp_version, UAT_REQUIRED_WP_VERSION, '<' ) ) {
				wp_die(__("You must be running at least WordPress 3.5 for this plugin to function properly.", UAT_SLUG), __('Incompatible WordPress Version.', UAT_SLUG));
			}

			return true;
		}

		/**
		 * Runs the database configuration file on activation
		 *
		 * @since    1.0.0
		 */
		private static function run_database_config() {
			require_once(UAT_GLOBAL_DIR . '/inc/class-uat-database-config.php');
			$uat_db = new UAT_Database_Config();

			$uat_db->check_db_tables();
		}
	}

endif;
