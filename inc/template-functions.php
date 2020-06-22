<?php

defined( 'ABSPATH' ) || exit();

add_filter( 'body_class', 'gutengeek_block_add_body_class' );
if ( ! function_exists( 'gtg_block_add_body_class' ) ) {
	function gtg_block_add_body_class($classes) {
		$classes[] = 'gutengeek-block-template';
		return $classes;
	}
}

/**
 * Get template part (for templates like the shop-loop).
 *
 * @param mixed $slug Template slug.
 * @param string $name Template name (default: '').
 */
function gtg_get_template_part( $slug, $name = '' ) {
	$cache_key = sanitize_key( implode( '-', [ 'template-part', $slug, $name ] ) );
	$template = (string)wp_cache_get( $cache_key, 'gutengeek' );

	if ( !$template ) {
		if ( $name ) {
			$template = locate_template(
				[
					"{$slug}-{$name}.php",
					gtg_advanced_blocks()->template_path() . "{$slug}-{$name}.php",
				]
			);

			if ( !$template ) {
				$fallback = gtg_advanced_blocks()->plugin_path() . "/views/templates/{$slug}-{$name}.php";
				$template = file_exists( $fallback ) ? $fallback : '';
			}
		}

		if ( !$template ) {
			// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/gutengeek/slug.php.
			$template = locate_template(
				[
					"{$slug}.php",
					gtg_advanced_blocks()->template_path() . "{$slug}.php",
				]
			);
		}

		wp_cache_set( $cache_key, $template, 'gutengeek' );
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'gutengeek_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @param string $template_name Template name.
 * @param array $args Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path Default path. (default: '').
 */
function gtg_get_template( $template_name, $args = [], $template_path = '', $default_path = '' ) {
	$cache_key = sanitize_key( implode( '-', [ 'template', $template_name, $template_path, $default_path ] ) );
	$template = (string)wp_cache_get( $cache_key, 'gutengeek' );

	if ( !$template ) {
		$template = gtg_locate_template( $template_name, $template_path, $default_path );
		wp_cache_set( $cache_key, $template, 'gutengeek' );
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$filter_template = apply_filters( 'gutengeek_get_template', $template, $template_name, $args, $template_path, $default_path );

	if ( $filter_template !== $template ) {
		if ( !file_exists( $filter_template ) ) {
			/* translators: %s template */
			_doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'gutengeek' ), '<code>' . $template . '</code>' ), '2.1' );
			return;
		}
		$template = $filter_template;
	}

	$action_args = [
		'template_name' => $template_name,
		'template_path' => $template_path,
		'located' => $template,
		'args' => $args,
	];

	if ( !empty( $args ) && is_array( $args ) ) {
		if ( isset( $args['action_args'] ) ) {
			_doing_it_wrong(
				__FUNCTION__,
				__( 'action_args should not be overwritten when calling gutengeek_get_template.', 'gutengeek' ),
				'1.0.0'
			);
			unset( $args['action_args'] );
		}
		extract( $args );
	}

	do_action( 'gutengeek_before_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );

	include $action_args['located'];

	do_action( 'gutengeek_after_template_part', $action_args['template_name'], $action_args['template_path'], $action_args['located'], $action_args['args'] );
}

/**
 * Like gutengeek_get_template, but returns the HTML instead of outputting.
 *
 * @see gutengeek_get_template
 * @since 2.5.0
 * @param string $template_name Template name.
 * @param array $args Arguments. (default: array).
 * @param string $template_path Template path. (default: '').
 * @param string $default_path Default path. (default: '').
 *
 * @return string
 */
function gtg_get_template_html( $template_name, $args = [], $template_path = '', $default_path = '' ) {
	ob_start();
	gtg_get_template( $template_name, $args, $template_path, $default_path );
	return ob_get_clean();
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 * yourtheme/$template_path/$template_name
 * yourtheme/$template_name
 * $default_path/$template_name
 *
 * @param string $template_name Template name.
 * @param string $template_path Template path. (default: '').
 * @param string $default_path Default path. (default: '').
 * @return string
 */
function gtg_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( !$template_path ) {
		$template_path = gtg_advanced_blocks()->template_path();
	}

	if ( !$default_path ) {
		$default_path = gtg_advanced_blocks()->plugin_path() . '/views/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		[
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		]
	);

	// Get default template/.
	if ( !$template ) {
		$template = $default_path . $template_name;
	}

	// Return what we found.
	return apply_filters( 'gutengeek_locate_template', $template, $template_name, $template_path );
}
