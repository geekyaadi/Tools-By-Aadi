<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$tab   = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'pending';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$paged = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1;
$posts_per_page = 10;

// Count total pending (missing featured image across ALL posts)
$pending_count_query = new WP_Query([
    'post_type'      => 'post',
    'post_status'    => [ 'publish', 'draft' ],
    'posts_per_page' => -1,
    'fields'         => 'ids',
    // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
    'meta_query'     => [
        [ 'key' => '_thumbnail_id', 'compare' => 'NOT EXISTS' ]
    ]
]);
$total_pending = $pending_count_query->post_count;

// Count total completed (with featured image across ALL posts)
$completed_count_query = new WP_Query([
    'post_type'      => 'post',
    'post_status'    => [ 'publish', 'draft' ],
    'posts_per_page' => -1,
    'fields'         => 'ids',
    // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
    'meta_query'     => [
        [ 'key' => '_thumbnail_id', 'compare' => 'EXISTS' ]
    ]
]);
$total_completed = $completed_count_query->post_count;

// Run paginated query for current tab
$args = [
    'post_type'      => 'post',
    'post_status'    => [ 'publish', 'draft' ],
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
    // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
    'meta_query'     => []
];

if ( $tab === 'pending' ) {
    // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
    $args['meta_query'][] = [ 'key' => '_thumbnail_id', 'compare' => 'NOT EXISTS' ];
} else {
    // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
    $args['meta_query'][] = [ 'key' => '_thumbnail_id', 'compare' => 'EXISTS' ];
}

$query = new WP_Query( $args );
$display_posts = $query->posts;
$total_pages   = $query->max_num_pages;
?>

