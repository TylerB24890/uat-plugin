<?php

/**
 * User Activity Tracking Shortcodes
 *
 * @author 	Tyler Bailey
 * @version 1.0
 * @package uat
 * @subpackage uat/includes
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('UAT_Shortcodes')) :

	class UAT_Shortcodes {

		/**
		 * Executed on class istantiation.
		 *
		 * Constructs the WP shortcodes
		 *
		 * @since    1.0.0
		 */
		public function __construct() {
			$this->uat_construct_shortcodes();

			add_action( 'wp_footer', array($this, 'uat_add_footer') );
		}

		/**
		 * Private function to execute shortcode additions
		 *
		 * @since    1.0.0
		 */
		private function uat_construct_shortcodes() {
			add_shortcode( 'uat_login_form', array($this, 'uat_display_login_form') );
			add_shortcode( 'uat_admin_counts', array($this, 'uat_display_admin_counts') );
		}

		/**
		 * Displays the user login form & enqueue's styles
		 *
		 * @param	 @atts - array of shortcode data
		 * @since    1.0.0
		 */
		public function uat_display_login_form($atts) {
			$inc_styles = get_option('uat_styles');

			if($inc_styles && $inc_styles == 1) {
				wp_enqueue_style('uat-bootstrap-css', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
				wp_enqueue_style('uat-login-style', UAT_GLOBAL_URL . 'inc/frontend/assets/style.css');

				wp_enqueue_script( 'uat-bootstrap-js', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array('jquery'), '1.0.0', true );
			}
			wp_enqueue_script( 'uat-modal-js', UAT_GLOBAL_URL . 'inc/frontend/assets/uat-modal.js', array('jquery', 'uat-bootstrap-js', 'uat-js', 'uat-jquery-cookie'), '1.0.0', true );
			include_once(UAT_GLOBAL_DIR . 'inc/frontend/uat-login-form.php');
		}

		/**
		 * Adds the uat_login_form shortcode to the footer of the site
		 *
		 * If user cookie is not found the markup is added
		 *
		 * @since    1.0.0
		 */
		public function uat_add_footer() {
			$uobj = new UAT_Users();

			if(!$uobj->uat_check_user_cookie()) {
				echo do_shortcode('[uat_login_form]');
			}
		}

		/**
		 * Executes a number of functions to return data for the overview page
		 *
		 * @param	 @atts - array of shortcode data
		 * @since    1.0.0
		 */
		public function uat_display_admin_counts($atts) {
			$atts = shortcode_atts(
						array(
							'post' => false,
							'user' => false
						), $atts, 'uat_admin_counts');

			$dlobj = new UAT_Database_Management();
			$dlobj->uat_admin_counts($atts['post'], $atts['user']);
		}
	}

	new UAT_Shortcodes();

endif;
