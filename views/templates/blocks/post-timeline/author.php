<?php

defined( 'ABSPATH' ) || exit();

$output = '';
do_action( "gutengeek_single_post_before_meta_{$attributes['post_type']}", get_the_ID(), $attributes );
if ( isset( $attributes['enableAuthor'] ) && $attributes['enableAuthor'] ) {
	echo sprintf(
		'<div class="gutengeek-timeline-author"><span class="dashicons-admin-users dashicons"></span><a class="gutengeek-timeline-author-link" href="%2$s">%1$s</a></div>',
		esc_html( get_the_author_meta( 'display_name', $author ) ),
		esc_html( get_author_posts_url( $author ) )
	);
}

do_action( "gutengeek_single_post_after_meta_{$attributes['post_type']}", get_the_ID(), $attributes );
