<?php

namespace GutenGeek\Interfaces;

defined( 'ABSPATH' ) || exit();

/**
 * The interface block class
 *
 * Interface Block
 * @package GutenGeek\Interfaces
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
