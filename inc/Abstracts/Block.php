<?php

namespace Gtg_Advanced_Blocks\Abstracts;

defined( 'ABSPATH' ) || exit();

abstract class Block {

	/**
	 * The namespace which the blocks are registered
	 *
	 * @var string
	 */
	private $block_prefix = 'gutengeek';

	/**
	 * The slug of the block.
	 *
	 * @var null | string
	 */
	protected $block_slug = null;

	/**
	 * Block attributes
	 *
	 * @var null
	 */
	protected $attributes = [];

	/**
	 * Block constructor.
	 */
	public function __construct() {
		$this->block_slug = $this->set_slug();
		$this->attributes = $this->set_attributes();
	}

	/**
	 * get block prefix
	 *
	 * @return mixed|void
	 */
	public function get_block_prefix() {
		return apply_filters( 'gutengeekblock_block_prefix', $this->block_prefix, $this );
	}

	/**
	 * set attributes
	 *
	 * @return mixed
	 */
	abstract function set_attributes();

	/**
	 * get block attributes
	 *
	 * @return mixed|void
	 */
	public function get_attributes() {
		return apply_filters( 'gutengeekblock_block_attributes', $this->attributes, $this );
	}

	/**
	 * set block slug
	 *
	 * @return mixed
	 */
	abstract function set_slug();

	/**
	 * get block slug
	 *
	 * @return mixed|void
	 */
	public function get_slug() {
		return apply_filters( 'gutengeekblock_block_slug', $this->block_slug, $this );
	}

	/**
	 * register_assets method called if 'editor_script' || 'editor_style' || 'script' || 'style'
	 *
	 * is determine in attributes already exists
	 *
	 * @return bool | mixed
	 */
	public function register_assets() {
		return true;
	}

	/**
	 * @return bool
	 */
	public function visible() {
		return true;
	}
}
