<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$filter = isset( $_GET['filter'] ) ? sanitize_text_field( $_GET['filter'] ) : 'all';
$rules  = SAB_Redirects::get_redirect_rules();
$logs_404 = SAB_Redirects::get_404_logs( $filter );

// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$updated        = isset( $_GET['updated'] ) && $_GET['updated'] === 'true';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$deleted        = isset( $_GET['deleted'] ) && $_GET['deleted'] === 'true';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$converted      = isset( $_GET['converted'] ) && $_GET['converted'] === 'true';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$logs_cleared   = isset( $_GET['logs_cleared'] ) && $_GET['logs_cleared'] === 'true';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$settings_saved = isset( $_GET['settings_saved'] ) && $_GET['settings_saved'] === 'true';

$redirect_404_to_home = get_option( 'sab_redirect_404_to_home', '0' );
?>

<div class="sab-wrap">
    <!-- Header -->
    <div class="sab-header">
        <div class="sab-header-inner">
            <div class="sab-logo">
                <img src="<?php echo esc_url( SAB_PLUGIN_URL . 'admin/soniji-auto-blogging-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="sab-logo-badge">🔀 Redirect & 404 Manager</span>
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
                <a href="<?php echo esc_url( admin_url('admin.php?page=sab-sitemap') ); ?>" class="sab-nav-link">Sitemap</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=sab-pages') ); ?>" class="sab-nav-link">Pages Generator</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=sab-redirects') ); ?>" class="sab-nav-link active">Redirect</a>
                    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-randomizer') ); ?>" class="sab-nav-link">Date Randomizer</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-codes') ); ?>" class="sab-nav-link">Codes</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=sab-settings') ); ?>" class="sab-nav-link">Settings</a>
            </div>
        </div>
    </div>

    <div class="sab-content">
        <?php if ( $settings_saved ): ?>
            <div class="sab-alert sab-alert-success" style="margin-bottom:20px;">
                <strong>✅ 404 Auto-Redirect Settings Saved!</strong>
            </div>
        <?php endif; ?>

        <?php if ( $updated ): ?>
            <div class="sab-alert sab-alert-success" style="margin-bottom:20px;">
                <strong>✅ Redirect Rule Saved Successfully!</strong>
            </div>
        <?php endif; ?>

        <?php if ( $deleted ): ?>
            <div class="sab-alert sab-alert-success" style="margin-bottom:20px;">
                <strong>✅ Redirect Rule Deleted.</strong>
            </div>
        <?php endif; ?>

        <?php if ( $converted ): ?>
            <div class="sab-alert sab-alert-success" style="margin-bottom:20px;">
                <strong>🎉 404 Error Converted to 301 Permanent Redirect!</strong> Traffic will now be automatically rerouted to your destination URL.
            </div>
        <?php endif; ?>

        <?php if ( $logs_cleared ): ?>
            <div class="sab-alert sab-alert-success" style="margin-bottom:20px;">
                <strong>🧹 404 Error Logs Cleared Successfully.</strong>
            </div>
        <?php endif; ?>

        <!-- CARD 1: 404 Auto-Redirect Global Setting Toggle -->
        <div class="sab-panel" style="margin-bottom:20px;">
            <div class="sab-panel-header">
                <h2 class="sab-panel-title">🏠 Auto-Redirect 404 Errors to Home Page</h2>
            </div>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( 'sab_redirect_nonce' ); ?>
                <input type="hidden" name="action" value="sab_save_404_redirect_settings">

                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
                    <div>
                        <label style="font-weight:700; font-size:14px; color:#1e293b; display:inline-flex; align-items:center; gap:10px; cursor:pointer;">
                            <input type="checkbox" name="sab_redirect_404_to_home" value="1" <?php checked( $redirect_404_to_home, '1' ); ?> style="width:18px; height:18px; cursor:pointer;">
                            <span>Enable Automatic 301 Redirect for All 404 Pages to Homepage</span>
                        </label>
                        <div class="sab-hint" style="margin-top:6px; color:#475569; font-size:13px;">
                            When enabled, any broken 404 page or missing URL on your website will automatically perform a 301 Permanent Redirect to <code><?php echo esc_url( home_url('/') ); ?></code>, protecting site SEO rank and preventing visitor bounce.
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="sab-btn sab-btn-primary" style="padding:8px 20px;">💾 Save 404 Settings</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- CARD 2: Add New Redirect Rule & Active Rules (Full Width Card) -->
        <div class="sab-panel" style="margin-bottom:20px;">
            <div class="sab-panel-header">
                <h2 class="sab-panel-title">➕ Add Custom 301/302 Redirect Rule</h2>
            </div>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-bottom:25px;">
                <?php wp_nonce_field( 'sab_redirect_nonce' ); ?>
                <input type="hidden" name="action" value="sab_save_redirect_rule">

                <div style="display:grid; grid-template-columns: 1fr 1fr 180px 140px; gap:12px; align-items:end;">
                    <div>
                        <label class="sab-label">Source URL (Old Path)</label>
                        <input type="text" name="source_url" class="sab-input" placeholder="/old-article-slug" required>
                    </div>
                    <div>
                        <label class="sab-label">Target Destination URL</label>
                        <input type="text" name="target_url" class="sab-input" placeholder="https://yoursite.com/new-article-slug" required>
                    </div>
                    <div>
                        <label class="sab-label">HTTP Code</label>
                        <select name="redirect_type" class="sab-select">
                            <option value="301" selected>301 Permanent</option>
                            <option value="302">302 Temporary</option>
                            <option value="307">307 Strict</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="sab-btn sab-btn-primary sab-btn-full" style="padding:9px 15px;">💾 Add Rule</button>
                    </div>
                </div>
            </form>

            <div style="border-top:1px solid #e2e8f0; padding-top:15px;">
                <h3 style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:12px;">📋 Active Custom Redirect Rules (<?php echo count($rules); ?>)</h3>

                <?php if ( empty( $rules ) ): ?>
                    <p style="color:#64748b; font-size:13px;">No custom redirect rules created yet.</p>
                <?php else: ?>
                    <table class="sab-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Source Path</th>
                                <th>Target Destination</th>
                                <th width="100">Type</th>
                                <th width="90">Hits</th>
                                <th width="110">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $rules as $r ): ?>
                                <tr>
                                    <td><code><?php echo esc_html( $r->source_url ); ?></code></td>
                                    <td><a href="<?php echo esc_url( $r->target_url ); ?>" target="_blank" style="color:#0284c7; font-weight:500;"><?php echo esc_html( $r->target_url ); ?></a></td>
                                    <td><span class="sab-badge" style="background:#e0e7ff; color:#3730a3; padding:2px 8px; border-radius:4px; font-weight:600; font-size:11px;"><?php echo esc_html( $r->redirect_type ); ?></span></td>
                                    <td><strong><?php echo number_format( $r->hit_count ); ?></strong></td>
                                    <td>
                                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline;">
                                            <?php wp_nonce_field( 'sab_redirect_nonce' ); ?>
                                            <input type="hidden" name="action" value="sab_delete_redirect_rule">
                                            <input type="hidden" name="rule_id" value="<?php echo (int)$r->id; ?>">
                                            <button type="submit" class="sab-btn sab-btn-ghost sab-btn-small" onclick="return confirm('Delete this redirect rule?');" style="color:#dc2626; border-color:#fca5a5;">❌ Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- CARD 3: Full Size Card — Live 404 Error Monitor & Log Tracker -->
        <div class="sab-panel" style="width:100%;">
            <div class="sab-panel-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                <h2 class="sab-panel-title">🚨 Live 404 Error Monitor & Log Tracker</h2>
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:0;">
                    <?php wp_nonce_field( 'sab_redirect_nonce' ); ?>
                    <input type="hidden" name="action" value="sab_clear_404_logs">
                    <button type="submit" class="sab-btn sab-btn-ghost sab-btn-small" onclick="return confirm('Clear all 404 logs?');" style="color:#dc2626; border-color:#fca5a5;">🧹 Clear All 404 Logs</button>
                </form>
            </div>

            <!-- Filter Tabs -->
            <div style="margin-bottom:15px; border-bottom:1px solid #e2e8f0; padding-bottom:10px; display:flex; align-items:center; gap:8px;">
                <span style="font-weight:700; font-size:13px; margin-right:5px; color:#334155;">Filter 404 Logs:</span>
                <a href="<?php echo esc_url( admin_url('admin.php?page=sab-redirects&filter=all') ); ?>" class="sab-btn sab-btn-small <?php echo $filter === 'all' ? 'sab-btn-primary' : 'sab-btn-ghost'; ?>">All 404s</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=sab-redirects&filter=links') ); ?>" class="sab-btn sab-btn-small <?php echo $filter === 'links' ? 'sab-btn-primary' : 'sab-btn-ghost'; ?>">🔗 Pages & Links Only</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=sab-redirects&filter=images') ); ?>" class="sab-btn sab-btn-small <?php echo $filter === 'images' ? 'sab-btn-primary' : 'sab-btn-ghost'; ?>">🖼️ Missing Images Only</a>
            </div>

            <?php if ( empty( $logs_404 ) ): ?>
                <div class="sab-empty-state" style="text-align:center; padding:30px 0; color:#64748b;">
                    🎉 Great news! No 404 errors detected in this view filter.
                </div>
            <?php else: ?>
                <div style="width:100%; overflow-x:auto;">
                    <table class="sab-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th width="100">Type</th>
                                <th>Broken Request Path / URL</th>
                                <th width="110">Hits Count</th>
                                <th width="180">Last Detected</th>
                                <th width="280">1-Click Convert to 301</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $logs_404 as $log ): ?>
                                <tr>
                                    <td>
                                        <?php if ( $log->is_image ): ?>
                                            <span style="background:#fef3c7; color:#92400e; padding:3px 8px; border-radius:4px; font-size:11px; font-weight:700;">🖼️ Image</span>
                                        <?php else: ?>
                                            <span style="background:#e0f2fe; color:#075985; padding:3px 8px; border-radius:4px; font-size:11px; font-weight:700;">🔗 Page</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong style="color:#0f172a; word-break:break-all; font-size:13px;"><?php echo esc_html( $log->url ); ?></strong>
                                    </td>
                                    <td>
                                        <span class="sab-badge" style="background:#fee2e2; color:#991b1b; padding:3px 9px; border-radius:10px; font-weight:700; font-size:12px;"><?php echo number_format( $log->hit_count ); ?></span>
                                    </td>
                                    <td style="color:#64748b; font-size:12px; font-weight:500;">
                                        <?php echo esc_html( $log->last_detected ); ?>
                                    </td>
                                    <td>
                                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:flex; gap:6px; align-items:center;">
                                            <?php wp_nonce_field( 'sab_redirect_nonce' ); ?>
                                            <input type="hidden" name="action" value="sab_convert_404_to_301">
                                            <input type="hidden" name="source_url" value="<?php echo esc_attr( $log->url ); ?>">
                                            <input type="text" name="target_url" class="sab-input" placeholder="Destination URL..." style="font-size:12px; padding:4px 8px; flex:1; height:30px;" required>
                                            <button type="submit" class="sab-btn sab-btn-primary sab-btn-small" style="font-size:11px; height:30px; white-space:nowrap;">Fix 301</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>
