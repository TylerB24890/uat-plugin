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

if(!class_exists('UAT_Admin')) :

	class UAT_Admin extends UAT_Downloads {

		/**
		* Executed on class istantiation.
		*
		* Constructs parent object
		* Adds menu pages on class load
		*
		* @since    1.0.0
		*/
		public function __construct() {

			if(!is_admin())
			exit("You must be an administrator.");

			parent::__construct();

			add_action( 'admin_menu', array( $this, 'uat_admin_menu_init' ) );
		}

		/**
		* Creates the top-level admin menu page
		*
		* @since    1.0.0
		*/
		public function uat_admin_menu_init() {
			// Create Top Level Menu
			add_menu_page(
				__('User Activity', UAT_SLUG),
				__('User Activity', UAT_SLUG),
				'manage_options',
				UAT_SLUG,
				array($this, 'uat_main_menu_page_render')
			);

			// Rename 'User Activity' top level submenu item to 'Overview'
			add_submenu_page(
				UAT_SLUG,
				__('UAT Overview', UAT_SLUG),
				__('Overview', UAT_SLUG),
				'manage_options',
				UAT_SLUG
			);

			// Create Users Page
			add_submenu_page(
				UAT_SLUG,
				__('UAT Users', UAT_SLUG),
				__('Users', UAT_SLUG),
				'manage_options',
				UAT_SLUG . '-users',
				array($this, 'uat_users_page_render')
			);

			// Create Downloads Page
			add_submenu_page(
				UAT_SLUG,
				__('UAT Downloads', UAT_SLUG),
				__('Downloads', UAT_SLUG),
				'manage_options',
				UAT_SLUG . '-downloads',
				array($this, 'uat_downloads_page_render')
			);
		}

		/**
		* Loads the landing page markup from admin partials
		*
		* @since    1.0.0
		*/
		public function uat_main_menu_page_render() {
			$this->uat_enqueue_styles();

			include_once(UAT_GLOBAL_DIR . 'inc/admin/partials/uat-admin-landing.php');
		}

		/**
		* Loads the users page markup from admin partials
		*
		* @since    1.0.0
		*/
		public function uat_users_page_render() {
			$this->uat_enqueue_styles();

			require_once(UAT_GLOBAL_DIR . 'inc/admin/class-uat-admin-users-list.php');
			include_once(UAT_GLOBAL_DIR . 'inc/admin/partials/uat-admin-users.php');
		}

		/**
		* Loads the downloads page markup from admin partials
		*
		* @since    1.0.0
		*/
		public function uat_downloads_page_render() {
			$this->uat_enqueue_styles();

			require_once(UAT_GLOBAL_DIR . 'inc/admin/class-uat-admin-downloads-list.php');
			include_once(UAT_GLOBAL_DIR . 'inc/admin/partials/uat-admin-downloads.php');
		}

		/**
		* Enqueue admin specific styles
		*
		* @since     1.0.0
		*/
		public function uat_enqueue_styles() {
			wp_enqueue_style(
				'uat-admin',
				plugin_dir_url(__FILE__) . 'styles/uat-admin.css',
				array(),
				UAT_VERSION,
				'all'
			);
		}
	}

	new UAT_Admin();

endif;
