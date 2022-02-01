=== WordPress Menu Caching ===
Contributors: theogk
Plugin Name: WordPress Menu Caching
Tags: wordpress menu, caching, menu cache, menu caching, speed up menu
Author: Theo Gkitsos
Author link: https://www.dicha.gr
Version: 1.0
Stable tag: 1.0
Requires at least: 5.3
Tested up to: 5.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Caches WordPress menus to improve page loading time.

== Description ==

We all know that database calls are the main performance bottleneck in WordPress. What most people don't know though, is how "expensive" in terms of performance the WordPress menus are.

This plugin will cache the menu HTML and show the cached version to your visitors, saving your database from far too many unnecessary calls.

Menu data are scattered across six(!) different database tables. When a user visits a page, an odyssey throughout the database begins.
In 'wp_terms', 'wp_term_taxonomy' and 'wp_options' tables we'll find menu ID, slug and theme location. Then 'wp_posts' and 'wp_postmeta' to find menu's nav items and their metas.
In the metas, we will find its targeted object, so let's pay 'wp_terms' or 'wp_posts' a visit again to find the menu item's target and 'wp_termmeta' to find its metas.

These are a lot of tables and even more database calls! When all rewuired data is collected, the menu HTML is created and it is shown to the user.

The same process is repeated for every menu on the page. Desktop menu, mobile menu, some menus on the footer, a menu for the customer account on the header's right next to the cart icon...

Imagine all these menus, some of them with decades of menu items, loading in every page for every visitor on your site.

Menu changes are very rare and this makes them an easy target for caching. Caching the generated menu HTML will prevent all these unnecessary database calls and greatly improve page loading times.


= Full features list =
- Caches all WordPress menus to improve page loading time.
- Enable/disable caching per menu.
- Clear all menu cache with a button on the settings page.
- Every time you edit a menu, its cache is automatically purged.
- All cached data are automatically purged every 10 hours to keep everything fresh, like nonces etc.

= How it works =

adaadw

= Test results - How effective is it anyway? =

== Screenshots ==

1. Plugin settings.
2. Speed improvement results.

== Frequently Asked Questions ==

 = Does it benefit my site if I use a page caching plugin? =
Yes.

 = How can I clean the menus' cache? =
Yes.

 = What about menus with links which contain nonces or any really special custom menu items? =
Yes.

 = Where are the cached data saved? =
Yes.

 = Why cache data are refreshing every 10 hours? Can I change that? =
Yes.

== Installation ==

1. Download the plugin from [Official WP Plugin Repository](https://wordpress.org/plugins/wordpress-menu-caching/).
2. Upload Plugin from your WP Dashboard ( Plugins>Add New>Upload Plugin ) the wordpress-menu-caching.zip file.
3. Activate the plugin through the 'Plugins' menu in WordPress Dashboard.
4. Plugin's settings are located inside Tools > Menu Caching.

== Changelog ==

= 1.0.0 =
* Initial Release
