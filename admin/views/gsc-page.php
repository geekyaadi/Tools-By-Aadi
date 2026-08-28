<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$gsc_json = get_option( 'sab_gsc_json', '' );
$has_creds = ! empty( $gsc_json );

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

<div class="sab-wrap">
    <div class="sab-header">
        <div class="sab-header-inner">
            <div class="sab-logo">
                <img src="<?php echo esc_url( SAB_PLUGIN_URL . 'admin/soniji-auto-blogging-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="sab-logo-badge">Google Indexing Tool</span>
            </div>
                        <div class="sab-header-nav">
    <a href="<?php echo esc_url( admin_url('admin.php?page=soniji-auto-blogging') ); ?>" class="sab-nav-link">Dashboard</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-generate') ); ?>" class="sab-nav-link">Generate Post</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-planner') ); ?>" class="sab-nav-link">Bulk Planner</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-scheduler') ); ?>" class="sab-nav-link">Scheduler</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-thumbnails') ); ?>" class="sab-nav-link">Thumbnail Tool</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-tags') ); ?>" class="sab-nav-link">Tags Tool</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-translator') ); ?>" class="sab-nav-link">Translator</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-gsc') ); ?>" class="sab-nav-link active">Indexing</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-rewriter') ); ?>" class="sab-nav-link">Rewriter</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-speed') ); ?>" class="sab-nav-link">Optimizer</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-sitemap') ); ?>" class="sab-nav-link">Sitemap</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-pages') ); ?>" class="sab-nav-link">Pages Generator</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-redirects') ); ?>" class="sab-nav-link">Redirect</a>
        <a href="<?php echo esc_url( admin_url('admin.php?page=sab-randomizer') ); ?>" class="sab-nav-link">Date Randomizer</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-codes') ); ?>" class="sab-nav-link">Codes</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-settings') ); ?>" class="sab-nav-link">Settings</a>
</div>
        </div>
    </div>

    <div class="sab-content">

        <!-- Status Panel -->
        <div class="sab-panel" style="margin-bottom:20px;">
            <div class="sab-panel-header">
                <h2 class="sab-panel-title">🔌 Google Indexing API Status</h2>
                <?php if ( $has_creds ): ?>
                <span class="sab-status-badge sab-status-active">✅ Credentials Configured</span>
                <?php else: ?>
                <span class="sab-status-badge sab-status-exhausted">❌ No Credentials</span>
                <?php endif; ?>
            </div>
            <?php if ( ! $has_creds ): ?>
            <div class="sab-alert sab-alert-warning" style="margin:15px 0 5px;">
                ⚠️ Google Service Account JSON key is not configured. Go to <a href="<?php echo esc_url( admin_url('admin.php?page=sab-settings') ); ?>">Settings → Internal Linking & Indexing</a> to paste or upload your credentials.
            </div>
            <?php else: ?>
            <div class="sab-hint" style="padding: 10px 0 0;">
                Your Google Service Account is connected. Use the buttons below to manually request instant indexing for any published post.
                <?php if ( get_option( 'sab_enable_gsc_auto_ping', 0 ) ): ?>
                <br><strong>Auto-Ping is ON</strong> — New posts will be automatically submitted to Google on publish.
                <?php else: ?>
                <br><em>Auto-Ping is OFF</em> — Enable it in <a href="<?php echo esc_url( admin_url('admin.php?page=sab-settings') ); ?>">Settings</a> to auto-submit new posts.
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Posts List -->
        <div class="sab-panel">
            <div class="sab-panel-header">
                <h2 class="sab-panel-title">🚀 Request Indexing (Showing <?php echo count($posts); ?> of <?php echo (int) $total_posts; ?>)</h2>
            </div>

            <?php if ( empty($posts) ): ?>
            <div class="sab-empty-state">No published posts found. Generate and publish some posts first!</div>
            <?php else: ?>
            <table class="sab-table" id="sab-gsc-table">
                <thead>
                    <tr>
                        <th width="80">Post ID</th>
                        <th>Title</th>
                        <th width="150">Published</th>
                        <th width="160">Last Indexed Ping</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $posts as $p ):
                        $last_ping = get_post_meta($p->ID, '_sab_gsc_last_ping', true);
                    ?>
                    <tr id="sab-gsc-row-<?php echo (int) $p->ID; ?>">
                        <td><code>#<?php echo (int) $p->ID; ?></code></td>
                        <td>
                            <a href="<?php echo esc_url( get_permalink($p->ID) ); ?>" target="_blank" style="font-weight:600; color:var(--sab-text-dark);">
                                <?php echo esc_html($p->post_title); ?>
                            </a>
                        </td>
                        <td><?php echo esc_html(get_the_date('M j, Y', $p->ID)); ?></td>
                        <td class="sab-gsc-ping-cell">
                            <?php if ( $last_ping ): ?>
                            <span class="sab-status-badge" style="background:#dcfce7; color:#166534; font-size:10px;">
                                ✅ <?php echo esc_html( gmdate( 'M j, H:i', strtotime($last_ping)) ); ?>
                            </span>
                            <?php else: ?>
                            <span style="color:#94a3b8; font-style:italic; font-size:11px;">— Never pinged</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button"
                                class="sab-btn sab-btn-primary sab-btn-small sab-btn-request-indexing"
                                data-post-id="<?php echo (int) $p->ID; ?>"
                                style="background:#059669; border-color:#059669; color:#fff; width:100%; border-radius:6px; font-weight:600;"
                                <?php disabled( ! $has_creds ); ?>>
                                🚀 Request Indexing
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Pagination Links -->
            <?php if ( $total_pages > 1 ): ?>
            <div class="sab-pagination" style="display:flex; justify-content:center; gap:5px; margin-top:20px;">
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
