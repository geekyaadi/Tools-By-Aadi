<?php
/**
 * Soniji Auto Blogging Uninstall Template
 * Clean up all options, transients, cron jobs, and database tables when the plugin is uninstalled.
 *
 * @package Soniji_Auto_Blogging
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

// 1. Clear scheduled cron hook
wp_clear_scheduled_hook( 'sab_scheduled_publish' );

// 2. Drop custom database tables
$tables = [
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
    $wpdb->prefix . 'sab_history',
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared, PluginCheck.Security.DirectDB.UnescapedDBParameter
    $wpdb->prefix . 'sab_queue'
];

foreach ( $tables as $table ) {
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $wpdb->query( "DROP TABLE IF EXISTS " . esc_sql( $table ) );
}

// 3. Delete all registered options
$options = [
    'sab_default_status',
    'sab_default_author',
    'sab_word_count',
    'sab_tag_count',
    'sab_content_tone',
    'sab_blacklist_words',
    'sab_key_reset_minutes',
    'sab_review_mode',
    'sab_text_model',
    'sab_image_model',
    'sab_active_provider',
    'sab_openai_model',
    'sab_enable_internal_linking',
    'sab_max_internal_links',
    'sab_enable_indexnow',
    'sab_enable_comments',
    'sab_comments_count',
    'sab_enable_text_overlay',
    'sab_overlay_font_size',
    'sab_overlay_color',
    'sab_overlay_bg_color',
    'sab_overlay_bg_opacity',
    'sab_overlay_position',
    'sab_thumb_type',
    'sab_t2i_bg_type',
    'sab_t2i_bg_val',
    'sab_t2i_size',
    'sab_enable_faq',
    'sab_faq_count',
    'sab_gsc_json',
    'sab_enable_gsc_auto_ping',
    'sab_prompt_titles',
    'sab_prompt_article',
    'sab_prompt_meta',
    'sab_prompt_tags',
    'sab_prompt_faq',
    'sab_db_version',
    'sab_default_reference_image',
    'sab_api_keys',
    'sab_scheduler_enabled',
    'sab_scheduler_niches',
    'sab_last_niche_index',
    'sab_scheduler_per_day'
];

foreach ( $options as $option ) {
    delete_option( $option );
}

// 4. Delete transient data
delete_site_transient( 'update_plugins' );
