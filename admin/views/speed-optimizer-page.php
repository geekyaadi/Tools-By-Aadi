<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Get Speed Optimizer Options with Defaults
$lazy_loading      = get_option( 'tba_speed_lazy_loading', '1' );
$html_minification = get_option( 'tba_speed_html_minification', '1' );
$webp_compression  = get_option( 'tba_speed_webp_compression', '1' );
$auto_cache_purge  = get_option( 'tba_speed_auto_cache_purge', '1' );
$preload_assets    = get_option( 'tba_speed_preload_assets', '1' );
$defer_js          = get_option( 'tba_speed_defer_js', '1' );

// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$saved = isset( $_GET['updated'] ) && $_GET['updated'] === 'true';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$purged = isset( $_GET['purged'] ) && $_GET['purged'] === 'true';
?>

<div class="tba-wrap">
    <!-- Header -->
    <div class="tba-header">
        <div class="tba-header-inner">
            <div class="tba-logo">
                <img src="<?php echo esc_url( TBA_PLUGIN_URL . 'admin/tools-by-aadi-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="tba-logo-badge">⚡ Speed Optimizer</span>
            </div>
                        <div class="tba-header-nav">
    <a href="<?php echo esc_url( admin_url('admin.php?page=tools-by-aadi') ); ?>" class="tba-nav-link">Dashboard</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-generate') ); ?>" class="tba-nav-link">Generate Post</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-planner') ); ?>" class="tba-nav-link">Bulk Planner</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-scheduler') ); ?>" class="tba-nav-link">Scheduler</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-thumbnails') ); ?>" class="tba-nav-link">Thumbnail Tool</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-tags') ); ?>" class="tba-nav-link">Tags Tool</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-translator') ); ?>" class="tba-nav-link">Translator</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-gsc') ); ?>" class="tba-nav-link">Indexing</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-rewriter') ); ?>" class="tba-nav-link">Rewriter</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-speed') ); ?>" class="tba-nav-link active">Optimizer</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-sitemap') ); ?>" class="tba-nav-link">Sitemap</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-pages') ); ?>" class="tba-nav-link">Pages Generator</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-redirects') ); ?>" class="tba-nav-link">Redirect</a>
        <a href="<?php echo esc_url( admin_url('admin.php?page=tba-randomizer') ); ?>" class="tba-nav-link">Date Randomizer</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-codes') ); ?>" class="tba-nav-link">Codes</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-settings') ); ?>" class="tba-nav-link">Settings</a>
