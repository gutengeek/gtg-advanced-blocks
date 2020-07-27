<?php

defined( 'ABSPATH' ) || exit();

if ( empty( $attributes['enablePostExcerpt'] ) || !$attributes['enablePostExcerpt'] ) {
	return;
}

$length = isset( $attributes['excerptLength'] ) ? $attributes['excerptLength'] : 25;

$excerpt = wp_trim_words( get_the_excerpt(), $length );

if ( !$excerpt ) {
	$excerpt = null;
}

$excerpt = apply_filters( "gutengeek_single_post_excerpt_{$attributes['post_type']}", $excerpt, get_the_ID(), $attributes );

do_action( "gutengeek_single_post_before_excerpt_{$attributes['post_type']}", get_the_ID(), $attributes );

?>
	<div class="gutengeek-post__excerpt">
		<?php echo $excerpt; ?>
	</div>

<?php
do_action( "gutengeek_single_post_after_excerpt_{$attributes['post_type']}", get_the_ID(), $attributes );
