<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$paged = isset( $_GET['paged'] ) ? max( 1, (int) $_GET['paged'] ) : 1;
$posts_per_page = 10;

// Query all WordPress posts (plugin generated, manual, or imported) with pagination
$args = [
    'post_type'      => 'post',
    'post_status'    => [ 'publish', 'draft' ],
    'posts_per_page' => $posts_per_page,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
];
$query = new WP_Query( $args );
$posts = $query->posts;
$total_posts = $query->found_posts;
$total_pages = $query->max_num_pages;

$languages = [
    'Spanish'    => 'Spanish 🇪🇸',
    'French'     => 'French 🇫🇷',
    'German'     => 'German 🇩🇪',
    'Hindi'      => 'Hindi 🇮🇳',
    'Italian'    => 'Italian 🇮🇹',
    'Portuguese' => 'Portuguese 🇵🇹',
    'Russian'    => 'Russian 🇷🇺',
    'Arabic'     => 'Arabic 🇸🇦',
    'Japanese'   => 'Japanese 🇯🇵',
    'Chinese'    => 'Chinese 🇨🇳',
    'English'    => 'English 🇺🇸',
];
?>

<div class="tba-wrap">
    <div class="tba-header">
        <div class="tba-header-inner">
            <div class="tba-logo">
                <img src="<?php echo esc_url( TBA_PLUGIN_URL . 'admin/tools-by-aadi-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="tba-logo-badge">Bulk Translator</span>
            </div>
                        <div class="tba-header-nav">
    <a href="<?php echo esc_url( admin_url('admin.php?page=tools-by-aadi') ); ?>" class="tba-nav-link">Dashboard</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-generate') ); ?>" class="tba-nav-link">Generate Post</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-planner') ); ?>" class="tba-nav-link">Bulk Planner</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-scheduler') ); ?>" class="tba-nav-link">Scheduler</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-thumbnails') ); ?>" class="tba-nav-link">Thumbnail Tool</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-tags') ); ?>" class="tba-nav-link">Tags Tool</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-translator') ); ?>" class="tba-nav-link active">Translator</a>
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

    <div class="tba-content">
        <!-- Translator Options -->
        <div class="tba-panel" style="margin-bottom: 20px;">
            <div class="tba-panel-header">
                <h2 class="tba-panel-title">🌐 Bulk Translation Options</h2>
            </div>
            
            <div style="display:flex; gap: 20px; align-items: flex-end; flex-wrap: wrap;">
                <div class="tba-field" style="flex:1; min-width:200px; margin-bottom:0;">
                    <label class="tba-label">Target Language</label>
                    <select id="tba-translator-target-lang" class="tba-select" style="width:100%; height:38px; padding:6px 12px; border-radius:6px;">
                        <?php foreach ( $languages as $code => $label ): ?>
                        <option value="<?php echo esc_attr($code); ?>"><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="tba-field" style="flex:1; min-width:200px; margin-bottom:0;">
                    <label class="tba-label">Destination Post Status</label>
                    <select id="tba-translator-status" class="tba-select" style="width:100%; height:38px; padding:6px 12px; border-radius:6px;">
                        <option value="draft" selected>Save as Draft</option>
                        <option value="publish">Publish Instantly</option>
                    </select>
                </div>

                <div style="flex:1; min-width:220px;">
                    <button type="button" id="tba-btn-translate-selected" class="tba-btn tba-btn-primary" style="background:#4f46e5; border-color:#4f46e5; width:100%; font-weight:600; height:38px; border-radius:6px; padding:0 20px; display:inline-flex; align-items:center; justify-content:center; gap:8px;">
                        🌐 Translate Selected Posts
                    </button>
                </div>
            </div>

            <!-- Progress Block -->
            <div id="tba-translator-progress-container" style="display:none; margin-top:20px; border-top:1px solid rgba(255,255,255,0.06); padding-top:20px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-weight:600; font-size:13px; color:var(--tba-text-dark);">
                    <span id="tba-trans-progress-text">Processing translation queue...</span>
                    <span id="tba-trans-progress-percent">0%</span>
                </div>
                <div style="background:rgba(255,255,255,0.05); height:8px; border-radius:4px; overflow:hidden; margin-bottom:15px;">
                    <div id="tba-trans-progress-bar" style="background:linear-gradient(90deg, #4f46e5, #06b6d4); width:0%; height:100%; transition: width 0.3s ease;"></div>
                </div>
                <div id="tba-trans-log" style="background:#0f172a; color:#cbd5e1; font-family:monospace; font-size:12px; padding:12px 15px; border-radius:6px; max-height:150px; overflow-y:auto; line-height:1.5;">
                    <div>[Console initialized] Select posts and click Translate to begin.</div>
                </div>
            </div>
        </div>

        <!-- Posts List -->
        <div class="tba-panel">
            <div class="tba-panel-header" style="display:flex; justify-content:space-between; align-items:center;">
                <h2 class="tba-panel-title">📚 Select Articles to Translate (Showing <?php echo count($posts); ?> of <?php echo (int) $total_posts; ?>)</h2>
                <div>
                    <label style="font-size:12px; font-weight:600; color:var(--tba-text-muted); cursor:pointer; display:flex; align-items:center; gap:5px;">
                        <input type="checkbox" id="tba-translator-select-all" style="vertical-align:middle; margin:0;"> Select All
                    </label>
                </div>
            </div>

            <?php if ( empty($posts) ): ?>
            <div class="tba-empty-state">No blog posts found on your WordPress site. Create or generate some posts first!</div>
            <?php else: ?>
            <table class="tba-table" id="tba-translator-table">
                <thead>
                    <tr>
                        <th width="40">Select</th>
                        <th width="80">Post ID</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Categories</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $posts as $p ): 
                        $cats = get_the_category($p->ID);
                        $cat_names = ! empty($cats) ? implode(', ', wp_list_pluck($cats, 'name')) : '—';
                    ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="tba-translator-checkbox" data-post-id="<?php echo (int) $p->ID; ?>">
                        </td>
                        <td><code>#<?php echo (int) $p->ID; ?></code></td>
                        <td>
                            <a href="<?php echo esc_url( get_edit_post_link($p->ID) ); ?>" target="_blank" style="font-weight:600; color:var(--tba-text-dark);">
                                <?php echo esc_html($p->post_title); ?>
                            </a>
                        </td>
                        <td>
                            <span class="tba-status-badge <?php echo $p->post_status === 'publish' ? 'tba-status-active' : 'tba-status-exhausted'; ?>">
                                <?php echo $p->post_status === 'publish' ? 'Published' : 'Draft'; ?>
                            </span>
                        </td>
                        <td><?php echo esc_html($cat_names); ?></td>
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
    </div>
</div>
