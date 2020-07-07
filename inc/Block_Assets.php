<?php
/**
 * Block_Assets.
 *
 * @package Gtg_Advanced_Blocks
 */

namespace Gtg_Advanced_Blocks;

defined( 'ABSPATH' ) || exit();

class Block_Assets {

	public static $instance = null;

	public $page_blocks = [];

	public static function instance() {
		if ( !self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		// Frontend assets
		add_action( 'enqueue_block_assets', [ $this, 'enqueue_block_assets' ] );
		// Editor assets
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_assets' ] );

		add_action( 'wp_head', [ $this, 'generate_block_style' ], 80 );
		// generate script in footer
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_scripts' ], 1000 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_custom_style' ], 999999 );
	}

	/**
	 * Enqueue Gutenberg block assets for both frontend + backend.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_block_assets() {

		// before enqueue block assets
		do_action( 'gutengeek_enqueue_block_assests' );

		if ( !wp_script_is( 'jquery', 'enqueued' ) ) {
			wp_enqueue_script( 'jquery' );
		}

		// Styles.
		wp_enqueue_style( 'gutengeek-block-css', GTG_AB_URL . 'assets/css/frontend/blocks.build.css', [], GTG_AB_VER );
		// Scripts.
		wp_enqueue_script( 'gutengeek-masonry', GTG_AB_URL . 'assets/js/libs/isotope.min.js', [ 'jquery' ], GTG_AB_VER, false );

		wp_enqueue_script( 'gutengeek-imagesloaded', GTG_AB_URL . 'assets/js/libs/imagesloaded.min.js', [ 'jquery' ], GTG_AB_VER, false );

		// Scripts.
		wp_enqueue_script( 'gutengeek-fitvids-js', GTG_AB_URL . 'assets/js/libs/fitvids.min.js', [ 'jquery' ], GTG_AB_VER, false );

		// Styles.
		wp_enqueue_style( 'gutengeek-slick-css', GTG_AB_URL . 'assets/css/libs/slick.css', [], GTG_AB_VER );

		// Styles.
		wp_enqueue_style( 'gutengeek-animate-css', GTG_AB_URL . 'assets/css/libs/animate.min.css', [], GTG_AB_VER );

		// Scripts.
		wp_enqueue_script( 'gutengeek-slick-js', GTG_AB_URL . 'assets/js/libs/slick.min.js', [ 'jquery' ], GTG_AB_VER, false );
		// Scripts.
		wp_enqueue_script( 'gutengeek-magnific-popup', GTG_AB_URL . 'assets/js/libs/jquery.magnific-popup.min.js', [ 'jquery' ], '1.1.0', false );

		// Styles.
		wp_enqueue_style( 'gutengeek-magnific-popup', GTG_AB_URL . 'assets/css/libs/magnific-popup.css', [], '1.1.0' );

		// common scripts
		wp_enqueue_script( 'gutengeek-frontend-scripts', GTG_AB_URL . 'assets/js/frontend/frontend.js', [ 'jquery' ], GTG_AB_VER, false );

		// enqueued block assets
		do_action( 'gutengeek_enqueued_block_assests' );
	}

	/**
	 * Enqueue Gutenberg block assets for backend editor.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_editor_assets() {
		// components
		wp_enqueue_script( 'gutengeek-components', GTG_AB_URL . 'assets/js/admin/components.js', [ 'wp-edit-post', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor', 'wp-api-fetch', 'wp-compose', 'wp-data', 'gutengeek-advanced-components' ], GTG_AB_VER, true );
		wp_enqueue_style( 'gutengeek-components-style', GTG_AB_URL . 'assets/css/admin/components.css', ['gutengeek-admin-style'], GTG_AB_VER );
		// block editor //'gutengeek-components',
		wp_enqueue_script( 'gutengeek-editor-js', GTG_AB_URL . 'assets/js/admin/blocks.editor.js', [ 'gutengeek-admin-script' ], GTG_AB_VER );
		// block editor styles
		wp_enqueue_style( 'gutengeek-block-editor-css', GTG_AB_URL . 'assets/css/admin/blocks.editor.css', [ 'wp-edit-blocks' ], GTG_AB_VER );
	}

	/**
	 * Generates stylesheet and appends in head tag.
	 *
	 * @since  1.0.0
	 */
	public function generate_block_style() {
		// load inline style
		$post_id = get_the_ID();
		// if is not post return
		if (! $post_id) {
			return;
		}
		$file = gtg_get_static_css_file_path( $post_id );
		if ( gtg_global_setting_name( 'css_method' ) === 'file' && file_exists( $file ) ) {
			return;
		}
		$css_content = $this->get_post_css_content( $post_id );

		if ( ! $css_content ) {
			return;
		}
		?>
		<style type="text/css" media="all" id="gutengeek-block-style">
			<?php printf( '%s', $css_content ) ?>
		</style>
		<?php
	}

	/**
	 * get the post css content form file content || post meta key '_gutengeek_css'
	 *
	 * @param null $post_id
	 * @return false|string
	 */
	private function get_post_css_content( $post_id = null ) {
		global $wp_filesystem;
		$file = gtg_get_static_css_file_path( $post_id );
		$css_content = '';
		if ( metadata_exists( 'post', $post_id, '_gutengeek_css' ) ) {
			$css_content = get_post_meta( $post_id, '_gutengeek_css', true );
		} else if ( file_exists( $file ) ) {
			$css_content = $wp_filesystem->get_contents( $file );
		}

		return apply_filters( 'gutengeek_post_css_content', $css_content, $post_id );
	}

	/**
	 * load google font storage in post meta
	 */
	public function enqueue_frontend_scripts() {

		// frontend scripts
		// wp_enqueue_script( 'gutengeek-frontend-scripts', GTG_AB_URL . 'assets/js/frontend/frontend.js', [ 'jquery' ], GTG_AB_VER, false );
		wp_localize_script( 'gutengeek-frontend-scripts', 'gtg_frontend_config', apply_filters( 'gutengeek_localize_frontend_scripts', [
				'url'             => GTG_AB_URL,
				'home_url'        => home_url(),
				'ajax_url'        => admin_url( 'admin-ajax.php' ),
				'rtl'          => is_rtl(),
				'global_settings' => gtg_advanced_blocks()->get_settings(),
				'_form_nonce' => wp_create_nonce('gutengeek-form-nonce')
			] )
		);
		// wp_enqueue_script( 'gutengeek-block-frontend', GTG_AB_URL . 'assets/js/frontend/blocks.build.js', [ 'gutengeek-frontend-scripts' ], GTG_AB_VER, false );
		wp_enqueue_style( 'gutengeek-block-frontend', GTG_AB_URL . 'assets/css/frontend/frontend.css', [], GTG_AB_VER );
	}

	public function enqueue_custom_style() {
		$postId = get_the_ID();

		if ( $postId ) {
			// load static css file created on save post
			$allowed = gtg_global_setting_name( 'css_method' );
			$file = gtg_get_static_css_file_path( $postId );
			if ( file_exists($file) && ( !$allowed || $allowed === 'file' ) ) {
				$file_url = gtg_get_static_css_file_url( $postId );
				if ( $file_url ) {
					$version = GTG_AB_VER;
					$version = defined( 'WP_DEBUG' ) && WP_DEBUG ? uniqid() : GTG_AB_VER;
					wp_enqueue_style( 'gutengeek-frontend-style', $file_url, [], $version );
				}
			}
		}
	}

}

Block_Assets::instance();
