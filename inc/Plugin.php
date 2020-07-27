<?php

namespace Gtg_Advanced_Blocks;

/**
 * Plugin main class
 *
 * @package Gtg_Advanced_Blocks\
 */

defined( 'ABSPATH' ) || exit();

/**
 * Class Plugin.
 */
final class Plugin {

	/**
	 * instance class
	 *
	 * @var $instance
	 */
	private static $instance;

	private $version = '1.0.4';

	/**
	 * block factory
	 *
	 * @var null | object
	 */
	public $block_factory;

	/**
	 * assets factory
	 *
	 * @var null | object
	 */
	public $assets_factory;

	/**
	 * api server init
	 *
	 * @var null | object
	 */
	public $api;

	/**
	 * @var null | User
	 */
	public $user;

	/**
	 * global settings
	 */
	public $global_settings;

	/**
	 * module
	 */
	public $module;

	/**
	 *  Initiator
	 */
	public static function instance() {
		if ( !isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Plugin class constructor
	 */
	public function __construct() {

		// define our constants
		$this->define_constants();

		// int hooks
		$this->init_hooks();

		// include needed files
		$this->_includes();

		add_action( 'plugins_loaded', [ $this, 'plugin_loaded' ] );
	}

	/**
	 * Define constants
	 *
	 * @since 1.0.0
	 */
	public function define_constants() {
		define( 'GTG_AB_BASE', plugin_basename( GTG_AB_FILE ) );
		define( 'GTG_AB_DIR', plugin_dir_path( GTG_AB_FILE ) );
		define( 'GTG_AB_URL', plugins_url( '/', GTG_AB_FILE ) );
		define( 'GTG_AB_VER', $this->version );
		define( 'GTG_AB_SLUG', 'gutengeek' );
		define( 'GTG_AB_SM_BREAKPOINT', 976 );
		define( 'GTG_AB_XS_BREAKPOINT', 767 );
		define( 'GTG_AB_GO_TO_PRO_URL', 'https://gutengeek.com/my-account/' );

		$upload_dir = wp_upload_dir();
		$static_css_path = trailingslashit( $upload_dir['basedir'] ) . 'gutengeek';
		$static_css_url = trailingslashit( $upload_dir['baseurl'] ) . 'gutengeek';
		define( 'GTG_AB_STATIC_CSS_PATH', trailingslashit( $static_css_path ) );
		define( 'GTG_AB_STATIC_CSS_URL', trailingslashit( $static_css_url ) );
	}

	/**
	 * init plugin hooks
	 */
	public function init_hooks() {
		register_activation_hook( GTG_AB_FILE, [ '\Gtg_Advanced_Blocks\Install', 'install' ] );
		register_deactivation_hook( GTG_AB_FILE, [ '\Gtg_Advanced_Blocks\Install', 'uninstall' ] );
	}

	/**
	 * Loads plugin files.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function plugin_loaded() {
		$this->load_textdomain();
		$this->block_factory = new Block_Factory();
	}

	public function get_settings() {
		if ( ! $this->global_settings ) {
			$this->global_settings = get_option( '_gutengeek_global_settings', [] );
		}
		return apply_filters( 'gutengeek_global_settings', $this->global_settings );
	}

	/**
	 * Includes.
	 *
	 * @since 1.0.0
	 */
	private function _includes() {
		if ( !function_exists( 'register_block_type' ) ) {
			return;
		}
		$this->_include( 'inc/functions.php' );
		$this->_include( 'inc/template-functions.php' );
		$this->_include( 'inc/Admin.php' );

		// $this->block_factory = new Block_Factory();
		$this->module = new Module();
		$this->assets_factory = Block_Assets::instance();
		$this->import_factory = Import_Factory::instance();
		$this->api = new Api();
		$this->user = new User();
	}

	/**
	 * Determine $file if exists include it, otherwise not
	 *
	 * @param $file
	 */
	private function _include( $file ) {
		$path = GTG_AB_DIR . $file;
		if ( file_exists( $path ) && is_readable( $path ) ) {
			require_once $path;
		}
	}

	/**
	 * Load text domain
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function load_textdomain() {
		$lang_dir = apply_filters( 'gutengeek_languages_directory', GTG_AB_ROOT . '/languages/' );
		load_plugin_textdomain( 'gutengeek', false, $lang_dir );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( GTG_AB_DIR );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'gutengeek_template_path', 'gutengeek/' );
	}

	/**
	 * Throw error on object clone
	 *
	 * we don't want the object to be cloned.
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'gutengeek' ), '1.0.0' );
	}
}


