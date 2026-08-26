<?php
// phpcs:ignoreFile
/**
 * GitHub Automatic Updater for Tools By Aadi
 * Allows direct update from the WordPress plugins screen by pulling releases from GitHub.
 *
 * Author: Anand Soni
 * GitHub: https://github.com/geekyaadi/Tools-By-Aadi
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class TBA_Updater {

    private $plugin_file;
    private $username;
    private $repository;
    private $slug;
    private $github_response;

    public function __construct( $plugin_file ) {
        $this->plugin_file = $plugin_file;
        $this->username    = 'geekyaadi';
        $this->repository  = 'tools-by-aadi';
        $this->slug        = plugin_basename( $plugin_file ); // e.g. 'tools-by-aadi/tools-by-aadi.php'

        // Check for updates
        add_filter( 'pre_set_site_trans_' . 'update_plugins', [ $this, 'check_update' ] );
        add_filter( 'pre_set_site_transient_' . 'update_plugins', [ $this, 'check_update' ] );
        
        // Show plugin info modal
        add_filter( 'plugins_' . 'api', [ $this, 'plugin_popup' ], 20, 3 );
        
        // Rename folder post-install if zipball name matches GitHub format
        add_filter( 'upgrader_post_install', [ $this, 'post_install' ], 10, 3 );

        // Automatic background updates support
        add_filter( 'auto_update_' . 'plugin', [ $this, 'should_auto_update' ], 10, 2 );
    }

    /**
     * Fetch latest release info from GitHub API
     */
    private function get_github_release_info() {
        if ( ! empty( $this->github_response ) ) {
            return $this->github_response;
        }

        $transient_key = 'tba_github_release_info';

        // Force check cache bypass if checking updates in WP Admin or update-core page
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        if ( isset( $_GET['force-check'] ) || ( is_admin() && isset( $GLOBALS['pagenow'] ) && ( $GLOBALS['pagenow'] === 'update-core.php' || $GLOBALS['pagenow'] === 'plugins.php' ) ) ) {
            delete_transient( $transient_key );
            delete_site_transient( 'update_' . 'plugins' );
        } else {
            $cached = get_transient( $transient_key );
            if ( $cached !== false ) {
                $this->github_response = $cached;
                return $cached;
            }
        }

        $url = "https://api.github.com/repos/{$this->username}/{$this->repository}/releases/latest";
        
        $response = wp_remote_get( $url, [
            'headers' => [
                'User-Agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url(),
            ],
            'timeout' => 10,
        ] );

        if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
            return false;
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );
        if ( empty( $data ) ) {
            return false;
        }

        // Cache for 6 hours (lightweight)
        set_transient( $transient_key, $data, 6 * HOUR_IN_SECONDS );
        $this->github_response = $data;
        return $data;
    }

    /**
     * Inject update payload into WordPress update_plugins transient
     */
    public function check_update( $transient ) {
        if ( empty( $transient ) || ! is_object( $transient ) ) {
            return $transient;
        }

        $release = $this->get_github_release_info();
        if ( ! $release || empty( $release['tag_name'] ) ) {
            return $transient;
        }

        $new_version     = ltrim( $release['tag_name'], 'v' );
        $current_version = TBA_VERSION;

        $logo_url = 'https://raw.githubusercontent.com/geekyaadi/tools-by-aadi/main/admin/tools-by-aadi-by-aadi.png';
        $obj = new stdClass();
        $obj->slug        = 'tools-by-aadi';
        $obj->plugin      = $this->slug;
        $obj->new_version = $new_version;
        $obj->url         = $release['html_url'];
        $obj->icons       = [
            'default' => $logo_url,
            '1x'      => $logo_url,
            '2x'      => $logo_url,
        ];

        if ( version_compare( $new_version, $current_version, '>' ) ) {
            $package = $release['zipball_url'];
            if ( ! empty( $release['assets'] ) ) {
                foreach ( $release['assets'] as $asset ) {
                    if ( isset( $asset['name'] ) && strpos( $asset['name'], '.zip' ) !== false ) {
                        $package = $asset['browser_download_url'];
                        break;
                    }
                }
            }
            $obj->package = $package;
            $transient->response[ $this->slug ] = $obj;
        } else {
            $obj->package = '';
            $transient->no_update[ $this->slug ] = $obj;
        }

        return $transient;
    }

    /**
     * Show release info modal in Plugins popup
     */
    public function plugin_popup( $result, $action, $args ) {
        if ( $action !== 'plugin_information' ) {
            return $result;
        }

        if ( ! isset( $args->slug ) || $args->slug !== 'tools-by-aadi' ) {
            return $result;
        }

        $release = $this->get_github_release_info();
        if ( ! $release ) {
            return $result;
        }

        $new_version = ltrim( $release['tag_name'], 'v' );

        $result = new stdClass();
        $result->name           = 'Tools By Aadi';
        $result->slug           = 'tools-by-aadi';
        $result->version        = $new_version;
        $result->author         = '<a href="https://github.com/geekyaadi" target="_blank">Anand Soni</a>';
        $result->homepage       = 'https://github.com/geekyaadi/Tools-By-Aadi';
        $download_url = $release['zipball_url'];
        if ( ! empty( $release['assets'] ) ) {
            foreach ( $release['assets'] as $asset ) {
                if ( isset( $asset['name'] ) && strpos( $asset['name'], '.zip' ) !== false ) {
                    $download_url = $asset['browser_download_url'];
                    break;
                }
            }
        }
        $result->download_link  = $download_url;
        $result->last_updated   = $release['published_at'];
        
        $logo_url = 'https://raw.githubusercontent.com/geekyaadi/tools-by-aadi/main/admin/tools-by-aadi-by-aadi.png';
        $result->icons = [
            '1x' => $logo_url,
            '2x' => $logo_url,
        ];
        $result->banners = [
            'low'  => $logo_url,
            'high' => $logo_url,
        ];

        $result->sections = [
            'description' => 'Auto-generate SEO blog posts using Google Gemini API — with multi-key rotation, scheduling, queue, history log, and full quality controls.',
            'changelog'   => wp_kses_post( wpautop( $release['body'] ) ),
        ];

        return $result;
    }

    /**
     * Rename folder back to 'tools-by-aadi' after zipball extraction
     */
    public function post_install( $response, $hook_extra, $result ) {
        if ( isset( $hook_extra['plugin'] ) && $hook_extra['plugin'] === $this->slug ) {
            global $wp_filesystem;
            
            // Ensure WP_Filesystem is initialized safely
            if ( empty( $wp_filesystem ) ) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                WP_Filesystem();
            }
            
            $correct_destination = WP_PLUGIN_DIR . '/' . $this->repository;
            
            if ( isset( $result['destination'] ) && $result['destination'] !== $correct_destination ) {
                if ( $wp_filesystem && $wp_filesystem->move( $result['destination'], $correct_destination ) ) {
                    $result['destination'] = $correct_destination;
                }
            }
        }
        return $response;
    }

    /**
     * Instruct WordPress automatic background updater to update this plugin
     * Respects WordPress native settings option.
     */
    public function should_auto_update( $update, $item ) {
        if ( is_object( $item ) && isset( $item->plugin ) && $item->plugin === $this->slug ) {
            $auto_updates = (array) get_site_option( 'auto_update_' . 'plugins', [] );
            if ( in_array( $this->slug, $auto_updates, true ) ) {
                return true;
            }
            return false;
        }
        return $update;
    }
}
