<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$history    = TBA_History::get_all(30);
$stats      = TBA_History::get_summary_stats();
$key_stats  = TBA_Key_Manager::get_stats();
$all_keys   = TBA_Key_Manager::get_all_keys();

$msg_map = [
    'deleted'        => ['type'=>'success','text'=>'✅ History entry deleted.'],
    'cleared'        => ['type'=>'success','text'=>'✅ History cleared.'],
    'cache_purged'   => ['type'=>'success','text'=>'⚡ All Site & Transients Caches Purged Successfully!'],
    'settings_reset' => ['type'=>'success','text'=>'⚙️ All Plugin Settings Reset to Defaults.'],
];
// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$msg = $_GET['msg'] ?? '';
$cost_per_1k = 0.00015; // average cost estimate in USD per 1k tokens across models
?>

<div class="tba-wrap">
    <!-- Main Top Navigation -->
    <div class="tba-header">
        <div class="tba-header-inner">
            <div class="tba-logo">
                <img src="<?php echo esc_url( TBA_PLUGIN_URL . 'admin/tools-by-aadi-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="tba-logo-badge">Control Center</span>
            </div>
            <div class="tba-header-nav">
                <a href="<?php echo esc_url( admin_url('admin.php?page=tools-by-aadi') ); ?>" class="tba-nav-link active">Dashboard</a>
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
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-codes') ); ?>" class="tba-nav-link">Codes</a>
                <a href="<?php echo esc_url( admin_url('admin.php?page=tba-settings') ); ?>" class="tba-nav-link">Settings</a>
            </div>
        </div>
    </div>

    <div class="tba-content">
        <?php if ( $msg && isset($msg_map[$msg]) ): ?>
            <div class="tba-alert tba-alert-<?php echo esc_attr( $msg_map[$msg]['type'] ); ?>" style="margin-bottom:18px;">
                <?php echo esc_html($msg_map[$msg]['text']); ?>
            </div>
        <?php endif; ?>

        <!-- Control Action Bar: Red Purge Cache & Reset Settings Buttons -->
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; margin-bottom:20px; background:#ffffff; border:1px solid #c3c4c7; padding:14px 20px; border-radius:6px; box-shadow:0 1px 2px rgba(0,0,0,0.05);">
            <div>
                <h2 style="font-size:16px; font-weight:600; margin:0 0 4px 0; color:var(--tba-text-dark); display:flex; align-items:center; gap:8px;">
                    <span>⚡</span> Tools By Aadi Control Center &amp; Quick Actions
                </h2>
                <p style="margin:0; font-size:13px; color:var(--tba-text-muted);">Manage AI Generation, API Keys, Scheduled Automation, &amp; Site Cache Purge.</p>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" style="margin:0;">
                    <?php wp_nonce_field('tba_purge_speed_cache'); ?>
                    <input type="hidden" name="action" value="tba_purge_speed_cache">
                    <button type="submit" class="button button-primary" style="background:#d63638; border-color:#b32d2e; color:#ffffff; font-weight:600; padding:4px 14px; cursor:pointer;">
                        ⚡ Purge All Site Caches Now
                    </button>
                </form>

                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" style="margin:0;" onsubmit="return confirm('Reset all plugin settings to defaults?')">
                    <?php wp_nonce_field('tba_reset_settings'); ?>
                    <input type="hidden" name="action" value="tba_reset_settings">
                    <button type="submit" class="button button-secondary" style="font-weight:600; padding:4px 14px; cursor:pointer;">
                        ⚙️ Reset Plugin Settings
                    </button>
                </form>
            </div>
        </div>

        <!-- Two Column Dashboard Grid -->
        <div class="tba-two-col">

            <!-- LEFT: Stats, Keys, & History -->
            <div class="tba-col-left">

                <!-- Stats Cards -->
                <div class="tba-stats-grid select-none">
                    <div class="tba-stat-card tba-stat-primary">
                        <div class="tba-stat-icon">📝</div>
                        <div class="tba-stat-value"><?php echo (int) $stats['total']; ?></div>
                        <div class="tba-stat-label">Total Generated</div>
                    </div>
                    <div class="tba-stat-card tba-stat-success">
                        <div class="tba-stat-icon">✅</div>
                        <div class="tba-stat-value"><?php echo (int) $stats['success']; ?></div>
                        <div class="tba-stat-label">Successful</div>
                    </div>
                    <div class="tba-stat-card tba-stat-info">
                        <div class="tba-stat-icon">🔢</div>
                        <div class="tba-stat-value"><?php echo number_format($stats['total_tokens']); ?></div>
                        <div class="tba-stat-label">Est. Tokens Used</div>
                    </div>
                    <div class="tba-stat-card tba-stat-warning">
                        <div class="tba-stat-icon">💰</div>
                        <div class="tba-stat-value">~$<?php echo number_format($stats['total_tokens'] * $cost_per_1k / 1000, 4); ?></div>
                        <div class="tba-stat-label">Est. Cost (USD)</div>
                    </div>
                </div>

                <!-- API Keys Status Panel -->
                <div class="tba-panel">
                    <div class="tba-panel-header">
                        <h2 class="tba-panel-title">🔑 API Key Health Dashboard</h2>
                        <div class="tba-panel-actions">
                            <button type="button" id="tba-btn-ping-all" class="tba-btn tba-btn-secondary tba-btn-sm">🏓 Ping All</button>
                            <a href="<?php echo esc_url( admin_url('admin.php?page=tba-settings') ); ?>" class="tba-btn tba-btn-ghost tba-btn-sm">⚙️ Manage Keys</a>
                        </div>
                    </div>

                    <?php if (empty($all_keys)): ?>
                    <div class="tba-empty-state">
                        No API keys configured. <a href="<?php echo esc_url( admin_url('admin.php?page=tba-settings') ); ?>">Configure keys first →</a>
                    </div>
                    <?php else: ?>
                    <table class="tba-table" id="tba-keys-table">
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
                                $reset = TBA_Key_Manager::seconds_until_reset($k);
                                $prov  = $k['provider'] ?? 'gemini';
                            ?>
                            <tr data-key-index="<?php echo (int) $i; ?>" data-reset-ts="<?php echo (int)($k['reset_at_ts'] ?? 0); ?>">
                                <td>
                                    <span class="tba-badge tba-badge-<?php echo esc_attr($prov); ?>">
                                        <?php echo $prov === 'openai' ? 'OpenAI' : 'Gemini'; ?>
                                    </span>
                                </td>
                                <td><code class="tba-key-masked"><?php echo esc_html(TBA_Key_Manager::mask_key($k['key'])); ?></code></td>
                                <td class="tba-key-status-cell">
                                    <span class="tba-status-badge tba-status-<?php echo esc_attr($k['status']); ?>">
                                        <?php
                                        if ($k['status'] === 'active')    echo '✅ Active';
                                        elseif ($k['status'] === 'invalid') echo '⛔ Invalid';
                                        else                               echo '🔴 Exhausted';
                                        ?>
                                    </span>
                                </td>
                                <td class="tba-key-countdown-cell">
                                    <?php if ($k['status'] === 'exhausted' && $reset !== null): ?>
                                    <span class="tba-countdown" data-reset-ts="<?php echo (int)$k['reset_at_ts']; ?>">
                                        ⏱ <span class="tba-countdown-val"><?php echo esc_html( TBA_Key_Manager::format_seconds($reset) ); ?></span>
                                    </span>
                                    <?php else: ?>
                                    <span class="tba-text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="tba-progress-pct"><?php echo (int) $rate; ?>%</span>
                                </td>
                                <td>
                                    <button type="button" class="tba-btn-small tba-btn-ghost tba-btn-ping" data-key-index="<?php echo (int) $i; ?>">🏓</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>

                <!-- History Log Panel -->
                <div class="tba-panel">
                    <div class="tba-panel-header">
                        <h2 class="tba-panel-title">📋 Recent Generations Log</h2>
                        <?php if (!empty($history)): ?>
                        <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" onsubmit="return confirm('Clear ALL history? This cannot be undone.')">
                            <?php wp_nonce_field('tba_clear_history'); ?>
                            <input type="hidden" name="action" value="tba_clear_history">
                            <button class="tba-btn-small tba-btn-danger" type="submit">🗑 Clear All</button>
                        </form>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($history)): ?>
                    <div class="tba-empty-state">No posts generated yet. Use the Quick Generator to start.</div>
                    <?php else: ?>
                    <table class="tba-table">
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
                                    <a href="<?php echo esc_url( get_permalink($row->post_id) ); ?>" target="_blank" class="tba-history-title-link" style="font-weight:600;">
                                        <?php echo esc_html(wp_trim_words($row->title, 8)); ?>
                                    </a>
                                    <?php
                                    $pending = [];
                                    if ( ! has_post_thumbnail( $row->post_id ) ) {
                                        $pending[] = '<span class="tba-status-badge tba-status-exhausted" style="font-size:10px; padding:1px 4px; margin-top:4px; display:inline-block; margin-right:4px;">🖼️ Thumbnail Pending</span>';
                                    }
                                    $t = get_the_tags( $row->post_id );
                                    if ( empty( $t ) ) {
                                        $pending[] = '<span class="tba-status-badge tba-status-exhausted" style="font-size:10px; padding:1px 4px; margin-top:4px; display:inline-block; margin-right:4px;">🏷️ Tags Pending</span>';
                                    }
                                    if ( ! empty( $pending ) ) {
                                        echo wp_kses_post( '<div class="tba-log-item-meta" style="margin-top:2px;">' . implode( '', $pending ) . '</div>' );
                                    }
                                    ?>
                                    <?php else: ?>
                                    <?php echo esc_html(wp_trim_words($row->title, 8)); ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="tba-badge tba-badge-<?php echo esc_attr($row->key_used && strpos($row->key_used, 'sk-') === 0 ? 'openai' : 'gemini'); ?>">
                                        <?php echo $row->key_used && strpos($row->key_used, 'sk-') === 0 ? 'OpenAI' : 'Gemini'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="tba-status-badge tba-status-<?php echo $row->status==='success'?'active':'exhausted'; ?>">
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
            <div class="tba-col-right">

                <div class="tba-panel tba-generate-panel">
                    <div class="tba-panel-header">
                        <h2 class="tba-panel-title">⚡ Quick Post Generator</h2>
                        <div class="tba-key-badge">
                            <span class="tba-key-dot <?php echo $key_stats['active'] > 0 ? 'active' : 'inactive'; ?>"></span>
                            <span><?php echo (int) $key_stats['active']; ?>/<?php echo (int) $key_stats['total']; ?> Keys Active</span>
                        </div>
                    </div>

                    <!-- Step 1: Niche & Keywords -->
                    <div class="tba-step" id="tba-step-niche">
                        <div class="tba-step-number">1</div>
                        <div class="tba-step-body">
                            <label class="tba-label" for="tba-niche-input">Enter Niche or Topic</label>
                            <input type="text" id="tba-niche-input" class="tba-input"
                                placeholder="e.g. Finance, Weight Loss, Tech Reviews..." autocomplete="off" />

                            <label class="tba-label" for="tba-keywords-input" style="margin-top: 14px;">Focus Keywords (SEO)</label>
                            <input type="text" id="tba-keywords-input" class="tba-input"
                                placeholder="e.g. weight loss tips, how to lose fat (comma separated)" autocomplete="off" />

                            <div style="margin-top: 14px;">
                                <button type="button" id="tba-btn-find-titles" class="tba-btn tba-btn-primary tba-btn-full">
                                    <span class="tba-btn-icon">🔍</span> Find Titles
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Title selection -->
                    <div class="tba-step tba-step-locked" id="tba-step-titles">
                        <div class="tba-step-number">2</div>
                        <div class="tba-step-body">
                            <label class="tba-label">Select Title</label>
                            <div class="tba-titles-list" id="tba-titles-list">
                                <div class="tba-titles-placeholder">Find titles first...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Options & Reference Image -->
                    <div class="tba-step tba-step-locked" id="tba-step-options">
                        <div class="tba-step-number">3</div>
                        <div class="tba-step-body">
                            <div class="tba-options-row">
                                <div class="tba-option-group">
                                    <label class="tba-option-label">Status</label>
                                    <select id="tba-post-status" class="tba-select">
                                        <option value="draft" <?php selected( get_option('tba_default_status','draft'), 'draft' ); ?>>Draft</option>
                                        <option value="publish" <?php selected( get_option('tba_default_status','draft'), 'publish' ); ?>>Published</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Image style reference -->
                            <div class="tba-ref-img-section">
                                <div class="tba-ref-img-header">
                                    <span class="tba-ref-img-title">🖼️ Thumbnail Reference Style</span>
                                    <?php $has_default = ! empty( TBA_Gemini::get_default_reference_image() ); ?>
                                    <?php if ( $has_default ): ?>
                                    <span class="tba-badge tba-badge-default" title="A default reference image is set in Settings">✦ Default Set</span>
                                    <?php endif; ?>
                                </div>

                                <div class="tba-upload-zone" id="tba-upload-zone">
                                    <div class="tba-upload-idle" id="tba-upload-idle">
                                        <span class="tba-upload-icon">📁</span>
                                        <span class="tba-upload-text">Drag sample image here or <label for="tba-ref-img-input" class="tba-upload-link">browse</label></span>
                                    </div>
                                    <div class="tba-upload-preview" id="tba-upload-preview" style="display:none;">
                                        <img id="tba-ref-img-thumb" src="" alt="Reference Preview">
                                        <div class="tba-upload-preview-info">
                                            <span id="tba-ref-img-name" class="tba-upload-filename"></span>
                                            <button type="button" id="tba-btn-clear-ref" class="tba-btn-small tba-btn-danger">✕</button>
                                        </div>
                                    </div>
                                    <input type="file" id="tba-ref-img-input" accept="image/*" style="display:none;">
                                </div>
                            </div>

                            <div class="tba-action-buttons" style="margin-top:20px;">
                                <button id="tba-btn-preview" class="tba-btn tba-btn-secondary">👁 Preview</button>
                                <button id="tba-btn-generate" class="tba-btn tba-btn-primary" disabled>⚡ Generate</button>
                            </div>
                        </div>
                    </div>

                    <!-- Progress pipeline -->
                    <div class="tba-progress-panel" id="tba-progress-steps" style="display:none;">
                        <div class="tba-panel-header" style="padding: 10px 0; border-bottom: 1px solid var(--tba-border);">
                            <h3 class="tba-panel-title" style="font-size:13px;">🔄 Pipeline Progress</h3>
                        </div>

                        <!-- Live key switch notice -->
                        <div class="tba-alert tba-alert-warning" id="tba-key-switch-notice" style="display:none; margin-top:10px;">
                            <span class="tba-alert-icon">⚡</span>
                            <span id="tba-key-switch-text">Switching keys...</span>
                        </div>

                        <div class="tba-progress-list">
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
                            <div class="tba-progress-step" id="tba-pstep-<?php echo esc_attr( $step_id ); ?>">
                                <div class="tba-pstep-dot waiting"></div>
                                <div class="tba-pstep-body">
                                    <div class="tba-pstep-label"><?php echo esc_html($step_label); ?></div>
                                    <div class="tba-pstep-meta"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Output Results -->
                    <div class="tba-result-box" id="tba-result" style="display:none;"></div>
                </div>

            </div>

        </div>

        <!-- Preview Modal Overlay -->
        <div class="tba-preview-panel" id="tba-preview-panel" style="display:none;">
            <div class="tba-preview-header">
                <div class="tba-preview-header-title">👁 Post Preview (SEO &amp; Readability Approved)</div>
                <div class="tba-preview-header-actions">
                    <button id="tba-btn-confirm-publish" class="tba-btn tba-btn-primary">🚀 Publish Post Now</button>
                    <button id="tba-btn-cancel-preview" class="tba-btn tba-btn-secondary">✕ Close</button>
                </div>
            </div>
            <div class="tba-preview-body">
                <div class="tba-preview-meta-row">
                    <strong>Category:</strong> <span id="tba-preview-category"></span> |
                    <strong>Tags:</strong> <span id="tba-preview-tags"></span>
                </div>
                <div class="tba-preview-meta-row">
                    <strong>Meta Description:</strong> <span id="tba-preview-meta-desc"></span>
                </div>
                <h1 class="tba-preview-post-title" id="tba-preview-title"></h1>
                <div class="tba-preview-content-area" id="tba-preview-content"></div>
            </div>
        </div>

    </div>
</div>

<input type="hidden" id="tba-session-id" value="">
