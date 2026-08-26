<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<!DOCTYPE html>
<div class="tba-wrap">

    <div class="tba-header">
        <div class="tba-header-inner">
            <div class="tba-logo">
                <img src="<?php echo esc_url( TBA_PLUGIN_URL . 'admin/tools-by-aadi-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="tba-logo-badge">Generator</span>
            </div>
                        <div class="tba-header-nav">
    <a href="<?php echo esc_url( admin_url('admin.php?page=tools-by-aadi') ); ?>" class="tba-nav-link">Dashboard</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-generate') ); ?>" class="tba-nav-link active">Generate Post</a>
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
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-codes') ); ?>" class="tba-nav-link">Codes</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-settings') ); ?>" class="tba-nav-link">Settings</a>
</div>
        </div>
    </div>

    <div class="tba-content">

        <?php
        $key_stats = TBA_Key_Manager::get_stats();
        if ( $key_stats['total'] === 0 ):
        ?>
        <div class="tba-alert tba-alert-warning">
            <span class="tba-alert-icon">⚠️</span>
            No API keys configured. <a href="<?php echo esc_url( admin_url('admin.php?page=tba-settings') ); ?>">Add your Gemini API key →</a>
        </div>
        <?php endif; ?>

        <?php if ( $key_stats['active'] === 0 && $key_stats['total'] > 0 ): ?>
        <div class="tba-alert tba-alert-error">
            <span class="tba-alert-icon">🔴</span>
            All API keys are currently exhausted. Keys auto-reset after <?php echo (int) get_option('tba_key_reset_minutes', 60); ?> minutes, or you can <a href="<?php echo esc_url( admin_url('admin.php?page=tba-settings') ); ?>">manually reset them</a>.
        </div>
        <?php endif; ?>

        <div class="tba-two-col">

            <!-- LEFT: Generation Form -->
            <div class="tba-panel tba-generate-panel">
                <div class="tba-panel-header">
                    <h2 class="tba-panel-title">✦ Generate New Post</h2>
                    <div class="tba-key-badge">
                        <span class="tba-key-dot <?php echo $key_stats['active'] > 0 ? 'active' : 'inactive'; ?>"></span>
                        <span><?php echo (int) $key_stats['active']; ?>/<?php echo (int) $key_stats['total']; ?> Keys Active</span>
                    </div>
                </div>

                <!-- Step 1: Niche Input -->
                <div class="tba-step" id="tba-step-niche">
                    <div class="tba-step-number">1</div>
                    <div class="tba-step-body">
                        <label class="tba-label" for="tba-niche-input">Enter Your Niche or Topic</label>
                        <input type="text" id="tba-niche-input" class="tba-input"
                            placeholder="e.g. Personal Finance, Fitness for Beginners, AI Tools..."
                            value="" autocomplete="off" style="margin-bottom:12px;" />

                        <label class="tba-label" for="tba-keywords-input">Focus Keywords (SEO)</label>
                        <input type="text" id="tba-keywords-input" class="tba-input"
                            placeholder="e.g. passive income tips, smart investing (comma separated)"
                            value="" autocomplete="off" style="margin-bottom:14px;" />

                        <button id="tba-btn-find-titles" class="tba-btn tba-btn-primary tba-btn-full" style="display:block; width:100%;">
                            <span class="tba-btn-icon">🔍</span> Find Titles
                        </button>
                        <div class="tba-hint" style="margin-top:8px;">AI will suggest 5 engaging title ideas tailored to your niche and keywords.</div>
                    </div>
                </div>

                <!-- Step 2: Title Selection -->
                <div class="tba-step tba-step-locked" id="tba-step-titles">
                    <div class="tba-step-number">2</div>
                    <div class="tba-step-body">
                        <label class="tba-label">Choose a Title</label>
                        <div id="tba-titles-list" class="tba-titles-list">
                            <div class="tba-titles-placeholder">Titles will appear here after Step 1</div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Options -->
                <div class="tba-step tba-step-locked" id="tba-step-options">
                    <div class="tba-step-number">3</div>
                    <div class="tba-step-body">
                        <label class="tba-label">Post Options</label>
                        <div class="tba-options-row" style="grid-template-columns: 1fr 1fr; margin-bottom: 12px;">
                            <div class="tba-option-group">
                                <label class="tba-option-label">Status</label>
                                <select id="tba-post-status" class="tba-select">
                                    <option value="draft" <?php selected( get_option('tba_default_status','draft'), 'draft' ); ?>>Draft</option>
                                    <option value="publish" <?php selected( get_option('tba_default_status','draft'), 'publish' ); ?>>Published</option>
                                </select>
                            </div>
                            <div class="tba-option-group">
                                <label class="tba-option-label">Tags Count</label>
                                <select id="tba-tag-count" class="tba-select">
                                    <?php
                                    $saved_count = (int) get_option( 'tba_tag_count', 15 );
                                    foreach ( [5, 10, 15, 20, 25, 30, 40, 50, 75, 100] as $tc ):
                                    ?>
                                    <option value="<?php echo (int) $tc; ?>" <?php selected( $saved_count, $tc ); ?>><?php echo (int) $tc; ?> tags</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="tba-options-row" style="grid-template-columns: 1fr; margin-bottom: 12px;">
                            <div class="tba-option-group">
                                <label class="tba-option-label">Category</label>
                                <select id="tba-post-category" class="tba-select" style="width: 100%;">
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
                        <div class="tba-options-row" style="grid-template-columns: 1fr 1fr; margin-bottom: 12px;">
                            <div class="tba-option-group">
                                <label class="tba-option-label">Thumbnail Method</label>
                                <select id="tba-thumb-type" class="tba-select">
                                    <option value="ai" <?php selected( get_option('tba_thumb_type','ai'), 'ai' ); ?>>AI Generated (Gemini)</option>
                                    <option value="text_to_image" <?php selected( get_option('tba_thumb_type','ai'), 'text_to_image' ); ?>>Title to Image (Local GD)</option>
                                </select>
                            </div>
                             <div class="tba-option-group tba-t2i-only" style="display:none;">
                                 <label class="tba-option-label">Background Selection</label>
                                  <select id="tba-t2i-bg-type" class="tba-select">
                                      <option value="gradient" <?php selected( get_option('tba_t2i_bg_type','gradient'), 'gradient' ); ?>>Gradient Background</option>
                                      <option value="solid" <?php selected( get_option('tba_t2i_bg_type','gradient'), 'solid' ); ?>>Solid Color Background</option>
                                      <option value="image" <?php selected( get_option('tba_t2i_bg_type','gradient'), 'image' ); ?>>Default Image Background (admin/default-thumbnail.jpg)</option>
                                      <option value="mix" <?php selected( get_option('tba_t2i_bg_type','gradient'), 'mix' ); ?>>🎲 Mix Background (Randomize)</option>
                                  </select>
                             </div>
                        </div>

                        <div class="tba-options-row tba-t2i-only" style="grid-template-columns: 1fr 1fr; margin-bottom: 12px; display:none;">
                            <div class="tba-option-group" id="tba-t2i-gradient-group">
                                <label class="tba-option-label">Gradient Color Palette</label>
                                <select id="tba-t2i-bg-val-gradient" class="tba-select">
                                    <?php 
                                    $saved_bg = get_option('tba_t2i_bg_val', 'blue_purple');
                                    foreach ( TBA_Text_To_Image::get_gradients() as $key => $g ): 
                                    ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($saved_bg, $key); ?>><?php echo esc_html($g['label']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="tba-option-group" id="tba-t2i-solid-group" style="display:none;">
                                <label class="tba-option-label">Solid Color Background</label>
                                <select id="tba-t2i-bg-val-solid" class="tba-select">
                                    <?php 
                                    $saved_bg = get_option('tba_t2i_bg_val', 'dark_slate');
                                    foreach ( TBA_Text_To_Image::get_solid_colors() as $key => $s ): 
                                    ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($saved_bg, $key); ?>><?php echo esc_html($s['label']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="tba-option-group">
                                <label class="tba-option-label">Image Ratio &amp; Size</label>
                                <select id="tba-t2i-size" class="tba-select">
                                    <option value="600x315" <?php selected(get_option('tba_t2i_size','600x315'), '600x315'); ?>>Landscape (2:1) — 600×315 px</option>
                                    <option value="1200x630" <?php selected(get_option('tba_t2i_size','600x315'), '1200x630'); ?>>OpenGraph (2:1) — 1200×630 px</option>
                                    <option value="500x500" <?php selected(get_option('tba_t2i_size','600x315'), '500x500'); ?>>Square (1:1) — 500×500 px</option>
                                    <option value="1000x1000" <?php selected(get_option('tba_t2i_size','600x315'), '1000x1000'); ?>>Square High-Res (1:1) — 1000×1000 px</option>
                                </select>
                            </div>
                        </div>

                        <!-- Reference Image Upload (AI Generated Only) -->
                        <div class="tba-ref-img-section tba-ai-thumb-only">
                            <div class="tba-ref-img-header">
                                <span class="tba-ref-img-title">🖼️ Thumbnail Style Reference</span>
                                <?php $has_default = ! empty( TBA_Gemini::get_default_reference_image() ); ?>
                                <?php if ( $has_default ): ?>
                                <span class="tba-badge tba-badge-default" title="A default reference image is set in Settings">✦ Default Set</span>
                                <?php endif; ?>
                                <span class="tba-ref-img-hint">Upload a sample image — Gemini will match its style for the 600×315 thumbnail</span>
                            </div>

                            <div class="tba-upload-zone" id="tba-upload-zone">
                                <div class="tba-upload-idle" id="tba-upload-idle">
                                    <span class="tba-upload-icon">📁</span>
                                    <span class="tba-upload-text">Drag & drop an image here, or <label for="tba-ref-img-input" class="tba-upload-link">browse</label></span>
                                    <span class="tba-upload-sub">JPG, PNG, WEBP — max 4MB</span>
                                </div>
                                <div class="tba-upload-preview" id="tba-upload-preview" style="display:none;">
                                    <img id="tba-ref-img-thumb" src="" alt="Reference preview">
                                    <div class="tba-upload-preview-info">
                                        <span id="tba-ref-img-name" class="tba-upload-filename"></span>
                                        <button type="button" id="tba-btn-clear-ref" class="tba-btn-small tba-btn-danger">✕ Remove</button>
                                    </div>
                                </div>
                                <input type="file" id="tba-ref-img-input" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;">
                            </div>

                            <?php if ( $has_default ): ?>
                            <div class="tba-ref-default-note" id="tba-ref-default-note">
                                <span>ℹ️ Using your <a href="<?php echo esc_url( admin_url('admin.php?page=tba-settings#thumbnail-ref') ); ?>">default reference image</a> from Settings. Upload above to override for this post only.</span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="tba-action-buttons">
                            <button id="tba-btn-preview" class="tba-btn tba-btn-secondary">
                                <span>👁</span> Preview First
                            </button>
                            <button id="tba-btn-generate" class="tba-btn tba-btn-primary" disabled>
                                <span>⚡</span> Generate &amp; Publish
                            </button>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="tba-session-id" value="">
            </div>

            <!-- RIGHT: Progress + Result -->
            <div class="tba-panel tba-progress-panel">
                <div class="tba-panel-header">
                    <h2 class="tba-panel-title">📊 Generation Progress</h2>
                </div>

                <div id="tba-progress-idle" class="tba-progress-idle">
                    <div class="tba-idle-icon">✦</div>
                    <p>Enter a niche and select a title to begin generation.</p>
                </div>

                <div id="tba-progress-steps" class="tba-progress-steps" style="display:none;">
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
                    <div class="tba-progress-step" id="tba-pstep-<?php echo esc_attr($key); ?>" data-step="<?php echo esc_attr($key); ?>">
                        <div class="tba-pstep-icon"><?php echo esc_html( $step['icon'] ); ?></div>
                        <div class="tba-pstep-body">
                            <div class="tba-pstep-label"><?php echo esc_html($step['label']); ?></div>
                            <div class="tba-pstep-meta"></div>
                        </div>
                        <div class="tba-pstep-status">
                            <span class="tba-pstep-dot waiting"></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Key switch notification -->
                <div id="tba-key-switch-notice" class="tba-key-switch-notice" style="display:none;">
                    <span class="tba-ksn-icon">⚡</span>
                    <span id="tba-key-switch-text"></span>
                </div>

                <!-- Result -->
                <div id="tba-result" class="tba-result" style="display:none;"></div>

                <!-- Preview Modal -->
                <div id="tba-preview-panel" class="tba-preview-panel" style="display:none;">
                    <div class="tba-preview-header">
                        <h3 id="tba-preview-title"></h3>
                        <div class="tba-preview-meta">
                            <span id="tba-preview-category" class="tba-preview-tag"></span>
                            <span id="tba-preview-meta-desc" class="tba-preview-meta-desc"></span>
                        </div>
                    </div>
                    <div id="tba-preview-content" class="tba-preview-content"></div>
                    <div class="tba-preview-tags">
                        <strong>Tags:</strong>
                        <span id="tba-preview-tags"></span>
                    </div>
                    <div class="tba-preview-actions">
                        <button id="tba-btn-confirm-publish" class="tba-btn tba-btn-primary">✅ Looks Good — Publish</button>
                        <button id="tba-btn-cancel-preview" class="tba-btn tba-btn-ghost">✏️ Back to Edit</button>
                    </div>
                </div>

            </div>

        </div><!-- .tba-two-col -->

    </div><!-- .tba-content -->
</div>
