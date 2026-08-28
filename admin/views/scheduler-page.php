<?php if ( ! defined( 'ABSPATH' ) ) exit;
$queue_items = SAB_Queue::get_all(50);
$queue_count = SAB_Queue::count_by_status();
$niches_text = get_option( SAB_Scheduler::OPTION_NICHES, '' );
$per_day     = (int) get_option( SAB_Scheduler::OPTION_PER_DAY, 3 );
$enabled     = SAB_Scheduler::is_enabled();
$next_run    = SAB_Scheduler::get_next_run();
$msg_map     = [
    'saved'                  => ['type'=>'success','text'=>'✅ Schedule settings saved.'],
    'queued'                 => ['type'=>'success','text'=>'✅ Niche added to queue.'],
    'queue_deleted'          => ['type'=>'success','text'=>'✅ Queue item deleted.'],
    'queue_paused'           => ['type'=>'success','text'=>'⏸ Queue item paused.'],
    'queue_resumed'          => ['type'=>'success','text'=>'▶ Queue item resumed/queued.'],
    'queue_cleared'          => ['type'=>'success','text'=>'🧹 Entire queue cleared successfully.'],
    'queue_selected_deleted' => ['type'=>'success','text'=>'🗑️ Selected queue items deleted.'],
];
$msg = isset( $_GET['msg'] ) ? sanitize_key( $_GET['msg'] ) : ( isset($_GET['saved']) ? 'saved' : '' );
?>
<div class="sab-wrap">
    <div class="sab-header">
        <div class="sab-header-inner">
            <div class="sab-logo">
                <img src="<?php echo esc_url( SAB_PLUGIN_URL . 'admin/soniji-auto-blogging-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="sab-logo-badge">Scheduler</span>
            </div>
                        <div class="sab-header-nav">
    <a href="<?php echo esc_url( admin_url('admin.php?page=soniji-auto-blogging') ); ?>" class="sab-nav-link">Dashboard</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-generate') ); ?>" class="sab-nav-link">Generate Post</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-planner') ); ?>" class="sab-nav-link">Bulk Planner</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-scheduler') ); ?>" class="sab-nav-link active">Scheduler</a>
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
        <div class="sab-alert sab-alert-<?php echo esc_attr( $msg_map[$msg]['type'] ); ?>"><?php echo esc_html($msg_map[$msg]['text']); ?></div>
        <?php endif; ?>

        <div class="sab-two-col">

            <!-- Schedule Settings -->
            <div class="sab-panel">
                <div class="sab-panel-header">
                    <h2 class="sab-panel-title">📅 Auto-Schedule Settings</h2>
                    <span class="sab-status-badge <?php echo $enabled ? 'sab-status-active' : 'sab-status-exhausted'; ?>">
                        <?php echo $enabled ? '✅ Running' : '⏸ Paused'; ?>
                    </span>
                </div>

                <?php if ( $enabled ): ?>
                <div class="sab-info-box">
                    <div class="sab-info-row">
                        <span class="sab-info-label">Next Run:</span>
                        <span class="sab-info-value"><?php echo esc_html($next_run); ?></span>
                    </div>
                    <div class="sab-info-row">
                        <span class="sab-info-label">Posts/Day:</span>
                        <span class="sab-info-value"><?php echo (int) $per_day; ?></span>
                    </div>
                    <div class="sab-info-row">
                        <span class="sab-info-label">Queue Pending:</span>
                        <span class="sab-info-value"><?php echo (int) SAB_Queue::count_by_status('queued'); ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                    <?php wp_nonce_field('sab_save_schedule'); ?>
                    <input type="hidden" name="action" value="sab_save_schedule">

                    <div class="sab-field">
                        <label class="sab-toggle">
                            <input type="checkbox" name="schedule_enabled" value="1" <?php checked($enabled,true); ?>>
                            <span class="sab-toggle-slider"></span>
                            <span class="sab-toggle-label"><strong>Enable Auto-Scheduler</strong></span>
                        </label>
                        <div class="sab-hint">Uses WP-Cron to automatically generate and publish posts on schedule.</div>
                    </div>

                    <div class="sab-field">
                        <label class="sab-label">Posts Per Day</label>
                        <input type="number" name="posts_per_day" class="sab-input sab-input-sm" value="<?php echo (int) $per_day; ?>" min="1" max="50">
                    </div>

                    <div class="sab-field">
                        <label class="sab-label">Niche Rotation List</label>
                        <textarea name="schedule_niches" class="sab-textarea" rows="6"
                            placeholder="Enter one niche per line:&#10;Personal Finance&#10;Home Improvement&#10;Digital Marketing&#10;Fitness Tips"
                        ><?php echo esc_textarea($niches_text); ?></textarea>
                        <div class="sab-hint">Scheduler will cycle through these niches in order. If queue has pending items, those are processed first.</div>
                    </div>

                    <div class="sab-form-actions">
                        <button type="submit" class="sab-btn sab-btn-primary">💾 Save Schedule</button>
                    </div>
                </form>
            </div>

            <!-- Post Queue -->
            <div class="sab-panel">
                <div class="sab-panel-header">
                    <h2 class="sab-panel-title">📋 Post Queue & Planner Tasks</h2>
                    <div class="sab-panel-actions">
                        <button type="button" id="sab-btn-run-queue" class="sab-btn sab-btn-secondary sab-btn-sm" style="font-weight:600;">
                            🚀 Run Queue Now
                        </button>
                    </div>
                </div>

                <!-- Live Queue Runner Console (Hidden by default) -->
                <div class="sab-queue-console" id="sab-queue-console" style="display:none; background: rgba(0,0,0,0.2); border:1px solid var(--sab-border); border-radius:6px; padding:12px; margin-bottom:15px; font-size:12px; font-family:var(--sab-font);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <strong>⚙️ Active Queue Processor Console</strong>
                        <span id="sab-queue-console-status" style="color:var(--sab-primary); font-weight:bold;">Running...</span>
                    </div>
                    <div id="sab-queue-console-logs" style="max-height:100px; overflow-y:auto; color:#a7f3d0; line-height:1.5; font-family: monospace;">
                        Starting queue processor...
                    </div>
                </div>

                <!-- Add to queue -->
                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" class="sab-add-key-form">
                    <?php wp_nonce_field('sab_enqueue_niche'); ?>
                    <input type="hidden" name="action" value="sab_enqueue_niche">
                    <div class="sab-input-row">
                        <input type="text" name="niche" class="sab-input" placeholder="Add custom niche line to queue (e.g. Finance | tips)...">
                        <button type="submit" class="sab-btn sab-btn-primary">➕ Add to Queue</button>
                    </div>
                </form>

                <?php if ( empty($queue_items) ): ?>
                <div class="sab-empty-state">Queue is empty. Plan some articles in the Bulk Planner.</div>
                <?php else: ?>
                
                <!-- Queue Bulk Actions Toolbar -->
                <div class="sab-queue-toolbar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; background:rgba(255,255,255,0.03); padding:10px; border-radius:6px; border:1px solid var(--sab-border);">
                    <div class="sab-queue-toolbar-left" style="display:flex; align-items:center; gap:10px;">
                        <button type="button" id="sab-btn-delete-selected" class="sab-btn sab-btn-danger sab-btn-sm" style="display:none; background:#ea580c; border-color:#ea580c;" onclick="sabDeleteSelectedQueue()">✕ Delete Selected</button>
                    </div>
                    <div class="sab-queue-toolbar-right">
                        <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" onsubmit="return confirm('Are you sure you want to clear the entire queue? This cannot be undone.')" style="margin:0;">
                            <?php wp_nonce_field('sab_clear_queue'); ?>
                            <input type="hidden" name="action" value="sab_clear_queue">
                            <button type="submit" class="sab-btn sab-btn-danger sab-btn-sm" style="background:#dc2626; border-color:#dc2626;">🗑️ Clear All Queue</button>
                        </form>
                    </div>
                </div>

                <!-- Hidden Delete Selected Form -->
                <form id="sab-form-delete-selected-queue" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                    <?php wp_nonce_field('sab_delete_selected_queue'); ?>
                    <input type="hidden" name="action" value="sab_delete_selected_queue">
                    <input type="hidden" name="queue_ids" id="sab-hidden-delete-queue-ids" value="">
                </form>

                <table class="sab-table sab-table-sm" id="sab-queue-table">
                    <thead>
                        <tr>
                            <th style="width: 30px; text-align: center;"><input type="checkbox" id="sab-select-all-queue"></th>
                            <th>#</th>
                            <th>Niche & Title</th>
                            <th>Language</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($queue_items as $item): ?>
                        <tr class="sab-queue-row-<?php echo esc_attr($item->status); ?>" id="sab-queue-row-<?php echo (int)$item->id; ?>">
                            <td style="text-align: center;"><input type="checkbox" class="sab-queue-checkbox" value="<?php echo (int)$item->id; ?>"></td>
                            <td><?php echo (int)$item->id; ?></td>
                            <td>
                                <strong style="color:var(--sab-text);"><?php echo esc_html($item->niche); ?></strong>
                                <?php if ($item->title): ?>
                                <div class="sab-queue-item-title" style="font-size:11px; margin-top:2px; color:rgba(255,255,255,0.6);"><?php echo esc_html($item->title); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-size:11px;"><?php echo esc_html($item->language ?? 'English'); ?></span>
                            </td>
                            <td>
                                <span class="sab-badge-category" style="font-size:11px; color:#a78bfa; background:rgba(167,139,250,0.1); padding:2px 6px; border-radius:4px; font-weight:600;">
                                    <?php echo esc_html($item->category ?: 'Auto-Suggest'); ?>
                                </span>
                            </td>
                            <td>
                                <span class="sab-status-badge sab-status-<?php echo esc_attr($item->status); ?>" style="display:inline-block;">
                                    <?php
                                    $status_labels = ['queued'=>'⏳ Queued','processing'=>'⚙️ Processing','published'=>'✅ Published','failed'=>'❌ Failed','paused'=>'⏸ Paused'];
                                    echo esc_html( $status_labels[$item->status] ?? $item->status );
                                    ?>
                                </span>
                                <?php if ($item->status === 'failed' && !empty($item->error_msg)): ?>
                                <div class="sab-queue-item-error" style="color:#f87171; font-size:10px; margin-top:4px; max-width:180px; line-height:1.2; word-break:break-word;">
                                    ⚠️ <?php echo esc_html($item->error_msg); ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td><span style="font-size:11px;"><?php echo esc_html(gmdate( 'M j, H:i', strtotime($item->created_at))); ?></span></td>
                            <td>
                                <?php if ($item->status === 'published' && $item->post_id): ?>
                                <a href="<?php echo esc_url( get_permalink($item->post_id) ); ?>" target="_blank" class="sab-btn-small sab-btn-ghost">View</a>
                                <?php endif; ?>
                                
                                <?php if ( in_array($item->status, ['queued', 'processing'], true) ): ?>
                                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" style="display:inline;">
                                    <?php wp_nonce_field('sab_pause_queue'); ?>
                                    <input type="hidden" name="action" value="sab_pause_queue">
                                    <input type="hidden" name="queue_id" value="<?php echo (int)$item->id; ?>">
                                    <button class="sab-btn-small sab-btn-ghost" type="submit" title="Pause Task" style="background:#fffcf0; border-color:#fbbf24; color:#d97706; font-size:9px;">⏸</button>
                                </form>
                                <?php elseif ( in_array($item->status, ['paused', 'failed'], true) ): ?>
                                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" style="display:inline;">
                                    <?php wp_nonce_field('sab_resume_queue'); ?>
                                    <input type="hidden" name="action" value="sab_resume_queue">
                                    <input type="hidden" name="queue_id" value="<?php echo (int)$item->id; ?>">
                                    <button class="sab-btn-small sab-btn-success" type="submit" title="<?php echo $item->status === 'failed' ? 'Retry Task' : 'Resume Task'; ?>" style="font-size:9px; background: #059669; border-color: #059669;">
                                        <?php echo $item->status === 'failed' ? '🔄 Retry' : '▶'; ?>
                                    </button>
                                </form>
                                <?php endif; ?>

                                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" style="display:inline;" onsubmit="return confirm('Delete this queue item?')">
                                    <?php wp_nonce_field('sab_delete_queue'); ?>
                                    <input type="hidden" name="action" value="sab_delete_queue">
                                    <input type="hidden" name="queue_id" value="<?php echo (int)$item->id; ?>">
                                    <button class="sab-btn-small sab-btn-danger" type="submit">✕</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>

        </div>

    </div>
</div>

