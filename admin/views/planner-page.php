<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$categories = get_categories( [ 'hide_empty' => false ] );
$languages  = [
    'English'    => 'English',
    'Hindi'      => 'Hindi (हिन्दी)',
    'Spanish'    => 'Spanish (Español)',
    'French'     => 'French (Français)',
    'German'     => 'German (Deutsch)',
    'Italian'    => 'Italian (Italiano)',
    'Portuguese' => 'Portuguese (Português)',
    'Arabic'     => 'Arabic (العربية)',
    'Russian'    => 'Russian (Русский)',
    'Japanese'   => 'Japanese (日本語)',
    'Bengali'    => 'Bengali (বাংলা)',
];
?>

<div class="tba-wrap">
    <div class="tba-header">
        <div class="tba-header-inner">
            <div class="tba-logo">
                <img src="<?php echo esc_url( TBA_PLUGIN_URL . 'admin/tools-by-aadi-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="tba-logo-badge">Bulk Planner</span>
            </div>
                        <div class="tba-header-nav">
    <a href="<?php echo esc_url( admin_url('admin.php?page=tools-by-aadi') ); ?>" class="tba-nav-link">Dashboard</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-generate') ); ?>" class="tba-nav-link">Generate Post</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-planner') ); ?>" class="tba-nav-link active">Bulk Planner</a>
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
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-codes') ); ?>" class="tba-nav-link">Codes</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-settings') ); ?>" class="tba-nav-link">Settings</a>
</div>
        </div>
    </div>

    <div class="tba-content">
        <div class="tba-panel">
            <div class="tba-panel-header">
                <h2 class="tba-panel-title">🗓️ Bulk Article Planner</h2>
                <div class="tba-hint">Plan multiple articles at once. The background queue processor will auto-generate and publish them.</div>
            </div>

            <!-- Planner Form -->
            <div class="tba-planner-form-row" style="display:flex; gap:15px; align-items:flex-end; flex-wrap:wrap; margin-bottom:10px;">
                <div class="tba-field-planner-niche" style="flex:2; min-width:250px;">
                    <label class="tba-label">Niche or Topic</label>
                    <input type="text" id="tba-planner-niche" class="tba-input" placeholder="e.g. Smart Gardening, Weight Loss tips..." autocomplete="off">
                </div>
                <div class="tba-field-planner-mode" style="flex:1; min-width:180px;">
                    <label class="tba-label">Planner Mode</label>
                    <select id="tba-planner-mode" class="tba-select">
                        <option value="standard">Standard Plan</option>
                        <option value="silo">Pillar & Silo (1 Pillar + 5 Clusters)</option>
                    </select>
                </div>
                <div class="tba-field-planner-count" id="tba-planner-count-wrapper" style="flex:1; min-width:120px;">
                    <label class="tba-label">Number of Posts</label>
                    <select id="tba-planner-count" class="tba-select">
                        <option value="5">5 Posts</option>
                        <option value="10">10 Posts</option>
                        <option value="15">15 Posts</option>
                        <option value="20" selected>20 Posts (Default)</option>
                        <option value="25">25 Posts</option>
                        <option value="30">30 Posts</option>
                        <option value="35">35 Posts</option>
                        <option value="40">40 Posts</option>
                        <option value="45">45 Posts</option>
                        <option value="50">50 Posts</option>
                    </select>
                </div>
                <div class="tba-field-planner-lang" style="flex:1; min-width:120px;">
                    <label class="tba-label">Language</label>
                    <select id="tba-planner-lang" class="tba-select">
                        <?php foreach ( $languages as $key => $lbl ): ?>
                        <option value="<?php echo esc_attr($key); ?>"><?php echo esc_html($lbl); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="tba-field-planner-cat" style="flex:1; min-width:180px;">
                    <label class="tba-label">Default Category</label>
                    <select id="tba-planner-default-cat" class="tba-select">
                        <option value="">— Suggest category automatically —</option>
                        <?php foreach ( $categories as $cat ): ?>
                        <option value="<?php echo esc_attr($cat->name); ?>"><?php echo esc_html($cat->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="flex:0 0 auto; min-width:120px;">
                    <label class="tba-label">Tags Count</label>
                    <select id="tba-planner-tag-count" class="tba-select">
                        <?php
                        $saved_count = (int) get_option( 'tba_tag_count', 15 );
                        foreach ( [5, 10, 15, 20, 25, 30, 40, 50, 75, 100] as $tc ):
                        ?>
                        <option value="<?php echo (int) $tc; ?>" <?php selected( $saved_count, $tc ); ?>><?php echo (int) $tc; ?> tags</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="tba-field-planner-btn" style="margin-bottom:2px;">
                    <button type="button" id="tba-btn-planner-find" class="tba-btn tba-btn-primary">
                        🔍 Generate Plan
                    </button>
                </div>
            </div>
        </div>

        <!-- Bulk Thumbnail Settings -->
        <div class="tba-panel" style="margin-top:20px;">
            <div class="tba-panel-header">
                <h2 class="tba-panel-title">🖼️ Bulk Thumbnail Settings</h2>
            </div>
            <div class="tba-settings-grid">
                <div class="tba-field" style="margin-bottom:0;">
                    <label class="tba-label">Thumbnail Method</label>
                    <select id="tba-planner-thumb-type" class="tba-select">
                        <option value="ai" <?php selected( get_option('tba_thumb_type','ai'), 'ai' ); ?>>AI Generated (Gemini)</option>
                        <option value="text_to_image" <?php selected( get_option('tba_thumb_type','ai'), 'text_to_image' ); ?>>Title to Image (Local GD)</option>
                    </select>
                </div>
                <div class="tba-field tba-t2i-only" style="display:none; margin-bottom:0;">
                    <label class="tba-label">Background Selection</label>
                    <select id="tba-planner-t2i-bg-type" class="tba-select">
                        <option value="gradient" <?php selected( get_option('tba_t2i_bg_type','gradient'), 'gradient' ); ?>>Gradient Background</option>
                        <option value="solid" <?php selected( get_option('tba_t2i_bg_type','gradient'), 'solid' ); ?>>Solid Color Background</option>
                        <option value="image" <?php selected( get_option('tba_t2i_bg_type','gradient'), 'image' ); ?>>Default Image Background (admin/default-thumbnail.jpg)</option>
                        <option value="mix" <?php selected( get_option('tba_t2i_bg_type','gradient'), 'mix' ); ?>>🎲 Mix Background (Randomize)</option>
                    </select>
                </div>
                <div class="tba-field tba-t2i-only" id="tba-planner-t2i-gradient-group" style="display:none; margin-bottom:0;">
                    <label class="tba-label">Gradient Color Palette</label>
                    <select id="tba-planner-t2i-bg-val-gradient" class="tba-select">
                        <?php 
                        $saved_bg = get_option('tba_t2i_bg_val', 'blue_purple');
                        foreach ( TBA_Text_To_Image::get_gradients() as $key => $g ): 
                        ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($saved_bg, $key); ?>><?php echo esc_html($g['label']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="tba-field tba-t2i-only" id="tba-planner-t2i-solid-group" style="display:none; margin-bottom:0;">
                    <label class="tba-label">Solid Color Background</label>
                    <select id="tba-planner-t2i-bg-val-solid" class="tba-select">
                        <?php 
                        $saved_bg = get_option('tba_t2i_bg_val', 'dark_slate');
                        foreach ( TBA_Text_To_Image::get_solid_colors() as $key => $s ): 
                        ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($saved_bg, $key); ?>><?php echo esc_html($s['label']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="tba-field tba-t2i-only" style="display:none; margin-bottom:0;">
                    <label class="tba-label">Image Ratio &amp; Size</label>
                    <select id="tba-planner-t2i-size" class="tba-select">
                        <option value="600x315" <?php selected(get_option('tba_t2i_size','600x315'), '600x315'); ?>>Landscape (2:1) — 600×315 px</option>
                        <option value="1200x630" <?php selected(get_option('tba_t2i_size','600x315'), '1200x630'); ?>>OpenGraph (2:1) — 1200×630 px</option>
                        <option value="500x500" <?php selected(get_option('tba_t2i_size','600x315'), '500x500'); ?>>Square (1:1) — 500×500 px</option>
                        <option value="1000x1000" <?php selected(get_option('tba_t2i_size','600x315'), '1000x1000'); ?>>Square High-Res (1:1) — 1000×1000 px</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Planned Titles Table (Hidden initially) -->
        <div class="tba-panel" id="tba-planner-results-panel" style="display:none;">
            <div class="tba-panel-header">
                <h2 class="tba-panel-title">✏️ Edit & Select Planned Titles</h2>
                <div class="tba-panel-actions">
                    <button type="button" id="tba-btn-select-all" class="tba-btn tba-btn-ghost tba-btn-sm">Select All</button>
                    <button type="button" id="tba-btn-deselect-all" class="tba-btn tba-btn-ghost tba-btn-sm">Deselect All</button>
                </div>
            </div>

            <table class="tba-table" id="tba-planner-table">
                <thead>
                    <tr>
                        <th width="40"><input type="checkbox" id="tba-check-master" checked></th>
                        <th>#</th>
                        <th>Post Title (You can edit this)</th>
                        <th>Target Category</th>
                    </tr>
                </thead>
                <tbody id="tba-planner-table-body">
                    <!-- Dynamic Rows -->
                </tbody>
            </table>

            <div class="tba-form-actions" style="margin-top:20px;">
                <button type="button" id="tba-btn-save-tasks" class="tba-btn tba-btn-primary">
                    💾 Save Selected as Background Tasks
                </button>
            </div>
        </div>

        <!-- Categories source template data (Hidden) -->
        <select id="tba-cat-template-source" style="display:none;">
            <option value="">— Auto Suggest Category —</option>
            <?php foreach ( $categories as $cat ): ?>
            <option value="<?php echo esc_attr($cat->name); ?>"><?php echo esc_html($cat->name); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
