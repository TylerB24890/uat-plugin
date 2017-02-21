<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to decativate the plugin
 *
 * @author 	Tyler Bailey
 * @version 1.0.0
 * @package uat
 * @subpackage uat/includes
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

if(!class_exists('UAT_Deactivator')) :
	
	class UAT_Deactivator {

		/**
		 * Fired on plugin deactivation
		 *
		 * @since    1.0.0
		 */
		public static function deactivate() {
			// nothing here yet
		}
	}

endif;
