<?php

defined( 'ABSPATH' ) || exit();

if ( !get_the_post_thumbnail_url() ) {
	return;
}

$target = ( isset( $attributes['linkTarget'] ) && ( true == $attributes['linkTarget'] ) ) ? '_blank' : '_self';
$imgSize = ! empty($attributes['imgSize']) && $attributes['imgSize'] ? $attributes['imgSize'] : 'thumbnail';
do_action( "gutengeek_single_post_before_featured_image_{$attributes['post_type']}", get_the_ID(), $attributes );
?>
	<div class='gutengeek-timeline-image'>
		<a href="<?php echo apply_filters( "gutengeek_single_post_link_{$attributes['post_type']}", get_the_permalink(), get_the_ID(), $attributes ); ?>"
		   target="<?php echo esc_attr( $target ); ?>"
		   rel="noopener noreferrer"><?php echo wp_get_attachment_image( get_post_thumbnail_id(), $imgSize ); ?>
		</a>
	</div>
<?php
do_action( "gutengeek_single_post_after_featured_image_{$attributes['post_type']}", get_the_ID(), $attributes );
