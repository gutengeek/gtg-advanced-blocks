<?php

namespace Gtg_Advanced_Blocks;

defined( 'ABSPATH' ) || exit();

use Gtg_Advanced_Blocks\Abstracts\Module as AbstractModule;

class Module {

	// instance class
	private static $instance;

	/**
	 * modules list registered
	 *
	 * @var array
	 * @since 1.0.0
	 */
	protected $modules = [];

	public function __construct() {
		// register default modules
		$this->register_modules();
	}

	/**
	 * register default modules
	 */
	public function register_modules() {
		$this->register( 'custom_fonts', '\Gtg_Advanced_Blocks\Modules\CustomFonts' );
		$this->register( 'custom_icons', '\Gtg_Advanced_Blocks\Modules\CustomIcons' );
	}

	/**
	 * register module to object
	 *
	 * @param name unique string
	 * @param module main module process classname, instance class
	 * @param callback callback when registerd module
	 *
	 * @return Gtg_Advanced_Blocks\Module
	 */
	public function register( $name, $module, $callback = null ) {
		if ( ! isset( $this->modules[ $name ] ) ) {
			if ( is_string( $name ) ) {
				$this->modules[ $name ] = new $module;
			} else if ( $name instanceof AbstractModule ) {
				$this->modules[ $name ] = $module;
			}
			if ( isset($this->modules[ $name ]) ) {
				// call register method in module class
				if ( isset($callback) && is_callable( $callback ) ) {
					call_user_func_array( [ $this->modules[ $name ], 'register' ], [ $callback, $this ] );
				} else {
					call_user_func_array( [ $this->modules[ $name], 'register' ], [ null, $this ]);
				}
			} else if ( is_callable( $module ) ) {
				// if $module is only function callback
				if ( is_callable( $callback ) ) {
					$this->modules[ $name ] = call_user_func_array( $module, [ $callback, $this ] );
				} else {
					$this->modules[ $name ] = call_user_func_array( $module, [ null, $this ] );
				}
			}
		}

		return $this;
	}

	/**
	 * get module
	 *
	 */
	public function get_module($name) {
		if ( ( ! isset( $name ) || ! isset( $this->modules[ $name ] ) ) && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			_doing_it_wrong( __FUNCTION__, __( 'Module name not found.', 'gutengeek' ) );
		}

		return $this->modules[ $name ];
	}

	/**
	 * get instance of class
	 *
	 * @var object || null
	 * @since 1.0.0
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new static();
		}

		return self::$instance;
	}
}
