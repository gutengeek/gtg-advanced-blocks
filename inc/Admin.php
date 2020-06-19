<?php
/**
 * Admin.
 *
 * @package WPOPAL
 */

namespace GutenGeek;

use GutenGeek\Admin\Ajax;
use GutenGeek\Admin\Menu;

defined( 'ABSPATH' ) || exit();

if ( !class_exists( 'Admin' ) ) {

	/**
	 * Class Admin.
	 */
	final class Admin {

		/**
		 * Calls on initialization
		 *
		 * @since  1.0.0
		 */
		public static function init() {
			// init admin menu
			Menu::init();

			// init ajax
			Ajax::init();
			self::initialise_plugin();
			add_action( 'after_setup_theme', [ __CLASS__, 'init_hooks' ] );
			// Activation hook.
			add_action( 'admin_init', [ __CLASS__, 'activation_redirect' ] );
		}

		/**
		 * we will redirect to setting page when first access after install plugin
		 */
		public static function activation_redirect() {
			if ( get_option( '_gutengeek_do_redirect' ) ) {
				delete_option( '_gutengeek_do_redirect' );
				if ( !is_multisite() ) {
					exit( wp_redirect( admin_url( 'admin.php?page=gutengeek-about' ) ) );
				}
			}
		}

		/**
		 * init admin hooks
		 */
		public static function init_hooks() {
			if ( !is_admin() ) {
				return;
			}

			add_action( 'admin_notices', [ __CLASS__, 'register_notices' ] );

			add_filter( 'wp_kses_allowed_html', [ __CLASS__, 'add_data_attributes' ], 10, 2 );

			// Enqueue admin scripts.
			add_action( 'admin_enqueue_scripts', [ __CLASS__, 'admin_enqueue_scripts' ] );
		}

		/**
		 * Filters and Returns a list of allowed tags and attributes for a given context.
		 *
		 * @param Array $allowedposttags Array of allowed tags.
		 * @param String $context Context type (explicit).
		 * @since 1.0.0
		 * @return Array
		 */
		public static function add_data_attributes( $allowedposttags, $context ) {
			$allowedposttags['a']['data-repeat-notice-after'] = true;

			return $allowedposttags;
		}

		/**
		 * Admin Notice
		 *
		 * @since 1.0.0
		 */
		public static function register_notices() {
			if ( !function_exists( 'register_block_type' ) ) {
				// Notice message if gutenberg not exists
				$class = 'notice notice-error';
				$message = sprintf( __( 'The %1$sGutenGeek%2$s plugin requires %1$sGutenberg%2$s plugin installed & activated.', 'gutengeek' ), '<strong>', '</strong>' );

				$plugin = 'gutenberg/gutenberg.php';

				if ( gutengeek_is_gutenberg_installed( $plugin ) ) {
					if ( !current_user_can( 'activate_plugins' ) ) {
						return;
					}
					$action_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
					$button_label = __( 'Activate Gutenberg', 'gutengeek' );
				} else {
					if ( !current_user_can( 'install_plugins' ) ) {
						return;
					}

					$action_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=gutenberg' ), 'install-plugin_gutenberg' );
					$button_label = __( 'Install Gutenberg', 'gutengeek' );
				}

				$button = '<p><a href="' . $action_url . '" class="button-primary">' . $button_label . '</a></p><p></p>';

				printf( '<div class="%1$s"><p>%2$s</p>%3$s</div>', esc_attr( $class ), $message, $button );
			}
		}

		/**
		 * Initialises the Plugin Name.
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public static function initialise_plugin() {
			define( 'GUTENGEEK_PLUGIN_NAME', 'GutenGeek' );
		}

		/**
		 * Enqueues the needed CSS/JS for the builder's admin settings page.
		 *
		 * @since 1.0.0
		 */
		public static function admin_enqueue_scripts() {
			global $pagenow;
			global $wp;
			wp_enqueue_style( 'gutengeek-admin-style', GUTENGEEK_URL . 'assets/css/admin/admin.css', [], GUTENGEEK_VER );
			// components
			$depends = ['gutengeek-admin-style'];
			if ( isset($_GET['page']) && $_GET['page'] === 'gutengeek' ) {
				wp_enqueue_style( 'wp-components' );
				$depends[] = 'wp-components';
			}
			if ( ! wp_script_is( 'gutengeek-components' ) ) {
				wp_enqueue_script( 'gutengeek-components', GUTENGEEK_URL . 'assets/js/admin/components.js', [ 'jquery', 'wp-util', 'updates', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-editor' ], GUTENGEEK_VER );
				wp_enqueue_style( 'gutengeek-components-style', GUTENGEEK_URL . 'assets/css/admin/components.css', $depends, GUTENGEEK_VER );
			}
			// end components
			// admin
			wp_enqueue_script( 'gutengeek-admin-script', GUTENGEEK_URL . 'assets/js/admin/admin.js', [ 'gutengeek-components' ], GUTENGEEK_VER );
			wp_set_script_translations( 'gutengeek-admin-script', 'gutengeek', GUTENGEEK_DIR . 'languages' );
			wp_localize_script( 'gutengeek-admin-script', 'gutengeek_blocks_plugin', apply_filters( 'gutengeek_localize_scripts', [
					'url' => GUTENGEEK_URL,
					'is_rtl' => is_rtl(),
					'home_url' => home_url(),
					'logo' => GUTENGEEK_URL . 'assets/images/logo.svg',
					'go_to_pro' => GUTENGEEK_GO_TO_PRO_URL,
					'settings_page' => [
						'login_url' => 'https://gutengeek.com/login/',
						'account_url' => 'https://gutengeek.com/my-account/',
						'document_url' => 'https://docs.gutengeek.com/',
					],
					'current_url' => home_url( add_query_arg( [], $wp->request ) ),
					'blocks' => gutengeek()->block_factory->get_block_attributes(),
					'global_settings' => gutengeek()->get_settings(),
					'assets' => [
						'no_image' => GUTENGEEK_URL . 'assets/images/no_image.jpg',
						'no_avatar' => GUTENGEEK_URL . 'assets/images/no_image.jpg',
					],
					'category' => 'gutengeek',
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'ajax_nonce' => wp_create_nonce( 'gutengeek-block-nonce' ),
					'breakpoints' => [
						'sm' => gutengeek_get_setting( 'sm_breakpoint', GUTENGEEK_SM_BREAKPOINT ),
						'xs' => gutengeek_get_setting( 'xs_breakpoint', GUTENGEEK_XS_BREAKPOINT ),
					],
					'image_sizes' => gutengeek_get_image_sizes(),
					'shapes' => gutengeek_shape_backgrounds(),
					'post_types' => gutengeek_get_post_types(),
					'taxonomies' => gutengeek_get_related_taxonomy(),
					'color_presets' => gutengeek_get_color_presets(),
					'button_styles' => gutengeek_get_button_styles(),
					'dividers' => gutengeek_divider_styles(),
					'fontawesome_icons' => gutengeek_load_fontawesome_icons(),
					'fonts' => gutengeek_get_custom_fonts(),
					'custom_icons' => gutengeek_get_custom_icons(),
					'mask_presets' => gutengeek_masks_presets()
				] )
			);
			// end admin
		}
	}

	Admin::init();
}
