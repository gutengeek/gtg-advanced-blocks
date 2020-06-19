<?php

defined( 'ABSPATH' ) || exit();

if ( ! empty($attributes['enableExcerpt']) && !$attributes['enableExcerpt'] ) {
	return;
}

$excerpt = wp_trim_words( get_the_excerpt(), (! empty($attributes['exerptLength']) ? $attributes['exerptLength'] : 20) );
if ( !$excerpt ) {
	$excerpt = null;
}

$excerpt = apply_filters( "gutengeek_single_post_excerpt_{$attributes['post_type']}", $excerpt, get_the_ID(), $attributes );
do_action( "gutengeek_single_post_before_excerpt_{$attributes['post_type']}", get_the_ID(), $attributes );
?>
	<div class="gutengeek-timeline-desc-content">
		<?php echo $excerpt; ?>
	</div>
<?php
do_action( "gutengeek_single_post_after_excerpt_{$attributes['post_type']}", get_the_ID(), $attributes );
