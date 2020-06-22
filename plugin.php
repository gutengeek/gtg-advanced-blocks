<?php
/**
 * Plugin Name: GTG Advanced Blocks
 * Plugin URI: https://gutengeek.com/
 * Description: Advanced gutenberg blocks, designs everything you need for gutenberg editor
 * Author: GutenGeek
 * Author URI: https://gutengeek.com/contact
 * Version: 1.0.0
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package gutengeek
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit();

define( 'GTG_AB_FILE', __FILE__ );
define( 'GTG_AB_ROOT', dirname( GTG_AB_FILE ) );
define( 'GTG_AB_YOUTUBE_DEMO_URL', 'https://www.youtube.com/embed/UkCBMzwYAkM' );
define( 'GTG_AB_DOCUMENT_URL', 'https://docs.gutengeek.com' );
define( 'GTG_AB_HOME_URL', 'https://gutengeek.com' );
// require autoload
require_once GTG_AB_ROOT . '/vendor/autoload.php';

/**
 * Initialize Plugin.
 */
if ( ! function_exists( 'gtg_advanced_blocks' ) ) {

	function gtg_advanced_blocks() {
		return Gtg_Advanced_Blocks\Plugin::instance();
	}
}

$GLOBALS['gtg_advanced_blocks'] = gtg_advanced_blocks();
