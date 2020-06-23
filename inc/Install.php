<?php

namespace Gtg_Advanced_Blocks;

class Install {

	/**
	 * constructor
	 */
	public function construct() {

	}

	/**
	 * install activation hook callback
	 */
	public static function install() {
		// create remote user
		self::create_remote_server_user();
		// init default options
		self::init_options();
		// create user roles
		self::create_roles();
		// create folder to storage static css file
		self::create_folders();
		// create tables
		self::create_tables();
	}

	/**
	 * uninstall activation hook callback
	 */
	public static function uninstall() {
		update_option( '_gutengeek_do_redirect', false );
	}

	/**
	 * init options on install action
	 */
	public static function init_options() {
		update_option( '_gutengeek_do_redirect', true );
		if ( ! get_option( '_gutengeek_global_settings' ) ) {
			add_option( '_gtg_global_settings', apply_filters( 'gutengeek_init_default_global_settings', [
				'css_method' => 'file'
			] ) );
		}
	}

	/**
	 * create user remote server
	 */
	public static function create_remote_server_user() {

	}

	/**
	 * create plugin roles
	 */
	public static function create_roles() {

	}

	/**
	 * create folders ...
	 */
	public static function create_folders() {
		WP_Filesystem();
		global $wp_filesystem;
		if ( !$wp_filesystem->is_dir( GTG_AB_STATIC_CSS_PATH ) || !$wp_filesystem->exists( GTG_AB_STATIC_CSS_PATH ) ) {
			$wp_filesystem->mkdir( GTG_AB_STATIC_CSS_PATH );
		}
	}

	/**
	 * create tables
	 */
	public static function create_tables() {
		try {
			global $wpdb;
			$wpdb->hide_errors();
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
			dbDelta( self::get_schema() );
		} catch ( \Exception $e ) {
			// do nothing
		}
	}

	public static function get_schema() {
		global $wpdb;
		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			$collate = $wpdb->get_charset_collate();
		}

		$tables = "
CREATE TABLE {$wpdb->prefix}gtg_block_fonts (
	font_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	font_name char(32) NOT NULL,
	font_user_id BIGINT UNSIGNED NOT NULL,
	font_status text NOT NULL,
	PRIMARY KEY  (font_id),
	UNIQUE KEY font_name (font_name)
) $collate;
CREATE TABLE {$wpdb->prefix}gtg_block_font_items (
	font_item_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	font_id BIGINT UNSIGNED NOT NULL,
	font_item_weight char(10) NOT NULL,
	font_item_style char(10) NOT NULL,
	PRIMARY KEY  (font_item_id),
	KEY font_id (font_id)
) $collate;
CREATE TABLE {$wpdb->prefix}gtg_block_font_item_attachments (
	font_item_attach_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	font_item_attach_type char(32) NOT NULL,
	font_attach_id BIGINT UNSIGNED NOT NULL,
	font_item_id BIGINT UNSIGNED NOT NULL,
	PRIMARY KEY  (font_item_attach_id),
	KEY font_item_id (font_item_id),
	KEY font_attach_id (font_item_id, font_attach_id)
) $collate;
CREATE TABLE {$wpdb->prefix}gtg_block_icons (
	icon_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
	icon_name char(32) NOT NULL,
	icon_dir_name varchar(200) NOT NULL,
	icon_user_id BIGINT UNSIGNED NOT NULL,
	icon_status text NOT NULL,
	PRIMARY KEY  (icon_id),
	UNIQUE KEY icon_dir_name (icon_dir_name(191))
) $collate;
		";

		return $tables;
	}

}
