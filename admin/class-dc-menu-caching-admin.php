<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.dicha.gr
 * @since      1.0.0
 *
 * @package    Dc_Menu_Caching
 * @subpackage Dc_Menu_Caching/admin
 */

class Dc_Menu_Caching_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    /**
     * Register submenu page under Tools.
     *
     * @since    1.0.0
     */
    public function dc_menu_caching_create_menu() {

        add_submenu_page(
            'tools.php',
            esc_html__( 'Menu Caching', 'dc-menu-caching' ),
            esc_html_x( 'Menu Caching', 'dc-menu-caching' ),
            'manage_options',
            'dc-menu-caching',
            array( $this, 'dc_menu_caching_plugin_settings' )
        );
    }

    /**
     * Creates main settings page.
     *
     * @since    1.0.0
     */
    function dc_menu_caching_plugin_settings() {
        if ( ! current_user_can( 'manage_options' ) ) return;

        $all_menus  = get_terms( 'nav_menu' );
        $menus_data = array_combine( wp_list_pluck( $all_menus, 'slug' ), wp_list_pluck( $all_menus, 'name' ) );
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <span><?php esc_html_e( 'Tools and info about menu caching.', 'dc-menu-caching' ); ?></span>
        </div>
        <div class="clear"></div>
        <div class="render-settings">
            <?php do_action( 'dc_menu_caching_plugin_settings' ); ?>
        </div>
        <div class="wrap">
            <h3><?php esc_html_e( 'Purge All Menus\' Cache', 'dc-menu-caching' ); ?></h3>
            <p>
                <button type="button" class="button" id="dc_menu_caching_purge_all"><?php esc_html_e( 'Purge Cache', 'dc-menu-caching' ); ?></button>
            </p>
        </div>
        <div class="clear"></div>
        <hr>
        <div class="wrap">
            <h3><?php esc_html_e( 'Enable/Disable Caching per Menu', 'dc-menu-caching' ); ?></h3>
            <p>
                <?php esc_html_e( 'Select the menus you want to enable caching. Caching is enabled by default for all menus.', 'dc-menu-caching' ); ?>
            </p>
            <div class="dc-mc-enable-wrapper">
                <?php foreach ( $menus_data as $menu_slug => $menu_name ) : ?>
                <div class="dc-mc-enable-menu">
                    <div class="dc-mc-enable-menu-name"><?php echo esc_html( $menu_name ); ?></div>
                    <div class="dc-mc-enable-menu-state-toggle">
                        <label class="switch">
                            <input type="checkbox" data-menu-slug="<?php echo esc_attr( $menu_slug ); ?>">
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
                <?php endforeach; ?>
                <div class="dc-mc-enable-submit">
                    <button type="button" class="button button-primary" id="dc_mc_enable_save"><?php esc_html_e( 'Save Settings', 'dc-menu-caching' ); ?></button>
                </div>
            </div>
        </div>
        <?php
    }


    /**
     * Saves the HTML content for the nav menu in a transient.
     * Also saves the transient hash to an index table for easy purging.
     *
     * @param string $nav_menu The HTML content for the navigation menu.
     * @param stdClass $args An object containing wp_nav_menu() arguments.
     * @return string The HTML content for the navigation menu.
     *
     * @since    1.0.0
     */
    function dc_save_menu_html( $nav_menu, $args ) {

        if ( !is_object( $args ) ) return $nav_menu;

        $menu_slug = $this->dc_get_menu_slug( $args );

        if ( $this->dc_is_menu_caching_disabled( $menu_slug ) ) return $nav_menu;

        $theme_location     = $args->theme_location;
        $menu_classes       = (string) $args->menu_class;
        $container_classes  = $args->container_class;
        $user_roles         = $this->dc_get_current_user_roles();
        $menu_hash          = md5( $menu_slug . $theme_location . $container_classes . $menu_classes . $user_roles );


//    another way to create the menu hash - using all $args for extra data or use extra data for user caching etc
//    global $wp_query; $menu_signature = md5( wp_json_encode( $args ) . $wp_query->query_vars_hash );

        if ( !empty( $menu_slug ) ) {

            if ( empty( get_transient( 'dc_menu_html_' . $menu_hash ) ) ) {

                set_transient( 'dc_menu_html_' . $menu_hash, $nav_menu, 10*HOUR_IN_SECONDS );

                $menu_html_index     = get_option( 'dc_menu_html_index', [] );
                $current_menu_hashes = !empty( $menu_html_index[ $menu_slug ] ) ? $menu_html_index[ $menu_slug ] : [];

                if ( !in_array( $menu_hash, $current_menu_hashes ) ) {
                    $current_menu_hashes[] = $menu_hash;
                    $menu_html_index[ $menu_slug ] = $current_menu_hashes;
                    update_option( 'dc_menu_html_index', $menu_html_index );
                }
            }
        }

        return $nav_menu;
    }


    /**
     * Returns the menu's HTML from cache, if exists.
     * Short-circuits the menu generation process.
     *
     * @param null $default Null is returned by default so nothing happens.
     * @param stdClass $args An object containing wp_nav_menu() arguments.
     * @return mixed|null Menu HTML if cache is found, else null to continue.
     *
     * @since    1.0.0
     */
    function dc_show_cached_menu_html( $default, $args ) {

        $menu_slug          = $this->dc_get_menu_slug( $args );
        $theme_location     = $args->theme_location;
        $container_classes  = $args->container_class;
        $menu_classes       = $args->menu_class;
        $user_roles         = $this->dc_get_current_user_roles();
        $menu_hash          = md5( $menu_slug . $theme_location . $container_classes . $menu_classes . $user_roles );

        $menu_cached_html   = get_transient( 'dc_menu_html_' . $menu_hash );

        return !empty( $menu_cached_html ) ? $menu_cached_html : null;
    }


    /**
     * Purges the menu cache after saving a menu.
     * Fires after a navigation menu has been successfully updated.
     *
     * @param int $menu_id The ID of the updated menu.
     *
     * @since    1.0.0
     */
    function dc_purge_updated_menu_transient( $menu_id ) {

        $nav_obj   = wp_get_nav_menu_object( $menu_id );
        $menu_slug = is_a( $nav_obj, 'WP_Term' ) ? $nav_obj->slug : '';

        $this->dc_purge_menu_html_transients( $menu_slug );
    }


    /**
     * Finds the menu WP_Term object and return its slug.
     * Code taken from WP Core: wp_nav_menu().
     *
     * @param stdClass $args Array of wp_nav_menu() arguments.
     * @return  string|null The menu slug.
     *
     * @since    1.0.0
     */
    function dc_get_menu_slug( $args ) {

        // Get the nav menu based on the requested menu.
        $menu = wp_get_nav_menu_object( $args->menu );

        // Get the nav menu based on the theme_location.
        $locations = get_nav_menu_locations();
        if ( ! $menu && $args->theme_location && $locations && isset( $locations[ $args->theme_location ] ) ) {
            $menu = wp_get_nav_menu_object( $locations[ $args->theme_location ] );
        }

        // Get the first menu that has items if we still can't find a menu.
        if ( ! $menu && ! $args->theme_location ) {
            $menus = wp_get_nav_menus();
            foreach ( $menus as $menu_maybe ) {
                $menu_items = wp_get_nav_menu_items( $menu_maybe->term_id, array( 'update_post_term_cache' => false ) );
                if ( $menu_items ) {
                    $menu = $menu_maybe;
                    break;
                }
            }
        }

        return is_a( $menu, 'WP_Term' ) ? $menu->slug : null;
    }


    /**
     * Gets current user's roles.
     *
     * @return string Returns user's roles concatenated in a string, or 'incognito' for non-logged-in users.
     *
     * @since    1.0.0
     */
    function dc_get_current_user_roles() {
        return is_user_logged_in() ? implode( '_', (array) wp_get_current_user()->roles ) : 'incognito';
    }


    /**
     * Finds whether a menu should be cached or not.
     *
     * @param string $menu_slug The menu slug.
     * @return bool Returns true if caching is disabled.
     */
    function dc_is_menu_caching_disabled( $menu_slug ) {

        $nocache_menus = get_option( 'dc_mc_nocache_menus', [] );

        return in_array( $menu_slug, $nocache_menus );
    }


    /**
     * Purges all or selected transients and empties the cache index array.
     *
     * @param string $slug_to_clean The menu slug to clean its transients. If none provided, then all transients will be cleared.
     *
     * @since    1.0.0
     */
    public function dc_purge_menu_html_transients( $slug_to_clean = '' ) {

        $menu_html_index = get_option( 'dc_menu_html_index', [] );

        if ( !empty( $menu_html_index ) ) {
            foreach ( $menu_html_index  as $menu_slug => $menu_hashes ) {

                if ( !empty( $slug_to_clean ) && $slug_to_clean !== $menu_slug ) continue;

                if ( !empty( $menu_hashes ) ) {
                    foreach ( $menu_hashes as $key => $menu_hash ) {
                        delete_transient( 'dc_menu_html_' . $menu_hash );
                        unset( $menu_html_index[ $menu_slug ][ $key ] );
                    }
                }
            }

            update_option( 'dc_menu_html_index', $menu_html_index );
        }
    }


    /**
     * Purges all menus' cache from the settings page button.
     * Called via AJAX.
     *
     * @since 1.0.0
     */
    function dc_purge_all_menus_settings_button() {

        if ( !isset( $_POST['nonce_ajax'] ) || ! wp_verify_nonce( $_POST['nonce_ajax'], 'dc-ajax-menu-caching-nonce' ) ) {
            wp_die( 'Unauthorized request. Go away!');
        }

        $this->dc_purge_menu_html_transients();

        wp_send_json_success();
    }


    /**
     * Saves the menus that have caching disabled.
     * These menu slugs are saved in the 'dc_mc_nocache_menus' option.
     * Called via AJAX.
     *
     * @since 1.0.0
     */
    function dc_save_nocache_menus() {

        if ( !isset( $_POST['nonce_ajax'] ) || ! wp_verify_nonce( $_POST['nonce_ajax'], 'dc-ajax-menu-caching-nonce' ) ) {
            wp_die( 'Unauthorized request. Go away!');
        }

        $nocache_menus = !empty( $_POST['nocache_menus'] ) ? array_map( 'esc_attr', $_POST['nocache_menus'] ) : [];

        update_option( 'dc_mc_nocache_menus', $nocache_menus, true );

        wp_send_json_success();
    }


    /**
     * Set custom links in plugins list page.
     *
     * @since    1.0.0
     *
     * @param   array $actions
     * @param   string $plugin_file
     * @return  array    $actions
     */
    public function dc_action_links( $actions, $plugin_file ) {

        $get_plugin_file = $this->plugin_name . '/' . $this->plugin_name . '.php';

        if ( $plugin_file === $get_plugin_file ) {
            $settings  = [
                'settings' => '<a href="' . esc_url( get_admin_url( null, 'tools.php?page=dc-menu-caching' ) ) . '">' . esc_html__( 'Settings', 'dc-menu-caching' ) . '</a>',
            ];

            $actions = array_merge( $settings, $actions );
        }

        return $actions;
    }


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
     *
	 */
	public function enqueue_styles( $hook ) {

        if ( 'tools_page_dc-menu-caching' === $hook ) {
            wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/dc-menu-caching-admin.css', array(), $this->version );
        }
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts( $hook ) {

        if ( 'tools_page_dc-menu-caching' === $hook ) {

            $ajax_data = [
                'ajaxurl'       => admin_url( 'admin-ajax.php' ),
                'nonce'         => wp_create_nonce( 'dc-ajax-menu-caching-nonce' ),
                'message'       => esc_html__( 'Menus cache purged successfully!', 'dc-menu-caching' ),
                'nocache_menus' => get_option( 'dc_mc_nocache_menus' ),
            ];

            wp_register_script( 'dc-menu-caching', plugin_dir_url( __FILE__ ) . 'js/dc-menu-caching-admin.js', array( 'jquery' ), $this->version, true );
            wp_localize_script( 'dc-menu-caching', 'ajax_data', $ajax_data );
            wp_enqueue_script( 'dc-menu-caching' );
        }
	}
}
