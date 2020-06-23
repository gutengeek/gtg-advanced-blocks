<?php

namespace Gtg_Advanced_Blocks\Abstracts;

defined( 'ABSPATH' ) || exit();

class Post_Block extends Block {

	public function set_slug() {
		return '';
	}

	public function set_attributes() {
		return [];
	}

	/**
	 * render posts html
	 *
	 * @param array $attributes
	 * @param $query
	 * @param string $layout
	 */
	public function gtg_get_post_html( $attributes = [], $query, $layout = 'grid' ) {
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
			'enableMetaIcon' => true,
			'enableCategories' => true,
			'categoriesPosition' => 'above_title',
			'taxonomyType' => 'category',
			'equalHeight' => true
		], $attributes);
		$attributes['post_type'] = $layout;

		$columns = ! empty( $attributes['columns'] ) ? $attributes['columns'] : [];
		$columnsDesktop = isset( $columns['desktop'] ) ? absint( $columns['desktop'] ) : 3;
		$columnsTablet = isset( $columns['tablet'] ) ? absint( $columns['tablet'] ) : 2;
		$columnsMobile = isset( $columns['mobile'] ) ? absint( $columns['mobile'] ) : 1;
		$wrap = [
			'gutengeek-grid-items gutengeek-grid-col-' . (isset( $columns['desktop'] ) ? absint( $columns['desktop'] ) : 3),
			'gutengeek-grid-col-tablet-' . $columnsTablet,
			'gutengeek-grid-col-mobile-' . $columnsMobile,
			'gutengeek-grid-image-position-' . (! empty( $attributes['imgPosition'] ) ? $attributes['imgPosition'] : 'top'),
		];

		$outerwrap = [
			'gutengeek-block-container',
			'gutengeek-block',
			'gutengeek-post-grid',
			!empty( $attributes['className'] ) ? $attributes['className'] : '',
			!empty( $attributes['align'] ) ? 'align' . $attributes['align'] : ''
		];

		$blockId = 'gutengeek-block-' . $attributes['blockId'];
		$attrs = [];

		switch ( $layout ) {
			case 'masonry':
				$outerwrap[] = 'gutengeek-post-grid-masonry';
				$wrap[] = 'gutengeek-masonry';
				break;

			case 'grid':
				$wrap[] = 'gutengeek-grid-template';
				if ( !empty( $attributes['layout'] ) ) {
					$wrap[] = 'gutengeek-grid-template--' . $attributes['layout'];
				}
				if ( !empty( $attributes['style'] ) ) {
					$wrap[] = !empty( $attributes['style'] ) ? 'gutengeek-grid-style--' . $attributes['style'] : '';
				}
				if ( ! empty($attributes['equalHeight']) && $attributes['equalHeight'] ) {
					$wrap[] = 'gutengeek-post__equal-height';
				}
				break;

			case 'carousel':
				$outerwrap = array_merge( $outerwrap, [ 'gutengeek-slick-carousel', 'gutengeek-slick-carousel-arrow-outside' ] );
				$wrap[] = 'gutengeek-carousel';
				if ( $attributes['equalHeight'] ) {
					$wrap[] = 'gutengeek-post__carousel_equal-height';
				}
				$settings = [
					'slidesToShow' => $columnsDesktop,
					'slidesToScroll' => !empty( $attributes['slidesToScroll'] ) ? absint( $attributes['slidesToScroll'] ) : 1,
					'autoplaySpeed' => !empty( $attributes['autoplaySpeed'] ) ? absint( $attributes['autoplaySpeed'] ) : 1,
					'autoplay' => !empty( $attributes['autoplay'] ) ? absint( $attributes['autoplay'] ) : 1,
					'infinite' => !empty( $attributes['infiniteLoop'] ) ? absint( $attributes['infiniteLoop'] ) : 2000,
					'pauseOnHover' => !empty( $attributes['pauseOnHover'] ) ? $attributes['pauseOnHover'] : true,
					'speed' => !empty( $attributes['transitionSpeed'] ) ? absint( $attributes['transitionSpeed'] ) : 20000,
					'arrows' => isset( $attributes['arrowDots'] ) && in_array( $attributes['arrowDots'], [ 'arrows', 'arrows_dots' ] ),
					'dots' => isset( $attributes['arrowDots'] ) && in_array( $attributes['arrowDots'], [ 'dots', 'arrows_dots' ] ),
					'rtl' => false,
					'draggable' => isset( $attributes['draggable'] ) ? $attributes['draggable'] : false,
					'responsive' => [
						[
							'breakpoint' => absint( gtg_get_sm_breakpoint_setting() ),
							'settings' => [
								'slidesToShow' => $columnsTablet,
								'slidesToScroll' => isset( $attributes['slidesToScroll'] ) ? $attributes['slidesToScroll'] : 1,
							]
						],
						[
							'breakpoint' => absint( gtg_get_xs_breakpoint_setting() ),
							'settings' => [
								'slidesToShow' => $columnsMobile,
								'slidesToScroll' => isset( $attributes['slidesToScroll'] ) ? $attributes['slidesToScroll'] : 1,
							]
						]
					],
					'equalHeight' => isset( $attributes['equalHeight'] ) ? $attributes['equalHeight'] : false,
				];
				$attrs['slider'] = wp_json_encode( $settings );
				break;

			default:
				break;
		}

		$template_args = [
			'blockId' => $blockId,
			'outerwrap' => $outerwrap,
			'query' => $query,
			'attributes' => $attributes,
			'wrap' => $wrap,
			'attrs' => $attrs,
			'shapeTop' => ! empty( $attributes['shapeTop'] ) ? $attributes['shapeTop'] : [],
			'shapeBottom' => ! empty( $attributes['shapeBottom'] ) ? $attributes['shapeBottom'] : [],
			'background' => ! empty( $attributes['blockBg'] ) ? $attributes['blockBg'] : [],
			'overlay' => ! empty( $attributes['blockOverlayBg'] ) ? $attributes['blockOverlayBg'] : []
		];
		// print template html
		echo gtg_get_template_html( 'blocks/post.php', $template_args );
	}

}
