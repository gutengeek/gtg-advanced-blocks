<?php

namespace Gtg_Advanced_Blocks\Blocks;

use Gtg_Advanced_Blocks\Abstracts\Block;
use Gtg_Advanced_Blocks\Interfaces\Block as InterfaceBlock;

defined( 'ABSPATH' ) || exit();

class Post_Timeline extends Block implements InterfaceBlock {

	/**
	 * register assets
	 *
	 * @return bool|mixed|void
	 */
	public function register_assets() {
		$suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';
	}

	/**
	 * Block id
	 *
	 * @return mixed|string
	 */
	public function set_slug() {
		return 'post-timeline';
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
	 * server rendering post timeline callback
	 *
	 * @param $attritbutes
	 * @return false|string
	 */
	public function render_callback( $attributes ) {
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
			'filterAjax' => false
		], $attributes);
		$attributes['post_type'] = 'timeline';

		$recent_posts = gtg_build_query( $attributes, 'timeline' );
		$post_tm_class = gtg_timeline_get_classes( $attributes );
		$blockId = 'gutengeek-block-' . $attributes['blockId'];

		if ( ! empty( $attributes['enableLink'] ) && $attributes['enableLink'] ) {
			$post_tm_class .= ' gutengeek_timeline__cta-enable';
		}

		$template_args = [
			'attributes' => $attributes,
			'posts' => $recent_posts,
			'post_tm_class' => $post_tm_class,
			'blockId' => $blockId,
			'shapeTop' => ! empty( $attributes['shapeTop'] ) ? $attributes['shapeTop'] : [],
			'shapeBottom' => ! empty( $attributes['shapeBottom'] ) ? $attributes['shapeBottom'] : [],
			'background' => ! empty( $attributes['blockBg'] ) ? $attributes['blockBg'] : [],
			'overlay' => ! empty( $attributes['blockOverlayBg'] ) ? $attributes['blockOverlayBg'] : []
		];
		ob_start();
		// print template html
		echo gtg_get_template_html( 'blocks/post-timeline.php', $template_args );
		return ob_get_clean();
	}

}
