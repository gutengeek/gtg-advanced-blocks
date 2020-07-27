<?php

namespace Gtg_Advanced_Blocks\Blocks;

use Gtg_Advanced_Blocks\Abstracts\Post_Block;
use Gtg_Advanced_Blocks\Interfaces\Block as InterfaceBlock;

defined( 'ABSPATH' ) || exit();

class Post_Carousel extends Post_Block implements InterfaceBlock {

	/**
	 * Block id
	 *
	 * @return mixed|string
	 */
	public function set_slug() {
		return 'post-carousel';
	}

	/**
	 * define block attributes
	 *
	 * @return mixed
	 */
	public function set_attributes() {
		return [
			'render_callback' => [ $this, 'render_callback' ],
		];
	}

	/**
	 * server render block
	 */
	public function render_callback( $attributes ) {
		global $gutengeek_post_settings;

		$attributes = array_merge([
			'imgSize' => 'large',
			'pagiLoadmoreLabel' => __('Load More', 'gutengeek'),
			'pagiNextEnableIcon' => true,
			'align' => 'center',
			'enablePostExcerpt' => true,
			'enableFilter' => false,
			'excerptLength' => 25,
			'pagiPageLimit' => 5,
			'enablePostTitle' => true,
			'pagiLoadmoreEnableIconAfter' => false,
			'enablePostImage' => true,
			'titleTag' => 'h3',
			'pagiItemSize' => 'medium',
			'orderBy' => 'date',
			'pagiType' => 'none',
			'enableButton' => false,
			'pagiNextEnableIcon' => false,
			'pagiLoadmoreEnableIconBefore' => false,
			'enablePostAuthor' => true,
			'order' => 'desc',
			'termType' => [],
			'enablePostDate' => true,
			'pagiShorten' => false,
			'enablePostComment' => true,
			'filterRootText' => __('All', 'gutengeek'),
			'numberOfPosts' => 6,
			'pagiNextLabel' => __('Next', 'gutengeek'),
			'pagiLoadmoreWidth' => 'auto',
			'filterRoot' => true,
			'newTab' => false,
			'pagiPrevEnableIcon' => false,
			'pagiLoadmoreSize' => 'medium',
			'buttonSize' => 'small',
			'buttonStyle' => 'primary',
			'filterAjax' => false,
			'attrs' => [
				'asdas' => 1
			]
		], $attributes);
		if ( empty( $attributes['blockId'] ) ) {
			return null;
		}
		$query = [];
		if ( $attributes['enableFilter'] && ! $attributes['filterAjax'] && $attributes['termType'] ) {
			foreach ( $attributes['termType'] as $term ) {
				$attributes['categories'] = $term;
				$new_query['query'] = gtg_build_query( $attributes, 'carousel' );
				$new_query['term'] = $term;
				$query[] = $new_query;
			}

			if ( isset( $_GET['term'] ) && $_GET['term'] !== '' ) {
				$attributes['categories'] = sanitize_text_field( $_GET['term'] );
			}
		} else {
			if ( isset( $_GET['term'] ) && $_GET['term'] !== '' ) {
				$attributes['categories'] = sanitize_text_field( $_GET['term'] );
			}

			$new_query['query'] = gtg_build_query( $attributes, 'carousel' );
			$new_query['term'] = '';
			$query[] = $new_query;
		}

		$gutengeek_post_settings['carousel'][ $attributes['blockId'] ] = $attributes;

		ob_start();
		$this->output( $attributes, $query, 'carousel' );

		return ob_get_clean();
	}

}
