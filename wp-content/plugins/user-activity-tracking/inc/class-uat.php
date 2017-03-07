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

			add_action( 'wp', array($this, 'uat_set_ajax_vars') );
			add_action( 'the_content', array($this, 'uat_add_footer_script') );
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
				require_once UAT_GLOBAL_DIR . 'inc/admin/class-uat-admin-settings.php';

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
		public function uat_set_ajax_vars() {

			// Localize JS variables
			wp_localize_script('uat-js', 'uat', array(
				'ajaxurl' => admin_url('admin-ajax.php'),
				'uat_nonce' => wp_create_nonce('uat_nonce'),
			));
		}

		public function uat_add_footer_script() {
			global $post;

			wp_reset_postdata();
			ob_start();

			$url = get_the_permalink($post->ID);
		?>
			<script>
			(function($) {

				// Loop through all <a> tags and add data-types if the link downloads a file
				$('a').each(function() {
					var target = $(this).attr('href');
					var fileType = target.substr(target.lastIndexOf('.') + 1);

					switch(fileType) {
						case 'jpg':
						case 'png':
						case 'gif':
							$(this).attr('data-type', 'image');
						break;
						case 'pdf':
							$(this).attr('data-type', 'pdf');
						break;
						case 'docx':
							$(this).attr('data-type', 'docx');
						break;
						case 'rtf':
							$(this).attr('data-type', 'rtf');
						break;
						case 'zip':
							$(this).attr('data-type', 'zip');
						break;
					}

					<?php if(!is_single()) : ?>
						if(target === "<?php echo $url; ?>") {
							$(this).attr('data-post', '<?php echo $post->ID; ?>');
						}
					<?php else : ?>
						$(this).attr('data-post', '<?php echo $post->ID; ?>');
					<?php endif; ?>
				});
			})(jQuery);
			</script>
		<?php

			return ob_get_clean();
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
