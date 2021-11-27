<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.dicha.gr
 * @since      1.0.0
 *
 * @package    Dc_Menu_Caching
 * @subpackage Dc_Menu_Caching/includes
 */

class Dc_Menu_Caching_Deactivator {

	/**
	 * Deletes all transients and options on deactivation.
     *
	 * @since    1.0.0
	 */
	public static function deactivate() {

        $menu_html_index = get_option( 'dc_menu_html_index', [] );

        if ( !empty( $menu_html_index ) ) {
            foreach ( $menu_html_index  as $menu_slug => $menu_hashes ) {
                if ( !empty( $menu_hashes ) ) {
                    foreach ( $menu_hashes  as $key => $menu_hash ) {
                        delete_transient(  'dc_menu_html_' . $menu_hash );
                    }
                }
            }

            delete_option( 'dc_menu_html_index' );
        }

        delete_option( 'dc_mc_nocache_menus' );
	}
}