<div class="tba-wrap">
    <div class="tba-header">
        <div class="tba-header-inner">
            <div class="tba-logo">
                <img src="<?php echo esc_url( TBA_PLUGIN_URL . 'admin/tools-by-aadi-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="tba-logo-badge">Thumbnail Manager</span>
            </div>
                        <div class="tba-header-nav">
    <a href="<?php echo esc_url( admin_url('admin.php?page=tools-by-aadi') ); ?>" class="tba-nav-link">Dashboard</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-generate') ); ?>" class="tba-nav-link">Generate Post</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-planner') ); ?>" class="tba-nav-link">Bulk Planner</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-scheduler') ); ?>" class="tba-nav-link">Scheduler</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-thumbnails') ); ?>" class="tba-nav-link active">Thumbnail Tool</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-tags') ); ?>" class="tba-nav-link">Tags Tool</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-translator') ); ?>" class="tba-nav-link">Translator</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-gsc') ); ?>" class="tba-nav-link">Indexing</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-rewriter') ); ?>" class="tba-nav-link">Rewriter</a>
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

    <!-- Tab navigation -->
    <div class="tba-tabs" style="display:flex; gap:10px; margin-bottom:20px; border-bottom:1px solid rgba(255,255,255,0.06); padding-bottom:10px;">
        <a href="<?php echo esc_url( admin_url('admin.php?page=tba-thumbnails&tab=pending') ); ?>" class="tba-btn <?php echo $tab === 'pending' ? 'tba-btn-primary' : 'tba-btn-secondary'; ?>" style="font-weight:600; border-radius:6px; font-size:12px; padding:8px 16px;">
            ⚠️ Pending Thumbnails (<?php echo (int) $total_pending; ?>)
        </a>
        <a href="<?php echo esc_url( admin_url('admin.php?page=tba-thumbnails&tab=completed') ); ?>" class="tba-btn <?php echo $tab === 'completed' ? 'tba-btn-primary' : 'tba-btn-secondary'; ?>" style="font-weight:600; border-radius:6px; font-size:12px; padding:8px 16px;">
            🖼️ Existing Featured Images (<?php echo (int) $total_completed; ?>)
        </a>
    </div>

    <div class="tba-content">
        <?php if ( $tab === 'pending' ): ?>
        <!-- PENDING THUMBNAILS TAB -->
        <div class="tba-panel">
            <div class="tba-panel-header">
                <h2 class="tba-panel-title">⚠️ Pending Thumbnails List</h2>
            </div>
            
            <?php if ( empty($display_posts) ): ?>
            <div class="tba-empty-state">🎉 All generated posts have thumbnails! No pending thumbnails found.</div>
            <?php else: ?>

            <!-- Bulk Actions Bar -->
            <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; padding:15px 0 10px;">
                <label style="font-size:12px; color:#94a3b8; font-weight:600;">Bulk Engine:</label>
                <select id="tba-bulk-thumb-engine" class="tba-select" style="width:auto; min-width:200px; font-size:12px; padding:6px 10px;">
                    <option value="ai">⚡ AI Generated (Gemini)</option>
                    <option value="text_to_image">🎨 Title Text Thumbnail (GD)</option>
                </select>
                <button type="button" id="tba-btn-generate-selected-thumbs" class="tba-btn tba-btn-primary tba-btn-small" style="background:#6366f1; border-color:#6366f1;" disabled>
                    🖼️ Generate Selected
                </button>
                <button type="button" id="tba-btn-generate-all-thumbs" class="tba-btn tba-btn-primary tba-btn-small" style="background:linear-gradient(135deg,#059669,#10b981); border:none;">
                    🚀 Generate All Pending (<?php echo (int) $total_pending; ?>)
                </button>
            </div>

            <!-- Bulk Progress Bar -->
            <div id="tba-bulk-thumb-progress" style="display:none; margin-bottom:12px;">
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                    <span id="tba-bulk-thumb-status" style="font-size:12px; color:#a5b4fc; font-weight:600;">Processing...</span>
                    <span id="tba-bulk-thumb-count" style="font-size:11px; color:#94a3b8;">0 / 0</span>
                </div>
                <div style="width:100%; height:8px; background:#1e293b; border-radius:4px; overflow:hidden;">
                    <div id="tba-bulk-thumb-bar" style="width:0%; height:100%; background:linear-gradient(90deg,#6366f1,#10b981); border-radius:4px; transition:width 0.4s ease;"></div>
                </div>
            </div>

            <table class="tba-table" id="tba-pending-thumbs-table">
                <thead>
                    <tr>
                        <th width="40"><input type="checkbox" id="tba-thumb-select-all" title="Select All"></th>
                        <th width="80">Post ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $display_posts as $p ): 
                        $cats = get_the_category($p->ID);
                        $cat_name = ! empty($cats) ? $cats[0]->name : '—';
                    ?>
                    <tr id="tba-thumb-row-<?php echo (int) $p->ID; ?>">
                        <td><input type="checkbox" class="tba-thumb-checkbox" data-post-id="<?php echo (int) $p->ID; ?>"></td>
                        <td><code>#<?php echo (int) $p->ID; ?></code></td>
                        <td>
                            <a href="<?php echo esc_url( get_edit_post_link($p->ID) ); ?>" target="_blank" style="font-weight:600;">
                                <?php echo esc_html($p->post_title); ?>
                            </a>
                        </td>
                        <td><?php echo esc_html($cat_name); ?></td>
                        <td>
                            <span class="tba-status-badge tba-status-exhausted">🖼️ Missing Thumbnail</span>
                        </td>
                        <td class="tba-thumb-action-cell" style="vertical-align: middle;">
                            <button type="button" class="tba-btn tba-btn-primary tba-btn-small tba-btn-gen-thumb-ai" data-post-id="<?php echo (int) $p->ID; ?>" style="margin-bottom: 5px; display: block; width: 100%; text-align: center;">
                                ⚡ Generate AI Thumbnail
                            </button>
                            <button type="button" class="tba-btn tba-btn-secondary tba-btn-small tba-btn-gen-thumb-t2i" data-post-id="<?php echo (int) $p->ID; ?>" style="display: block; width: 100%; text-align: center; background: #4f46e5; border-color: #4f46e5; color: #fff;">
                                🎨 Generate Title Text Thumbnail
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

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

        <?php else: ?>
        <!-- COMPLETED THUMBNAILS TAB -->
        <div class="tba-panel">
            <div class="tba-panel-header">
                <h2 class="tba-panel-title">🖼️ Existing Featured Images List</h2>
            </div>
            
            <?php if ( empty($display_posts) ): ?>
            <div class="tba-empty-state">No posts with thumbnails found yet.</div>
            <?php else: ?>
            <table class="tba-table">
                <thead>
                    <tr>
                        <th width="100">Preview</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $display_posts as $p ): 
                        $cats = get_the_category($p->ID);
                        $cat_name = ! empty($cats) ? $cats[0]->name : '—';
                        $thumb_url = get_the_post_thumbnail_url($p->ID, 'thumbnail');
                    ?>
                    <tr id="tba-thumb-row-<?php echo (int) $p->ID; ?>">
                        <td class="tba-thumb-preview-cell">
                            <img src="<?php echo esc_url($thumb_url); ?>" alt="Preview" style="max-height:50px; border-radius:4px; border:1px solid #ccd0d4; display:block;">
                        </td>
                        <td>
                            <a href="<?php echo esc_url( get_edit_post_link($p->ID) ); ?>" target="_blank" style="font-weight:600;">
                                <?php echo esc_html($p->post_title); ?>
                            </a>
                        </td>
                        <td><?php echo esc_html($cat_name); ?></td>
                        <td class="tba-thumb-action-cell" style="vertical-align: middle;">
                            <button type="button" class="tba-btn tba-btn-primary tba-btn-small tba-btn-gen-thumb-ai" data-post-id="<?php echo (int) $p->ID; ?>" style="margin-bottom: 5px; display: block; width: 100%; text-align: center;">
                                ⚡ Re-generate AI
                            </button>
                            <button type="button" class="tba-btn tba-btn-secondary tba-btn-small tba-btn-gen-thumb-t2i" data-post-id="<?php echo (int) $p->ID; ?>" style="display: block; width: 100%; text-align: center; background: #4f46e5; border-color: #4f46e5; color: #fff;">
                                🎨 Re-generate Title Text
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

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
        <?php endif; ?>
    </div>
</div>
