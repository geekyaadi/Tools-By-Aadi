<?php
/**
 * Admin Settings — registers all admin menu pages and handles option saving.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class SAB_Settings {

    public static function init() {
        add_action( 'admin_menu',            [ __CLASS__, 'register_menus' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
        add_action( 'admin_post_sab_save_settings', [ __CLASS__, 'save_settings' ] );
        add_action( 'admin_post_sab_add_key',        [ __CLASS__, 'handle_add_key' ] );
        add_action( 'admin_post_sab_delete_key',     [ __CLASS__, 'handle_delete_key' ] );
        add_action( 'admin_post_sab_reset_key',      [ __CLASS__, 'handle_reset_key' ] );
        add_action( 'admin_post_sab_save_schedule',  [ __CLASS__, 'save_schedule' ] );
        add_action( 'admin_post_sab_enqueue_niche',  [ __CLASS__, 'handle_enqueue_niche' ] );
        add_action( 'admin_post_sab_delete_queue',   [ __CLASS__, 'handle_delete_queue' ] );
        add_action( 'admin_post_sab_pause_queue',    [ __CLASS__, 'handle_pause_queue' ] );
        add_action( 'admin_post_sab_resume_queue',   [ __CLASS__, 'handle_resume_queue' ] );
        add_action( 'admin_post_sab_clear_queue',    [ __CLASS__, 'handle_clear_queue' ] );
        add_action( 'admin_post_sab_delete_selected_queue', [ __CLASS__, 'handle_delete_selected_queue' ] );
        add_action( 'admin_post_sab_delete_history', [ __CLASS__, 'handle_delete_history' ] );
        add_action( 'admin_post_sab_clear_history',  [ __CLASS__, 'handle_clear_history' ] );
        add_action( 'admin_post_sab_reset_settings',   [ __CLASS__, 'handle_reset_settings' ] );
        add_action( 'admin_post_sab_clear_plugin_data', [ __CLASS__, 'handle_clear_plugin_data' ] );
        add_action( 'admin_post_sab_save_speed_settings', [ __CLASS__, 'save_speed_settings' ] );
        add_action( 'admin_post_sab_purge_speed_cache',   [ __CLASS__, 'handle_purge_speed_cache' ] );
        add_action( 'admin_post_sab_save_sitemap_settings', [ __CLASS__, 'save_sitemap_settings' ] );
        add_action( 'admin_post_sab_generate_essential_pages', [ __CLASS__, 'handle_generate_essential_pages' ] );
        add_action( 'admin_post_sab_save_cookie_settings',     [ __CLASS__, 'handle_save_cookie_settings' ] );
    }

    // -------------------------------------------------------------------------
    // Admin Menus
    // -------------------------------------------------------------------------

    public static function register_menus() {
        add_menu_page(
            __( 'Soniji Auto Blogging', 'soniji-auto-blogging' ),
            __( 'Soniji Auto Blogging', 'soniji-auto-blogging' ),
            'manage_options',
            'soniji-auto-blogging',
            [ __CLASS__, 'render_dashboard_page' ],
            'dashicons-dashboard',
            null
        );

        add_submenu_page( 'soniji-auto-blogging', __( 'Dashboard', 'soniji-auto-blogging' ),      __( 'Dashboard', 'soniji-auto-blogging' ),      'manage_options', 'soniji-auto-blogging',             [ __CLASS__, 'render_dashboard_page' ] );
        add_submenu_page( 'soniji-auto-blogging', __( 'Generate Post', 'soniji-auto-blogging' ),  __( 'Generate Post', 'soniji-auto-blogging' ),  'manage_options', 'sab-generate',             [ __CLASS__, 'render_generate_page' ] );
        add_submenu_page( 'soniji-auto-blogging', __( 'Bulk Planner', 'soniji-auto-blogging' ),   __( 'Bulk Planner', 'soniji-auto-blogging' ),   'manage_options', 'sab-planner',              [ __CLASS__, 'render_planner_page' ] );
        add_submenu_page( 'soniji-auto-blogging', __( 'Scheduler & Queue', 'soniji-auto-blogging' ), __( 'Scheduler & Queue', 'soniji-auto-blogging' ), 'manage_options', 'sab-scheduler',      [ __CLASS__, 'render_scheduler_page' ] );
        add_submenu_page( 'soniji-auto-blogging', __( 'Thumbnail Manager', 'soniji-auto-blogging' ), __( 'Thumbnail Manager', 'soniji-auto-blogging' ), 'manage_options', 'sab-thumbnails',   [ __CLASS__, 'render_thumbnails_page' ] );
        add_submenu_page( 'soniji-auto-blogging', __( 'Tags Manager', 'soniji-auto-blogging' ),    __( 'Tags Manager', 'soniji-auto-blogging' ),    'manage_options', 'sab-tags',                 [ __CLASS__, 'render_tags_page' ] );
        add_submenu_page( 'soniji-auto-blogging', __( 'Bulk Translator', 'soniji-auto-blogging' ),  __( 'Bulk Translator', 'soniji-auto-blogging' ),  'manage_options', 'sab-translator',           [ __CLASS__, 'render_translator_page' ] );
        add_submenu_page( 'soniji-auto-blogging', __( 'Google Indexing Tool', 'soniji-auto-blogging' ), __( 'Google Indexing Tool', 'soniji-auto-blogging' ), 'manage_options', 'sab-gsc',      [ __CLASS__, 'render_gsc_page' ] );
        add_submenu_page( 'soniji-auto-blogging', __( 'Article Rewriter', 'soniji-auto-blogging' ), __( 'Article Rewriter', 'soniji-auto-blogging' ), 'manage_options', 'sab-rewriter',   [ __CLASS__, 'render_rewriter_page' ] );
        add_submenu_page( 'soniji-auto-blogging', __( 'Speed Optimizer', 'soniji-auto-blogging' ), __( '⚡ Speed Optimizer', 'soniji-auto-blogging' ), 'manage_options', 'sab-speed',      [ __CLASS__, 'render_speed_page' ] );
        add_submenu_page( 'soniji-auto-blogging', __( 'XML Sitemap Manager', 'soniji-auto-blogging' ), __( '🗺️ Sitemap Manager', 'soniji-auto-blogging' ), 'manage_options', 'sab-sitemap',   [ __CLASS__, 'render_sitemap_page' ] );
        add_submenu_page( 'soniji-auto-blogging', __( 'Pages & Cookie Consent', 'soniji-auto-blogging' ), __( '📄 Pages & Cookies', 'soniji-auto-blogging' ), 'manage_options', 'sab-pages',   [ __CLASS__, 'render_pages_generator_page' ] );
        add_submenu_page( 'soniji-auto-blogging', __( 'Redirect & 404 Manager', 'soniji-auto-blogging' ), __( '🔀 Redirect Manager', 'soniji-auto-blogging' ), 'manage_options', 'sab-redirects', [ __CLASS__, 'render_redirects_page' ] );
        add_submenu_page( 'soniji-auto-blogging', __( 'Date Randomizer', 'soniji-auto-blogging' ),       __( '📅 Date Randomizer', 'soniji-auto-blogging' ), 'manage_options', 'sab-randomizer',[ __CLASS__, 'render_randomizer_page' ] );
        add_submenu_page( 'soniji-auto-blogging', __( 'ads.txt Manager', 'soniji-auto-blogging' ),       __( '💰 ads.txt Manager', 'soniji-auto-blogging' ), 'manage_options', 'sab-codes',     [ __CLASS__, 'render_codes_page' ] );
        add_submenu_page( 'soniji-auto-blogging', __( 'Settings', 'soniji-auto-blogging' ),       __( 'Settings', 'soniji-auto-blogging' ),       'manage_options', 'sab-settings',             [ __CLASS__, 'render_settings_page' ] );
    }

    // -------------------------------------------------------------------------
    // Enqueue Assets
    // -------------------------------------------------------------------------

    public static function enqueue_assets( $hook ) {
        $sab_pages = [
            'toplevel_page_soniji-auto-blogging',
            'soniji-auto-blogging_page_sab-generate',
            'soniji-auto-blogging_page_sab-planner',
            'soniji-auto-blogging_page_sab-scheduler',
            'soniji-auto-blogging_page_sab-settings',
            'soniji-auto-blogging_page_sab-thumbnails',
            'soniji-auto-blogging_page_sab-tags',
            'soniji-auto-blogging_page_sab-translator',
            'soniji-auto-blogging_page_sab-gsc',
            'soniji-auto-blogging_page_sab-rewriter',
            'soniji-auto-blogging_page_sab-speed',
            'soniji-auto-blogging_page_sab-sitemap',
            'soniji-auto-blogging_page_sab-pages',
            'soniji-auto-blogging_page_sab-redirects',
            'soniji-auto-blogging_page_sab-randomizer',
            'soniji-auto-blogging_page_sab-codes',
        ];
        if ( ! in_array( $hook, $sab_pages, true ) && strpos( $hook, 'sab' ) === false ) return;

        $css_path = SAB_PLUGIN_DIR . 'admin/css/admin.css';
        $css_ver  = file_exists( $css_path ) ? filemtime( $css_path ) : SAB_VERSION;

        wp_enqueue_style(
            'sab-admin-v2',
            SAB_PLUGIN_URL . 'admin/css/admin.css',
            [],
            $css_ver
        );

        $js_path = SAB_PLUGIN_DIR . 'admin/js/admin.js';
        $js_ver  = file_exists( $js_path ) ? filemtime( $js_path ) : SAB_VERSION;

        wp_enqueue_script(
            'sab-admin-v2',
            SAB_PLUGIN_URL . 'admin/js/admin.js',
            [ 'jquery' ],
            $js_ver,
            true
        );

        wp_localize_script( 'sab-admin-v2', 'sabData', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'sab_nonce' ),
            'strings' => [
                'generating'      => __( 'Generating...', 'soniji-auto-blogging' ),
                'switchedKey'     => __( 'API key exhausted — switching to next key...', 'soniji-auto-blogging' ),
                'allExhausted'    => __( 'All API keys exhausted. Please add more keys or wait for reset.', 'soniji-auto-blogging' ),
                'success'         => __( 'Post created successfully!', 'soniji-auto-blogging' ),
                'error'           => __( 'An error occurred. Please try again.', 'soniji-auto-blogging' ),
                'confirmDelete'   => __( 'Are you sure you want to delete this?', 'soniji-auto-blogging' ),
                'duplicate'       => __( 'A similar post already exists. Do you want to continue anyway?', 'soniji-auto-blogging' ),
            ],
        ] );
    }

    // -------------------------------------------------------------------------
    // Page Renderers
    // -------------------------------------------------------------------------

    public static function render_generate_page() {
        require_once SAB_PLUGIN_DIR . 'admin/views/generate-page.php';
    }

    public static function render_planner_page() {
        require_once SAB_PLUGIN_DIR . 'admin/views/planner-page.php';
    }

    public static function render_tags_page() {
        require_once SAB_PLUGIN_DIR . 'admin/views/tags-page.php';
    }

    public static function render_translator_page() {
        require_once SAB_PLUGIN_DIR . 'admin/views/translator-page.php';
    }

    public static function render_gsc_page() {
        require_once SAB_PLUGIN_DIR . 'admin/views/gsc-page.php';
    }

    public static function render_rewriter_page() {
        require_once SAB_PLUGIN_DIR . 'admin/views/rewriter-page.php';
    }

    public static function render_speed_page() {
        require_once SAB_PLUGIN_DIR . 'admin/views/speed-optimizer-page.php';
    }

    public static function render_sitemap_page() {
        require_once SAB_PLUGIN_DIR . 'admin/views/sitemap-page.php';
    }

    public static function render_pages_generator_page() {
        require_once SAB_PLUGIN_DIR . 'admin/views/pages-generator-page.php';
    }

    public static function render_redirects_page() {
        require_once SAB_PLUGIN_DIR . 'admin/views/redirects-page.php';
    }

    public static function render_randomizer_page() {
        require_once SAB_PLUGIN_DIR . 'admin/views/date-randomizer-page.php';
    }

    public static function render_codes_page() {
        require_once SAB_PLUGIN_DIR . 'admin/views/codes-page.php';
    }

    public static function render_settings_page() {
        require_once SAB_PLUGIN_DIR . 'admin/views/settings-page.php';
    }

    public static function render_scheduler_page() {
        require_once SAB_PLUGIN_DIR . 'admin/views/scheduler-page.php';
    }

    public static function render_dashboard_page() {
        require_once SAB_PLUGIN_DIR . 'admin/views/dashboard-page.php';
    }

    // -------------------------------------------------------------------------
    // Essential Pages & Cookie Handlers
    // -------------------------------------------------------------------------

    public static function handle_generate_essential_pages() {
        check_admin_referer( 'sab_generate_essential_pages' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $site_name = sanitize_text_field( $_POST['sab_site_name'] ?? '' );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $site_url  = esc_url_raw( $_POST['sab_site_url'] ?? '' );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $email     = sanitize_email( $_POST['sab_contact_email'] ?? '' );

        update_option( 'sab_site_name', $site_name );
        update_option( 'sab_site_url', $site_url );
        update_option( 'sab_contact_email', $email );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $selected_pages = isset( $_POST['pages'] ) && is_array( $_POST['pages'] ) ? $_POST['pages'] : [];
        $created_count = 0;

        foreach ( $selected_pages as $page_type ) {
            $res = SAB_Pages_Generator::create_page( sanitize_text_field( $page_type ), $site_name, $site_url, $email );
            if ( ! is_wp_error( $res ) ) {
                $created_count++;
            }
        }

        wp_safe_redirect( admin_url( 'admin.php?page=sab-pages&pages_created=' . $created_count ) );
        exit;
    }

    public static function handle_save_cookie_settings() {
        check_admin_referer( 'sab_save_cookie_settings' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $enable        = isset( $_POST['sab_cookie_enable'] ) ? '1' : '0';
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $style         = sanitize_text_field( $_POST['sab_cookie_style'] ?? 'bottom_banner' );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $text          = sanitize_textarea_field( $_POST['sab_cookie_text'] ?? '' );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $btn_text      = sanitize_text_field( $_POST['sab_cookie_btn_text'] ?? 'Accept All' );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $enable_reject = isset( $_POST['sab_cookie_enable_reject'] ) ? '1' : '0';
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $reject_text   = sanitize_text_field( $_POST['sab_cookie_reject_btn_text'] ?? 'Decline Non-Essential' );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $privacy_url   = esc_url_raw( $_POST['sab_cookie_privacy_url'] ?? '' );

        update_option( 'sab_cookie_enable', $enable );
        update_option( 'sab_cookie_style', $style );
        update_option( 'sab_cookie_text', $text );
        update_option( 'sab_cookie_btn_text', $btn_text );
        update_option( 'sab_cookie_enable_reject', $enable_reject );
        update_option( 'sab_cookie_reject_btn_text', $reject_text );
        update_option( 'sab_cookie_privacy_url', $privacy_url );

        wp_safe_redirect( admin_url( 'admin.php?page=sab-pages&cookie_updated=true' ) );
        exit;
    }

    // -------------------------------------------------------------------------
    // Sitemap Manager Handlers
    // -------------------------------------------------------------------------

    public static function save_sitemap_settings() {
        check_admin_referer( 'sab_save_sitemap_settings' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $slug = isset( $_POST['sab_sitemap_slug'] ) ? sanitize_title( $_POST['sab_sitemap_slug'] ) : 'sitemap';
        if ( empty( $slug ) ) $slug = 'sitemap';
        if ( strpos( $slug, '.xml' ) === false ) $slug .= '.xml';
        update_option( 'sab_sitemap_slug', $slug );

        $fields = [
            'sab_sitemap_priority_home'    => 'sanitize_text_field',
            'sab_sitemap_changefreq_home'  => 'sanitize_text_field',
            'sab_sitemap_priority_post'    => 'sanitize_text_field',
            'sab_sitemap_changefreq_post'  => 'sanitize_text_field',
            'sab_sitemap_priority_page'    => 'sanitize_text_field',
            'sab_sitemap_changefreq_page'  => 'sanitize_text_field',
            'sab_sitemap_priority_cat'     => 'sanitize_text_field',
            'sab_sitemap_changefreq_cat'   => 'sanitize_text_field',
            'sab_sitemap_priority_tag'     => 'sanitize_text_field',
            'sab_sitemap_changefreq_tag'   => 'sanitize_text_field',
        ];

        foreach ( $fields as $opt => $sanitizer ) {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $val = isset( $_POST[ $opt ] ) ? $sanitizer( $_POST[ $opt ] ) : '';
            update_option( $opt, $val );
        }

        $checkboxes = [
            'sab_sitemap_enable_home',
            'sab_sitemap_enable_posts',
            'sab_sitemap_enable_pages',
            'sab_sitemap_enable_cats',
            'sab_sitemap_enable_tags',
            'sab_sitemap_include_images',
            'sab_sitemap_auto_ping_google',
            'sab_sitemap_auto_ping_bing',
        ];

        foreach ( $checkboxes as $cb ) {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $val = isset( $_POST[ $cb ] ) ? '1' : '0';
            update_option( $cb, $val );
        }

        // Flush rewrite rules for custom sitemap slug
        SAB_Sitemap::add_rewrite_rules();
        flush_rewrite_rules();

        if ( function_exists( 'sab_purge_all_caches' ) ) {
            sab_purge_all_caches();
        }

        wp_safe_redirect( admin_url( 'admin.php?page=sab-sitemap&updated=true' ) );
        exit;
    }

    // -------------------------------------------------------------------------
    // Speed Optimizer Handlers
    // -------------------------------------------------------------------------

    public static function save_speed_settings() {
        check_admin_referer( 'sab_save_speed_settings' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        $speed_options = [
            'sab_speed_lazy_loading',
            'sab_speed_html_minification',
            'sab_speed_webp_compression',
            'sab_speed_auto_cache_purge',
            'sab_speed_preload_assets',
            'sab_speed_defer_js',
        ];

        foreach ( $speed_options as $opt ) {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $val = isset( $_POST[ $opt ] ) ? '1' : '0';
            update_option( $opt, $val );
        }

        if ( function_exists( 'sab_purge_all_caches' ) ) {
            sab_purge_all_caches();
        }

        wp_safe_redirect( admin_url( 'admin.php?page=sab-speed&updated=true' ) );
        exit;
    }

    public static function handle_purge_speed_cache() {
        check_admin_referer( 'sab_purge_speed_cache' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        if ( function_exists( 'sab_purge_all_caches' ) ) {
            sab_purge_all_caches();
        }

        wp_safe_redirect( admin_url( 'admin.php?page=sab-speed&purged=true' ) );
        exit;
    }

    // -------------------------------------------------------------------------
    // Save Main Settings
    // -------------------------------------------------------------------------

    public static function save_settings() {
        check_admin_referer( 'sab_save_settings' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        $fields = [
            'sab_default_status'          => 'sanitize_text_field',
            'sab_auto_update_enabled'     => 'intval',
            'sab_default_author'          => 'intval',
            'sab_word_count'              => 'intval',
            'sab_tag_count'               => 'intval',
            'sab_content_tone'            => 'sanitize_text_field',
            'sab_blacklist_words'         => 'sanitize_textarea_field',
            'sab_key_reset_minutes'       => 'intval',
            'sab_review_mode'             => 'intval',
            'sab_text_model'              => 'sanitize_text_field',
            'sab_image_model'             => 'sanitize_text_field',
            'sab_active_provider'         => 'sanitize_text_field',
            'sab_openai_model'            => 'sanitize_text_field',
            'sab_enable_internal_linking' => 'intval',
            'sab_max_internal_links'      => 'intval',
            'sab_internal_link_style'     => 'sanitize_text_field',
            'sab_enable_outbound_linking' => 'intval',
            'sab_max_outbound_links'      => 'intval',
            'sab_outbound_target'         => 'sanitize_text_field',
            'sab_outbound_rel'            => 'sanitize_text_field',
            'sab_outbound_blacklist'      => 'sanitize_textarea_field',
            'sab_enable_indexnow'         => 'intval',
            'sab_enable_comments'         => 'intval',
            'sab_comments_count'          => 'intval',
            'sab_enable_text_overlay'     => 'intval',
            'sab_overlay_font_size'       => 'intval',
            'sab_overlay_color'           => 'sanitize_text_field',
            'sab_overlay_bg_color'        => 'sanitize_text_field',
            'sab_overlay_bg_opacity'      => 'intval',
            'sab_overlay_position'        => 'sanitize_text_field',
            'sab_thumb_type'              => 'sanitize_text_field',
            'sab_t2i_bg_type'             => 'sanitize_text_field',
            'sab_t2i_bg_val'              => 'sanitize_text_field',
            'sab_t2i_size'                => 'sanitize_text_field',
            'sab_enable_toc'              => 'intval',
            'sab_toc_default_state'       => 'sanitize_text_field',
            'sab_enable_faq'              => 'intval',
            'sab_faq_count'               => 'intval',
            'sab_gsc_json'                => 'sanitize_textarea_field',
            'sab_enable_gsc_auto_ping'    => 'intval',
            'sab_prompt_titles'           => 'sanitize_textarea_field',
            'sab_prompt_article'          => 'sanitize_textarea_field',
            'sab_prompt_meta'             => 'sanitize_textarea_field',
            'sab_prompt_tags'             => 'sanitize_textarea_field',
            'sab_prompt_faq'              => 'sanitize_textarea_field',
        ];

        foreach ( $fields as $key => $sanitizer ) {
            if ( strpos( $key, 'sab_enable_' ) === 0 || $key === 'sab_review_mode' ) {
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                $val = isset( $_POST[ $key ] ) ? 1 : 0;
            } elseif ( $key === 'sab_t2i_bg_val' ) {
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                $bg_type = isset( $_POST['sab_t2i_bg_type'] ) ? sanitize_text_field( $_POST['sab_t2i_bg_type'] ) : 'gradient';
                if ( $bg_type === 'gradient' ) {
                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                    $val = isset( $_POST['sab_t2i_bg_val_gradient'] ) ? sanitize_text_field( $_POST['sab_t2i_bg_val_gradient'] ) : 'blue_purple';
                } else {
                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                    $val = isset( $_POST['sab_t2i_bg_val_solid'] ) ? sanitize_text_field( $_POST['sab_t2i_bg_val_solid'] ) : 'dark_slate';
                }
            } else {
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                $val = isset( $_POST[ $key ] ) ? $sanitizer( $_POST[ $key ] ) : '';
            }
            update_option( $key, $val );
        }

        // Sync native update settings option
        $auto_updates = (array) get_site_option( 'auto_update_' . 'plugins', [] );
        $plugin_slug  = 'soniji-auto-blogging/soniji-auto-blogging.php';
        if ( get_option( 'sab_auto_update_enabled', 0 ) ) {
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

        wp_safe_redirect( add_query_arg( [ 'page' => 'sab-settings', 'saved' => '1' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    // -------------------------------------------------------------------------
    // API Key Handlers
    // -------------------------------------------------------------------------

    public static function handle_add_key() {
        check_admin_referer( 'sab_add_key' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $key      = trim( sanitize_text_field( $_POST['api_key'] ?? '' ) );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $provider = sanitize_text_field( $_POST['api_key_provider'] ?? 'gemini' );
        if ( $key ) {
            $added = SAB_Key_Manager::add_key( $key, $provider );
            $msg   = $added ? 'key_added' : 'key_exists';
        } else {
            $msg = 'key_empty';
        }

        wp_safe_redirect( add_query_arg( [ 'page' => 'sab-settings', 'msg' => $msg ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_delete_key() {
        check_admin_referer( 'sab_delete_key' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $index = (int) ( $_POST['key_index'] ?? -1 );
        SAB_Key_Manager::delete_key( $index );

        wp_safe_redirect( add_query_arg( [ 'page' => 'sab-settings', 'msg' => 'key_deleted' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_reset_key() {
        check_admin_referer( 'sab_reset_key' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $index = (int) ( $_POST['key_index'] ?? -1 );
        SAB_Key_Manager::reset_key( $index );

        wp_safe_redirect( add_query_arg( [ 'page' => 'sab-settings', 'msg' => 'key_reset' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    // -------------------------------------------------------------------------
    // Scheduler Handlers
    // -------------------------------------------------------------------------

    public static function save_schedule() {
        check_admin_referer( 'sab_save_schedule' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $enabled     = isset( $_POST['schedule_enabled'] ) ? 1 : 0;
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $per_day     = (int) ( $_POST['posts_per_day'] ?? 3 );
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $niches_text = sanitize_textarea_field( $_POST['schedule_niches'] ?? '' );

        update_option( SAB_Scheduler::OPTION_PER_DAY, $per_day );
        SAB_Scheduler::save_niches_list( $niches_text );

        if ( $enabled ) {
            SAB_Scheduler::enable();
        } else {
            SAB_Scheduler::disable();
        }

        wp_safe_redirect( add_query_arg( [ 'page' => 'sab-scheduler', 'saved' => '1' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_enqueue_niche() {
        check_admin_referer( 'sab_enqueue_niche' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $niche = sanitize_text_field( $_POST['niche'] ?? '' );
        if ( $niche ) {
            SAB_Queue::enqueue( $niche );
        }

        wp_safe_redirect( add_query_arg( [ 'page' => 'sab-scheduler', 'msg' => 'queued' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_delete_queue() {
        check_admin_referer( 'sab_delete_queue' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $id = (int) ( $_POST['queue_id'] ?? 0 );
        if ( $id ) SAB_Queue::delete( $id );

        wp_safe_redirect( add_query_arg( [ 'page' => 'sab-scheduler', 'msg' => 'queue_deleted' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_pause_queue() {
        check_admin_referer( 'sab_pause_queue' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $id = (int) ( $_POST['queue_id'] ?? 0 );
        if ( $id ) {
            SAB_Queue::mark_paused( $id );
        }

        wp_safe_redirect( add_query_arg( [ 'page' => 'sab-scheduler', 'msg' => 'queue_paused' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_resume_queue() {
        check_admin_referer( 'sab_resume_queue' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $id = (int) ( $_POST['queue_id'] ?? 0 );
        if ( $id ) {
            SAB_Queue::mark_resumed( $id );
        }

        wp_safe_redirect( add_query_arg( [ 'page' => 'sab-scheduler', 'msg' => 'queue_resumed' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_clear_queue() {
        check_admin_referer( 'sab_clear_queue' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        SAB_Queue::clear_all();

        wp_safe_redirect( add_query_arg( [ 'page' => 'sab-scheduler', 'msg' => 'queue_cleared' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_delete_selected_queue() {
        check_admin_referer( 'sab_delete_selected_queue' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $ids_str = sanitize_text_field( $_POST['queue_ids'] ?? '' );
        if ( ! empty( $ids_str ) ) {
            $ids = explode( ',', $ids_str );
            SAB_Queue::delete_multiple( $ids );
        }

        wp_safe_redirect( add_query_arg( [ 'page' => 'sab-scheduler', 'msg' => 'queue_selected_deleted' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    // -------------------------------------------------------------------------
    // History Handlers
    // -------------------------------------------------------------------------

    public static function handle_delete_history() {
        check_admin_referer( 'sab_delete_history' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $id = (int) ( $_POST['history_id'] ?? 0 );
        if ( $id ) SAB_History::delete( $id );

        wp_safe_redirect( add_query_arg( [ 'page' => 'sab-dashboard', 'msg' => 'deleted' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_clear_history() {
        check_admin_referer( 'sab_clear_history' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        SAB_History::clear_all();

        wp_safe_redirect( add_query_arg( [ 'page' => 'sab-dashboard', 'msg' => 'cleared' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_reset_settings() {
        check_admin_referer( 'sab_reset_settings' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        $options_to_delete = [
            'sab_default_status', 'sab_default_author', 'sab_word_count', 'sab_tag_count',
            'sab_content_tone', 'sab_blacklist_words', 'sab_key_reset_minutes', 'sab_review_mode',
            'sab_text_model', 'sab_image_model', 'sab_active_provider', 'sab_openai_model',
            'sab_enable_internal_linking', 'sab_max_internal_links', 'sab_enable_indexnow',
            'sab_enable_comments', 'sab_comments_count', 'sab_enable_text_overlay',
            'sab_overlay_font_size', 'sab_overlay_color', 'sab_overlay_bg_color',
            'sab_overlay_bg_opacity', 'sab_overlay_position', 'sab_thumb_type',
            'sab_t2i_bg_type', 'sab_t2i_bg_val', 'sab_t2i_size', 'sab_enable_faq',
            'sab_faq_count', 'sab_gsc_json', 'sab_enable_gsc_auto_ping', 'sab_prompt_titles',
            'sab_prompt_article', 'sab_prompt_meta', 'sab_prompt_tags', 'sab_prompt_faq',
            'sab_default_reference_image'
        ];

        foreach ( $options_to_delete as $opt ) {
            delete_option( $opt );
        }

        if ( function_exists( 'sab_purge_all_caches' ) ) {
            sab_purge_all_caches();
        }

        wp_safe_redirect( add_query_arg( [ 'page' => 'sab-settings', 'msg' => 'settings_reset' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function handle_clear_plugin_data() {
        check_admin_referer( 'sab_clear_plugin_data' );
        if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );

        // 1. Delete all plugin transients
        global $wpdb;
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_sab_%' OR option_name LIKE '_site_transient_sab_%'" );

        // 2. Clear Queue
        SAB_Queue::clear_all();

        // 3. Clear History Log
        SAB_History::clear_all();

        // 4. Multi-engine Cache Purge
        if ( function_exists( 'sab_purge_all_caches' ) ) {
            sab_purge_all_caches();
        }

        wp_safe_redirect( add_query_arg( [ 'page' => 'sab-settings', 'msg' => 'data_cleared' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    public static function render_thumbnails_page() {
        require_once SAB_PLUGIN_DIR . 'admin/views/thumbnails-page.php';
    }
}
