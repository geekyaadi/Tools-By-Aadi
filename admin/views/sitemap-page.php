<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$slug              = get_option( 'sab_sitemap_slug', 'sitemap.xml' );

$enable_home       = get_option( 'sab_sitemap_enable_home', '1' );
$home_priority     = get_option( 'sab_sitemap_priority_home', '1.0' );
$home_freq         = get_option( 'sab_sitemap_changefreq_home', 'daily' );

$enable_posts      = get_option( 'sab_sitemap_enable_posts', '1' );
$post_priority     = get_option( 'sab_sitemap_priority_post', '0.8' );
$post_freq         = get_option( 'sab_sitemap_changefreq_post', 'daily' );

$enable_pages      = get_option( 'sab_sitemap_enable_pages', '1' );
$page_priority     = get_option( 'sab_sitemap_priority_page', '0.6' );
$page_freq         = get_option( 'sab_sitemap_changefreq_page', 'weekly' );

$enable_cats       = get_option( 'sab_sitemap_enable_cats', '1' );
$cat_priority      = get_option( 'sab_sitemap_priority_cat', '0.5' );
$cat_freq          = get_option( 'sab_sitemap_changefreq_cat', 'weekly' );

$enable_tags       = get_option( 'sab_sitemap_enable_tags', '1' );
$tag_priority      = get_option( 'sab_sitemap_priority_tag', '0.4' );
$tag_freq          = get_option( 'sab_sitemap_changefreq_tag', 'monthly' );

$include_images    = get_option( 'sab_sitemap_include_images', '1' );
$auto_ping_google  = get_option( 'sab_sitemap_auto_ping_google', '1' );
$auto_ping_bing    = get_option( 'sab_sitemap_auto_ping_bing', '1' );

$sitemap_url = home_url( '/' . trim( $slug, '/' ) );
// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$saved = isset( $_GET['updated'] ) && $_GET['updated'] === 'true';
?>

<div class="sab-wrap">
    <!-- Header -->
    <div class="sab-header">
        <div class="sab-header-inner">
            <div class="sab-logo">
                <img src="<?php echo esc_url( SAB_PLUGIN_URL . 'admin/soniji-auto-blogging-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="sab-logo-badge">🗺️ XML Sitemap Controller</span>
            </div>
                        <div class="sab-header-nav">
    <a href="<?php echo esc_url( admin_url('admin.php?page=soniji-auto-blogging') ); ?>" class="sab-nav-link">Dashboard</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-generate') ); ?>" class="sab-nav-link">Generate Post</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-planner') ); ?>" class="sab-nav-link">Bulk Planner</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-scheduler') ); ?>" class="sab-nav-link">Scheduler</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-thumbnails') ); ?>" class="sab-nav-link">Thumbnail Tool</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-tags') ); ?>" class="sab-nav-link">Tags Tool</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-translator') ); ?>" class="sab-nav-link">Translator</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-gsc') ); ?>" class="sab-nav-link">Indexing</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-rewriter') ); ?>" class="sab-nav-link">Rewriter</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-speed') ); ?>" class="sab-nav-link">Optimizer</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-sitemap') ); ?>" class="sab-nav-link active">Sitemap</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-pages') ); ?>" class="sab-nav-link">Pages Generator</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-redirects') ); ?>" class="sab-nav-link">Redirect</a>
        <a href="<?php echo esc_url( admin_url('admin.php?page=sab-randomizer') ); ?>" class="sab-nav-link">Date Randomizer</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-codes') ); ?>" class="sab-nav-link">Codes</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-settings') ); ?>" class="sab-nav-link">Settings</a>
