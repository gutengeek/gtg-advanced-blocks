<?php

defined( 'ABSPATH' ) || exit();

?>
<article <?php post_class( 'gutengeek-grid-view' ); ?>>

	<?php do_action( "gutengeek_post_before_inner_wrap_{$attributes['post_type']}", get_the_ID(), $attributes ); ?>

	<div class="gutengeek-grid-post-inner">
		<?php

		gtg_get_template( 'blocks/post/complete-box.php', [ 'attributes' => $attributes ] );

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
						gtg_get_template( 'blocks/post/category.php', [ 'attributes' => $attributes ] ); ?>

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

				/**
				 * post excerpt
				 */
				gtg_get_template( 'blocks/post/excerpt.php', [ 'attributes' => $attributes ] );

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
