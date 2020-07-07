<?php

namespace Gtg_Advanced_Blocks\Interfaces;

defined( 'ABSPATH' ) || exit();

/**
 * The interface block class
 *
 * Interface Block
 * @package Gtg_Advanced_Blocks\Interfaces
 */
interface Block {

	/**
	 * define block id depend of 'gutengeek/'
	 *
	 * @return mixed
	 */
	public function set_slug();

	/**
	 * define block attributes
	 *
	 * @return mixed
	 */
	public function set_attributes();

}
