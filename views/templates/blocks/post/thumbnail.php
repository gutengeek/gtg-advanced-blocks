<?php
if ( empty($attributes['enablePostImage']) || !$attributes['enablePostImage'] ) {
	return;
}

if ( !get_the_post_thumbnail_url() ) {
	return;
}

$target = ! empty($attributes['newTab']) && $attributes['newTab'] ? '_blank' : '_self';
$permalink = apply_filters( "gutengeek_single_post_link_{$attributes['post_type']}", get_the_permalink(), get_the_ID(), $attributes );

do_action( "gutengeek_single_post_before_featured_image_{$attributes['post_type']}", get_the_ID(), $attributes );

?>

	<div class='gutengeek-post__image thumbnail'>
		<a href="<?php echo esc_url( $permalink ) ?>" target="<?php echo esc_attr( $target ); ?>" rel="bookmark noopener noreferrer">
			<?php echo wp_get_attachment_image( get_post_thumbnail_id(), $attributes['imgSize'] ); ?>
		</a>
	</div>

<?php

do_action( "gutengeek_single_post_after_featured_image_{$attributes['post_type']}", get_the_ID(), $attributes );
