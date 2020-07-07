<?php

namespace GutenGeek\Components;

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

define( 'GUTENGEEK_COMPONENTS_VERSION', '1.0.0' );
define( 'PLUGIN_DIR', plugin_dir_path( dirname( dirname( dirname( __FILE__ ) ) ) ) );
define( 'PLUGIN_URL', plugins_url( '/vendor/gutengeek/components/', PLUGIN_DIR ) );

add_action( 'admin_enqueue_scripts', 'GutenGeek\Components\gutengeek_components_register_scripts', -1 );
add_action( 'enqueue_block_editor_assets', 'GutenGeek\Components\gutengeek_components_register_scripts', -1 );
if ( ! function_exists( 'gutengeek_components_register_scripts' ) ) {
	function gutengeek_components_register_scripts() {
		// register
		wp_register_script( 'gutengeek-advanced-components', PLUGIN_URL . 'build/index.js', [ 'wp-edit-post', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor', 'wp-api-fetch', 'wp-compose', 'wp-data' ], GUTENGEEK_COMPONENTS_VERSION, true );
		wp_register_style( 'gutengeek-advanced-components-style', PLUGIN_URL . 'build/index.css', [], GUTENGEEK_COMPONENTS_VERSION );
	}
}

// enqueue block editor components
add_action( 'enqueue_block_editor_assets', 'GutenGeek\Components\gutengeek_components_enqueue_scripts', 1 );

if ( ! function_exists( 'gutengeek_components_enqueue_scripts' ) ) {

	/**
	 * register block editor assets
	 */
	function gutengeek_components_enqueue_scripts() {
		// enqueue
		wp_enqueue_script( 'gutengeek-advanced-components' );
		wp_enqueue_style( 'gutengeek-advanced-components-style' );
	}

}
