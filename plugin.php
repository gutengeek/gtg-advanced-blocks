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

define( 'GUTENGEEK_FILE', __FILE__ );
define( 'GUTENGEEK_ROOT', dirname( GUTENGEEK_FILE ) );
define( 'GUTENGEEK_YOUTUBE_DEMO_URL', 'https://www.youtube.com/embed/UkCBMzwYAkM' );
define( 'GUTENGEEK_DOCUMENT_URL', 'https://docs.gutengeek.com' );
define( 'GUTENGEEK_HOME_URL', 'https://gutengeek.com' );
// require autoload
require_once GUTENGEEK_ROOT . '/vendor/autoload.php';

/**
 * Initialize Plugin.
 */
if ( ! function_exists( 'gutengeek' ) ) {

	function gutengeek() {
		return GutenGeek\Plugin::instance();
	}
}

$GLOBALS['gutengeek'] = gutengeek();
