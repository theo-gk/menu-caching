<?php
/**
 *
 * @link              https://www.dicha.gr
 * @since             1.0.0
 * @package           Dc_Menu_Caching
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress Menu Caching
 * Plugin URI:        https://www.dicha.gr
 * Description:       Caches WordPress menus to improve loading time.
 * Version:           1.0.0
 * Author:            Theo Gkitsos
 * Author URI:        https://www.dicha.gr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       dc-menu-caching
 * Domain Path:       /languages
 * Requires at least: 5.3
 * Tested up to:      5.9
 * Stable tag:        1.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'DC_MENU_CACHING_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-dc-menu-caching-activator.php
 */
function activate_dc_menu_caching() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-dc-menu-caching-activator.php';
    Dc_Menu_Caching_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-dc-menu-caching-deactivator.php
 */
function deactivate_dc_menu_caching() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-dc-menu-caching-deactivator.php';
    Dc_Menu_Caching_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_dc_menu_caching' );
register_deactivation_hook( __FILE__, 'deactivate_dc_menu_caching' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-dc-menu-caching.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_dc_menu_caching() {

	$plugin = new Dc_Menu_Caching();
	$plugin->run();

}
run_dc_menu_caching();
