<?php

defined( 'ABSPATH' ) || exit();

/**
 * Load more
 */
if ( ! empty($attributes['pagiType']) && 'loadmore' === $attributes['pagiType'] ) {
	gutengeek_get_template( 'blocks/post/loadmore.php', [ 'attributes' => $attributes ] );
}

/**
 * Pagination.
 */
if ( ! empty($attributes['pagiType']) && in_array( $attributes['pagiType'], [ 'numbers', 'prev_next', 'numbers_and_prev_next' ] ) ) {
	gutengeek_get_template( 'blocks/post/pagination.php', [ 'attributes' => $attributes, 'query' => $query ] );
}
