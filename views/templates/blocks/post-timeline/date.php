<?php

defined( 'ABSPATH' ) || exit();

global $post;
$post_id = $post->ID;
$enable = isset( $attributes['enableDate'] ) ? $attributes['enableDate'] : true;

if ( $enable ) {
	echo sprintf(
		'<div datetime="%1$s" class="%2$s">%3$s</div>',
		esc_attr( get_the_date( 'c', $post_id ) ),
		$class,
		esc_html( get_the_date( '', $post_id ) )
	);
}
