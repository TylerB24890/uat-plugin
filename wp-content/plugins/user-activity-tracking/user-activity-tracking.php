<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://tylerb.me
 * @since             1.0.0
 * @package           uat
 *
 * @wordpress-plugin
 * Plugin Name:       User Activity Tracking
 * Plugin URI:        http://tylerb.me
 * Description:       A passwordless login system used to track user activity and downloads
 * Version:           1.0.0
 * Author:            Tyler Bailey
 * Author URI:        http://tylerb.me
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       uat
 */



// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die("Sneaky sneaky...");
}

// Define constants
define('UAT_VERSION', '1.0.0');
define('UAT_SLUG', 'uat');
define('UAT_COOKIE', UAT_SLUG . '_logged');
define('UAT_GLOBAL_DIR', plugin_dir_path( __FILE__ ));
define('UAT_GLOBAL_URL', plugin_dir_url( __FILE__ ));
define('UAT_REQUIRED_PHP_VERSION', '5.3');
define('UAT_REQUIRED_WP_VERSION',  '3.1');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-post-exporter-activator.php
 */
function activate_uat() {
	require_once UAT_GLOBAL_DIR . 'inc/class-uat-activator.php';
	UAT_Activator::activate();
}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-post-exporter-deactivator.php
 */
function deactivate_uat() {
	require_once UAT_GLOBAL_DIR . 'inc/class-uat-deactivator.php';
	UAT_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_uat' );
register_deactivation_hook( __FILE__, 'deactivate_uat' );


/**
 * The core plugin classes that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require UAT_GLOBAL_DIR .  'inc/class-uat.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
if(!function_exists('uat_init')) {
	function uat_init() {
		new UAT();
	}
}
add_action('init', 'uat_init');
