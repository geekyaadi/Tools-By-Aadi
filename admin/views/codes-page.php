<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$header_code = get_option( 'tba_header_code', '' );
$footer_code = get_option( 'tba_footer_code', '' );
$ads_txt     = get_option( 'tba_ads_txt_content', '' );

$ads_txt_url = home_url( '/ads.txt' );
// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$saved = isset( $_GET['updated'] ) && $_GET['updated'] === 'true';
?>

<div class="tba-wrap">
    <!-- Header -->
    <div class="tba-header">
        <div class="tba-header-inner">
            <div class="tba-logo">
                <img src="<?php echo esc_url( TBA_PLUGIN_URL . 'admin/tools-by-aadi-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="tba-logo-badge">💻 Header, Footer & ads.txt</span>
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
        <a href="<?php echo esc_url( admin_url('admin.php?page=tba-randomizer') ); ?>" class="tba-nav-link">Date Randomizer</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-codes') ); ?>" class="tba-nav-link active">Codes</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-settings') ); ?>" class="tba-nav-link">Settings</a>
</div>
        </div>
    </div>

    <div class="tba-content">
        <?php if ( $saved ): ?>
            <div class="tba-alert tba-alert-success" style="margin-bottom:20px;">
                <strong>✅ Header, Footer & ads.txt Codes Saved Successfully!</strong> All custom scripts and ads.txt rules are active on your site.
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <?php wp_nonce_field( 'tba_codes_nonce' ); ?>
            <input type="hidden" name="action" value="tba_save_custom_codes">

            <div class="tba-two-col">
                <!-- Column 1: Header & Footer Scripts -->
                <div>
                    <div class="tba-panel">
                        <div class="tba-panel-header">
                            <h2 class="tba-panel-title">💻 Custom Header &amp; Footer Code Injector</h2>
                        </div>

                        <div class="tba-field">
                            <label class="tba-label">Header Code (Injected inside &lt;head&gt;)</label>
                            <textarea name="tba_header_code" class="tba-textarea" rows="7" placeholder="e.g. Google Analytics (gtag.js), Google Tag Manager, Meta Pixel, or custom <style> tags..."><?php echo esc_textarea( $header_code ); ?></textarea>
                            <div class="tba-hint">Placed right before <code>&lt;/head&gt;</code> on all frontend site pages.</div>
                        </div>

                        <div class="tba-field">
                            <label class="tba-label">Footer Code (Injected right before &lt;/body&gt;)</label>
                            <textarea name="tba_footer_code" class="tba-textarea" rows="7" placeholder="e.g. Chat widgets, tracking scripts, or custom JavaScript..."><?php echo esc_textarea( $footer_code ); ?></textarea>
                            <div class="tba-hint">Placed right before <code>&lt;/body&gt;</code> on all frontend site pages.</div>
                        </div>
                    </div>
                </div>

                <!-- Column 2: ads.txt Manager -->
                <div>
                    <div class="tba-panel">
                        <div class="tba-panel-header" style="display:flex; justify-content:space-between; align-items:center;">
                            <h2 class="tba-panel-title">💰 Google AdSense ads.txt Manager</h2>
                            <a href="<?php echo esc_url( $ads_txt_url ); ?>" target="_blank" class="button button-secondary button-small">↗️ View Live ads.txt</a>
                        </div>

                        <p style="color:var(--tba-text-muted); font-size:12px; margin-bottom:15px;">
                            Manage your official <code>ads.txt</code> records for Google AdSense, Ezoic, Mediavine, AdX, or setup sellers. This plugin automatically syncs and serves the live <code>ads.txt</code> file at <a href="<?php echo esc_url( $ads_txt_url ); ?>" target="_blank"><?php echo esc_url( $ads_txt_url ); ?></a>.
                        </p>

                        <div class="tba-field">
                            <label class="tba-label">ads.txt Content (1 seller entry per line)</label>
                            <textarea name="tba_ads_txt_content" class="tba-textarea" rows="15" style="font-family:monospace; font-size:12px;" placeholder="google.com, pub-0000000000000000, DIRECT, f00c287aed0ee64b&#10;ezoic.com, 12345, RESELLER"><?php echo esc_textarea( $ads_txt ); ?></textarea>
                            <div class="tba-hint">Example format: <code>google.com, pub-XXXXXXX, DIRECT, f00c287aed0ee64b</code></div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top:10px;">
                <button type="submit" class="button button-primary button-large" style="padding:6px 24px; font-size:14px;">💾 Save All Custom Codes &amp; Sync ads.txt</button>
            </div>
        </form>
    </div>
</div>
