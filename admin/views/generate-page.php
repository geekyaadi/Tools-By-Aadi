<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<!DOCTYPE html>
<div class="sab-wrap">

    <div class="sab-header">
        <div class="sab-header-inner">
            <div class="sab-logo">
                <img src="<?php echo esc_url( SAB_PLUGIN_URL . 'admin/soniji-auto-blogging-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="sab-logo-badge">Generator</span>
            </div>
                        <div class="sab-header-nav">
    <a href="<?php echo esc_url( admin_url('admin.php?page=soniji-auto-blogging') ); ?>" class="sab-nav-link">Dashboard</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-generate') ); ?>" class="sab-nav-link active">Generate Post</a>
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
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-codes') ); ?>" class="sab-nav-link">Codes</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-settings') ); ?>" class="sab-nav-link">Settings</a>
</div>
        </div>
    </div>

    <div class="sab-content">

        <?php
        $key_stats = SAB_Key_Manager::get_stats();
        if ( $key_stats['total'] === 0 ):
        ?>
        <div class="sab-alert sab-alert-warning">
            <span class="sab-alert-icon">⚠️</span>
            No API keys configured. <a href="<?php echo esc_url( admin_url('admin.php?page=sab-settings') ); ?>">Add your Gemini API key →</a>
        </div>
        <?php endif; ?>

        <?php if ( $key_stats['active'] === 0 && $key_stats['total'] > 0 ): ?>
        <div class="sab-alert sab-alert-error">
            <span class="sab-alert-icon">🔴</span>
            All API keys are currently exhausted. Keys auto-reset after <?php echo (int) get_option('sab_key_reset_minutes', 60); ?> minutes, or you can <a href="<?php echo esc_url( admin_url('admin.php?page=sab-settings') ); ?>">manually reset them</a>.
        </div>
        <?php endif; ?>

        <div class="sab-two-col">

            <!-- LEFT: Generation Form -->
            <div class="sab-panel sab-generate-panel">
                <div class="sab-panel-header">
                    <h2 class="sab-panel-title">✦ Generate New Post</h2>
                    <div class="sab-key-badge">
                        <span class="sab-key-dot <?php echo $key_stats['active'] > 0 ? 'active' : 'inactive'; ?>"></span>
                        <span><?php echo (int) $key_stats['active']; ?>/<?php echo (int) $key_stats['total']; ?> Keys Active</span>
                    </div>
                </div>

                <!-- Step 1: Niche Input -->
                <div class="sab-step" id="sab-step-niche">
                    <div class="sab-step-number">1</div>
                    <div class="sab-step-body">
                        <label class="sab-label" for="sab-niche-input">Enter Your Niche or Topic</label>
                        <input type="text" id="sab-niche-input" class="sab-input"
                            placeholder="e.g. Personal Finance, Fitness for Beginners, AI Tools..."
                            value="" autocomplete="off" style="margin-bottom:12px;" />

                        <label class="sab-label" for="sab-keywords-input">Focus Keywords (SEO)</label>
                        <input type="text" id="sab-keywords-input" class="sab-input"
                            placeholder="e.g. passive income tips, smart investing (comma separated)"
                            value="" autocomplete="off" style="margin-bottom:14px;" />

                        <button id="sab-btn-find-titles" class="sab-btn sab-btn-primary sab-btn-full" style="display:block; width:100%;">
                            <span class="sab-btn-icon">🔍</span> Find Titles
                        </button>
                        <div class="sab-hint" style="margin-top:8px;">AI will suggest 5 engaging title ideas tailored to your niche and keywords.</div>
                    </div>
                </div>

                <!-- Step 2: Title Selection -->
                <div class="sab-step sab-step-locked" id="sab-step-titles">
                    <div class="sab-step-number">2</div>
                    <div class="sab-step-body">
                        <label class="sab-label">Choose a Title</label>
                        <div id="sab-titles-list" class="sab-titles-list">
                            <div class="sab-titles-placeholder">Titles will appear here after Step 1</div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Options -->
                <div class="sab-step sab-step-locked" id="sab-step-options">
                    <div class="sab-step-number">3</div>
                    <div class="sab-step-body">
                        <label class="sab-label">Post Options</label>
                        <div class="sab-options-row" style="grid-template-columns: 1fr 1fr; margin-bottom: 12px;">
                            <div class="sab-option-group">
                                <label class="sab-option-label">Status</label>
                                <select id="sab-post-status" class="sab-select">
                                    <option value="draft" <?php selected( get_option('sab_default_status','draft'), 'draft' ); ?>>Draft</option>
                                    <option value="publish" <?php selected( get_option('sab_default_status','draft'), 'publish' ); ?>>Published</option>
                                </select>
                            </div>
                            <div class="sab-option-group">
                                <label class="sab-option-label">Tags Count</label>
                                <select id="sab-tag-count" class="sab-select">
                                    <?php
                                    $saved_count = (int) get_option( 'sab_tag_count', 15 );
                                    foreach ( [5, 10, 15, 20, 25, 30, 40, 50, 75, 100] as $tc ):
                                    ?>
                                    <option value="<?php echo (int) $tc; ?>" <?php selected( $saved_count, $tc ); ?>><?php echo (int) $tc; ?> tags</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="sab-options-row" style="grid-template-columns: 1fr; margin-bottom: 12px;">
                            <div class="sab-option-group">
                                <label class="sab-option-label">Category</label>
                                <select id="sab-post-category" class="sab-select" style="width: 100%;">
                                    <option value="">⚙️ Auto-Suggest (Gemini)</option>
                                    <?php 
                                    $categories = get_categories( [ 'hide_empty' => false ] );
                                    foreach ( $categories as $cat ): 
                                    ?>
                                    <option value="<?php echo esc_attr( $cat->name ); ?>"><?php echo esc_html( $cat->name ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Thumbnail Generation Options -->
                        <div class="sab-options-row" style="grid-template-columns: 1fr 1fr; margin-bottom: 12px;">
                            <div class="sab-option-group">
                                <label class="sab-option-label">Thumbnail Method</label>
                                <select id="sab-thumb-type" class="sab-select">
                                    <option value="ai" <?php selected( get_option('sab_thumb_type','ai'), 'ai' ); ?>>AI Generated (Gemini)</option>
                                    <option value="text_to_image" <?php selected( get_option('sab_thumb_type','ai'), 'text_to_image' ); ?>>Title to Image (Local GD)</option>
                                </select>
                            </div>
                             <div class="sab-option-group sab-t2i-only" style="display:none;">
                                 <label class="sab-option-label">Background Selection</label>
                                  <select id="sab-t2i-bg-type" class="sab-select">
                                      <option value="gradient" <?php selected( get_option('sab_t2i_bg_type','gradient'), 'gradient' ); ?>>Gradient Background</option>
                                      <option value="solid" <?php selected( get_option('sab_t2i_bg_type','gradient'), 'solid' ); ?>>Solid Color Background</option>
                                      <option value="image" <?php selected( get_option('sab_t2i_bg_type','gradient'), 'image' ); ?>>Default Image Background (admin/default-thumbnail.jpg)</option>
                                      <option value="mix" <?php selected( get_option('sab_t2i_bg_type','gradient'), 'mix' ); ?>>🎲 Mix Background (Randomize)</option>
                                  </select>
                             </div>
                        </div>

                        <div class="sab-options-row sab-t2i-only" style="grid-template-columns: 1fr 1fr; margin-bottom: 12px; display:none;">
                            <div class="sab-option-group" id="sab-t2i-gradient-group">
                                <label class="sab-option-label">Gradient Color Palette</label>
                                <select id="sab-t2i-bg-val-gradient" class="sab-select">
                                    <?php 
                                    $saved_bg = get_option('sab_t2i_bg_val', 'blue_purple');
                                    foreach ( SAB_Text_To_Image::get_gradients() as $key => $g ): 
                                    ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($saved_bg, $key); ?>><?php echo esc_html($g['label']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="sab-option-group" id="sab-t2i-solid-group" style="display:none;">
                                <label class="sab-option-label">Solid Color Background</label>
                                <select id="sab-t2i-bg-val-solid" class="sab-select">
                                    <?php 
                                    $saved_bg = get_option('sab_t2i_bg_val', 'dark_slate');
                                    foreach ( SAB_Text_To_Image::get_solid_colors() as $key => $s ): 
                                    ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($saved_bg, $key); ?>><?php echo esc_html($s['label']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="sab-option-group">
                                <label class="sab-option-label">Image Ratio &amp; Size</label>
                                <select id="sab-t2i-size" class="sab-select">
                                    <option value="600x315" <?php selected(get_option('sab_t2i_size','600x315'), '600x315'); ?>>Landscape (2:1) — 600×315 px</option>
                                    <option value="1200x630" <?php selected(get_option('sab_t2i_size','600x315'), '1200x630'); ?>>OpenGraph (2:1) — 1200×630 px</option>
                                    <option value="500x500" <?php selected(get_option('sab_t2i_size','600x315'), '500x500'); ?>>Square (1:1) — 500×500 px</option>
                                    <option value="1000x1000" <?php selected(get_option('sab_t2i_size','600x315'), '1000x1000'); ?>>Square High-Res (1:1) — 1000×1000 px</option>
                                </select>
                            </div>
                        </div>

                        <!-- Reference Image Upload (AI Generated Only) -->
                        <div class="sab-ref-img-section sab-ai-thumb-only">
                            <div class="sab-ref-img-header">
                                <span class="sab-ref-img-title">🖼️ Thumbnail Style Reference</span>
                                <?php $has_default = ! empty( SAB_Gemini::get_default_reference_image() ); ?>
                                <?php if ( $has_default ): ?>
                                <span class="sab-badge sab-badge-default" title="A default reference image is set in Settings">✦ Default Set</span>
                                <?php endif; ?>
                                <span class="sab-ref-img-hint">Upload a sample image — Gemini will match its style for the 600×315 thumbnail</span>
                            </div>

                            <div class="sab-upload-zone" id="sab-upload-zone">
                                <div class="sab-upload-idle" id="sab-upload-idle">
                                    <span class="sab-upload-icon">📁</span>
                                    <span class="sab-upload-text">Drag & drop an image here, or <label for="sab-ref-img-input" class="sab-upload-link">browse</label></span>
                                    <span class="sab-upload-sub">JPG, PNG, WEBP — max 4MB</span>
                                </div>
                                <div class="sab-upload-preview" id="sab-upload-preview" style="display:none;">
                                    <img id="sab-ref-img-thumb" src="" alt="Reference preview">
                                    <div class="sab-upload-preview-info">
                                        <span id="sab-ref-img-name" class="sab-upload-filename"></span>
                                        <button type="button" id="sab-btn-clear-ref" class="sab-btn-small sab-btn-danger">✕ Remove</button>
                                    </div>
                                </div>
                                <input type="file" id="sab-ref-img-input" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;">
                            </div>

                            <?php if ( $has_default ): ?>
                            <div class="sab-ref-default-note" id="sab-ref-default-note">
                                <span>ℹ️ Using your <a href="<?php echo esc_url( admin_url('admin.php?page=sab-settings#thumbnail-ref') ); ?>">default reference image</a> from Settings. Upload above to override for this post only.</span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="sab-action-buttons">
                            <button id="sab-btn-preview" class="sab-btn sab-btn-secondary">
                                <span>👁</span> Preview First
                            </button>
                            <button id="sab-btn-generate" class="sab-btn sab-btn-primary" disabled>
                                <span>⚡</span> Generate &amp; Publish
                            </button>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="sab-session-id" value="">
            </div>

            <!-- RIGHT: Progress + Result -->
            <div class="sab-panel sab-progress-panel">
                <div class="sab-panel-header">
                    <h2 class="sab-panel-title">📊 Generation Progress</h2>
                </div>

                <div id="sab-progress-idle" class="sab-progress-idle">
                    <div class="sab-idle-icon">✦</div>
                    <p>Enter a niche and select a title to begin generation.</p>
                </div>

                <div id="sab-progress-steps" class="sab-progress-steps" style="display:none;">
                    <?php
                    $steps = [
                        'article'   => ['icon' => '📝', 'label' => 'Writing Article (~1000 words)'],
                        'tags'      => ['icon' => '🏷️', 'label' => 'Generating Tags'],
                        'meta'      => ['icon' => '🔍', 'label' => 'Creating Meta Description'],
                        'category'  => ['icon' => '📂', 'label' => 'Assigning Category'],
                        'thumbnail' => ['icon' => '🖼️', 'label' => 'Generating Thumbnail'],
                        'og_image'  => ['icon' => '📸', 'label' => 'Creating OG Image (1200×630)'],
                        'alt_text'  => ['icon' => '♿', 'label' => 'Writing Alt Text'],
                        'publish'   => ['icon' => '🚀', 'label' => 'Publishing Post'],
                    ];
                    foreach ( $steps as $key => $step ):
                    ?>
                    <div class="sab-progress-step" id="sab-pstep-<?php echo esc_attr($key); ?>" data-step="<?php echo esc_attr($key); ?>">
                        <div class="sab-pstep-icon"><?php echo esc_html( $step['icon'] ); ?></div>
                        <div class="sab-pstep-body">
                            <div class="sab-pstep-label"><?php echo esc_html($step['label']); ?></div>
                            <div class="sab-pstep-meta"></div>
                        </div>
                        <div class="sab-pstep-status">
                            <span class="sab-pstep-dot waiting"></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Key switch notification -->
                <div id="sab-key-switch-notice" class="sab-key-switch-notice" style="display:none;">
                    <span class="sab-ksn-icon">⚡</span>
                    <span id="sab-key-switch-text"></span>
                </div>

                <!-- Result -->
                <div id="sab-result" class="sab-result" style="display:none;"></div>

                <!-- Preview Modal -->
                <div id="sab-preview-panel" class="sab-preview-panel" style="display:none;">
                    <div class="sab-preview-header">
                        <h3 id="sab-preview-title"></h3>
                        <div class="sab-preview-meta">
                            <span id="sab-preview-category" class="sab-preview-tag"></span>
                            <span id="sab-preview-meta-desc" class="sab-preview-meta-desc"></span>
                        </div>
                    </div>
                    <div id="sab-preview-content" class="sab-preview-content"></div>
                    <div class="sab-preview-tags">
                        <strong>Tags:</strong>
                        <span id="sab-preview-tags"></span>
                    </div>
                    <div class="sab-preview-actions">
                        <button id="sab-btn-confirm-publish" class="sab-btn sab-btn-primary">✅ Looks Good — Publish</button>
                        <button id="sab-btn-cancel-preview" class="sab-btn sab-btn-ghost">✏️ Back to Edit</button>
                    </div>
                </div>

            </div>

        </div><!-- .sab-two-col -->

    </div><!-- .sab-content -->
</div>
