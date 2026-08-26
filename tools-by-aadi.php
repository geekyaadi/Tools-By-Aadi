<?php
/**
 * Plugin Name:       Tools By Aadi
 * Plugin URI:        https://github.com/geekyaadi/Tools-By-Aadi
 * Description:       Auto-generate SEO blog posts using Google Gemini API - with multi-key rotation, scheduling, queue, history log, and full quality controls.
 * Version:           1.0.0
 * Author:            Aadi
 * Author URI:        https://github.com/geekyaadi
 * Contributors:      Anand Soni
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tools-by-aadi
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Constants
define( 'TBA_VERSION',     '1.0.0' );
define( 'TBA_PLUGIN_FILE', __FILE__ );
define( 'TBA_PLUGIN_DIR',  plugin_dir_path( __FILE__ ) );
define( 'TBA_PLUGIN_URL',  plugin_dir_url( __FILE__ ) );

// Autoload includes
$includes = [
    'includes/class-tba-key-manager.php',
    'includes/class-tba-rate-limits.php',
    'includes/class-tba-gemini.php',
    'includes/class-tba-post-creator.php',
    'includes/class-tba-settings.php',
    'includes/class-tba-scheduler.php',
    'includes/class-tba-queue.php',
    'includes/class-tba-history.php',
    'includes/class-tba-duplicate-check.php',
    'includes/class-tba-ajax.php',
    'includes/class-tba-updater.php',
    'includes/class-tba-text-to-image.php',
    'includes/class-tba-gsc-helper.php',
    'includes/class-tba-speed-optimizer.php',
    'includes/class-tba-sitemap.php',
    'includes/class-tba-pages-generator.php',
    'includes/class-tba-cookie-consent.php',
    'includes/class-tba-outbound-linker.php',
    'includes/class-tba-redirects.php',
    'includes/class-tba-retrofitter.php',
    'includes/class-tba-codes-manager.php',
    'includes/class-tba-date-randomizer.php',
];

foreach ( $includes as $file ) {
    require_once TBA_PLUGIN_DIR . $file;
}

// Comprehensive Auto-Cache Purge Function
function tba_purge_all_caches() {
    delete_transient( 'tba_github_release_info' );
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
register_activation_hook( __FILE__, 'tba_clear_transient_on_activate' );
function tba_clear_transient_on_activate() {
    tba_purge_all_caches();
    TBA_History::create_tables();
    TBA_Queue::create_tables();
}
register_deactivation_hook( __FILE__, [ 'TBA_Scheduler', 'deactivate' ] );

// Automatic Cache Purge on Plugin Upgrade
add_action( 'upgrader_process_complete', 'tba_on_plugin_update', 10, 2 );
function tba_on_plugin_update( $upgrader_object, $options ) {
    if ( isset( $options['action'] ) && $options['action'] === 'update' && isset( $options['type'] ) && $options['type'] === 'plugin' ) {
        tba_purge_all_caches();
    }
}

// Bootstrap
add_action( 'plugins_loaded', 'tba_init' );

function tba_init() {
    TBA_Queue::create_tables();
    TBA_Settings::init();
    TBA_Ajax::init();
    TBA_Scheduler::init();
    TBA_Speed_Optimizer::init();
    TBA_Sitemap::init();
    TBA_Cookie_Consent::init();
    TBA_Redirects::init();
    TBA_Retrofitter::init();
    TBA_Codes_Manager::init();
    TBA_Date_Randomizer::init();
    
    // Enable automated updates from GitHub Release API
    if ( is_admin() ) {
        new TBA_Updater( TBA_PLUGIN_FILE );

        // Version update check to clear transients and page caches
        $db_ver = get_option( 'tba_db_version' );
        if ( $db_ver !== TBA_VERSION ) {
            tba_purge_all_caches();
            update_option( 'tba_db_version', TBA_VERSION );
        }
    }
}

// Serve dynamic IndexNow key verification file
add_action( 'init', 'tba_indexnow_serve_key' );
function tba_indexnow_serve_key() {
    $key = get_option( 'tba_indexnow_key' );
    if ( empty( $key ) ) {
        $key = md5( get_bloginfo( 'url' ) . time() );
        update_option( 'tba_indexnow_key', $key );
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
add_action( 'transition_post_status', 'tba_indexnow_submit_on_publish', 10, 3 );
function tba_indexnow_submit_on_publish( $new_status, $old_status, $post ) {
    if ( $post->post_type !== 'post' || $new_status !== 'publish' || $old_status === 'publish' ) {
        return;
    }

    if ( ! get_option( 'tba_enable_indexnow', 0 ) ) {
        return;
    }

    $key = get_option( 'tba_indexnow_key' );
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
add_action( 'transition_post_status', 'tba_gsc_auto_ping_on_publish', 10, 3 );
function tba_gsc_auto_ping_on_publish( $new_status, $old_status, $post ) {
    if ( $new_status !== 'publish' || $old_status === 'publish' ) {
        return;
    }
    if ( ! get_option( 'tba_enable_gsc_auto_ping', 0 ) ) {
        return;
    }
    $json_creds = get_option( 'tba_gsc_json', '' );
    if ( empty( $json_creds ) ) {
        return;
    }
    $url = get_permalink( $post->ID );
    if ( $url ) {
        TBA_GSC_Helper::submit_url( $url, 'URL_UPDATED' );
        update_post_meta( $post->ID, '_tba_gsc_last_ping', current_time( 'mysql' ) );
    }
}
