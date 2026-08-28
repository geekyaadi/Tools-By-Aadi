<?php
/**
 * Plugin Name:       Soniji Auto Blogging
 * Plugin URI:        https://github.com/geekyaadi/Tools-By-Aadi
 * Description:       Auto-generate SEO blog posts using Google Gemini API - with multi-key rotation, scheduling, queue, history log, and full quality controls.
 * Version:           1.0.0
 * Author:            Aadi
 * Author URI:        https://github.com/geekyaadi
 * Contributors:      Anand Soni
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       soniji-auto-blogging
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Constants
define( 'SAB_VERSION',     '1.0.0' );
define( 'SAB_PLUGIN_FILE', __FILE__ );
define( 'SAB_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'SAB_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );

// Autoload includes
$includes = [
    'includes/class-sab-key-manager.php',
    'includes/class-sab-rate-limits.php',
    'includes/class-sab-gemini.php',
    'includes/class-sab-post-creator.php',
    'includes/class-sab-settings.php',
    'includes/class-sab-scheduler.php',
    'includes/class-sab-queue.php',
    'includes/class-sab-history.php',
    'includes/class-sab-duplicate-check.php',
    'includes/class-sab-ajax.php',
    'includes/class-sab-text-to-image.php',
    'includes/class-sab-gsc-helper.php',
    'includes/class-sab-speed-optimizer.php',
    'includes/class-sab-sitemap.php',
    'includes/class-sab-pages-generator.php',
    'includes/class-sab-cookie-consent.php',
    'includes/class-sab-outbound-linker.php',
    'includes/class-sab-redirects.php',
    'includes/class-sab-retrofitter.php',
    'includes/class-sab-codes-manager.php',
    'includes/class-sab-date-randomizer.php',
];

foreach ( $includes as $file ) {
    require_once SAB_PLUGIN_DIR . $file;
}

// Comprehensive Auto-Cache Purge Function
function sab_purge_all_caches() {
    delete_site_transient( 'update_' . 'plugins' );

    // 1. Reset PHP OPcache (forces server to load updated PHP code immediately)
    if ( function_exists( 'opcache_reset' ) ) {
        @opcache_reset();
    }

    // 2. Flush WP Object Cache
    if ( function_exists( 'wp_cache_flush' ) ) {
        @wp_cache_flush();
    }

    // 3. Purge LiteSpeed Cache
    do_action( 'litespeed_purge_all' );
    if ( class_exists( 'LiteSpeed_Cache_API' ) && method_exists( 'LiteSpeed_Cache_API', 'purge_all' ) ) {
        @LiteSpeed_Cache_API::purge_all();
    }

    // 4. Purge WP Rocket
    if ( function_exists( 'rocket_clean_domain' ) ) {
        @rocket_clean_domain();
    }

    // 5. Purge WP Super Cache
    if ( function_exists( 'wp_cache_clear_cache' ) ) {
        @wp_cache_clear_cache();
    }

    // 6. Purge Autoptimize
    if ( class_exists( 'autoptimizeCache' ) && method_exists( 'autoptimizeCache', 'clearall' ) ) {
        @autoptimizeCache::clearall();
    }

    // 7. Purge W3 Total Cache
    if ( function_exists( 'w3tc_flush_all' ) ) {
        @w3tc_flush_all();
    }
}

// Activation / Deactivation
register_activation_hook( __FILE__, 'sab_clear_transient_on_activate' );
function sab_clear_transient_on_activate() {
    sab_purge_all_caches();
    SAB_History::create_tables();
    SAB_Queue::create_tables();
}
register_deactivation_hook( __FILE__, [ 'SAB_Scheduler', 'deactivate' ] );

// Automatic Cache Purge on Plugin Upgrade
add_action( 'upgrader_process_complete', 'sab_on_plugin_update', 10, 2 );
function sab_on_plugin_update( $upgrader_object, $options ) {
    if ( isset( $options['action'] ) && $options['action'] === 'update' && isset( $options['type'] ) && $options['type'] === 'plugin' ) {
        sab_purge_all_caches();
    }
}

// Bootstrap
add_action( 'plugins_loaded', 'sab_init' );

function sab_init() {
    SAB_Queue::create_tables();
    SAB_Settings::init();
    SAB_Ajax::init();
    SAB_Scheduler::init();
    SAB_Speed_Optimizer::init();
    SAB_Sitemap::init();
    SAB_Cookie_Consent::init();
    SAB_Redirects::init();
    SAB_Retrofitter::init();
    SAB_Codes_Manager::init();
    SAB_Date_Randomizer::init();
    
    if ( is_admin() ) {
        // Version update check to clear transients and page caches
        $db_ver = get_option( 'sab_db_version' );
        if ( $db_ver !== SAB_VERSION ) {
            sab_purge_all_caches();
            update_option( 'sab_db_version', SAB_VERSION );
        }
    }
}

// Serve dynamic IndexNow key verification file
add_action( 'init', 'sab_indexnow_serve_key' );
function sab_indexnow_serve_key() {
    $key = get_option( 'sab_indexnow_key' );
    if ( empty( $key ) ) {
        $key = md5( get_bloginfo( 'url' ) . time() );
        update_option( 'sab_indexnow_key', $key );
    }

    $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
    $path = trim( wp_parse_url( $request_uri, PHP_URL_PATH ), '/' );

    if ( $path === $key . '.txt' ) {
        header( 'Content-Type: text/plain; charset=utf-8' );
        echo esc_html( $key );
        exit;
    }
}

// Hook to transition post status and ping IndexNow
add_action( 'transition_post_status', 'sab_indexnow_submit_on_publish', 10, 3 );
function sab_indexnow_submit_on_publish( $new_status, $old_status, $post ) {
    if ( $post->post_type !== 'post' || $new_status !== 'publish' || $old_status === 'publish' ) {
        return;
    }

    if ( ! get_option( 'sab_enable_indexnow', 0 ) ) {
        return;
    }

    $key = get_option( 'sab_indexnow_key' );
    if ( empty( $key ) ) {
        return;
    }

    $permalink = get_permalink( $post->ID );
    if ( ! $permalink ) {
        return;
    }

    $host = wp_parse_url( home_url(), PHP_URL_HOST );
    $key_location = home_url( '/' . $key . '.txt' );

    $body = [
        'host'        => $host,
        'key'         => $key,
        'keyLocation' => $key_location,
        'urlList'     => [ $permalink ],
    ];

    wp_remote_post( 'https://api.indexnow.org/indexnow', [
        'headers'   => [ 'Content-Type' => 'application/json; charset=utf-8' ],
        'body'      => json_encode( $body ),
        'timeout'   => 15,
        'blocking'  => false,
    ] );
}

// Google Indexing API auto-ping on publish
add_action( 'transition_post_status', 'sab_gsc_auto_ping_on_publish', 10, 3 );
function sab_gsc_auto_ping_on_publish( $new_status, $old_status, $post ) {
    if ( $new_status !== 'publish' || $old_status === 'publish' ) {
        return;
    }
    if ( ! get_option( 'sab_enable_gsc_auto_ping', 0 ) ) {
        return;
    }
    $json_creds = get_option( 'sab_gsc_json', '' );
    if ( empty( $json_creds ) ) {
        return;
    }
    $url = get_permalink( $post->ID );
    if ( $url ) {
        SAB_GSC_Helper::submit_url( $url, 'URL_UPDATED' );
        update_post_meta( $post->ID, '_sab_gsc_last_ping', current_time( 'mysql' ) );
    }
}
