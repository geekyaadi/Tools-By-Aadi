<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$ads_txt     = get_option( 'sab_ads_txt_content', '' );

$ads_txt_url = home_url( '/ads.txt' );
// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$saved = isset( $_GET['updated'] ) && $_GET['updated'] === 'true';
?>

<div class="sab-wrap">
    <!-- Header -->
    <div class="sab-header">
        <div class="sab-header-inner">
            <div class="sab-logo">
                <img src="<?php echo esc_url( SAB_PLUGIN_URL . 'admin/soniji-auto-blogging.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="sab-logo-badge">💰 ads.txt Manager</span>
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
        <a href="<?php echo esc_url( admin_url('admin.php?page=sab-randomizer') ); ?>" class="sab-nav-link">Date Randomizer</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-codes') ); ?>" class="sab-nav-link active">Codes</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-settings') ); ?>" class="sab-nav-link">Settings</a>
</div>
        </div>
    </div>

    <div class="sab-content">
        <?php if ( $saved ): ?>
            <div class="sab-alert sab-alert-success" style="margin-bottom:20px;">
                <strong>✅ ads.txt Saved Successfully!</strong> All ads.txt rules are active on your site.
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <?php wp_nonce_field( 'sab_codes_nonce' ); ?>
            <input type="hidden" name="action" value="sab_save_custom_codes">

            <div class="sab-panel" style="max-width: 800px; margin: 0 auto;">
                <div class="sab-panel-header" style="display:flex; justify-content:space-between; align-items:center;">
                    <h2 class="sab-panel-title">💰 Google AdSense ads.txt Manager</h2>
                    <a href="<?php echo esc_url( $ads_txt_url ); ?>" target="_blank" class="button button-secondary button-small">↗️ View Live ads.txt</a>
                </div>

                <p style="color:var(--sab-text-muted); font-size:12px; margin-bottom:15px;">
                    Manage your official <code>ads.txt</code> records for Google AdSense, Ezoic, Mediavine, AdX, or setup sellers. This plugin automatically syncs and serves the live <code>ads.txt</code> file at <a href="<?php echo esc_url( $ads_txt_url ); ?>" target="_blank"><?php echo esc_url( $ads_txt_url ); ?></a>.
                </p>

                <div class="sab-field">
                    <label class="sab-label">ads.txt Content (1 seller entry per line)</label>
                    <textarea name="sab_ads_txt_content" class="sab-textarea" rows="18" style="font-family:monospace; font-size:12px;" placeholder="google.com, pub-0000000000000000, DIRECT, f00c287aed0ee64b&#10;ezoic.com, 12345, RESELLER"><?php echo esc_textarea( $ads_txt ); ?></textarea>
                    <div class="sab-hint">Example format: <code>google.com, pub-XXXXXXX, DIRECT, f00c287aed0ee64b</code></div>
                </div>
            </div>

            <div style="margin-top:20px; text-align: center;">
                <button type="submit" class="button button-primary button-large" style="padding:6px 24px; font-size:14px;">💾 Save &amp; Sync ads.txt</button>
            </div>
        </form>
    </div>
</div>
