<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$history    = SAB_History::get_all(30);
$stats      = SAB_History::get_summary_stats();
$key_stats  = SAB_Key_Manager::get_stats();
$all_keys   = SAB_Key_Manager::get_all_keys();

$msg_map = [
    'deleted'        => ['type'=>'success','text'=>'✅ History entry deleted.'],
    'cleared'        => ['type'=>'success','text'=>'✅ History cleared.'],
    'cache_purged'   => ['type'=>'success','text'=>'⚡ All Site & Transients Caches Purged Successfully!'],
    'settings_reset' => ['type'=>'success','text'=>'⚙️ All Plugin Settings Reset to Defaults.'],
];
$msg = isset( $_GET['msg'] ) ? sanitize_key( $_GET['msg'] ) : '';
$cost_per_1k = 0.00015; // average cost estimate in USD per 1k tokens across models
?>

<div class="sab-wrap">
    <!-- Main Top Navigation -->
    <div class="sab-header">
        <div class="sab-header-inner">
            <div class="sab-logo">
                <img src="<?php echo esc_url( SAB_PLUGIN_URL . 'admin/soniji-auto-blogging-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="sab-logo-badge">Control Center</span>
            </div>
            <div class="sab-header-nav">
                <a href="<?php echo esc_url( admin_url('admin.php?page=soniji-auto-blogging') ); ?>" class="sab-nav-link active">Dashboard</a>
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
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-codes') ); ?>" class="sab-nav-link">Codes</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=sab-settings') ); ?>" class="sab-nav-link">Settings</a>
            </div>
        </div>
    </div>

    <div class="sab-content">
        <?php if ( $msg && isset($msg_map[$msg]) ): ?>
            <div class="sab-alert sab-alert-<?php echo esc_attr( $msg_map[$msg]['type'] ); ?>" style="margin-bottom:18px;">
                <?php echo esc_html($msg_map[$msg]['text']); ?>
            </div>
        <?php endif; ?>

        <!-- Control Action Bar: Red Purge Cache & Reset Settings Buttons -->
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; margin-bottom:20px; background:#ffffff; border:1px solid #c3c4c7; padding:14px 20px; border-radius:6px; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
            <div>
                <h2 style="font-size:16px; font-weight:600; margin:0 0 4px 0; color:var(--sab-text-dark); display:flex; align-items:center; gap:8px;">
                    <span>⚡</span> Soniji Auto Blogging Control Center &amp; Quick Actions
                </h2>
                <p style="margin:0; font-size:13px; color:var(--sab-text-muted);">Manage AI Generation, API Keys, Scheduled Automation, &amp; Site Cache Purge.</p>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" style="margin:0;">
                    <?php wp_nonce_field('sab_purge_speed_cache'); ?>
                    <input type="hidden" name="action" value="sab_purge_speed_cache">
                    <button type="submit" class="button button-primary" style="background:#d63638; border-color:#b32d2e; color:#ffffff; font-weight:600; padding:4px 14px; cursor:pointer;">
                        ⚡ Purge All Site Caches Now
                    </button>
                </form>

                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" style="margin:0;" onsubmit="return confirm('Reset all plugin settings to defaults?')">
                    <?php wp_nonce_field('sab_reset_settings'); ?>
                    <input type="hidden" name="action" value="sab_reset_settings">
                    <button type="submit" class="button button-secondary" style="font-weight:600; padding:4px 14px; cursor:pointer;">
                        ⚙️ Reset Plugin Settings
                    </button>
                </form>
            </div>
        </div>

        <!-- Two Column Dashboard Grid -->
        <div class="sab-two-col">

            <!-- LEFT: Stats, Keys, & History -->
            <div class="sab-col-left">

                <!-- Stats Cards -->
                <div class="sab-stats-grid select-none">
                    <div class="sab-stat-card sab-stat-primary">
                        <div class="sab-stat-icon">📝</div>
                        <div class="sab-stat-value"><?php echo (int) $stats['total']; ?></div>
                        <div class="sab-stat-label">Total Generated</div>
                    </div>
                    <div class="sab-stat-card sab-stat-success">
                        <div class="sab-stat-icon">✅</div>
                        <div class="sab-stat-value"><?php echo (int) $stats['success']; ?></div>
                        <div class="sab-stat-label">Successful</div>
                    </div>
                    <div class="sab-stat-card sab-stat-info">
                        <div class="sab-stat-icon">🔢</div>
                        <div class="sab-stat-value"><?php echo number_format($stats['total_tokens']); ?></div>
                        <div class="sab-stat-label">Est. Tokens Used</div>
                    </div>
                    <div class="sab-stat-card sab-stat-warning">
                        <div class="sab-stat-icon">💰</div>
                        <div class="sab-stat-value">~$<?php echo number_format($stats['total_tokens'] * $cost_per_1k / 1000, 4); ?></div>
                        <div class="sab-stat-label">Est. Cost (USD)</div>
                    </div>
                </div>

                <!-- API Keys Status Panel -->
                <div class="sab-panel">
                    <div class="sab-panel-header">
                        <h2 class="sab-panel-title">🔑 API Key Health Dashboard</h2>
                        <div class="sab-panel-actions">
                            <button type="button" id="sab-btn-ping-all" class="sab-btn sab-btn-secondary sab-btn-sm">🏓 Ping All</button>
                            <a href="<?php echo esc_url( admin_url('admin.php?page=sab-settings') ); ?>" class="sab-btn sab-btn-ghost sab-btn-sm">⚙️ Manage Keys</a>
                        </div>
                    </div>

                    <?php if (empty($all_keys)): ?>
                    <div class="sab-empty-state">
                        No API keys configured. <a href="<?php echo esc_url( admin_url('admin.php?page=sab-settings') ); ?>">Configure keys first →</a>
                    </div>
                    <?php else: ?>
                    <table class="sab-table" id="sab-keys-table">
                        <thead>
                            <tr>
                                <th>Provider</th>
                                <th>API Key</th>
                                <th>Status</th>
                                <th>Resets In</th>
                                <th>Success Rate</th>
                                <th>Test</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_keys as $i => $k):
                                $req   = (int)($k['requests'] ?? 0);
                                $fail  = (int)($k['failures'] ?? 0);
                                $rate  = $req > 0 ? round(($req - $fail) / $req * 100) : 100;
                                $reset = SAB_Key_Manager::seconds_until_reset($k);
                                $prov  = $k['provider'] ?? 'gemini';
                            ?>
                            <tr data-key-index="<?php echo (int) $i; ?>" data-reset-ts="<?php echo (int)($k['reset_at_ts'] ?? 0); ?>">
                                <td>
                                    <span class="sab-badge sab-badge-<?php echo esc_attr($prov); ?>">
                                        <?php echo $prov === 'openai' ? 'OpenAI' : 'Gemini'; ?>
                                    </span>
                                </td>
                                <td><code class="sab-key-masked"><?php echo esc_html(SAB_Key_Manager::mask_key($k['key'])); ?></code></td>
                                <td class="sab-key-status-cell">
                                    <span class="sab-status-badge sab-status-<?php echo esc_attr($k['status']); ?>">
                                        <?php
                                        if ($k['status'] === 'active')    echo '✅ Active';
                                        elseif ($k['status'] === 'invalid') echo '⛔ Invalid';
                                        else                               echo '🔴 Exhausted';
                                        ?>
                                    </span>
                                </td>
                                <td class="sab-key-countdown-cell">
                                    <?php if ($k['status'] === 'exhausted' && $reset !== null): ?>
                                    <span class="sab-countdown" data-reset-ts="<?php echo (int)$k['reset_at_ts']; ?>">
                                        ⏱ <span class="sab-countdown-val"><?php echo esc_html( SAB_Key_Manager::format_seconds($reset) ); ?></span>
                                    </span>
                                    <?php else: ?>
                                    <span class="sab-text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="sab-progress-pct"><?php echo (int) $rate; ?>%</span>
                                </td>
                                <td>
                                    <button type="button" class="sab-btn-small sab-btn-ghost sab-btn-ping" data-key-index="<?php echo (int) $i; ?>">🏓</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>

                <!-- History Log Panel -->
                <div class="sab-panel">
                    <div class="sab-panel-header">
                        <h2 class="sab-panel-title">📋 Recent Generations Log</h2>
                        <?php if (!empty($history)): ?>
                        <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" onsubmit="return confirm('Clear ALL history? This cannot be undone.')">
                            <?php wp_nonce_field('sab_clear_history'); ?>
                            <input type="hidden" name="action" value="sab_clear_history">
                            <button class="sab-btn-small sab-btn-danger" type="submit">🗑 Clear All</button>
                        </form>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($history)): ?>
                    <div class="sab-empty-state">No posts generated yet. Use the Quick Generator to start.</div>
                    <?php else: ?>
                    <table class="sab-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Title</th>
                                <th>Provider</th>
                                <th>Status</th>
                                <th>Tokens</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($history as $row): ?>
                            <tr>
                                <td><?php echo esc_html(gmdate( 'M j, H:i', strtotime($row->created_at))); ?></td>
                                <td>
                                    <?php if ($row->post_id): ?>
                                    <a href="<?php echo esc_url( get_permalink($row->post_id) ); ?>" target="_blank" class="sab-history-title-link" style="font-weight:600;">
                                        <?php echo esc_html(wp_trim_words($row->title, 8)); ?>
                                    </a>
                                    <?php
                                    $pending = [];
                                    if ( ! has_post_thumbnail( $row->post_id ) ) {
                                        $pending[] = '<span class="sab-status-badge sab-status-exhausted" style="font-size:10px; padding:1px 4px; margin-top:4px; display:inline-block; margin-right:4px;">🖼️ Thumbnail Pending</span>';
                                    }
                                    $t = get_the_tags( $row->post_id );
                                    if ( empty( $t ) ) {
                                        $pending[] = '<span class="sab-status-badge sab-status-exhausted" style="font-size:10px; padding:1px 4px; margin-top:4px; display:inline-block; margin-right:4px;">🏷️ Tags Pending</span>';
                                    }
                                    if ( ! empty( $pending ) ) {
                                        echo wp_kses_post( '<div class="sab-log-item-meta" style="margin-top:2px;">' . implode( '', $pending ) . '</div>' );
                                    }
                                    ?>
                                    <?php else: ?>
                                    <?php echo esc_html(wp_trim_words($row->title, 8)); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="sab-badge sab-badge-<?php echo esc_attr($row->key_used && strpos($row->key_used, 'sk-') === 0 ? 'openai' : 'gemini'); ?>">
                                        <?php echo $row->key_used && strpos($row->key_used, 'sk-') === 0 ? 'OpenAI' : 'Gemini'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="sab-status-badge sab-status-<?php echo $row->status==='success'?'active':'exhausted'; ?>">
                                        <?php echo $row->status==='success' ? '✅ Success' : '❌ Failed'; ?>
                                    </span>
                                </td>
                                <td>~<?php echo number_format($row->token_estimate); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>

            </div>

            <!-- RIGHT: Quick Generator Form -->
            <div class="sab-col-right">

                <div class="sab-panel sab-generate-panel">
                    <div class="sab-panel-header">
                        <h2 class="sab-panel-title">⚡ Quick Post Generator</h2>
                        <div class="sab-key-badge">
                            <span class="sab-key-dot <?php echo $key_stats['active'] > 0 ? 'active' : 'inactive'; ?>"></span>
                            <span><?php echo (int) $key_stats['active']; ?>/<?php echo (int) $key_stats['total']; ?> Keys Active</span>
                        </div>
                    </div>

                    <!-- Step 1: Niche & Keywords -->
                    <div class="sab-step" id="sab-step-niche">
                        <div class="sab-step-number">1</div>
                        <div class="sab-step-body">
                            <label class="sab-label" for="sab-niche-input">Enter Niche or Topic</label>
                            <input type="text" id="sab-niche-input" class="sab-input"
                                placeholder="e.g. Finance, Weight Loss, Tech Reviews..." autocomplete="off" />

                            <label class="sab-label" for="sab-keywords-input" style="margin-top: 14px;">Focus Keywords (SEO)</label>
                            <input type="text" id="sab-keywords-input" class="sab-input"
                                placeholder="e.g. weight loss tips, how to lose fat (comma separated)" autocomplete="off" />

                            <div style="margin-top: 14px;">
                                <button type="button" id="sab-btn-find-titles" class="sab-btn sab-btn-primary sab-btn-full">
                                    <span class="sab-btn-icon">🔍</span> Find Titles
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Title selection -->
                    <div class="sab-step sab-step-locked" id="sab-step-titles">
                        <div class="sab-step-number">2</div>
                        <div class="sab-step-body">
                            <label class="sab-label">Select Title</label>
                            <div class="sab-titles-list" id="sab-titles-list">
                                <div class="sab-titles-placeholder">Find titles first...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Options & Reference Image -->
                    <div class="sab-step sab-step-locked" id="sab-step-options">
                        <div class="sab-step-number">3</div>
                        <div class="sab-step-body">
                            <div class="sab-options-row">
                                <div class="sab-option-group">
                                    <label class="sab-option-label">Status</label>
                                    <select id="sab-post-status" class="sab-select">
                                        <option value="draft" <?php selected( get_option('sab_default_status','draft'), 'draft' ); ?>>Draft</option>
                                        <option value="publish" <?php selected( get_option('sab_default_status','draft'), 'publish' ); ?>>Published</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Image style reference -->
                            <div class="sab-ref-img-section">
                                <div class="sab-ref-img-header">
                                    <span class="sab-ref-img-title">🖼️ Thumbnail Reference Style</span>
                                    <?php $has_default = ! empty( SAB_Gemini::get_default_reference_image() ); ?>
                                    <?php if ( $has_default ): ?>
                                    <span class="sab-badge sab-badge-default" title="A default reference image is set in Settings">✦ Default Set</span>
                                    <?php endif; ?>
                                </div>

                                <div class="sab-upload-zone" id="sab-upload-zone">
                                    <div class="sab-upload-idle" id="sab-upload-idle">
                                        <span class="sab-upload-icon">📁</span>
                                        <span class="sab-upload-text">Drag sample image here or <label for="sab-ref-img-input" class="sab-upload-link">browse</label></span>
                                    </div>
                                    <div class="sab-upload-preview" id="sab-upload-preview" style="display:none;">
                                        <img id="sab-ref-img-thumb" src="" alt="Reference Preview">
                                        <div class="sab-upload-preview-info">
                                            <span id="sab-ref-img-name" class="sab-upload-filename"></span>
                                            <button type="button" id="sab-btn-clear-ref" class="sab-btn-small sab-btn-danger">✕</button>
                                        </div>
                                    </div>
                                    <input type="file" id="sab-ref-img-input" accept="image/*" style="display:none;">
                                </div>
                            </div>

                            <div class="sab-action-buttons" style="margin-top:20px;">
                                <button id="sab-btn-preview" class="sab-btn sab-btn-secondary">👁 Preview</button>
                                <button id="sab-btn-generate" class="sab-btn sab-btn-primary" disabled>⚡ Generate</button>
                            </div>
                        </div>
                    </div>

                    <!-- Progress pipeline -->
                    <div class="sab-progress-panel" id="sab-progress-steps" style="display:none;">
                        <div class="sab-panel-header" style="padding: 10px 0; border-bottom: 1px solid var(--sab-border);">
                            <h3 class="sab-panel-title" style="font-size:13px;">🔄 Pipeline Progress</h3>
                        </div>

                        <!-- Live key switch notice -->
                        <div class="sab-alert sab-alert-warning" id="sab-key-switch-notice" style="display:none; margin-top:10px;">
                            <span class="sab-alert-icon">⚡</span>
                            <span id="sab-key-switch-text">Switching keys...</span>
                        </div>

                        <div class="sab-progress-list">
                            <?php
                            $steps = [
                                'article'   => '📝 Write Article (Unique & Human)',
                                'tags'      => '🏷️ SEO Tags Generation',
                                'meta'      => '🔍 Meta Description',
                                'category'  => '📂 Category Auto-Assignment',
                                'thumbnail' => '🖼️ Thumbnail Style Match',
                                'og_image'  => '📱 Social OpenGraph Image',
                                'alt_text'  => '✍️ Image Alt Text Tags',
                                'publish'   => '🚀 Create Post & Publish',
                            ];
                            foreach ( $steps as $step_id => $step_label ):
                            ?>
                            <div class="sab-progress-step" id="sab-pstep-<?php echo esc_attr( $step_id ); ?>">
                                <div class="sab-pstep-dot waiting"></div>
                                <div class="sab-pstep-body">
                                    <div class="sab-pstep-label"><?php echo esc_html($step_label); ?></div>
                                    <div class="sab-pstep-meta"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Output Results -->
                    <div class="sab-result-box" id="sab-result" style="display:none;"></div>
                </div>

            </div>

        </div>

        <!-- Preview Modal Overlay -->
        <div class="sab-preview-panel" id="sab-preview-panel" style="display:none;">
            <div class="sab-preview-header">
                <div class="sab-preview-header-title">👁 Post Preview (SEO &amp; Readability Approved)</div>
                <div class="sab-preview-header-actions">
                    <button id="sab-btn-confirm-publish" class="sab-btn sab-btn-primary">🚀 Publish Post Now</button>
                    <button id="sab-btn-cancel-preview" class="sab-btn sab-btn-secondary">✕ Close</button>
                </div>
            </div>
            <div class="sab-preview-body">
                <div class="sab-preview-meta-row">
                    <strong>Category:</strong> <span id="sab-preview-category"></span> |
                    <strong>Tags:</strong> <span id="sab-preview-tags"></span>
                </div>
                <div class="sab-preview-meta-row">
                    <strong>Meta Description:</strong> <span id="sab-preview-meta-desc"></span>
                </div>
                <h1 class="sab-preview-post-title" id="sab-preview-title"></h1>
                <div class="sab-preview-content-area" id="sab-preview-content"></div>
            </div>
        </div>

    </div>
</div>

<input type="hidden" id="sab-session-id" value="">
