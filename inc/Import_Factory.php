<?php
/**
 * WPOPAL Import Factory.
 *
 * @package Gtg_Advanced_Blocks
 */

namespace Gtg_Advanced_Blocks;

defined( 'ABSPATH' ) || exit();

class Import_Factory {

	/**
	 * @var processer
	 */
	private $processers = [];

	/**
	 * instance class
	 */
	public static $instance = null;

	/**
	 * instance import factory
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * constructor
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * init import factory
	 */
	private function init() {
		do_action( 'gutengeek_import_factory_before_init', $this );
		/**
		 * block import
		 */
		$this->register( 'block', '\Gtg_Advanced_Blocks\Imports\Block' );

		/**
		 * theme import
		 */
		$this->register( 'block', '\Gtg_Advanced_Blocks\Imports\Theme' );
		do_action( 'gutengeek_import_factory_inited', $this );
	}

	/**
	 * register new processer
	 */
	public function register( $name = '', $processer, $override = false ) {
		if ( ! $name ) {
			throw new \Exception( __( 'Name invalid', 'gutengeek' ) );
		}

		if ( ! $processer ) {
			throw new \Exception( __( 'Processer invalid', 'gutengeek' ) );
		}

		if ( (isset($this->processers[$name]) && $override) || ! isset($this->processers[$name]) ) {
			$this->processers[$name] = $processer;
		}

		// registered hook
		do_action( 'gutengeek_import_factory_registerd', $name, $processer );
	}

	/**
	 * magic method
	 */
	public function __get( $name ) {
		if ( isset( $name ) && isset( $this->processers[$name] ) ) {
			return new $this->processers[$name];
		}
		return false;
	}
}
