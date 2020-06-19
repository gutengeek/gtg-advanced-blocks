<?php
/**
 * A template partial to output pagination.
 */

defined( 'ABSPATH' ) || exit();

$page_limit = $query->max_num_pages;
if ( isset($attributes['pagiPageLimit']) && '' !== $attributes['pagiPageLimit'] ) {
	$page_limit = min( $attributes['pagiPageLimit'], $page_limit );
}

if ( 2 > $page_limit ) {
	return;
}

$has_numbers   = isset($attributes['pagiType']) && in_array( $attributes['pagiType'], [ 'numbers', 'numbers_and_prev_next' ] );
$has_prev_next = isset($attributes['pagiType']) && in_array( $attributes['pagiType'], [ 'prev_next', 'numbers_and_prev_next' ] );

$links = [];
$paged = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );

if ( $has_numbers ) {
	$links = paginate_links( [
		'type'               => 'array',
		'current'            => $paged,
		'total'              => $page_limit,
		'prev_next'          => false,
		'show_all'           => empty($attributes['pagiShorten']) || ! $attributes['pagiShorten'],
		'before_page_number' => '<span class="gutengeek-screen-only">' . __( 'Page', 'gutengeek' ) . '</span>',
	] );
}

$prev_next = [];

$link_template     = '<a class="page-numbers %s" href="%s">%s</a>';
$disabled_template = '<span class="page-numbers %s">%s</span>';

if ( $paged > 1 ) {
	$next_page = intval( $paged ) - 1;
	if ( $next_page < 1 ) {
		$next_page = 1;
	}

	$prev_next['prev'] = sprintf( $link_template, 'prev', get_pagenum_link( $next_page ), 'Previous' );
} else {
	$prev_next['prev'] = sprintf( $disabled_template, 'prev', 'Previous' );
}

$next_page = intval( $paged ) + 1;

if ( $next_page <= $page_limit ) {
	$prev_next['next'] = sprintf( $link_template, 'next', get_pagenum_link( $next_page ), 'Next' );
} else {
	$prev_next['next'] = sprintf( $disabled_template, 'next', 'Next' );
}

if ( $has_prev_next ) {
	array_unshift( $links, $prev_next['prev'] );
	$links[] = $prev_next['next'];
}

?>
<div class="gutengeek-post__pagination">
	<nav class="gutengeek-pagination" role="navigation" aria-label="<?php esc_attr_e( 'Pagination', 'gutengeek' ); ?>">
		<?php echo implode( PHP_EOL, $links ); ?>
	</nav>
</div>
