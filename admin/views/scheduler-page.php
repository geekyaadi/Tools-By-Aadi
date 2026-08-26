<?php if ( ! defined( 'ABSPATH' ) ) exit;
$queue_items = TBA_Queue::get_all(50);
$queue_count = TBA_Queue::count_by_status();
$niches_text = get_option( TBA_Scheduler::OPTION_NICHES, '' );
$per_day     = (int) get_option( TBA_Scheduler::OPTION_PER_DAY, 3 );
$enabled     = TBA_Scheduler::is_enabled();
$next_run    = TBA_Scheduler::get_next_run();
$msg_map     = [
    'saved'                  => ['type'=>'success','text'=>'✅ Schedule settings saved.'],
    'queued'                 => ['type'=>'success','text'=>'✅ Niche added to queue.'],
    'queue_deleted'          => ['type'=>'success','text'=>'✅ Queue item deleted.'],
    'queue_paused'           => ['type'=>'success','text'=>'⏸ Queue item paused.'],
    'queue_resumed'          => ['type'=>'success','text'=>'▶ Queue item resumed/queued.'],
    'queue_cleared'          => ['type'=>'success','text'=>'🧹 Entire queue cleared successfully.'],
    'queue_selected_deleted' => ['type'=>'success','text'=>'🗑️ Selected queue items deleted.'],
];
// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
$msg = $_GET['msg'] ?? ( isset($_GET['saved']) ? 'saved' : '' );
?>
<div class="tba-wrap">
    <div class="tba-header">
        <div class="tba-header-inner">
            <div class="tba-logo">
                <img src="<?php echo esc_url( TBA_PLUGIN_URL . 'admin/tools-by-aadi-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="tba-logo-badge">Scheduler</span>
            </div>
                        <div class="tba-header-nav">
    <a href="<?php echo esc_url( admin_url('admin.php?page=tools-by-aadi') ); ?>" class="tba-nav-link">Dashboard</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-generate') ); ?>" class="tba-nav-link">Generate Post</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-planner') ); ?>" class="tba-nav-link">Bulk Planner</a>
    <a href="<?php echo esc_url( admin_url('admin.php?page=tba-scheduler') ); ?>" class="tba-nav-link active">Scheduler</a>
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
        <div class="tba-alert tba-alert-<?php echo esc_attr( $msg_map[$msg]['type'] ); ?>"><?php echo esc_html($msg_map[$msg]['text']); ?></div>
        <?php endif; ?>

        <div class="tba-two-col">

            <!-- Schedule Settings -->
            <div class="tba-panel">
                <div class="tba-panel-header">
                    <h2 class="tba-panel-title">📅 Auto-Schedule Settings</h2>
                    <span class="tba-status-badge <?php echo $enabled ? 'tba-status-active' : 'tba-status-exhausted'; ?>">
                        <?php echo $enabled ? '✅ Running' : '⏸ Paused'; ?>
                    </span>
                </div>

                <?php if ( $enabled ): ?>
                <div class="tba-info-box">
                    <div class="tba-info-row">
                        <span class="tba-info-label">Next Run:</span>
                        <span class="tba-info-value"><?php echo esc_html($next_run); ?></span>
                    </div>
                    <div class="tba-info-row">
                        <span class="tba-info-label">Posts/Day:</span>
                        <span class="tba-info-value"><?php echo (int) $per_day; ?></span>
                    </div>
                    <div class="tba-info-row">
                        <span class="tba-info-label">Queue Pending:</span>
                        <span class="tba-info-value"><?php echo (int) TBA_Queue::count_by_status('queued'); ?></span>
                    </div>
                </div>
                <?php endif; ?>

                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                    <?php wp_nonce_field('tba_save_schedule'); ?>
                    <input type="hidden" name="action" value="tba_save_schedule">

                    <div class="tba-field">
                        <label class="tba-toggle">
                            <input type="checkbox" name="schedule_enabled" value="1" <?php checked($enabled,true); ?>>
                            <span class="tba-toggle-slider"></span>
                            <span class="tba-toggle-label"><strong>Enable Auto-Scheduler</strong></span>
                        </label>
                        <div class="tba-hint">Uses WP-Cron to automatically generate and publish posts on schedule.</div>
                    </div>

                    <div class="tba-field">
                        <label class="tba-label">Posts Per Day</label>
                        <input type="number" name="posts_per_day" class="tba-input tba-input-sm" value="<?php echo (int) $per_day; ?>" min="1" max="50">
                    </div>

                    <div class="tba-field">
                        <label class="tba-label">Niche Rotation List</label>
                        <textarea name="schedule_niches" class="tba-textarea" rows="6"
                            placeholder="Enter one niche per line:&#10;Personal Finance&#10;Home Improvement&#10;Digital Marketing&#10;Fitness Tips"
                        ><?php echo esc_textarea($niches_text); ?></textarea>
                        <div class="tba-hint">Scheduler will cycle through these niches in order. If queue has pending items, those are processed first.</div>
                    </div>

                    <div class="tba-form-actions">
                        <button type="submit" class="tba-btn tba-btn-primary">💾 Save Schedule</button>
                    </div>
                </form>
            </div>

            <!-- Post Queue -->
            <div class="tba-panel">
                <div class="tba-panel-header">
                    <h2 class="tba-panel-title">📋 Post Queue & Planner Tasks</h2>
                    <div class="tba-panel-actions">
                        <button type="button" id="tba-btn-run-queue" class="tba-btn tba-btn-secondary tba-btn-sm" style="font-weight:600;">
                            🚀 Run Queue Now
                        </button>
                    </div>
                </div>

                <!-- Live Queue Runner Console (Hidden by default) -->
                <div class="tba-queue-console" id="tba-queue-console" style="display:none; background: rgba(0,0,0,0.2); border:1px solid var(--tba-border); border-radius:6px; padding:12px; margin-bottom:15px; font-size:12px; font-family:var(--tba-font);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                        <strong>⚙️ Active Queue Processor Console</strong>
                        <span id="tba-queue-console-status" style="color:var(--tba-primary); font-weight:bold;">Running...</span>
                    </div>
                    <div id="tba-queue-console-logs" style="max-height:100px; overflow-y:auto; color:#a7f3d0; line-height:1.5; font-family: monospace;">
                        Starting queue processor...
                    </div>
                </div>

                <!-- Add to queue -->
                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" class="tba-add-key-form">
                    <?php wp_nonce_field('tba_enqueue_niche'); ?>
                    <input type="hidden" name="action" value="tba_enqueue_niche">
                    <div class="tba-input-row">
                        <input type="text" name="niche" class="tba-input" placeholder="Add custom niche line to queue (e.g. Finance | tips)...">
                        <button type="submit" class="tba-btn tba-btn-primary">➕ Add to Queue</button>
                    </div>
                </form>

                <?php if ( empty($queue_items) ): ?>
                <div class="tba-empty-state">Queue is empty. Plan some articles in the Bulk Planner.</div>
                <?php else: ?>
                
                <!-- Queue Bulk Actions Toolbar -->
                <div class="tba-queue-toolbar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; background:rgba(255,255,255,0.03); padding:10px; border-radius:6px; border:1px solid var(--tba-border);">
                    <div class="tba-queue-toolbar-left" style="display:flex; align-items:center; gap:10px;">
                        <button type="button" id="tba-btn-delete-selected" class="tba-btn tba-btn-danger tba-btn-sm" style="display:none; background:#ea580c; border-color:#ea580c;" onclick="aapDeleteSelectedQueue()">✕ Delete Selected</button>
                    </div>
                    <div class="tba-queue-toolbar-right">
                        <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" onsubmit="return confirm('Are you sure you want to clear the entire queue? This cannot be undone.')" style="margin:0;">
                            <?php wp_nonce_field('tba_clear_queue'); ?>
                            <input type="hidden" name="action" value="tba_clear_queue">
                            <button type="submit" class="tba-btn tba-btn-danger tba-btn-sm" style="background:#dc2626; border-color:#dc2626;">🗑️ Clear All Queue</button>
                        </form>
                    </div>
                </div>

                <!-- Hidden Delete Selected Form -->
                <form id="tba-form-delete-selected-queue" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
                    <?php wp_nonce_field('tba_delete_selected_queue'); ?>
                    <input type="hidden" name="action" value="tba_delete_selected_queue">
                    <input type="hidden" name="queue_ids" id="tba-hidden-delete-queue-ids" value="">
                </form>

                <table class="tba-table tba-table-sm" id="tba-queue-table">
                    <thead>
                        <tr>
                            <th style="width: 30px; text-align: center;"><input type="checkbox" id="tba-select-all-queue"></th>
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
                        <tr class="tba-queue-row-<?php echo esc_attr($item->status); ?>" id="tba-queue-row-<?php echo (int)$item->id; ?>">
                            <td style="text-align: center;"><input type="checkbox" class="tba-queue-checkbox" value="<?php echo (int)$item->id; ?>"></td>
                            <td><?php echo (int)$item->id; ?></td>
                            <td>
                                <strong style="color:var(--tba-text);"><?php echo esc_html($item->niche); ?></strong>
                                <?php if ($item->title): ?>
                                <div class="tba-queue-item-title" style="font-size:11px; margin-top:2px; color:rgba(255,255,255,0.6);"><?php echo esc_html($item->title); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-size:11px;"><?php echo esc_html($item->language ?? 'English'); ?></span>
                            </td>
                            <td>
                                <span class="tba-badge-category" style="font-size:11px; color:#a78bfa; background:rgba(167,139,250,0.1); padding:2px 6px; border-radius:4px; font-weight:600;">
                                    <?php echo esc_html($item->category ?: 'Auto-Suggest'); ?>
                                </span>
                            </td>
                            <td>
                                <span class="tba-status-badge tba-status-<?php echo esc_attr($item->status); ?>" style="display:inline-block;">
                                    <?php
                                    $status_labels = ['queued'=>'⏳ Queued','processing'=>'⚙️ Processing','published'=>'✅ Published','failed'=>'❌ Failed','paused'=>'⏸ Paused'];
                                    echo esc_html( $status_labels[$item->status] ?? $item->status );
                                    ?>
                                </span>
                                <?php if ($item->status === 'failed' && !empty($item->error_msg)): ?>
                                <div class="tba-queue-item-error" style="color:#f87171; font-size:10px; margin-top:4px; max-width:180px; line-height:1.2; word-break:break-word;">
                                    ⚠️ <?php echo esc_html($item->error_msg); ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td><span style="font-size:11px;"><?php echo esc_html(gmdate( 'M j, H:i', strtotime($item->created_at))); ?></span></td>
                            <td>
                                <?php if ($item->status === 'published' && $item->post_id): ?>
                                <a href="<?php echo esc_url( get_permalink($item->post_id) ); ?>" target="_blank" class="tba-btn-small tba-btn-ghost">View</a>
                                <?php endif; ?>
                                
                                <?php if ( in_array($item->status, ['queued', 'processing'], true) ): ?>
                                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" style="display:inline;">
                                    <?php wp_nonce_field('tba_pause_queue'); ?>
                                    <input type="hidden" name="action" value="tba_pause_queue">
                                    <input type="hidden" name="queue_id" value="<?php echo (int)$item->id; ?>">
                                    <button class="tba-btn-small tba-btn-ghost" type="submit" title="Pause Task" style="background:#fffcf0; border-color:#fbbf24; color:#d97706; font-size:9px;">⏸</button>
                                </form>
                                <?php elseif ( in_array($item->status, ['paused', 'failed'], true) ): ?>
                                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" style="display:inline;">
                                    <?php wp_nonce_field('tba_resume_queue'); ?>
                                    <input type="hidden" name="action" value="tba_resume_queue">
                                    <input type="hidden" name="queue_id" value="<?php echo (int)$item->id; ?>">
                                    <button class="tba-btn-small tba-btn-success" type="submit" title="<?php echo $item->status === 'failed' ? 'Retry Task' : 'Resume Task'; ?>" style="font-size:9px; background: #059669; border-color: #059669;">
                                        <?php echo $item->status === 'failed' ? '🔄 Retry' : '▶'; ?>
                                    </button>
                                </form>
                                <?php endif; ?>

                                <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" style="display:inline;" onsubmit="return confirm('Delete this queue item?')">
                                    <?php wp_nonce_field('tba_delete_queue'); ?>
                                    <input type="hidden" name="action" value="tba_delete_queue">
                                    <input type="hidden" name="queue_id" value="<?php echo (int)$item->id; ?>">
                                    <button class="tba-btn-small tba-btn-danger" type="submit">✕</button>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('tba-select-all-queue');
    const checkboxes = document.querySelectorAll('.tba-queue-checkbox');
    const deleteBtn = document.getElementById('tba-btn-delete-selected');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateDeleteSelectedBtn();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateDeleteSelectedBtn);
    });

    function updateDeleteSelectedBtn() {
        const checked = document.querySelectorAll('.tba-queue-checkbox:checked');
        if (checked.length > 0) {
            deleteBtn.style.display = 'inline-block';
            deleteBtn.textContent = '✕ Delete Selected (' + checked.length + ')';
        } else {
            deleteBtn.style.display = 'none';
        }
    }

    window.aapDeleteSelectedQueue = function() {
        if (!confirm('Are you sure you want to delete the selected queue items?')) return;
        const checked = document.querySelectorAll('.tba-queue-checkbox:checked');
        const ids = Array.from(checked).map(cb => cb.value);
        document.getElementById('tba-hidden-delete-queue-ids').value = ids.join(',');
        document.getElementById('tba-form-delete-selected-queue').submit();
    };
});
</script>
