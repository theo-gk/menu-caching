<?php
/**
 * Plugin Name:       Menu Caching
 * Description:       This plugin caches WordPress classic menus to improve page loading time.
 * Version:           1.1.4
 * Author:            Theo Gkitsos
 * Author URI:        https://theodorosgkitsos.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       menu-caching
 * Domain Path:       /languages
 * Requires at least: 5.3
 * Tested up to:      6.7.1
 * Requires PHP:      7.4
 * Stable tag:        1.1.4
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'WP_MENU_CACHING_VERSION', '1.1.4' );
define( 'WP_MENU_CACHING_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'WP_MENU_CACHING_BASE_FILE', 'menu-caching/menu-caching.php' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-menu-caching.php';

/**
 * Begins execution of the plugin.
 */
function run_dc_menu_caching() {
	$plugin = new Wp_Menu_Caching();
	$plugin->run();
}

run_dc_menu_caching();
