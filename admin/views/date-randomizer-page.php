<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$start_date   = get_option( 'tba_randomizer_start_date', gmdate( 'Y-m-d H:i:s', strtotime( '-90 days' ) ) );
$end_date     = get_option( 'tba_randomizer_end_date', gmdate( 'Y-m-d H:i:s' ) );
$rand_posts   = get_option( 'tba_randomizer_posts', '1' );
$rand_cmts    = get_option( 'tba_randomizer_comments', '1' );
$post_type    = get_option( 'tba_randomizer_post_type', 'post' );
$set_modified = get_option( 'tba_randomizer_modified_date', '1' );

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

<div class="tba-wrap">
    <!-- Header -->
    <div class="tba-header">
        <div class="tba-header-inner">
            <div class="tba-logo">
                <img src="<?php echo esc_url( TBA_PLUGIN_URL . 'admin/tools-by-aadi-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="tba-logo-badge">📅 Date Randomizer</span>
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
                <a href="<?php echo esc_url( admin_url('admin.php?page=tba-redirects') ); ?>" class="tba-nav-link">Redirect</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=tba-randomizer') ); ?>" class="tba-nav-link active">Date Randomizer</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=tba-codes') ); ?>" class="tba-nav-link">Codes</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=tba-settings') ); ?>" class="tba-nav-link">Settings</a>
            </div>
        </div>
    </div>

    <div class="tba-content">

        <?php if ( $saved ): ?>
            <div class="tba-alert tba-alert-success" style="margin-bottom:20px;">
                <strong>✅ Randomization Settings Saved Successfully!</strong>
            </div>
        <?php endif; ?>

        <?php if ( $run ): ?>
            <div class="tba-alert tba-alert-success" style="margin-bottom:20px;">
                <strong>🎉 Success! Successfully randomized publication dates for <?php echo (int) $run_count; ?> articles!</strong>
            </div>
        <?php endif; ?>

        <?php if ( $error === 'invalid_range' ): ?>
            <div class="tba-alert tba-alert-warning" style="margin-bottom:20px;">
                <strong>⚠️ Invalid Date Range!</strong> Please ensure Start Date is earlier than End Date.
            </div>
        <?php endif; ?>

        <!-- Warning Alert Banner -->
        <div class="tba-alert tba-alert-warning" style="margin-bottom:20px; background:#fef3c7; border-color:#f59e0b; color:#92400e;">
            <strong>⚠️ Warning:</strong> This action is irreversible. Please backup your database before randomizing dates.
        </div>

        <!-- PANEL 1: Randomization Settings -->
        <div class="tba-panel" style="margin-bottom:20px;">
            <div class="tba-panel-header">
                <h2 class="tba-panel-title">⚙️ Randomization Settings</h2>
            </div>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <?php wp_nonce_field( 'tba_randomizer_nonce' ); ?>
                <input type="hidden" name="action" value="tba_save_randomizer_settings">

                <div class="tba-field" style="margin-bottom:18px;">
                    <label class="tba-label" for="tba-start-date" style="font-weight:700;">Start Date:</label>
                    <input type="text" id="tba-start-date" name="start_date" class="tba-input" value="<?php echo esc_attr( $start_date ); ?>" style="max-width:400px; font-family:monospace;" placeholder="YYYY-MM-DD HH:MM:SS" required>
                    <div class="tba-hint" style="color:#64748b; font-size:12px; margin-top:4px;">Format: Y-m-d H:i:s (e.g. 2026-03-03 10:13:47)</div>
                </div>

                <div class="tba-field" style="margin-bottom:20px;">
                    <label class="tba-label" for="tba-end-date" style="font-weight:700;">End Date:</label>
                    <input type="text" id="tba-end-date" name="end_date" class="tba-input" value="<?php echo esc_attr( $end_date ); ?>" style="max-width:400px; font-family:monospace;" placeholder="YYYY-MM-DD HH:MM:SS" required>
                    <div class="tba-hint" style="color:#64748b; font-size:12px; margin-top:4px;">Format: Y-m-d H:i:s (e.g. 2026-06-14 10:13:47)</div>
                </div>

                <div class="tba-field" style="margin-bottom:15px;">
                    <label style="font-weight:600; font-size:14px; color:#1e293b; display:inline-flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" name="randomize_posts" value="1" <?php checked( $rand_posts, '1' ); ?> style="width:17px; height:17px; cursor:pointer;">
                        <span>Enable randomizing dates for published posts.</span>
                    </label>
                </div>

                <div class="tba-field" style="margin-bottom:20px;">
                    <label style="font-weight:600; font-size:14px; color:#1e293b; display:inline-flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" name="randomize_comments" value="1" <?php checked( $rand_cmts, '1' ); ?> style="width:17px; height:17px; cursor:pointer;">
                        <span>Enable randomizing dates for approved comments.</span>
                    </label>
                </div>

                <div class="tba-field" style="margin-bottom:20px;">
                    <label class="tba-label" for="tba-post-type" style="font-weight:700;">Post Type:</label>
                    <select id="tba-post-type" name="post_type" class="tba-select" style="max-width:300px;">
                        <?php foreach ( $post_types as $pt ): ?>
                            <option value="<?php echo esc_attr( $pt->name ); ?>" <?php selected( $post_type, $pt->name ); ?>>
                                <?php echo esc_html( $pt->label . ' (' . $pt->name . ')' ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="tba-hint" style="color:#64748b; font-size:12px; margin-top:4px;">Select the type of posts whose dates should be randomized.</div>
                </div>

                <div class="tba-field" style="margin-bottom:25px;">
                    <label style="font-weight:600; font-size:14px; color:#1e293b; display:inline-flex; align-items:center; gap:8px; cursor:pointer;">
                        <input type="checkbox" name="set_modified_date" value="1" <?php checked( $set_modified, '1' ); ?> style="width:17px; height:17px; cursor:pointer;">
                        <span>Update the "Last Modified" date to match the new randomized "Published" date.</span>
                    </label>
                </div>

                <button type="submit" class="tba-btn tba-btn-primary" style="padding:10px 24px; font-weight:600; font-size:14px;">💾 Save Settings</button>
            </form>
        </div>

        <!-- PANEL 2: Run Randomizer -->
        <div class="tba-panel" style="border:1px solid #fca5a5; background:#fff5f5;">
            <div class="tba-panel-header" style="border-bottom:1px solid #fecaca; padding-bottom:10px; margin-bottom:15px;">
                <h2 class="tba-panel-title" style="color:#b91c1c;">🎲 Run Randomizer</h2>
            </div>
            <p style="color:#475569; font-size:13px; margin-bottom:20px;">
                Click the button below to randomize dates according to the currently saved settings. Ensure you have saved any setting changes first.
            </p>

            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('⚠️ ARE YOU SURE? This will permanently update post dates in your database within the selected range!');">
                <?php wp_nonce_field( 'tba_randomizer_nonce' ); ?>
                <input type="hidden" name="action" value="tba_run_randomizer">
                <button type="submit" class="tba-btn tba-btn-danger" style="background:#dc2626; color:#ffffff; padding:12px 24px; font-weight:700; font-size:14px; border:none; border-radius:6px; cursor:pointer;">
                    🎲 Randomize Selected Item Dates Now
                </button>
            </form>
        </div>

    </div>
</div>
