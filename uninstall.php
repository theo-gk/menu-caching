<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @package    Dc_Menu_Caching
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if ( !class_exists( 'Dc_Menu_Caching_Admin' ) ) exit;

$plugin_admin = new Dc_Menu_Caching_Admin( 'dc-menu-caching', '1.0' );
$plugin_admin->dc_purge_menu_html_transients();

delete_option( 'dc_menu_html_index' );
delete_option( 'dc_menu_nonces_index' );
