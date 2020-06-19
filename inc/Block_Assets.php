<?php
/**
 * Block_Assets.
 *
 * @package WPOPAL
 */

namespace GutenGeek;

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
		wp_enqueue_style( 'gutengeek-block-css', GUTENGEEK_URL . 'assets/css/frontend/blocks.build.css', [], GUTENGEEK_VER );
		// Scripts.
		wp_enqueue_script( 'gutengeek-masonry', GUTENGEEK_URL . 'assets/js/libs/isotope.min.js', [ 'jquery' ], GUTENGEEK_VER, false );

		wp_enqueue_script( 'gutengeek-imagesloaded', GUTENGEEK_URL . 'assets/js/libs/imagesloaded.min.js', [ 'jquery' ], GUTENGEEK_VER, false );

		// Scripts.
		wp_enqueue_script( 'gutengeek-fitvids-js', GUTENGEEK_URL . 'assets/js/libs/fitvids.min.js', [ 'jquery' ], GUTENGEEK_VER, false );

		// Styles.
		wp_enqueue_style( 'gutengeek-slick-css', GUTENGEEK_URL . 'assets/css/libs/slick.css', [], GUTENGEEK_VER );

		// Styles.
		wp_enqueue_style( 'gutengeek-animate-css', GUTENGEEK_URL . 'assets/css/libs/animate.min.css', [], GUTENGEEK_VER );

		// Scripts.
		wp_enqueue_script( 'gutengeek-slick-js', GUTENGEEK_URL . 'assets/js/libs/slick.min.js', [ 'jquery' ], GUTENGEEK_VER, false );
		// Scripts.
		wp_enqueue_script( 'gutengeek-magnific-popup', GUTENGEEK_URL . 'assets/js/libs/jquery.magnific-popup.min.js', [ 'jquery' ], '1.1.0', false );

		// Styles.
		wp_enqueue_style( 'gutengeek-magnific-popup', GUTENGEEK_URL . 'assets/css/libs/magnific-popup.css', [], '1.1.0' );

		// common scripts
		wp_enqueue_script( 'gsap', GUTENGEEK_URL . 'assets/js/libs/gsap.min.js', [], '3.2.6', false );
		wp_enqueue_script( 'gutengeek-frontend-scripts', GUTENGEEK_URL . 'assets/js/frontend/frontend.js', [ 'jquery' ], GUTENGEEK_VER, false );

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
		wp_enqueue_script( 'gutengeek-components', GUTENGEEK_URL . 'assets/js/admin/components.js', [ 'wp-edit-post', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor', 'wp-api-fetch', 'wp-compose', 'wp-data' ], GUTENGEEK_VER, true );
		wp_enqueue_style( 'gutengeek-components-style', GUTENGEEK_URL . 'assets/css/admin/components.css', ['gutengeek-admin-style'], GUTENGEEK_VER );
		// block editor //'gutengeek-components',
		wp_enqueue_script( 'gutengeek-editor-js', GUTENGEEK_URL . 'assets/js/admin/blocks.editor.js', [ 'gutengeek-admin-script' ], GUTENGEEK_VER );
		// block editor styles
		wp_enqueue_style( 'gutengeek-block-editor-css', GUTENGEEK_URL . 'assets/css/admin/blocks.editor.css', [ 'wp-edit-blocks' ], GUTENGEEK_VER );
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
		$file = gutengeek_get_static_css_file_path( $post_id );
		if ( gutengeek_global_setting_name( 'css_method' ) === 'file' && file_exists( $file ) ) {
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
		$file = gutengeek_get_static_css_file_path( $post_id );
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
		// wp_enqueue_script( 'gutengeek-frontend-scripts', GUTENGEEK_URL . 'assets/js/frontend/frontend.js', [ 'jquery' ], GUTENGEEK_VER, false );
		wp_localize_script( 'gutengeek-frontend-scripts', 'gutengeek_frontend_config', apply_filters( 'gutengeek_localize_frontend_scripts', [
				'url'             => GUTENGEEK_URL,
				'home_url'        => home_url(),
				'ajax_url'        => admin_url( 'admin-ajax.php' ),
				'rtl'          => is_rtl(),
				'global_settings' => gutengeek()->get_settings(),
				'_form_nonce' => wp_create_nonce('gutengeek-form-nonce')
			] )
		);
		// wp_enqueue_script( 'gutengeek-block-frontend', GUTENGEEK_URL . 'assets/js/frontend/blocks.build.js', [ 'gutengeek-frontend-scripts' ], GUTENGEEK_VER, false );
		wp_enqueue_style( 'gutengeek-block-frontend', GUTENGEEK_URL . 'assets/css/frontend/frontend.css', [], GUTENGEEK_VER );
	}

	public function enqueue_custom_style() {
		$postId = get_the_ID();

		if ( $postId ) {
			// load static css file created on save post
			$allowed = gutengeek_global_setting_name( 'css_method' );
			$file = gutengeek_get_static_css_file_path( $postId );
			if ( file_exists($file) && ( !$allowed || $allowed === 'file' ) ) {
				$file_url = gutengeek_get_static_css_file_url( $postId );
				if ( $file_url ) {
					$version = GUTENGEEK_VER;
					$version = defined( 'WP_DEBUG' ) && WP_DEBUG ? uniqid() : GUTENGEEK_VER;
					wp_enqueue_style( 'gutengeek-frontend-style', $file_url, [], $version );
				}
			}
		}
	}

}

Block_Assets::instance();
