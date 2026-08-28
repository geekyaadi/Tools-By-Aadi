<?php
/**
 * Codes & ads.txt Manager for Soniji Auto Blogging
 * Injects Custom Header (<head>) and Footer (</body>) scripts and manages ads.txt dynamically and physically.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class SAB_Codes_Manager {

    public static function init() {
        add_action( 'init',      [ __CLASS__, 'serve_ads_txt_dynamically' ] );
        add_action( 'admin_post_sab_save_custom_codes', [ __CLASS__, 'handle_save_custom_codes' ] );
    }

    public static function serve_ads_txt_dynamically() {
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $request_uri = isset( $_SERVER['REQUEST_URI'] ) ? strtok( wp_unslash( $_SERVER['REQUEST_URI'] ), '?' ) : '';
        if ( empty( $request_uri ) ) return;

        $path = trim( $request_uri, '/' );
        if ( strtolower( $path ) === 'ads.txt' ) {
            $content = get_option( 'sab_ads_txt_content', '' );
            header( 'Content-Type: text/plain; charset=utf-8' );
            header( 'X-Robots-Tag: noindex, follow', true );
            echo esc_html( $content );
            exit;
        }
    }

    public static function sync_physical_ads_txt( $content ) {
        // phpcs:ignore PluginCheck.CodeAnalysis.WriteFile.ABSPATHDetected, WordPress.WP.AlternativeFunctions.file_system_operations_is_writable
        $file_path = ABSPATH . 'ads.txt';
        if ( wp_is_writable( ABSPATH ) || ( file_exists( $file_path ) && wp_is_writable( $file_path ) ) ) {
            // phpcs:ignore PluginCheck.CodeAnalysis.WriteFile.ABSPATHDetected, WordPress.WP.AlternativeFunctions.file_put_contents_file_put_contents
            @file_put_contents( $file_path, $content );
        }
    }

    public static function handle_save_custom_codes() {
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
        check_admin_referer( 'sab_codes_nonce' );

        $ads_txt     = isset( $_POST['sab_ads_txt_content'] ) ? sanitize_textarea_field( wp_unslash( $_POST['sab_ads_txt_content'] ) ) : '';

        update_option( 'sab_ads_txt_content', $ads_txt );

        self::sync_physical_ads_txt( $ads_txt );

        wp_safe_redirect( admin_url( 'admin.php?page=sab-codes&updated=true' ) );
        exit;
    }
}
