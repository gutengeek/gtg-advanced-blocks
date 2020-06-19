<?php

defined( 'ABSPATH' ) || exit();

global $post;

do_action( "gutengeek_single_post_before_meta_{$attributes['post_type']}", get_the_ID(), $attributes );
?>

<div class="gutengeek-post-meta<?php echo esc_attr( ! isset($attributes['enableMetaIcon']) || ! $attributes['enableMetaIcon'] ? ' icon-hidden' : '' ) ?>">

	<?php if ( isset($attributes['enableCategories'], $attributes['categoriesPosition']) && $attributes['enableCategories'] && $attributes['categoriesPosition'] === '' ) : ?>
		<?php
			/**
			 * post title
			 */
			gutengeek_get_template( 'blocks/post/category.php', [ 'attributes' => $attributes ] );
		?>
	<?php endif; ?>
	<?php if ( ! empty($attributes['enablePostAuthor']) && $attributes['enablePostAuthor'] ) { ?>
		<span class="gutengeek-post__author entry-meta">
			<?php if ( ! empty($attributes['enableMetaIcon']) && $attributes['enableMetaIcon'] ) : ?>
				<span class="dashicons-admin-users dashicons"></span>
			<?php else : ?>
				<span class="posted-by"><?php _e( 'By', 'gutengeek' ) ?></span>
			<?php endif ?>
			<?php the_author_posts_link(); ?>
		</span>
	<?php }
	if ( ! empty($attributes['enablePostDate']) && $attributes['enablePostDate'] ) { ?>
		<time datetime="<?php echo esc_attr( get_the_date( 'c', $post->ID ) ); ?>" class="gutengeek-post__date entry-meta">
			<?php if ( ! empty($attributes['enableMetaIcon']) && $attributes['enableMetaIcon'] ) : ?>
				<span class="dashicons-calendar dashicons"></span>
			<?php endif ?>
			<?php echo esc_html( get_the_date( '', $post->ID ) ); ?>
		</time>
	<?php }
	if ( ! empty($attributes['enablePostComment']) && $attributes['enablePostComment'] ) { ?>
		<span class="gutengeek-post__comment entry-meta">
			<?php if ( ! empty($attributes['enableMetaIcon']) && $attributes['enableMetaIcon'] ) : ?>
				<span class="dashicons-admin-comments dashicons"></span>
			<?php else : ?>
				<?php printf( _n( '%d Comment', '%d Comments', get_comments_number(), 'gutengeek' ), get_comments_number() ) ?>
			<?php endif ?>
		</span>
	<?php } ?>
</div>

<?php do_action( "gutengeek_single_post_after_meta_{$attributes['post_type']}", get_the_ID(), $attributes );
