<?php

defined( 'ABSPATH' ) || exit();
$align = 'align' . (! empty($attributes['align']) ? $attributes['align'] : 'wide');
?>

<div class="gutengeek-block-container gutengeek-block gutengeek-block-timeline gutengeek-timeline-outer-wrap <?php echo esc_attr( $align ); ?>" id="<?php echo esc_attr( $blockId ); ?>">
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

			<div class="gutengeek-block-wrapper">

				<div class="gutengeek-timeline-container">
					<div class="gutengeek-timeline-inner">

						<!-- Timeline -->
						<div class="gutengeek-timeline-content-wrap <?php echo esc_attr( $post_tm_class ); ?>">
							<div class="gutengeek-timeline-wrapper">
								<div class="gutengeek-timeline-main">
									<?php
									if ( empty( $posts ) ) {
										_e( 'No posts found', 'gutengeek' );
									} else { ?>
										<div class="gutengeek-timeline-days">
											<?php
											$index = 0;
											while ( $posts->have_posts() ) {
												$posts->the_post();
												global $post;
											?>
												<article class="gutengeek-timeline-field gutengeek-timeline-item" key="<?php echo esc_attr( $index ); ?>">
													<div class="<?php echo esc_attr( gutengeek_timeline_get_align_classes( $attributes, $index ) ); ?>">
														<?php
															gutengeek_get_template( '/blocks/post-timeline/marker.php', [ 'attributes' => $attributes ] );
														?>
														<div class="<?php echo esc_attr( gutengeek_timeline_get_day_align_classes( $attributes, $index ) ); ?>">
															<div class="gutengeek-timeline-milestone">
																<div class="gutengeek-timeline-milestone-inner">
																	<?php
																		gutengeek_get_template( '/blocks/post-timeline/thumbnail.php', [ 'attributes' => $attributes ] )
																	?>

																	<div class="gutengeek-content">
																		<div class="gutengeek-timeline-date-hide gutengeek-timeline-date-inner">
																			<?php
																				gutengeek_get_template( '/blocks/post-timeline/date.php', [ 'attributes' => $attributes, 'class' => 'gutengeek-timeline-inner-date-new' ] );
																			?>
																		</div>
																		<?php
																		/**
																		 * post timeline title
																		 */
																		gutengeek_get_template( '/blocks/post-timeline/title.php', [ 'attributes' => $attributes ] );

																		/**
																		 * post timeline author
																		 */
																		gutengeek_get_template( '/blocks/post-timeline/author.php', [ 'attributes' => $attributes, 'post' => $post ] );

																		/**
																		 * post timeline excerpt
																		 */
																		gutengeek_get_template( '/blocks/post-timeline/excerpt.php', [ 'attributes' => $attributes, 'post' => $post ] );

																		/**
																		 * post timeline excerpt
																		 */
																		gutengeek_get_template( '/blocks/post-timeline/readmore.php', [ 'attributes' => $attributes, 'post' => $post ] );
																		?>
																		<div class="gutengeek-timeline-arrow"></div>
																	</div>
																</div>
															</div>
														</div>
														<?php if ( ! isset($attributes['layout']) || 'center' === $attributes['layout'] ) { ?>
															<div class="gutengeek-timeline-date">
																<?php
																	gutengeek_get_template( '/blocks/post-timeline/date.php', [ 'attributes' => $attributes, 'class' => 'gutengeek-timeline-date' ] );
																?>
															</div>
														<?php } ?>
													</div>
												</article>
												<?php $index++; } wp_reset_postdata(); ?>
										</div>
									<?php } ?>
									<div class="gutengeek-timeline-line">
										<div class="gutengeek-timeline-line__inner"></div>
									</div>
								</div>
							</div>
						</div>
						<!-- End Timeline -->
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if ( isset($shapeBottom) && ! empty( $shapeBottom['shape'] )) : ?>
		<div class="gutengeek-shape-divider gutengeek-shape-bottom"><?php printf( '%s', gutengeek_render_shape_background( $shapeBottom['shape'] ) ) ?></div>
	<?php endif; ?>
</div>

