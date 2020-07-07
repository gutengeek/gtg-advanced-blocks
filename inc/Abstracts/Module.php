<?php

namespace Gtg_Advanced_Blocks\Abstracts;

defined( 'ABSPATH' ) || exit();

abstract class ModuleAbstract {

	public function __construct() {
		// call boot method when init hook is fire
		add_action( 'init', [ $this, 'boot' ], 10 );
	}

	/**
	 * register method call when class init
	 *
	 * for example register hooks
	 */
	public function register($callback = null) {
		// do some magic
	}

	/**
	 * remove hooks added
	 */
	public function un_register() {

	}

	/**
	 *
	 */
	public function boot() {
		if ( current_hook() !== 'init' ) {
			return;
		}

		// init hook
		$this->init();
	}

	public function init() {}

}
