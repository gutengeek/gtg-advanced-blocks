<?php

defined( 'ABSPATH' ) || exit();

$icon = isset($attributes['connectorIcon']) ? $attributes['connectorIcon'] : ['lib' => 'fontawesome', 'value' => 'fab fa fa-bell'];

if (! $icon) {
	return;
}

?>

<div class="gutengeek-timeline-marker gutengeek-timeline-out-view-icon" >
	<span class="gutengeek-timeline-icon-new gutengeek-timeline-out-view-icon">
		<?php printf('%s', gutengeek_render_svg_html( $icon )) ?>
	</span>
</div>