</div>
        </div>
    </div>

    <div class="tba-content">
        <?php if ( $saved ): ?>
            <div class="tba-alert tba-alert-success" style="margin-bottom:20px;">
                <strong>✅ Speed Optimization Settings Saved Successfully!</strong> All active performance rules are now enforced on generated posts.
            </div>
        <?php endif; ?>

        <?php if ( $purged ): ?>
            <div class="tba-alert tba-alert-success" style="margin-bottom:20px;">
                <strong>⚡ All Server Caches & Transients Purged!</strong> OPcache, Object Cache, LiteSpeed, WP Rocket, and Autoptimize cleared.
            </div>
        <?php endif; ?>

        <!-- Hero Speed Overview Card -->
        <div class="tba-panel" style="margin-bottom:20px; background: #ffffff; border: 1px solid var(--tba-border);">
            <div class="tba-panel-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
                <div>
                    <h2 class="tba-panel-title" style="color:var(--tba-text-dark); font-size:20px; margin:0 0 5px 0; display:flex; align-items:center; gap:8px;">
                        <span>⚡</span> Tools By Aadi Speed & Performance Turbo Engine
                    </h2>
                    <p style="color:var(--tba-text-muted); margin:0; font-size:13px;">Optimize Core Web Vitals (LCP, CLS, INP) for Google PageSpeed 95+ and instant mobile rendering.</p>
                </div>
                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" style="margin:0;">
                    <?php wp_nonce_field('tba_purge_speed_cache'); ?>
                    <input type="hidden" name="action" value="tba_purge_speed_cache">
                    <button type="submit" class="button button-primary" style="background:#d63638; border-color:#b32d2e; color:#ffffff; padding:6px 16px; font-weight:600; cursor:pointer;">
                        ⚡ Purge All Site Caches Now
                    </button>
                </form>
            </div>
        </div>

        <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
            <?php wp_nonce_field('tba_save_speed_settings'); ?>
            <input type="hidden" name="action" value="tba_save_speed_settings">

            <!-- Toolset 1: PageSpeed & Media Optimization -->
            <div class="tba-panel" style="margin-bottom:20px;">
                <div class="tba-panel-header">
                    <h3 class="tba-panel-title">🖼️ Media & Article Content Optimization</h3>
                </div>
                <div class="tba-panel-body">
                    <table class="form-table tba-settings-table">
                        <tr>
                            <th scope="row">
                                <label for="tba_speed_lazy_loading">Native Image Lazy Loading & Priority Hints</label>
                            </th>
                            <td>
                                <label class="tba-switch">
                                    <input type="checkbox" id="tba_speed_lazy_loading" name="tba_speed_lazy_loading" value="1" <?php checked($lazy_loading, '1'); ?>>
                                    <span class="tba-slider round"></span>
                                </label>
                                <p class="description">Automatically adds <code>loading="lazy"</code> to inline images/iframes and sets <code>fetchpriority="high"</code> on top featured images to boost LCP (Largest Contentful Paint).</p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="tba_speed_webp_compression">Auto WebP Conversion & 82% Compression</label>
                            </th>
                            <td>
                                <label class="tba-switch">
                                    <input type="checkbox" id="tba_speed_webp_compression" name="tba_speed_webp_compression" value="1" <?php checked($webp_compression, '1'); ?>>
                                    <span class="tba-slider round"></span>
                                </label>
                                <p class="description">Automatically converts all generated thumbnails and OpenGraph images into lightweight <code>.webp</code> format (saving ~75% disk space and reducing load times).</p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="tba_speed_html_minification">HTML Article Code Minification</label>
                            </th>
                            <td>
                                <label class="tba-switch">
                                    <input type="checkbox" id="tba_speed_html_minification" name="tba_speed_html_minification" value="1" <?php checked($html_minification, '1'); ?>>
                                    <span class="tba-slider round"></span>
                                </label>
                                <p class="description">Strips redundant whitespaces, double linebreaks, and blank space characters from generated HTML content before saving to WP database.</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Toolset 2: Server Cache & Script Execution -->
            <div class="tba-panel" style="margin-bottom:20px;">
                <div class="tba-panel-header">
                    <h3 class="tba-panel-title">🚀 Caching Engine & Script Optimization</h3>
                </div>
                <div class="tba-panel-body">
                    <table class="form-table tba-settings-table">
                        <tr>
                            <th scope="row">
                                <label for="tba_speed_auto_cache_purge">Auto Cache Flush on Publish</label>
                            </th>
                            <td>
                                <label class="tba-switch">
                                    <input type="checkbox" id="tba_speed_auto_cache_purge" name="tba_speed_auto_cache_purge" value="1" <?php checked($auto_cache_purge, '1'); ?>>
                                    <span class="tba-slider round"></span>
                                </label>
                                <p class="description">Triggers instant cache clearing for LiteSpeed, WP Rocket, WP Super Cache, Autoptimize, W3 Total Cache & OPcache whenever a new post is auto-published.</p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="tba_speed_preload_assets">Font & Style Resource Preloading</label>
                            </th>
                            <td>
                                <label class="tba-switch">
                                    <input type="checkbox" id="tba_speed_preload_assets" name="tba_speed_preload_assets" value="1" <?php checked($preload_assets, '1'); ?>>
                                    <span class="tba-slider round"></span>
                                </label>
                                <p class="description">Preloads key typography assets and inline TOC/Callout CSS rules to prevent Layout Shifts (CLS 0.00).</p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="tba_speed_defer_js">Non-Critical Script Deferring</label>
                            </th>
                            <td>
                                <label class="tba-switch">
                                    <input type="checkbox" id="tba_speed_defer_js" name="tba_speed_defer_js" value="1" <?php checked($defer_js, '1'); ?>>
                                    <span class="tba-slider round"></span>
                                </label>
                                <p class="description">Defers non-essential plugin scripts on post pages to prevent render-blocking JavaScript bottlenecks.</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Submit Button -->
            <div style="margin-top:20px; display:flex; gap:12px; align-items:center;">
                <button type="submit" class="tba-btn tba-btn-primary" style="padding:12px 25px; font-size:15px;">💾 Save Speed Optimization Tools</button>
            </div>
        </form>
    </div>
</div>
