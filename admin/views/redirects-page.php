<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$filter = isset( $_GET['filter'] ) ? sanitize_text_field( $_GET['filter'] ) : 'all';
$rules  = TBA_Redirects::get_redirect_rules();
$logs_404 = TBA_Redirects::get_404_logs( $filter );

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

$redirect_404_to_home = get_option( 'tba_redirect_404_to_home', '0' );
?>

<div class="tba-wrap">
    <!-- Header -->
    <div class="tba-header">
        <div class="tba-header-inner">
            <div class="tba-logo">
                <img src="<?php echo esc_url( TBA_PLUGIN_URL . 'admin/tools-by-aadi-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="tba-logo-badge">🔀 Redirect & 404 Manager</span>
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
                <a href="<?php echo esc_url( admin_url('admin.php?page=tba-speed') ); ?>" class="tba-nav-link">Optimizer</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=tba-sitemap') ); ?>" class="tba-nav-link">Sitemap</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=tba-pages') ); ?>" class="tba-nav-link">Pages Generator</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=tba-redirects') ); ?>" class="tba-nav-link active">Redirect</a>
                    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-randomizer') ); ?>" class="tba-nav-link">Date Randomizer</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-codes') ); ?>" class="tba-nav-link">Codes</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=tba-settings') ); ?>" class="tba-nav-link">Settings</a>
            </div>
        </div>
    </div>

    <div class="tba-content">
        <?php if ( $settings_saved ): ?>
            <div class="tba-alert tba-alert-success" style="margin-bottom:20px;">
                <strong>✅ 404 Auto-Redirect Settings Saved!</strong>
            </div>
        <?php endif; ?>

        <?php if ( $updated ): ?>
            <div class="tba-alert tba-alert-success" style="margin-bottom:20px;">
                <strong>✅ Redirect Rule Saved Successfully!</strong>
            </div>
        <?php endif; ?>

        <?php if ( $deleted ): ?>
            <div class="tba-alert tba-alert-success" style="margin-bottom:20px;">
                <strong>✅ Redirect Rule Deleted.</strong>
            </div>
        <?php endif; ?>

        <?php if ( $converted ): ?>
            <div class="tba-alert tba-alert-success" style="margin-bottom:20px;">
                <strong>🎉 404 Error Converted to 301 Permanent Redirect!</strong> Traffic will now be automatically rerouted to your destination URL.
            </div>
        <?php endif; ?>

        <?php if ( $logs_cleared ): ?>
            <div class="tba-alert tba-alert-success" style="margin-bottom:20px;">
                <strong>🧹 404 Error Logs Cleared Successfully.</strong>
            </div>
        <?php endif; ?>

        <!-- CARD 1: 404 Auto-Redirect Global Setting Toggle -->
        <div class="tba-panel" style="margin-bottom:20px;">
            <div class="tba-panel-header">
                <h2 class="tba-panel-title">🏠 Auto-Redirect 404 Errors to Home Page</h2>
            </div>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( 'tba_redirect_nonce' ); ?>
                <input type="hidden" name="action" value="tba_save_404_redirect_settings">

                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
                    <div>
                        <label style="font-weight:700; font-size:14px; color:#1e293b; display:inline-flex; align-items:center; gap:10px; cursor:pointer;">
                            <input type="checkbox" name="tba_redirect_404_to_home" value="1" <?php checked( $redirect_404_to_home, '1' ); ?> style="width:18px; height:18px; cursor:pointer;">
                            <span>Enable Automatic 301 Redirect for All 404 Pages to Homepage</span>
                        </label>
                        <div class="tba-hint" style="margin-top:6px; color:#475569; font-size:13px;">
                            When enabled, any broken 404 page or missing URL on your website will automatically perform a 301 Permanent Redirect to <code><?php echo esc_url( home_url('/') ); ?></code>, protecting site SEO rank and preventing visitor bounce.
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="tba-btn tba-btn-primary" style="padding:8px 20px;">💾 Save 404 Settings</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- CARD 2: Add New Redirect Rule & Active Rules (Full Width Card) -->
        <div class="tba-panel" style="margin-bottom:20px;">
            <div class="tba-panel-header">
                <h2 class="tba-panel-title">➕ Add Custom 301/302 Redirect Rule</h2>
            </div>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-bottom:25px;">
                <?php wp_nonce_field( 'tba_redirect_nonce' ); ?>
                <input type="hidden" name="action" value="tba_save_redirect_rule">

                <div style="display:grid; grid-template-columns: 1fr 1fr 180px 140px; gap:12px; align-items:end;">
                    <div>
                        <label class="tba-label">Source URL (Old Path)</label>
                        <input type="text" name="source_url" class="tba-input" placeholder="/old-article-slug" required>
                    </div>
                    <div>
                        <label class="tba-label">Target Destination URL</label>
                        <input type="text" name="target_url" class="tba-input" placeholder="https://yoursite.com/new-article-slug" required>
                    </div>
                    <div>
                        <label class="tba-label">HTTP Code</label>
                        <select name="redirect_type" class="tba-select">
                            <option value="301" selected>301 Permanent</option>
                            <option value="302">302 Temporary</option>
                            <option value="307">307 Strict</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="tba-btn tba-btn-primary tba-btn-full" style="padding:9px 15px;">💾 Add Rule</button>
                    </div>
                </div>
            </form>

            <div style="border-top:1px solid #e2e8f0; padding-top:15px;">
                <h3 style="font-size:14px; font-weight:700; color:#0f172a; margin-bottom:12px;">📋 Active Custom Redirect Rules (<?php echo count($rules); ?>)</h3>

                <?php if ( empty( $rules ) ): ?>
                    <p style="color:#64748b; font-size:13px;">No custom redirect rules created yet.</p>
                <?php else: ?>
                    <table class="tba-table" style="width:100%;">
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
                                    <td><span class="tba-badge" style="background:#e0e7ff; color:#3730a3; padding:2px 8px; border-radius:4px; font-weight:600; font-size:11px;"><?php echo esc_html( $r->redirect_type ); ?></span></td>
                                    <td><strong><?php echo number_format( $r->hit_count ); ?></strong></td>
                                    <td>
                                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline;">
                                            <?php wp_nonce_field( 'tba_redirect_nonce' ); ?>
                                            <input type="hidden" name="action" value="tba_delete_redirect_rule">
                                            <input type="hidden" name="rule_id" value="<?php echo (int)$r->id; ?>">
                                            <button type="submit" class="tba-btn tba-btn-ghost tba-btn-small" onclick="return confirm('Delete this redirect rule?');" style="color:#dc2626; border-color:#fca5a5;">❌ Delete</button>
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
        <div class="tba-panel" style="width:100%;">
            <div class="tba-panel-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                <h2 class="tba-panel-title">🚨 Live 404 Error Monitor & Log Tracker</h2>
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:0;">
                    <?php wp_nonce_field( 'tba_redirect_nonce' ); ?>
                    <input type="hidden" name="action" value="tba_clear_404_logs">
                    <button type="submit" class="tba-btn tba-btn-ghost tba-btn-small" onclick="return confirm('Clear all 404 logs?');" style="color:#dc2626; border-color:#fca5a5;">🧹 Clear All 404 Logs</button>
                </form>
            </div>

            <!-- Filter Tabs -->
            <div style="margin-bottom:15px; border-bottom:1px solid #e2e8f0; padding-bottom:10px; display:flex; align-items:center; gap:8px;">
                <span style="font-weight:700; font-size:13px; margin-right:5px; color:#334155;">Filter 404 Logs:</span>
                <a href="<?php echo esc_url( admin_url('admin.php?page=tba-redirects&filter=all') ); ?>" class="tba-btn tba-btn-small <?php echo $filter === 'all' ? 'tba-btn-primary' : 'tba-btn-ghost'; ?>">All 404s</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=tba-redirects&filter=links') ); ?>" class="tba-btn tba-btn-small <?php echo $filter === 'links' ? 'tba-btn-primary' : 'tba-btn-ghost'; ?>">🔗 Pages & Links Only</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=tba-redirects&filter=images') ); ?>" class="tba-btn tba-btn-small <?php echo $filter === 'images' ? 'tba-btn-primary' : 'tba-btn-ghost'; ?>">🖼️ Missing Images Only</a>
            </div>

            <?php if ( empty( $logs_404 ) ): ?>
                <div class="tba-empty-state" style="text-align:center; padding:30px 0; color:#64748b;">
                    🎉 Great news! No 404 errors detected in this view filter.
                </div>
            <?php else: ?>
                <div style="width:100%; overflow-x:auto;">
                    <table class="tba-table" style="width:100%;">
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
                                        <span class="tba-badge" style="background:#fee2e2; color:#991b1b; padding:3px 9px; border-radius:10px; font-weight:700; font-size:12px;"><?php echo number_format( $log->hit_count ); ?></span>
                                    </td>
                                    <td style="color:#64748b; font-size:12px; font-weight:500;">
                                        <?php echo esc_html( $log->last_detected ); ?>
                                    </td>
                                    <td>
                                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:flex; gap:6px; align-items:center;">
                                            <?php wp_nonce_field( 'tba_redirect_nonce' ); ?>
                                            <input type="hidden" name="action" value="tba_convert_404_to_301">
                                            <input type="hidden" name="source_url" value="<?php echo esc_attr( $log->url ); ?>">
                                            <input type="text" name="target_url" class="tba-input" placeholder="Destination URL..." style="font-size:12px; padding:4px 8px; flex:1; height:30px;" required>
                                            <button type="submit" class="tba-btn tba-btn-primary tba-btn-small" style="font-size:11px; height:30px; white-space:nowrap;">Fix 301</button>
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
