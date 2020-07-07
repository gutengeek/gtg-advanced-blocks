<?php

defined( 'ABSPATH' ) || exit();

$layout = ! empty( $attributes['layout'] ) ? $attributes['layout'] : 'layout-1';
$style = ! empty( $attributes['style'] ) ? $attributes['style'] : 'style-1';
$imgPosition = ! empty( $attributes['imgPosition'] ) ? $attributes['imgPosition'] : '';
$contentAlignStyle3 = ! empty( $attributes['contentAlignStyle3'] ) ? $attributes['contentAlignStyle3'] : 'top';
$contentAlignStyle2 = ! empty( $attributes['contentAlignStyle2'] ) ? $attributes['contentAlignStyle2'] : 'top';
$columns = ! empty( $attributes['columns'] ) ? $attributes['columns'] : [];

$post_classes = [
	'gutengeek-grid-view',
	'gutengeek-post-' . $layout,
	'gutengeek-post-' . $style,
	$imgPosition ? 'gutengeek-post-image-' . $imgPosition : '',
	$style === 'style-3' ? ( $index === 0 ? 'large-item' : 'small-item' ) : '',
	$style === 'style-3' ? 'align-' . $contentAlignStyle3 : '',
	$style === 'style-2' ? 'align-' . $contentAlignStyle2 : '',
	'grid-' . ( ! empty( $columns['desktop'] ) ? $columns['desktop'] : 3 ),
	'grid-sm-' . ( ! empty( $columns['tablet'] ) ? $columns['tablet'] : 2 ),
	'grid-xs-' . ( ! empty( $columns['mobile'] ) ? $columns['mobile'] : 2 )
];
?>
<article <?php post_class( implode( ' ', $post_classes ) ); ?>>

	<?php do_action( "gutengeek_post_before_inner_wrap_{$attributes['post_type']}", get_the_ID(), $attributes ); ?>

	<div class="gutengeek-grid-post-inner">
		<?php

		/**
		 * post thumbnail
		 */
		gtg_get_template( 'blocks/post/thumbnail.php', [ 'attributes' => $attributes ] );

		?>

		<div class="gutengeek-post-side">
			<div class="gutengeek-post__text">
				<?php
					if ( isset($attributes['enableCategories'], $attributes['categoriesPosition']) && $attributes['enableCategories'] && $attributes['categoriesPosition'] === 'above_title' ) : ?>

						<div class="gutengeek-post-meta">
							<?php
							/**
							 * post title
							 */
							gtg_get_template( 'blocks/post/category.php', [ 'attributes' => $attributes ] );
							?>
						</div>
					<?php endif;
					/**
					 * post title
					 */
					gtg_get_template( 'blocks/post/title.php', [ 'attributes' => $attributes ] );

					/**
					 * post meta
					 */
					gtg_get_template( 'blocks/post/meta.php', [ 'attributes' => $attributes ] );

					if ( ( $layout === 'layout-4' && $index === 0 ) || $layout !== 'layout-4' ) {
						/**
						 * post excerpt
						 */
						gtg_get_template( 'blocks/post/excerpt.php', [ 'attributes' => $attributes ] );
					}

					/**
					 * post button
					 */
					gtg_get_template( 'blocks/post/button.php', [ 'attributes' => $attributes ] );
				?>
			</div>
		</div>
	</div>

	<?php do_action( "gutengeek_post_after_inner_wrap_{$attributes['post_type']}", get_the_ID(), $attributes ); ?>
</article>
