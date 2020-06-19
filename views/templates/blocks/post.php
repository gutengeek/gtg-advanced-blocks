<?php

defined( 'ABSPATH' ) || exit();

$attrs_string = [];
if ( ! empty( $attrs ) ) {
	foreach ( $attrs as $key => $attr ) {
		$attrs_string[] = 'data-' . $key . '="' . _wp_specialchars( $attr, ENT_QUOTES, 'UTF-8', true ) . '"';
	}
}

$attrs_string = implode( ' ', $attrs_string );

if ( ! empty($attributes['enableFilter']) && $attributes['enableFilter'] && ! $attributes['filterAjax'] ) {
	$outerwrap[] = 'gutengeek-post-filter-disable-ajax';
}

if ( ! empty($attributes['hasPagination']) && $attributes['hasPagination'] ) {
	$outerwrap[] = 'gutengeek-post-has-pagination';
}

?>

<div id="<?php echo esc_attr( $blockId ); ?>" class="<?php echo implode( ' ', ! empty( $outerwrap ) ? $outerwrap : [] ); ?>">

	<?php if ( isset($shapeTop) && ! empty( $shapeTop['shape'] )) : ?>
		<div class="gutengeek-shape-divider gutengeek-shape-top"><?php printf( '%s', gutengeek_render_shape_background( $shapeTop['shape'] ) ) ?></div>
	<?php endif; ?>

	<?php if ( isset( $overlay, $overlay['source'] ) ): ?>
		<div class="gutengeek-block__overlay"></div>
	<?php endif; ?>

	<div class="gutengeek-block-inner">

		<div class="gutengeek-block-content">
			<!-- Block Heading -->
			<?php gutengeek_get_template( 'blocks/block-heading.php', [ 'attributes' => $attributes ] ); ?>
			<!-- End Block Heading -->

			<div class="gutengeek-block-wrapper gutengeek-block-post-wrapper">

				<div class="gutengeek-post-container">
					<div class="gutengeek-post-inner">

						<?php
						/**
						 * Filter.
						 */
						if ( ! empty($attributes['enableFilter']) && $attributes['enableFilter'] ) {
							gutengeek_get_template( 'blocks/post/filter.php', [ 'attributes' => $attributes ] );
						}
						?>

						<div class="gutengeek-post__container-wrap">
							<?php foreach ( $query as $index => $query_item ) : ?>
								<?php
								$container_classes = [ 'gutengeek-post__container' ];

								if ( ( isset( $_GET['term'] ) && $_GET['term'] === $query_item['term'] ) || ( ! isset( $_GET['term'] ) && ( 0 === $index ) ) ) {
									$container_classes[] = 'gutengeek-active';
								}
								?>
								<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $container_classes ) ) ); ?>">
									<div class="<?php echo esc_attr( implode( ' ', ! empty( $wrap ) ? $wrap : [] ) ); ?>" <?php echo sprintf( '%s', $attrs_string ) ?>>

										<?php if ( isset( $query_item['query'] ) && $query_item['query'] ) :
											$post_query = $query_item['query'];
											while ( $post_query->have_posts() ) :
												$post_query->the_post(); ?>

												<?php do_action( "gutengeek_post_before_article_{$attributes['post_type']}", get_the_ID(), $attributes ); ?>

												<?php gutengeek_get_template( 'blocks/loop-post-grid.php', [ 'attributes' => $attributes ] ); ?>

												<?php do_action( "gutengeek_post_after_article_{$attributes['post_type']}", get_the_ID(), $attributes ); ?>

											<?php
											endwhile;
										endif;
										?>
									</div>

									<?php
									/**
									 * Navigation.
									 */
									if ( ! empty($attributes['hasPagination']) && $attributes['hasPagination'] ) {
										gutengeek_get_template( 'blocks/post/navigation.php', [ 'attributes' => $attributes, 'query' => $post_query ] );
									}
									?>
									<?php wp_reset_postdata(); ?>
								</div>
							<?php endforeach; ?>
						</div>

						<input type="hidden" class="gutengeek-post__attributes" name="attributes" data-attributes='<?php echo json_encode( $attributes ); ?>'>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php if ( isset($shapeBottom) && ! empty( $shapeBottom['shape'] )) : ?>
		<div class="gutengeek-shape-divider gutengeek-shape-bottom"><?php printf( '%s', gutengeek_render_shape_background( $shapeBottom['shape'] ) ) ?></div>
	<?php endif; ?>
</div>
