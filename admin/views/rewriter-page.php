<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$paged = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1;
$posts_per_page = 10;

// Query published posts with pagination
$args = [
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
];
$query = new WP_Query( $args );
$posts = $query->posts;
$total_posts = $query->found_posts;
$total_pages = $query->max_num_pages;
?>

<div class="tba-wrap">
    <div class="tba-header">
        <div class="tba-header-inner">
            <div class="tba-logo">
                <img src="<?php echo esc_url( TBA_PLUGIN_URL . 'admin/tools-by-aadi-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="tba-logo-badge">AI Article Rewriter</span>
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
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-rewriter') ); ?>" class="tba-nav-link active">Rewriter</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-speed') ); ?>" class="tba-nav-link">Optimizer</a>
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

        <!-- Info Panel -->
        <div class="tba-panel" style="margin-bottom:20px;">
            <div class="tba-panel-header">
                <h2 class="tba-panel-title">✍️ AI Article Rewriter & Freshness Updater</h2>
            </div>
            <div class="tba-hint" style="padding:10px 0 0;">
                Select any published post to rewrite & freshen its content using AI. You can optionally add custom instructions (e.g., "make it more conversational", "add latest 2026 updates", "shorten the article"). The AI will preserve the topic and heading structure while improving readability and SEO.
            </div>
        </div>

        <!-- Posts List -->
        <div class="tba-panel">
            <div class="tba-panel-header">
                <h2 class="tba-panel-title">📄 Published Posts (Total: <?php echo (int) $total_posts; ?>)</h2>
            </div>

            <?php if ( empty($posts) ): ?>
            <div class="tba-empty-state">No published posts found.</div>
            <?php else: ?>
            <div class="tba-rewriter-list">
                <?php foreach ( $posts as $p ):
                    $word_count = str_word_count( wp_strip_all_tags( $p->post_content ) );
                ?>
                <div class="tba-rewriter-item" id="tba-rewriter-row-<?php echo (int) $p->ID; ?>" style="border:1px solid var(--tba-border); border-radius:10px; padding:18px; margin-bottom:12px; background:var(--tba-surface-2);">
                    <div style="display:flex; align-items:flex-start; gap:15px; flex-wrap:wrap;">
                        <!-- Post Info -->
                        <div style="flex:1; min-width:280px;">
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                                <code style="font-size:11px; color:#6366f1;">#<?php echo (int) $p->ID; ?></code>
                                <a href="<?php echo esc_url( get_permalink($p->ID) ); ?>" target="_blank" style="font-weight:700; color:var(--tba-text-dark); font-size:14px; text-decoration:none; transition: color 0.15s ease-in-out;">
                                    <?php echo esc_html($p->post_title); ?>
                                </a>
                            </div>
                            <div style="display:flex; gap:12px; font-size:11px; color:var(--tba-text-muted);">
                                <span>📅 <?php echo esc_html(get_the_date('M j, Y', $p->ID)); ?></span>
                                <span>📝 ~<?php echo number_format($word_count); ?> words</span>
                                <span>
                                    <?php if ( get_post_meta($p->ID, '_tba_generated', true) ): ?>
                                    <span style="background:rgba(99,102,241,0.15); color:#6366f1; padding:2px 8px; border-radius:4px; font-size:10px; font-weight:600;">AI Generated</span>
                                    <?php else: ?>
                                    <span style="background:rgba(148,163,184,0.15); color:#475569; padding:2px 8px; border-radius:4px; font-size:10px; font-weight:600;">Manual</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>

                        <!-- Instruction Input + Buttons -->
                        <div style="display:flex; align-items:center; gap:8px; flex-shrink:0; flex-wrap:wrap;">
                            <input type="text"
                                id="tba-rewrite-instructions-<?php echo (int) $p->ID; ?>"
                                class="tba-input"
                                placeholder="Optional: custom instructions..."
                                style="width:260px; font-size:12px; padding:8px 12px; border-radius:6px; border: 1px solid var(--tba-border);">
                            <button type="button"
                                class="tba-btn tba-btn-primary tba-btn-small tba-btn-rewrite-post"
                                data-post-id="<?php echo (int) $p->ID; ?>"
                                data-save="preview"
                                style="background:linear-gradient(135deg,#6366f1,#8b5cf6); border:none; color:#fff; white-space:nowrap; border-radius:6px; font-weight:600; padding:8px 16px;">
                                🔄 Rewrite Preview
                            </button>
                        </div>
                    </div>

                    <!-- Preview Container (hidden by default) -->
                    <div id="tba-rewrite-preview-<?php echo (int) $p->ID; ?>" style="display:none; margin-top:12px;"></div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination Links -->
            <?php if ( $total_pages > 1 ): ?>
            <div class="tba-pagination" style="display:flex; justify-content:center; gap:5px; margin-top:20px;">
                <?php echo wp_kses_post( paginate_links([
                    'base'     => add_query_arg( 'paged', '%#%' ),
                    'format'   => '',
                    'total'    => $total_pages,
                    'current'  => $paged,
                    'prev_text' => '« Prev',
                    'next_text' => 'Next »',
                ]) ); ?>
            </div>
            <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
</div>