</div>
        </div>
    </div>

    <div class="sab-content">
        <?php if ( $saved ): ?>
            <div class="sab-alert sab-alert-success" style="margin-bottom:20px;">
                <strong>✅ XML Sitemap Settings Saved & Rules Flushed!</strong> Live Sitemap: <a href="<?php echo esc_url($sitemap_url); ?>" target="_blank" style="color:#15803d; font-weight:700; text-decoration:underline;"><?php echo esc_html($sitemap_url); ?> 🔗</a>
            </div>
        <?php endif; ?>

        <!-- Hero Card -->
        <div class="sab-panel" style="margin-bottom:20px;">
            <div class="sab-panel-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; border-bottom:1px solid #e2e8f0; padding-bottom:12px; margin-bottom:15px;">
                <div>
                    <h2 class="sab-panel-title" style="font-size:16px; font-weight:600; margin:0 0 4px 0; color:var(--sab-text-dark);">
                        🗺️ Complete XML Sitemap Index & Priority Controller
                    </h2>
                    <p style="color:var(--sab-text-muted); margin:0; font-size:13px;">Manage Site URL, Posts, Pages, Categories & Tags with custom priority weights and change frequency.</p>
                </div>
                <div>
                    <a href="<?php echo esc_url($sitemap_url); ?>" target="_blank" class="button button-primary">
                        🌐 View Live XML Sitemap ↗
                    </a>
                </div>
            </div>
        </div>

        <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
            <?php wp_nonce_field('sab_save_sitemap_settings'); ?>
            <input type="hidden" name="action" value="sab_save_sitemap_settings">

            <!-- Card 1: Main Sitemap Index Name & Slug -->
            <div class="sab-panel" style="margin-bottom:20px;">
                <div class="sab-panel-header">
                    <h3 class="sab-panel-title">🏷️ Sitemap Architecture &amp; Sub-Sitemaps Index</h3>
                </div>
                <div class="sab-panel-body">
                    <table class="form-table sab-settings-table">
                        <tr>
                            <th scope="row">
                                <label for="sab_sitemap_slug">Main Sitemap Index Slug</label>
                            </th>
                            <td>
                                <input type="text" id="sab_sitemap_slug" name="sab_sitemap_slug" value="<?php echo esc_attr($slug); ?>" class="regular-text" style="font-family:monospace; font-weight:600;" placeholder="sitemap.xml">
                                <p class="description">Main Index URL: <code><?php echo esc_html(home_url('/')); ?><strong><?php echo esc_html($slug); ?></strong></code> or <code><?php echo esc_html(home_url('/sitemap_index.xml')); ?></code>.</p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="sab_sitemap_mode">Sitemap Structure Mode</label>
                            </th>
                            <td>
                                <select id="sab_sitemap_mode" name="sab_sitemap_mode" class="regular-text">
                                    <option value="index" <?php selected(get_option('sab_sitemap_mode', 'index'), 'index'); ?>>🗺️ Modular Sitemap Index (RankMath / Yoast Style - Recommended)</option>
                                    <option value="single" <?php selected(get_option('sab_sitemap_mode', 'index'), 'single'); ?>>📄 Single Combined XML File</option>
                                </select>
                                <p class="description">Modular Index organizes posts, pages, categories, and tags into separate specialized sub-sitemaps for faster crawling.</p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">Active Modular Sub-Sitemaps</th>
                            <td>
                                <div style="background:#f8fafc; border:1px solid #cbd5e1; padding:12px 16px; border-radius:6px; font-family:monospace; font-size:12px;">
                                    <div style="margin-bottom:6px;">📝 Posts Sitemap: <a href="<?php echo esc_url(home_url('/post-sitemap.xml')); ?>" target="_blank" style="color:#2563eb; font-weight:600;"><?php echo esc_html(home_url('/post-sitemap.xml')); ?> ↗</a></div>
                                    <div style="margin-bottom:6px;">📄 Pages Sitemap: <a href="<?php echo esc_url(home_url('/page-sitemap.xml')); ?>" target="_blank" style="color:#2563eb; font-weight:600;"><?php echo esc_html(home_url('/page-sitemap.xml')); ?> ↗</a></div>
                                    <div style="margin-bottom:6px;">📁 Categories Sitemap: <a href="<?php echo esc_url(home_url('/category-sitemap.xml')); ?>" target="_blank" style="color:#2563eb; font-weight:600;"><?php echo esc_html(home_url('/category-sitemap.xml')); ?> ↗</a></div>
                                    <div>🏷️ Tags Sitemap: <a href="<?php echo esc_url(home_url('/tag-sitemap.xml')); ?>" target="_blank" style="color:#2563eb; font-weight:600;"><?php echo esc_html(home_url('/tag-sitemap.xml')); ?> ↗</a></div>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="sab_sitemap_include_images">Include Image Metadata</label>
                            </th>
                            <td>
                                <label class="sab-switch">
                                    <input type="checkbox" id="sab_sitemap_include_images" name="sab_sitemap_include_images" value="1" <?php checked($include_images, '1'); ?>>
                                    <span class="sab-slider round"></span>
                                </label>
                                <p class="description">Includes featured image tags (<code>&lt;image:image&gt;</code>) for Google Images indexing.</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Card 2: Individual Priority & Frequency Controller Matrix -->
            <div class="sab-panel" style="margin-bottom:20px;">
                <div class="sab-panel-header">
                    <h3 class="sab-panel-title">📊 Content Sections, Priority & Change Frequency Matrix</h3>
                </div>
                <div class="sab-panel-body">
                    <table class="wp-list-table widefat fixed striped" style="border-radius:8px; overflow:hidden;">
                        <thead>
                            <tr>
                                <th style="width:25%;">Section / Content Type</th>
                                <th style="width:15%;">Enable in Sitemap</th>
                                <th style="width:30%;">Priority Weight (0.1 - 1.0)</th>
                                <th style="width:30%;">Change Frequency</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Site URL / Homepage -->
                            <tr>
                                <td><strong>🏠 Homepage (Site URL)</strong></td>
                                <td>
                                    <label class="sab-switch">
                                        <input type="checkbox" name="sab_sitemap_enable_home" value="1" <?php checked($enable_home, '1'); ?>>
                                        <span class="sab-slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <select name="sab_sitemap_priority_home" style="width:100%;">
                                        <?php foreach(['1.0', '0.9', '0.8', '0.7', '0.6', '0.5'] as $p): ?>
                                            <option value="<?php echo esc_html( $p ); ?>" <?php selected($home_priority, $p); ?>><?php echo esc_html( $p ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="sab_sitemap_changefreq_home" style="width:100%;">
                                        <?php foreach(['always', 'hourly', 'daily', 'weekly', 'monthly'] as $f): ?>
                                            <option value="<?php echo esc_html( $f ); ?>" <?php selected($home_freq, $f); ?>><?php echo esc_html( ucfirst($f) ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>

                            <!-- Posts -->
                            <tr>
                                <td><strong>📝 Articles (Posts)</strong></td>
                                <td>
                                    <label class="sab-switch">
                                        <input type="checkbox" name="sab_sitemap_enable_posts" value="1" <?php checked($enable_posts, '1'); ?>>
                                        <span class="sab-slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <select name="sab_sitemap_priority_post" style="width:100%;">
                                        <?php foreach(['1.0', '0.9', '0.8', '0.7', '0.6', '0.5'] as $p): ?>
                                            <option value="<?php echo esc_html( $p ); ?>" <?php selected($post_priority, $p); ?>><?php echo esc_html( $p ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="sab_sitemap_changefreq_post" style="width:100%;">
                                        <?php foreach(['always', 'hourly', 'daily', 'weekly', 'monthly'] as $f): ?>
                                            <option value="<?php echo esc_html( $f ); ?>" <?php selected($post_freq, $f); ?>><?php echo esc_html( ucfirst($f) ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>

                            <!-- Pages -->
                            <tr>
                                <td><strong>📄 Pages</strong></td>
                                <td>
                                    <label class="sab-switch">
                                        <input type="checkbox" name="sab_sitemap_enable_pages" value="1" <?php checked($enable_pages, '1'); ?>>
                                        <span class="sab-slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <select name="sab_sitemap_priority_page" style="width:100%;">
                                        <?php foreach(['1.0', '0.9', '0.8', '0.7', '0.6', '0.5', '0.4'] as $p): ?>
                                            <option value="<?php echo esc_html( $p ); ?>" <?php selected($page_priority, $p); ?>><?php echo esc_html( $p ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="sab_sitemap_changefreq_page" style="width:100%;">
                                        <?php foreach(['always', 'hourly', 'daily', 'weekly', 'monthly'] as $f): ?>
                                            <option value="<?php echo esc_html( $f ); ?>" <?php selected($page_freq, $f); ?>><?php echo esc_html( ucfirst($f) ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>

                            <!-- Categories (Cats) -->
                            <tr>
                                <td><strong>📁 Categories (Cats)</strong></td>
                                <td>
                                    <label class="sab-switch">
                                        <input type="checkbox" name="sab_sitemap_enable_cats" value="1" <?php checked($enable_cats, '1'); ?>>
                                        <span class="sab-slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <select name="sab_sitemap_priority_cat" style="width:100%;">
                                        <?php foreach(['0.8', '0.7', '0.6', '0.5', '0.4', '0.3'] as $p): ?>
                                            <option value="<?php echo esc_html( $p ); ?>" <?php selected($cat_priority, $p); ?>><?php echo esc_html( $p ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="sab_sitemap_changefreq_cat" style="width:100%;">
                                        <?php foreach(['always', 'hourly', 'daily', 'weekly', 'monthly'] as $f): ?>
                                            <option value="<?php echo esc_html( $f ); ?>" <?php selected($cat_freq, $f); ?>><?php echo esc_html( ucfirst($f) ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>

                            <!-- Tags -->
                            <tr>
                                <td><strong>🏷️ Tags</strong></td>
                                <td>
                                    <label class="sab-switch">
                                        <input type="checkbox" name="sab_sitemap_enable_tags" value="1" <?php checked($enable_tags, '1'); ?>>
                                        <span class="sab-slider round"></span>
                                    </label>
                                </td>
                                <td>
                                    <select name="sab_sitemap_priority_tag" style="width:100%;">
                                        <?php foreach(['0.6', '0.5', '0.4', '0.3', '0.2', '0.1'] as $p): ?>
                                            <option value="<?php echo esc_html( $p ); ?>" <?php selected($tag_priority, $p); ?>><?php echo esc_html( $p ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <select name="sab_sitemap_changefreq_tag" style="width:100%;">
                                        <?php foreach(['always', 'hourly', 'daily', 'weekly', 'monthly'] as $f): ?>
                                            <option value="<?php echo esc_html( $f ); ?>" <?php selected($tag_freq, $f); ?>><?php echo esc_html( ucfirst($f) ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Card 3: Auto Pinging Search Engines -->
            <div class="sab-panel" style="margin-bottom:20px;">
                <div class="sab-panel-header">
                    <h3 class="sab-panel-title">🚀 Instant Search Engine Auto-Ping</h3>
                </div>
                <div class="sab-panel-body">
                    <table class="form-table sab-settings-table">
                        <tr>
                            <th scope="row"><label for="sab_sitemap_auto_ping_google">Auto-Ping Google on Publish</label></th>
                            <td>
                                <label class="sab-switch">
                                    <input type="checkbox" id="sab_sitemap_auto_ping_google" name="sab_sitemap_auto_ping_google" value="1" <?php checked($auto_ping_google, '1'); ?>>
                                    <span class="sab-slider round"></span>
                                </label>
                                <p class="description">Notifies Google (<code>google.com/ping?sitemap=...</code>) on article publish.</p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="sab_sitemap_auto_ping_bing">Auto-Ping Bing on Publish</label></th>
                            <td>
                                <label class="sab-switch">
                                    <input type="checkbox" id="sab_sitemap_auto_ping_bing" name="sab_sitemap_auto_ping_bing" value="1" <?php checked($auto_ping_bing, '1'); ?>>
                                    <span class="sab-slider round"></span>
                                </label>
                                <p class="description">Notifies Bing (<code>bing.com/ping?sitemap=...</code>) on article publish.</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div style="margin-top:20px;">
                <button type="submit" class="sab-btn sab-btn-primary" style="padding:12px 25px; font-size:15px; background:#4f46e5; border:none;">💾 Save Sitemap Matrix Settings</button>
            </div>
        </form>
    </div>
</div>
