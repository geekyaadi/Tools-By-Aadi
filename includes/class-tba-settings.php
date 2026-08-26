<?php
/**
 * Admin Settings — registers all admin menu pages and handles option saving.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class TBA_Settings {

    public static function init() {
        add_action( 'admin_menu',            [ __CLASS__, 'register_menus' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
        add_action( 'admin_post_tba_save_settings', [ __CLASS__, 'save_settings' ] );
        add_action( 'admin_post_tba_add_key',        [ __CLASS__, 'handle_add_key' ] );
        add_action( 'admin_post_tba_delete_key',     [ __CLASS__, 'handle_delete_key' ] );
        add_action( 'admin_post_tba_reset_key',      [ __CLASS__, 'handle_reset_key' ] );
        add_action( 'admin_post_tba_save_schedule',  [ __CLASS__, 'save_schedule' ] );
        add_action( 'admin_post_tba_enqueue_niche',  [ __CLASS__, 'handle_enqueue_niche' ] );
        add_action( 'admin_post_tba_delete_queue',   [ __CLASS__, 'handle_delete_queue' ] );
        add_action( 'admin_post_tba_pause_queue',    [ __CLASS__, 'handle_pause_queue' ] );
        add_action( 'admin_post_tba_resume_queue',   [ __CLASS__, 'handle_resume_queue' ] );
        add_action( 'admin_post_tba_clear_queue',    [ __CLASS__, 'handle_clear_queue' ] );
        add_action( 'admin_post_tba_delete_selected_queue', [ __CLASS__, 'handle_delete_selected_queue' ] );
        add_action( 'admin_post_tba_delete_history', [ __CLASS__, 'handle_delete_history' ] );
        add_action( 'admin_post_tba_clear_history',  [ __CLASS__, 'handle_clear_history' ] );
        add_action( 'admin_post_tba_reset_settings',   [ __CLASS__, 'handle_reset_settings' ] );
        add_action( 'admin_post_tba_clear_plugin_data', [ __CLASS__, 'handle_clear_plugin_data' ] );
        add_action( 'admin_post_tba_save_speed_settings', [ __CLASS__, 'save_speed_settings' ] );
        add_action( 'admin_post_tba_purge_speed_cache',   [ __CLASS__, 'handle_purge_speed_cache' ] );
        add_action( 'admin_post_tba_save_sitemap_settings', [ __CLASS__, 'save_sitemap_settings' ] );
        add_action( 'admin_post_tba_generate_essential_pages', [ __CLASS__, 'handle_generate_essential_pages' ] );
        add_action( 'admin_post_tba_save_cookie_settings',     [ __CLASS__, 'handle_save_cookie_settings' ] );
    }

    // -------------------------------------------------------------------------
    // Admin Menus
    // -------------------------------------------------------------------------

    public static function register_menus() {
        add_menu_page(
            __( 'Tools By Aadi', 'tools-by-aadi' ),
            __( 'Tools By Aadi', 'tools-by-aadi' ),
            'manage_options',
            'tools-by-aadi',
            [ __CLASS__, 'render_dashboard_page' ],
            'dashicons-dashboard',
            25
        );

        add_submenu_page( 'tools-by-aadi', __( 'Dashboard', 'tools-by-aadi' ),      __( 'Dashboard', 'tools-by-aadi' ),      'manage_options', 'tools-by-aadi',             [ __CLASS__, 'render_dashboard_page' ] );
        add_submenu_page( 'tools-by-aadi', __( 'Generate Post', 'tools-by-aadi' ),  __( 'Generate Post', 'tools-by-aadi' ),  'manage_options', 'tba-generate',             [ __CLASS__, 'render_generate_page' ] );
        add_submenu_page( 'tools-by-aadi', __( 'Bulk Planner', 'tools-by-aadi' ),   __( 'Bulk Planner', 'tools-by-aadi' ),   'manage_options', 'tba-planner',              [ __CLASS__, 'render_planner_page' ] );
        add_submenu_page( 'tools-by-aadi', __( 'Scheduler & Queue', 'tools-by-aadi' ), __( 'Scheduler & Queue', 'tools-by-aadi' ), 'manage_options', 'tba-scheduler',      [ __CLASS__, 'render_scheduler_page' ] );
        add_submenu_page( 'tools-by-aadi', __( 'Thumbnail Manager', 'tools-by-aadi' ), __( 'Thumbnail Manager', 'tools-by-aadi' ), 'manage_options', 'tba-thumbnails',   [ __CLASS__, 'render_thumbnails_page' ] );
        add_submenu_page( 'tools-by-aadi', __( 'Tags Manager', 'tools-by-aadi' ),    __( 'Tags Manager', 'tools-by-aadi' ),    'manage_options', 'tba-tags',                 [ __CLASS__, 'render_tags_page' ] );
        add_submenu_page( 'tools-by-aadi', __( 'Bulk Translator', 'tools-by-aadi' ),  __( 'Bulk Translator', 'tools-by-aadi' ),  'manage_options', 'tba-translator',           [ __CLASS__, 'render_translator_page' ] );
        add_submenu_page( 'tools-by-aadi', __( 'Google Indexing Tool', 'tools-by-aadi' ), __( 'Google Indexing Tool', 'tools-by-aadi' ), 'manage_options', 'tba-gsc',      [ __CLASS__, 'render_gsc_page' ] );
        add_submenu_page( 'tools-by-aadi', __( 'Article Rewriter', 'tools-by-aadi' ), __( 'Article Rewriter', 'tools-by-aadi' ), 'manage_options', 'tba-rewriter',   [ __CLASS__, 'render_rewriter_page' ] );
        add_submenu_page( 'tools-by-aadi', __( 'Speed Optimizer', 'tools-by-aadi' ), __( '⚡ Speed Optimizer', 'tools-by-aadi' ), 'manage_options', 'tba-speed',      [ __CLASS__, 'render_speed_page' ] );
        add_submenu_page( 'tools-by-aadi', __( 'XML Sitemap Manager', 'tools-by-aadi' ), __( '🗺️ Sitemap Manager', 'tools-by-aadi' ), 'manage_options', 'tba-sitemap',   [ __CLASS__, 'render_sitemap_page' ] );
        add_submenu_page( 'tools-by-aadi', __( 'Pages & Cookie Consent', 'tools-by-aadi' ), __( '📄 Pages & Cookies', 'tools-by-aadi' ), 'manage_options', 'tba-pages',   [ __CLASS__, 'render_pages_generator_page' ] );
        add_submenu_page( 'tools-by-aadi', __( 'Redirect & 404 Manager', 'tools-by-aadi' ), __( '🔀 Redirect Manager', 'tools-by-aadi' ), 'manage_options', 'tba-redirects', [ __CLASS__, 'render_redirects_page' ] );
        add_submenu_page( 'tools-by-aadi', __( 'Date Randomizer', 'tools-by-aadi' ),       __( '📅 Date Randomizer', 'tools-by-aadi' ), 'manage_options', 'tba-randomizer',[ __CLASS__, 'render_randomizer_page' ] );
        add_submenu_page( 'tools-by-aadi', __( 'Codes & ads.txt', 'tools-by-aadi' ),       __( '💻 Codes & ads.txt', 'tools-by-aadi' ), 'manage_options', 'tba-codes',     [ __CLASS__, 'render_codes_page' ] );
        add_submenu_page( 'tools-by-aadi', __( 'Settings', 'tools-by-aadi' ),       __( 'Settings', 'tools-by-aadi' ),       'manage_options', 'tba-settings',             [ __CLASS__, 'render_settings_page' ] );
    }

    // -------------------------------------------------------------------------
    // Enqueue Assets
    // -------------------------------------------------------------------------

    public static function enqueue_assets( $hook ) {
        $tba_pages = [
            'toplevel_page_tools-by-aadi',
            'tools-by-aadi_page_tba-generate',
            'tools-by-aadi_page_tba-planner',
            'tools-by-aadi_page_tba-scheduler',
            'tools-by-aadi_page_tba-settings',
            'tools-by-aadi_page_tba-thumbnails',
            'tools-by-aadi_page_tba-tags',
            'tools-by-aadi_page_tba-translator',
            'tools-by-aadi_page_tba-gsc',
            'tools-by-aadi_page_tba-rewriter',
            'tools-by-aadi_page_tba-speed',
            'tools-by-aadi_page_tba-sitemap',
            'tools-by-aadi_page_tba-pages',
            'tools-by-aadi_page_tba-redirects',
            'tools-by-aadi_page_tba-randomizer',
            'tools-by-aadi_page_tba-codes',
        ];
        if ( ! in_array( $hook, $tba_pages, true ) && strpos( $hook, 'aap' ) === false ) return;

        $css_path = TBA_PLUGIN_DIR . 'admin/css/admin.css';
        $css_ver  = file_exists( $css_path ) ? filemtime( $css_path ) : TBA_VERSION;

        wp_enqueue_style(
            'tba-admin-v2',
            TBA_PLUGIN_URL . 'admin/css/admin.css',
            [],
            $css_ver
        );

        $js_path = TBA_PLUGIN_DIR . 'admin/js/admin.js';
        $js_ver  = file_exists( $js_path ) ? filemtime( $js_path ) : TBA_VERSION;

        wp_enqueue_script(
            'tba-admin-v2',
            TBA_PLUGIN_URL . 'admin/js/admin.js',
            [ 'jquery' ],
            $js_ver,
            true
        );

        wp_localize_script( 'tba-admin-v2', 'aapData', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'tba_nonce' ),
            'strings' => [
                'generating'      => __( 'Generating...', 'tools-by-aadi' ),
                'switchedKey'     => __( 'API key exhausted — switching to next key...', 'tools-by-aadi' ),
                'allExhausted'    => __( 'All API keys exhausted. Please add more keys or wait for reset.', 'tools-by-aadi' ),
                'success'         => __( 'Post created successfully!', 'tools-by-aadi' ),
                'error'           => __( 'An error occurred. Please try again.', 'tools-by-aadi' ),
                'confirmDelete'   => __( 'Are you sure you want to delete this?', 'tools-by-aadi' ),
                'duplicate'       => __( 'A similar post already exists. Do you want to continue anyway?', 'tools-by-aadi' ),
            ],
        ] );
    }

    // -------------------------------------------------------------------------
    // Page Renderers
    // -------------------------------------------------------------------------

    public static function render_generate_page() {
        require_once TBA_PLUGIN_DIR . 'admin/views/generate-page.php';
    }

    public static function render_planner_page() {
        require_once TBA_PLUGIN_DIR . 'admin/views/planner-page.php';
    }

    public static function render_tags_page() {
        require_once TBA_PLUGIN_DIR . 'admin/views/tags-page.php';
    }

    public static function render_translator_page() {
        require_once TBA_PLUGIN_DIR . 'admin/views/translator-page.php';
    }

    public static function render_gsc_page() {
        require_once TBA_PLUGIN_DIR . 'admin/views/gsc-page.php';
    }

    public static function render_rewriter_page() {
        require_once TBA_PLUGIN_DIR . 'admin/views/rewriter-page.php';
    }

    public static function render_speed_page() {
        require_once TBA_PLUGIN_DIR . 'admin/views/speed-optimizer-page.php';
    }

    public static function render_sitemap_page() {
        require_once TBA_PLUGIN_DIR . 'admin/views/sitemap-page.php';
    }

    public static function render_pages_generator_page() {
        require_once TBA_PLUGIN_DIR . 'admin/views/pages-generator-page.php';
    }

    public static function render_redirects_page() {
        require_once TBA_PLUGIN_DIR . 'admin/views/redirects-page.php';
    }

    public static function render_randomizer_page() {
        require_once TBA_PLUGIN_DIR . 'admin/views/date-randomizer-page.php';
    }

    public static function render_codes_page() {
        require_once TBA_PLUGIN_DIR . 'admin/views/codes-page.php';
    }

    public static function render_settings_page() {
        require_once TBA_PLUGIN_DIR . 'admin/views/settings-page.php';
    }

    public static function render_scheduler_page() {
        require_once TBA_PLUGIN_DIR . 'admin/views/scheduler-page.php';
    }

    public static function render_dashboard_page() {
        require_once TBA_PLUGIN_DIR . 'admin/views/dashboard-page.php';
    }

    // -------------------------------------------------------------------------
    // Essential Pages & Cookie Handlers
    // -------------------------------------------------------------------------

    public static function handle_generate_essential_pages() {
        check_admin_referer( 'tba_generate_essential_pages' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $site_name = sanitize_text_field( $_POST['tba_site_name'] ?? '' );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $site_url  = esc_url_raw( $_POST['tba_site_url'] ?? '' );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $email     = sanitize_email( $_POST['tba_contact_email'] ?? '' );

        update_option( 'tba_site_name', $site_name );
        update_option( 'tba_site_url', $site_url );
        update_option( 'tba_contact_email', $email );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $selected_pages = isset( $_POST['pages'] ) && is_array( $_POST['pages'] ) ? $_POST['pages'] : [];
        $created_count = 0;

        foreach ( $selected_pages as $page_type ) {
            $res = TBA_Pages_Generator::create_page( sanitize_text_field( $page_type ), $site_name, $site_url, $email );
            if ( ! is_wp_error( $res ) ) {
                $created_count++;
            }
        }

        wp_safe_redirect( admin_url( 'admin.php?page=tba-pages&pages_created=' . $created_count ) );
        exit;
    }

    public static function handle_save_cookie_settings() {
        check_admin_referer( 'tba_save_cookie_settings' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $enable        = isset( $_POST['tba_cookie_enable'] ) ? '1' : '0';
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $style         = sanitize_text_field( $_POST['tba_cookie_style'] ?? 'bottom_banner' );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $text          = sanitize_textarea_field( $_POST['tba_cookie_text'] ?? '' );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $btn_text      = sanitize_text_field( $_POST['tba_cookie_btn_text'] ?? 'Accept All' );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $enable_reject = isset( $_POST['tba_cookie_enable_reject'] ) ? '1' : '0';
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $reject_text   = sanitize_text_field( $_POST['tba_cookie_reject_btn_text'] ?? 'Decline Non-Essential' );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $privacy_url   = esc_url_raw( $_POST['tba_cookie_privacy_url'] ?? '' );

        update_option( 'tba_cookie_enable', $enable );
        update_option( 'tba_cookie_style', $style );
        update_option( 'tba_cookie_text', $text );
        update_option( 'tba_cookie_btn_text', $btn_text );
        update_option( 'tba_cookie_enable_reject', $enable_reject );
        update_option( 'tba_cookie_reject_btn_text', $reject_text );
        update_option( 'tba_cookie_privacy_url', $privacy_url );

        wp_safe_redirect( admin_url( 'admin.php?page=tba-pages&cookie_updated=true' ) );
        exit;
    }

    // -------------------------------------------------------------------------
    // Sitemap Manager Handlers
    // -------------------------------------------------------------------------

    public static function save_sitemap_settings() {
        check_admin_referer( 'tba_save_sitemap_settings' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $slug = isset( $_POST['tba_sitemap_slug'] ) ? sanitize_title( $_POST['tba_sitemap_slug'] ) : 'sitemap';
        if ( empty( $slug ) ) $slug = 'sitemap';
        if ( strpos( $slug, '.xml' ) === false ) $slug .= '.xml';
        update_option( 'tba_sitemap_slug', $slug );

        $fields = [
            'tba_sitemap_priority_home'    => 'sanitize_text_field',
            'tba_sitemap_changefreq_home'  => 'sanitize_text_field',
            'tba_sitemap_priority_post'    => 'sanitize_text_field',
            'tba_sitemap_changefreq_post'  => 'sanitize_text_field',
            'tba_sitemap_priority_page'    => 'sanitize_text_field',
            'tba_sitemap_changefreq_page'  => 'sanitize_text_field',
            'tba_sitemap_priority_cat'     => 'sanitize_text_field',
            'tba_sitemap_changefreq_cat'   => 'sanitize_text_field',
            'tba_sitemap_priority_tag'     => 'sanitize_text_field',
            'tba_sitemap_changefreq_tag'   => 'sanitize_text_field',
        ];

        foreach ( $fields as $opt => $sanitizer ) {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $val = isset( $_POST[ $opt ] ) ? $sanitizer( $_POST[ $opt ] ) : '';
            update_option( $opt, $val );
        }

        $checkboxes = [
            'tba_sitemap_enable_home',
            'tba_sitemap_enable_posts',
            'tba_sitemap_enable_pages',
            'tba_sitemap_enable_cats',
            'tba_sitemap_enable_tags',
            'tba_sitemap_include_images',
            'tba_sitemap_auto_ping_google',
            'tba_sitemap_auto_ping_bing',
        ];

        foreach ( $checkboxes as $cb ) {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $val = isset( $_POST[ $cb ] ) ? '1' : '0';
            update_option( $cb, $val );
        }

        // Flush rewrite rules for custom sitemap slug
        TBA_Sitemap::add_rewrite_rules();
        flush_rewrite_rules();

        if ( function_exists( 'tba_purge_all_caches' ) ) {
            tba_purge_all_caches();
        }

        wp_safe_redirect( admin_url( 'admin.php?page=tba-sitemap&updated=true' ) );
        exit;
    }

    // -------------------------------------------------------------------------
    // Speed Optimizer Handlers
    // -------------------------------------------------------------------------

    public static function save_speed_settings() {
        check_admin_referer( 'tba_save_speed_settings' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        $speed_options = [
            'tba_speed_lazy_loading',
            'tba_speed_html_minification',
            'tba_speed_webp_compression',
            'tba_speed_auto_cache_purge',
            'tba_speed_preload_assets',
            'tba_speed_defer_js',
        ];

        foreach ( $speed_options as $opt ) {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $val = isset( $_POST[ $opt ] ) ? '1' : '0';
            update_option( $opt, $val );
        }

        if ( function_exists( 'tba_purge_all_caches' ) ) {
            tba_purge_all_caches();
        }

        wp_safe_redirect( admin_url( 'admin.php?page=tba-speed&updated=true' ) );
        exit;
    }

    public static function handle_purge_speed_cache() {
        check_admin_referer( 'tba_purge_speed_cache' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        if ( function_exists( 'tba_purge_all_caches' ) ) {
            tba_purge_all_caches();
        }

        wp_safe_redirect( admin_url( 'admin.php?page=tba-speed&purged=true' ) );
        exit;
    }

    // -------------------------------------------------------------------------
    // Save Main Settings
    // -------------------------------------------------------------------------

    public static function save_settings() {
        check_admin_referer( 'tba_save_settings' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        $fields = [
            'tba_default_status'          => 'sanitize_text_field',
            'tba_auto_update_enabled'     => 'intval',
            'tba_default_author'          => 'intval',
            'tba_word_count'              => 'intval',
            'tba_tag_count'               => 'intval',
            'tba_content_tone'            => 'sanitize_text_field',
            'tba_blacklist_words'         => 'sanitize_textarea_field',
            'tba_key_reset_minutes'       => 'intval',
            'tba_review_mode'             => 'intval',
            'tba_text_model'              => 'sanitize_text_field',
            'tba_image_model'             => 'sanitize_text_field',
            'tba_active_provider'         => 'sanitize_text_field',
            'tba_openai_model'            => 'sanitize_text_field',
            'tba_enable_internal_linking' => 'intval',
            'tba_max_internal_links'      => 'intval',
            'tba_internal_link_style'     => 'sanitize_text_field',
            'tba_enable_outbound_linking' => 'intval',
            'tba_max_outbound_links'      => 'intval',
            'tba_outbound_target'         => 'sanitize_text_field',
            'tba_outbound_rel'            => 'sanitize_text_field',
            'tba_outbound_blacklist'      => 'sanitize_textarea_field',
            'tba_enable_indexnow'         => 'intval',
            'tba_enable_comments'         => 'intval',
            'tba_comments_count'          => 'intval',
            'tba_enable_text_overlay'     => 'intval',
            'tba_overlay_font_size'       => 'intval',
            'tba_overlay_color'           => 'sanitize_text_field',
            'tba_overlay_bg_color'        => 'sanitize_text_field',
            'tba_overlay_bg_opacity'      => 'intval',
            'tba_overlay_position'        => 'sanitize_text_field',
            'tba_thumb_type'              => 'sanitize_text_field',
            'tba_t2i_bg_type'             => 'sanitize_text_field',
            'tba_t2i_bg_val'              => 'sanitize_text_field',
            'tba_t2i_size'                => 'sanitize_text_field',
            'tba_enable_toc'              => 'intval',
            'tba_toc_default_state'       => 'sanitize_text_field',
            'tba_enable_faq'              => 'intval',
            'tba_faq_count'               => 'intval',
            'tba_gsc_json'                => 'sanitize_textarea_field',
            'tba_enable_gsc_auto_ping'    => 'intval',
            'tba_prompt_titles'           => 'sanitize_textarea_field',
            'tba_prompt_article'          => 'sanitize_textarea_field',
            'tba_prompt_meta'             => 'sanitize_textarea_field',
            'tba_prompt_tags'             => 'sanitize_textarea_field',
            'tba_prompt_faq'              => 'sanitize_textarea_field',
        ];

        foreach ( $fields as $key => $sanitizer ) {
            if ( strpos( $key, 'tba_enable_' ) === 0 || $key === 'tba_review_mode' ) {
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                $val = isset( $_POST[ $key ] ) ? 1 : 0;
            } elseif ( $key === 'tba_t2i_bg_val' ) {
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                $bg_type = isset( $_POST['tba_t2i_bg_type'] ) ? sanitize_text_field( $_POST['tba_t2i_bg_type'] ) : 'gradient';
                if ( $bg_type === 'gradient' ) {
                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                    $val = isset( $_POST['tba_t2i_bg_val_gradient'] ) ? sanitize_text_field( $_POST['tba_t2i_bg_val_gradient'] ) : 'blue_purple';
                } else {
                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                    $val = isset( $_POST['tba_t2i_bg_val_solid'] ) ? sanitize_text_field( $_POST['tba_t2i_bg_val_solid'] ) : 'dark_slate';
                }
            } else {
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                $val = isset( $_POST[ $key ] ) ? $sanitizer( $_POST[ $key ] ) : '';
            }
            update_option( $key, $val );
        }

        // Sync native update settings option
        $auto_updates = (array) get_site_option( 'auto_update_' . 'plugins', [] );
        $plugin_slug  = 'tools-by-aadi/tools-by-aadi.php';
        if ( get_option( 'tba_auto_update_enabled', 0 ) ) {
            if ( ! in_array( $plugin_slug, $auto_updates, true ) ) {
                $auto_updates[] = $plugin_slug;
                update_site_option( 'auto_update_' . 'plugins', array_unique( $auto_updates ) );
            }
        } else {
            if ( in_array( $plugin_slug, $auto_updates, true ) ) {
                $auto_updates = array_diff( $auto_updates, [ $plugin_slug ] );
                update_site_option( 'auto_update_' . 'plugins', array_values( $auto_updates ) );
            }
        }

        // Clear LiteSpeed / Autoptimize cache to refresh settings pages
        if ( class_exists( 'LiteSpeed_Cache_API' ) && method_exists( 'LiteSpeed_Cache_API', 'purge_all' ) ) {
            LiteSpeed_Cache_API::purge_all();
        }
        if ( class_exists( 'autoptimizeCache' ) && method_exists( 'autoptimizeCache', 'clearall' ) ) {
            autoptimizeCache::clearall();
        }

        wp_safe_redirect( add_query_arg( [ 'page' => 'tba-settings', 'saved' => '1' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    // -------------------------------------------------------------------------
    // API Key Handlers
    // -------------------------------------------------------------------------

    public static function handle_add_key() {
        check_admin_referer( 'tba_add_key' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $key      = trim( sanitize_text_field( $_POST['api_key'] ?? '' ) );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $provider = sanitize_text_field( $_POST['api_key_provider'] ?? 'gemini' );
        if ( $key ) {
            $added = TBA_Key_Manager::add_key( $key, $provider );
            $msg   = $added ? 'key_added' : 'key_exists';
        } else {
            $msg = 'key_empty';
        }

        wp_safe_redirect( add_query_arg( [ 'page' => 'tba-settings', 'msg' => $msg ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_delete_key() {
        check_admin_referer( 'tba_delete_key' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $index = (int) ( $_POST['key_index'] ?? -1 );
        TBA_Key_Manager::delete_key( $index );

        wp_safe_redirect( add_query_arg( [ 'page' => 'tba-settings', 'msg' => 'key_deleted' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_reset_key() {
        check_admin_referer( 'tba_reset_key' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $index = (int) ( $_POST['key_index'] ?? -1 );
        TBA_Key_Manager::reset_key( $index );

        wp_safe_redirect( add_query_arg( [ 'page' => 'tba-settings', 'msg' => 'key_reset' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    // -------------------------------------------------------------------------
    // Scheduler Handlers
    // -------------------------------------------------------------------------

    public static function save_schedule() {
        check_admin_referer( 'tba_save_schedule' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $enabled     = isset( $_POST['schedule_enabled'] ) ? 1 : 0;
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $per_day     = (int) ( $_POST['posts_per_day'] ?? 3 );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $niches_text = sanitize_textarea_field( $_POST['schedule_niches'] ?? '' );

        update_option( TBA_Scheduler::OPTION_PER_DAY, $per_day );
        TBA_Scheduler::save_niches_list( $niches_text );

        if ( $enabled ) {
            TBA_Scheduler::enable();
        } else {
            TBA_Scheduler::disable();
        }

        wp_safe_redirect( add_query_arg( [ 'page' => 'tba-scheduler', 'saved' => '1' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_enqueue_niche() {
        check_admin_referer( 'tba_enqueue_niche' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $niche = sanitize_text_field( $_POST['niche'] ?? '' );
        if ( $niche ) {
            TBA_Queue::enqueue( $niche );
        }

        wp_safe_redirect( add_query_arg( [ 'page' => 'tba-scheduler', 'msg' => 'queued' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_delete_queue() {
        check_admin_referer( 'tba_delete_queue' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $id = (int) ( $_POST['queue_id'] ?? 0 );
        if ( $id ) TBA_Queue::delete( $id );

        wp_safe_redirect( add_query_arg( [ 'page' => 'tba-scheduler', 'msg' => 'queue_deleted' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_pause_queue() {
        check_admin_referer( 'tba_pause_queue' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $id = (int) ( $_POST['queue_id'] ?? 0 );
        if ( $id ) {
            TBA_Queue::mark_paused( $id );
        }

        wp_safe_redirect( add_query_arg( [ 'page' => 'tba-scheduler', 'msg' => 'queue_paused' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_resume_queue() {
        check_admin_referer( 'tba_resume_queue' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $id = (int) ( $_POST['queue_id'] ?? 0 );
        if ( $id ) {
            TBA_Queue::mark_resumed( $id );
        }

        wp_safe_redirect( add_query_arg( [ 'page' => 'tba-scheduler', 'msg' => 'queue_resumed' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_clear_queue() {
        check_admin_referer( 'tba_clear_queue' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        TBA_Queue::clear_all();

        wp_safe_redirect( add_query_arg( [ 'page' => 'tba-scheduler', 'msg' => 'queue_cleared' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_delete_selected_queue() {
        check_admin_referer( 'tba_delete_selected_queue' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $ids_str = sanitize_text_field( $_POST['queue_ids'] ?? '' );
        if ( ! empty( $ids_str ) ) {
            $ids = explode( ',', $ids_str );
            TBA_Queue::delete_multiple( $ids );
        }

        wp_safe_redirect( add_query_arg( [ 'page' => 'tba-scheduler', 'msg' => 'queue_selected_deleted' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    // -------------------------------------------------------------------------
    // History Handlers
    // -------------------------------------------------------------------------

    public static function handle_delete_history() {
        check_admin_referer( 'tba_delete_history' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $id = (int) ( $_POST['history_id'] ?? 0 );
        if ( $id ) TBA_History::delete( $id );

        wp_safe_redirect( add_query_arg( [ 'page' => 'tba-dashboard', 'msg' => 'deleted' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_clear_history() {
        check_admin_referer( 'tba_clear_history' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        TBA_History::clear_all();

        wp_safe_redirect( add_query_arg( [ 'page' => 'tba-dashboard', 'msg' => 'cleared' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_reset_settings() {
        check_admin_referer( 'tba_reset_settings' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        $options_to_delete = [
            'tba_default_status', 'tba_default_author', 'tba_word_count', 'tba_tag_count',
            'tba_content_tone', 'tba_blacklist_words', 'tba_key_reset_minutes', 'tba_review_mode',
            'tba_text_model', 'tba_image_model', 'tba_active_provider', 'tba_openai_model',
            'tba_enable_internal_linking', 'tba_max_internal_links', 'tba_enable_indexnow',
            'tba_enable_comments', 'tba_comments_count', 'tba_enable_text_overlay',
            'tba_overlay_font_size', 'tba_overlay_color', 'tba_overlay_bg_color',
            'tba_overlay_bg_opacity', 'tba_overlay_position', 'tba_thumb_type',
            'tba_t2i_bg_type', 'tba_t2i_bg_val', 'tba_t2i_size', 'tba_enable_faq',
            'tba_faq_count', 'tba_gsc_json', 'tba_enable_gsc_auto_ping', 'tba_prompt_titles',
            'tba_prompt_article', 'tba_prompt_meta', 'tba_prompt_tags', 'tba_prompt_faq',
            'tba_default_reference_image'
        ];

        foreach ( $options_to_delete as $opt ) {
            delete_option( $opt );
        }

        if ( function_exists( 'tba_purge_all_caches' ) ) {
            tba_purge_all_caches();
        }

        wp_safe_redirect( add_query_arg( [ 'page' => 'tba-settings', 'msg' => 'settings_reset' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_clear_plugin_data() {
        check_admin_referer( 'tba_clear_plugin_data' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // 1. Delete all plugin transients
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_tba_%' OR option_name LIKE '_site_transient_tba_%'" );

        // 2. Clear Queue
        TBA_Queue::clear_all();

        // 3. Clear History Log
        TBA_History::clear_all();

        // 4. Multi-engine Cache Purge
        if ( function_exists( 'tba_purge_all_caches' ) ) {
            tba_purge_all_caches();
        }

        wp_safe_redirect( add_query_arg( [ 'page' => 'tba-settings', 'msg' => 'data_cleared' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function render_thumbnails_page() {
        require_once TBA_PLUGIN_DIR . 'admin/views/thumbnails-page.php';
    }
}
