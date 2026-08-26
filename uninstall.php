<?php
/**
 * Tools By Aadi Uninstall Template
 * Clean up all options, transients, cron jobs, and database tables when the plugin is uninstalled.
 *
 * @package Tools_By_Aadi
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

// 1. Clear scheduled cron hook
wp_clear_scheduled_hook( 'tba_scheduled_publish' );

// 2. Drop custom database tables
$tables = [
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
    $wpdb->prefix . 'tba_history',
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
    $wpdb->prefix . 'tba_queue'
];

foreach ( $tables as $table ) {
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $wpdb->query( "DROP TABLE IF EXISTS " . esc_sql( $table ) );
}

// 3. Delete all registered options
$options = [
    'tba_default_status',
    'tba_default_author',
    'tba_word_count',
    'tba_tag_count',
    'tba_content_tone',
    'tba_blacklist_words',
    'tba_key_reset_minutes',
    'tba_review_mode',
    'tba_text_model',
    'tba_image_model',
    'tba_active_provider',
    'tba_openai_model',
    'tba_enable_internal_linking',
    'tba_max_internal_links',
    'tba_enable_indexnow',
    'tba_enable_comments',
    'tba_comments_count',
    'tba_enable_text_overlay',
    'tba_overlay_font_size',
    'tba_overlay_color',
    'tba_overlay_bg_color',
    'tba_overlay_bg_opacity',
    'tba_overlay_position',
    'tba_thumb_type',
    'tba_t2i_bg_type',
    'tba_t2i_bg_val',
    'tba_t2i_size',
    'tba_enable_faq',
    'tba_faq_count',
    'tba_gsc_json',
    'tba_enable_gsc_auto_ping',
    'tba_prompt_titles',
    'tba_prompt_article',
    'tba_prompt_meta',
    'tba_prompt_tags',
    'tba_prompt_faq',
    'tba_db_version',
    'tba_default_reference_image',
    'tba_api_keys',
    'tba_scheduler_enabled',
    'tba_scheduler_niches',
    'tba_last_niche_index',
    'tba_scheduler_per_day'
];

foreach ( $options as $option ) {
    delete_option( $option );
}

// 4. Delete transient data
delete_transient( 'tba_github_release_info' );
delete_site_transient( 'update_plugins' );
