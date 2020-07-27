<?php

namespace Gtg_Advanced_Blocks\Imports;

use Gtg_Advanced_Blocks\Abstracts\Import as Import;

class Block extends Import {

	/**
	 * blocks list
	 */
	protected $blocks = null;

	/**
	 * media list to import
	 */
	protected $media = null;

	/**
	 * mapping
	 */
	protected $mapping = [];

	/**
	 * variables
	 */
	private function setup_variables( $blocks ) {
		$this->blocks = $blocks;
		$this->media = null;
		$this->mapping = [];

		do_action( 'gutengeek_block_import_setup', $this );
	}

	/**
	 * prepare block
	 */
	protected function prepare_block( $block = [] ) {
		$attrs = ! empty( $block['attributes'] ) ? $block['attributes'] : [];
		$innerBlocks = ! empty( $block['innerBlocks'] ) ? $block['innerBlocks'] : [];
		$data = $block;

		foreach ( $attrs as $name => $attr ) {
			if ( $name === 'blockId' ) {
				continue;
			}

			$data['attributes'][$name] = $attr;
		}
		if ( $innerBlocks ) {
			foreach ( $innerBlocks as $innerBlock ) {
				$data['innerBlocks'][] = $this->prepare_block( $innerBlock );
			}
		}

		return $data;
	}

	/**
	 * prepare import
	 */
	public function prepare_import() {
		$blocks = [];
		foreach ( $this->blocks as $key => $block ) {
			// blockName
			if ( isset( $block['blockName'] ) && $block['blockName'] ) {
				$block = $this->prepare_block( $block );
				$blocks[] = $block;
			}
		}
		$this->blocks = $blocks;
		do_action( 'gutengeek_block_import_iprepare_block', $this );
	}

	/**
	 * process blocks import
	 *
	 * @return blocks
	 */
	public function process_import( $blocks = [] ) {
		// Block Importer only process import media and nothing else.
		// Because block will be import by javascript on client side

		// set up variables
		$this->setup_variables( $blocks );

		// prepare import before
		$this->prepare_import();

		// process import media
		$this->process_import_media( $block );

		// process mapping data
		$this->process_mapping_data();

		do_action( 'gutengeek_block_process_block_imported', $this );

		return $this;
	}

	/**
	 * process import media
	 */
	protected function process_import_media() {
		do_action( 'gutengeek_block_before_import_media', $this );

		do_action( 'gutengeek_block_media_imported', $this );
	}

	/**
	 * process mapping data after import all
	 */
	protected function process_mapping_data() {
		do_action( 'gutengeek_block_mapping_success', $this );
	}

	/**
	 *
	 */
	public function get_blocks() {
		return $this->blocks;
	}

}
