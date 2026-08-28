<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$start_date   = get_option( 'sab_randomizer_start_date', gmdate( 'Y-m-d H:i:s', strtotime( '-90 days' ) ) );
$end_date     = get_option( 'sab_randomizer_end_date', gmdate( 'Y-m-d H:i:s' ) );
$rand_posts   = get_option( 'sab_randomizer_posts', '1' );
$rand_cmts    = get_option( 'sab_randomizer_comments', '1' );
$post_type    = get_option( 'sab_randomizer_post_type', 'post' );
$set_modified = get_option( 'sab_randomizer_modified_date', '1' );

// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$saved     = isset( $_GET['saved'] ) && $_GET['saved'] === 'true';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$error     = isset( $_GET['error'] ) ? sanitize_text_field( $_GET['error'] ) : '';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$run       = isset( $_GET['run'] ) && $_GET['run'] === 'success';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$run_count = isset( $_GET['count'] ) ? (int) $_GET['count'] : 0;

$post_types = get_post_types( [ 'public' => true ], 'objects' );
?>

<div class="sab-wrap">
    <!-- Header -->
    <div class="sab-header">
        <div class="sab-header-inner">
            <div class="sab-logo">
                <img src="<?php echo esc_url( SAB_PLUGIN_URL . 'admin/soniji-auto-blogging-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="sab-logo-badge">📅 Date Randomizer</span>
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
                <a href="<?php echo esc_url( admin_url('admin.php?page=sab-redirects') ); ?>" class="sab-nav-link">Redirect</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=sab-randomizer') ); ?>" class="sab-nav-link active">Date Randomizer</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=sab-codes') ); ?>" class="sab-nav-link">Codes</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=sab-settings') ); ?>" class="sab-nav-link">Settings</a>
            </div>
        </div>
    </div>

    <div class="sab-content">

        <?php if ( $saved ): ?>
            <div class="sab-alert sab-alert-success" style="margin-bottom:20px;">
                <strong>✅ Randomization Settings Saved Successfully!</strong>
            </div>
        <?php endif; ?>

        <?php if ( $run ): ?>
            <div class="sab-alert sab-alert-success" style="margin-bottom:20px;">
                <strong>🎉 Success! Successfully randomized publication dates for <?php echo (int) $run_count; ?> articles!</strong>
            </div>
        <?php endif; ?>

        <?php if ( $error === 'invalid_range' ): ?>
            <div class="sab-alert sab-alert-warning" style="margin-bottom:20px;">
                <strong>⚠️ Invalid Date Range!</strong> Please ensure Start Date is earlier than End Date.
            </div>
        <?php endif; ?>

        <!-- Warning Alert Banner -->
        <div class="sab-alert sab-alert-warning" style="margin-bottom:20px; background:#fef3c7; border-color:#f59e0b; color:#92400e;">
            <strong>⚠️ Warning:</strong> This action is irreversible. Please backup your database before randomizing dates.
        </div>

        <!-- PANEL 1: Randomization Settings -->
        <div class="sab-panel" style="margin-bottom:20px;">
            <div class="sab-panel-header">
                <h2 class="sab-panel-title">⚙️ Randomization Settings</h2>
            </div>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( 'sab_randomizer_nonce' ); ?>
                <input type="hidden" name="action" value="sab_save_randomizer_settings">

                <div class="sab-field" style="margin-bottom:18px;">
                    <label class="sab-label" for="sab-start-date" style="font-weight:700;">Start Date:</label>
                    <input type="text" id="sab-start-date" name="start_date" class="sab-input" value="<?php echo esc_attr( $start_date ); ?>" style="max-width:400px; font-family:monospace;" placeholder="YYYY-MM-DD HH:MM:SS" required>
                    <div class="sab-hint" style="color:#64748b; font-size:12px; margin-top:4px;">Format: Y-m-d H:i:s (e.g. 2026-03-03 10:13:47)</div>
                </div>

                <div class="sab-field" style="margin-bottom:20px;">
                    <label class="sab-label" for="sab-end-date" style="font-weight:700;">End Date:</label>
                    <input type="text" id="sab-end-date" name="end_date" class="sab-input" value="<?php echo esc_attr( $end_date ); ?>" style="max-width:400px; font-family:monospace;" placeholder="YYYY-MM-DD HH:MM:SS" required>
                    <div class="sab-hint" style="color:#64748b; font-size:12px; margin-top:4px;">Format: Y-m-d H:i:s (e.g. 2026-06-14 10:13:47)</div>
                </div>

                <div class="sab-field" style="margin-bottom:15px;">
                    <label style="font-weight:600; font-size:14px; color:#1e293b; display:inline-flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" name="randomize_posts" value="1" <?php checked( $rand_posts, '1' ); ?> style="width:17px; height:17px; cursor:pointer;">
                        <span>Enable randomizing dates for published posts.</span>
                    </label>
                </div>

                <div class="sab-field" style="margin-bottom:20px;">
                    <label style="font-weight:600; font-size:14px; color:#1e293b; display:inline-flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" name="randomize_comments" value="1" <?php checked( $rand_cmts, '1' ); ?> style="width:17px; height:17px; cursor:pointer;">
                        <span>Enable randomizing dates for approved comments.</span>
                    </label>
                </div>

                <div class="sab-field" style="margin-bottom:20px;">
                    <label class="sab-label" for="sab-post-type" style="font-weight:700;">Post Type:</label>
                    <select id="sab-post-type" name="post_type" class="sab-select" style="max-width:300px;">
                        <?php foreach ( $post_types as $pt ): ?>
                            <option value="<?php echo esc_attr( $pt->name ); ?>" <?php selected( $post_type, $pt->name ); ?>>
                                <?php echo esc_html( $pt->label . ' (' . $pt->name . ')' ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="sab-hint" style="color:#64748b; font-size:12px; margin-top:4px;">Select the type of posts whose dates should be randomized.</div>
                </div>

                <div class="sab-field" style="margin-bottom:25px;">
                    <label style="font-weight:600; font-size:14px; color:#1e293b; display:inline-flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" name="set_modified_date" value="1" <?php checked( $set_modified, '1' ); ?> style="width:17px; height:17px; cursor:pointer;">
                        <span>Update the "Last Modified" date to match the new randomized "Published" date.</span>
                    </label>
                </div>

                <button type="submit" class="sab-btn sab-btn-primary" style="padding:10px 24px; font-weight:600; font-size:14px;">💾 Save Settings</button>
            </form>
        </div>

        <!-- PANEL 2: Run Randomizer -->
        <div class="sab-panel" style="border:1px solid #fca5a5; background:#fff5f5;">
            <div class="sab-panel-header" style="border-bottom:1px solid #fecaca; padding-bottom:10px; margin-bottom:15px;">
                <h2 class="sab-panel-title" style="color:#b91c1c;">🎲 Run Randomizer</h2>
            </div>
            <p style="color:#475569; font-size:13px; margin-bottom:20px;">
                Click the button below to randomize dates according to the currently saved settings. Ensure you have saved any setting changes first.
            </p>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('⚠️ ARE YOU SURE? This will permanently update post dates in your database within the selected range!');">
                <?php wp_nonce_field( 'sab_randomizer_nonce' ); ?>
                <input type="hidden" name="action" value="sab_run_randomizer">
                <button type="submit" class="sab-btn sab-btn-danger" style="background:#dc2626; color:#ffffff; padding:12px 24px; font-weight:700; font-size:14px; border:none; border-radius:6px; cursor:pointer;">
                    🎲 Randomize Selected Item Dates Now
                </button>
            </form>
        </div>

    </div>
</div>
