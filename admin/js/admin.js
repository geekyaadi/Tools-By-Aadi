/**
 * Soniji Auto Blogging — Admin JavaScript
 * Step-by-step generation pipeline with live progress, key-switch notices, preview, and duplicate handling.
 * Includes: reference image upload, drag-drop, base64 conversion, and Settings default image management.
 */

/* global sabData, jQuery */
jQuery(document).ready(function ($) {
    'use strict';

    // =========================================================================
    // State
    // =========================================================================
    const state = {
        sessionId:      '',
        niche:          '',
        title:          '',
        postStatus:     'draft',
        previewOnly:    false,
        steps:          ['article', 'tags', 'meta', 'category', 'thumbnail', 'og_image', 'alt_text', 'publish'],
        currentStep:    0,
        running:        false,
        // Reference image for this session (overrides default from Settings)
        refImageB64:    '',
        refImageMime:   '',
    };

    // =========================================================================
    // DOM Refs
    // =========================================================================
    const $nicheInput     = $('#sab-niche-input');
    const $btnFindTitles  = $('#sab-btn-find-titles');
    const $titlesList     = $('#sab-titles-list');
    const $stepTitles     = $('#sab-step-titles');
    const $stepOptions    = $('#sab-step-options');
    const $btnGenerate    = $('#sab-btn-generate');
    const $btnPreview     = $('#sab-btn-preview');
    const $progressIdle   = $('#sab-progress-idle');
    const $progressSteps  = $('#sab-progress-steps');
    const $keySwitchNotice= $('#sab-key-switch-notice');
    const $keySwitchText  = $('#sab-key-switch-text');
    const $result         = $('#sab-result');
    const $previewPanel   = $('#sab-preview-panel');
    const $btnConfirmPublish = $('#sab-btn-confirm-publish');
    const $btnCancelPreview  = $('#sab-btn-cancel-preview');
    const $sessionId      = $('#sab-session-id');
    const $postStatus     = $('#sab-post-status');

    // =========================================================================
    // Step 1: Find Titles
    // =========================================================================
    $btnFindTitles.on('click', function () {
        const niche = $nicheInput.val().trim();
        if (!niche) {
            showAlert('Please enter a niche first.', 'warning');
            return;
        }

        state.niche     = niche;
        state.sessionId = 'sab_' + Date.now();
        $sessionId.val(state.sessionId);

        $btnFindTitles.html('<span class="sab-spinner"></span> Finding Titles...');
        $btnFindTitles.prop('disabled', true);
        $titlesList.html('<div class="sab-titles-placeholder"><span class="sab-spinner"></span> Asking Gemini for title ideas...</div>');

        $.post(sabData.ajaxUrl, {
            action:         'sab_get_titles',
            nonce:          sabData.nonce,
            niche:          niche,
            focus_keywords: $('#sab-keywords-input').val() || '',
            session_id:     state.sessionId,
        }, function (res) {
            $btnFindTitles.html('<span class="sab-btn-icon">🔍</span> Find Titles').prop('disabled', false);

            if (!res.success) {
                showAlert(res.data.message || sabData.strings.error, 'error');
                $titlesList.html('<div class="sab-titles-placeholder">Failed to fetch titles. Please try again.</div>');
                return;
            }

            renderTitles(res.data.titles);
            $stepTitles.addClass('sab-step-unlocked').removeClass('sab-step-locked');
            $stepOptions.addClass('sab-step-unlocked').removeClass('sab-step-locked');

            if (res.data.switched) {
                showKeySwitchNotice('Key switched during title generation.');
            }
        }).fail(function () {
            $btnFindTitles.html('<span class="sab-btn-icon">🔍</span> Find Titles').prop('disabled', false);
            showAlert(sabData.strings.error, 'error');
        });
    });

    // Also allow pressing Enter in niche field
    $nicheInput.on('keydown', function (e) {
        if (e.key === 'Enter') $btnFindTitles.trigger('click');
    });

    // =========================================================================
    // Render Titles
    // =========================================================================
    function renderTitles(titles) {
        $titlesList.empty();

        if (!titles || !titles.length) {
            $titlesList.html('<div class="sab-titles-placeholder">No titles returned. Try a different niche.</div>');
            return;
        }

        titles.forEach(function (title, i) {
            const id    = 'sab-title-' + i;
            const $item = $('<div class="sab-title-option"></div>');
            const $radio = $('<input type="radio" name="sab_title">')
                .attr('id', id).val(title);
            const $label = $('<label></label>').attr('for', id).text(title);

            $item.append($radio, $label);
            $titlesList.append($item);

            $item.on('click', function () {
                $('.sab-title-option').removeClass('selected');
                $item.addClass('selected');
                $radio.prop('checked', true);
                state.title = title;
                $btnGenerate.prop('disabled', false);
            });
        });
    }

    // =========================================================================
    // Step 2+: Generate (Preview or Publish)
    // =========================================================================
    $btnGenerate.on('click', function () {
        if (!state.title) return;
        state.previewOnly = false;
        state.postStatus  = $postStatus.val();
        startGeneration();
    });

    $btnPreview.on('click', function () {
        if (!state.title) {
            showAlert('Please select a title first.', 'warning');
            return;
        }
        state.previewOnly = false; // We'll do all steps then show preview before publishing
        state.postStatus  = $postStatus.val();
        startGeneration(true); // pass previewMode=true
    });

    function startGeneration(previewMode) {
        state.currentStep = 0;
        state.running     = true;
        state.previewOnly = !!previewMode;

        $progressIdle.hide();
        $progressSteps.show();
        $result.hide().empty();
        $previewPanel.hide();
        $keySwitchNotice.hide();

        // Reset all step indicators
        $('.sab-progress-step').each(function () {
            $(this).removeClass('running done error');
            $(this).find('.sab-pstep-dot').attr('class', 'sab-pstep-dot waiting');
            $(this).find('.sab-pstep-meta').text('');
        });

        $btnGenerate.prop('disabled', true).html('<span class="sab-spinner"></span> Generating...');
        $btnPreview.prop('disabled', true);

        runNextStep();
    }

    // =========================================================================
    // Step Runner
    // =========================================================================
    function runNextStep() {
        if (!state.running) return;

        const step = state.steps[state.currentStep];
        if (!step) return;

        // For publish step, pass previewOnly flag
        const isPublish = step === 'publish';

        markStepRunning(step);

        // Build AJAX payload
        const payload = {
            action:         'sab_generate_post',
            nonce:          sabData.nonce,
            session_id:     state.sessionId,
            title:          state.title,
            niche:          state.niche,
            step:           step,
            post_status:    state.postStatus,
            focus_keywords: $('#sab-keywords-input').val() || '',
            preview_only:   isPublish && state.previewOnly ? 1 : 0,
            tag_count:      parseInt($('#sab-tag-count').val() || '0', 10),
            category:       $('#sab-post-category').val() || '',
        };

        // Attach reference image only for thumbnail step (AI Generated Only)
        if (step === 'thumbnail') {
            const method = $('#sab-thumb-type').val() || 'ai';
            if (method === 'ai' && state.refImageB64) {
                payload.ref_image_b64  = state.refImageB64;
                payload.ref_image_mime = state.refImageMime;
            } else if (method === 'text_to_image') {
                payload.thumb_type  = 'text_to_image';
                payload.t2i_bg_type = $('#sab-t2i-bg-type').val();
                payload.t2i_bg_val  = $('#sab-t2i-bg-type').val() === 'gradient'
                    ? $('#sab-t2i-bg-val-gradient').val()
                    : $('#sab-t2i-bg-val-solid').val();
                payload.t2i_size    = $('#sab-t2i-size').val();
            }
        }

        // Attach Title-to-Image options for OG Image step if selected
        if (step === 'og_image') {
            const method = $('#sab-thumb-type').val() || 'ai';
            if (method === 'text_to_image') {
                payload.thumb_type  = 'text_to_image';
                payload.t2i_bg_type = $('#sab-t2i-bg-type').val();
                payload.t2i_bg_val  = $('#sab-t2i-bg-type').val() === 'gradient'
                    ? $('#sab-t2i-bg-val-gradient').val()
                    : $('#sab-t2i-bg-val-solid').val();
            }
        }

        $.post(sabData.ajaxUrl, payload, function (res) {

            if (!res.success) {
                // If thumbnail or og_image step fails, mark it but continue to next step instead of getting stuck
                if (step === 'thumbnail' || step === 'og_image') {
                    markStepError(step, res.data.message || 'Failed/Skipped');
                    state.currentStep++;
                    runNextStep();
                    return;
                }
                
                markStepError(step, res.data.message);
                showAlert('Error at step "' + step + '": ' + (res.data.message || sabData.strings.error), 'error');
                finishGeneration(false);
                return;
            }

            const data = res.data;

            // Key switch notification
            if (data.switched) {
                showKeySwitchNotice('API key exhausted — switched to key: ' + (data.key_used || 'next key'));
            }

            // Handle special responses
            if (data.step === 'duplicate_warning') {
                markStepDone(step);
                showDuplicateWarning(data);
                finishGeneration(false, true); // partial finish
                return;
            }

            if (data.step === 'preview') {
                markStepDone(step);
                showPreview(data);
                finishGeneration(false, true);
                return;
            }

            if (data.step === 'done') {
                markStepDone(step);
                showSuccessResult(data);
                finishGeneration(true);
                return;
            }

            // Normal step done
            markStepDone(step, data);
            state.currentStep++;
            runNextStep();

        }).fail(function () {
            markStepError(step, 'Request failed. Check your connection.');
            showAlert(sabData.strings.error, 'error');
            finishGeneration(false);
        });
    }

    // =========================================================================
    // Step UI Helpers
    // =========================================================================
    function markStepRunning(step) {
        const $step = $('#sab-pstep-' + step);
        $step.addClass('running').removeClass('done error');
        $step.find('.sab-pstep-dot').attr('class', 'sab-pstep-dot running');
        $step.find('.sab-pstep-meta').text('Generating...');
    }

    function markStepDone(step, data) {
        const $step = $('#sab-pstep-' + step);
        $step.addClass('done').removeClass('running error');
        $step.find('.sab-pstep-dot').attr('class', 'sab-pstep-dot done');

        let meta = '✓ Done';
        if (data) {
            if (data.cached)         meta = '⚡ Cached (resumed)';
            if (data.count)          meta += ' — ' + data.count + ' generated';
            if (data.used_reference) meta += ' · 🖼️ styled from reference';
            if (data.key_used && !data.cached) meta += ' — Key: ' + data.key_used;
        }
        $step.find('.sab-pstep-meta').text(meta);
    }

    function markStepError(step, msg) {
        const $step = $('#sab-pstep-' + step);
        $step.addClass('error').removeClass('running done');
        $step.find('.sab-pstep-dot').attr('class', 'sab-pstep-dot error');
        $step.find('.sab-pstep-meta').text('❌ ' + (msg || 'Error'));
    }

    function finishGeneration(success, partial) {
        state.running = false;
        if (!partial) {
            $btnGenerate.prop('disabled', !success).html('<span>⚡</span> Generate &amp; Publish');
            $btnPreview.prop('disabled', false);
        }
    }

    // =========================================================================
    // Success Result
    // =========================================================================
    function showSuccessResult(data) {
        const statusLabel = data.post_status === 'publish' ? 'Published' : 'Draft';
        $result.html(`
            <div class="sab-result-title">🎉 Post ${statusLabel} Successfully!</div>
            <div class="sab-result-meta">
                Estimated tokens: ~${numberFormat(data.token_est)} &nbsp;|&nbsp;
                Status: <strong>${statusLabel}</strong>
            </div>
            <div class="sab-result-links">
                <a href="${data.edit_url}" target="_blank" class="sab-btn sab-btn-secondary">✏️ Edit Post</a>
                ${data.post_status === 'publish' ? `<a href="${data.post_url}" target="_blank" class="sab-btn sab-btn-primary">👁 View Post</a>` : ''}
            </div>
        `).show();
    }

    // =========================================================================
    // Preview Panel
    // =========================================================================
    function showPreview(data) {
        $('#sab-preview-title').text(data.title);
        $('#sab-preview-category').text(data.category || '');
        $('#sab-preview-meta-desc').text(data.meta || '');
        $('#sab-preview-content').html(data.article || '');
        $('#sab-preview-tags').text(data.tags || '');
        $previewPanel.show();

        // Re-enable buttons
        $btnGenerate.prop('disabled', false).html('<span>⚡</span> Generate &amp; Publish');
        $btnPreview.prop('disabled', false);
    }

    $btnConfirmPublish.on('click', function () {
        $previewPanel.hide();
        state.previewOnly = false;
        // Jump straight to publish step
        state.currentStep = state.steps.indexOf('publish');
        state.running = true;
        $btnGenerate.prop('disabled', true).html('<span class="sab-spinner"></span> Publishing...');
        runNextStep();
    });

    $btnCancelPreview.on('click', function () {
        $previewPanel.hide();
        $btnGenerate.prop('disabled', false);
        $btnPreview.prop('disabled', false);
    });

    // =========================================================================
    // Duplicate Warning
    // =========================================================================
    function showDuplicateWarning(data) {
        const html = `
            <div class="sab-dup-warning">
                <p>⚠️ A similar post already exists: <a href="${data.dup_url}" target="_blank">"${data.dup_title}"</a></p>
                <div class="sab-dup-actions">
                    <button id="sab-btn-force-publish" class="sab-btn sab-btn-primary">✅ Publish Anyway</button>
                    <button id="sab-btn-cancel-dup" class="sab-btn sab-btn-ghost">✕ Cancel</button>
                </div>
            </div>
        `;
        $result.html(html).show();

        $('#sab-btn-force-publish').on('click', function () {
            $result.empty().hide();
            state.running = true;
            state.currentStep = state.steps.indexOf('publish');
            // Use force_publish step
            markStepRunning('publish');
            $.post(sabData.ajaxUrl, {
                action:      'sab_generate_post',
                nonce:       sabData.nonce,
                session_id:  state.sessionId,
                title:       state.title,
                niche:       state.niche,
                step:        'force_publish',
                post_status: state.postStatus,
            }, function (res) {
                if (res.success && res.data.step === 'done') {
                    markStepDone('publish', res.data);
                    showSuccessResult(res.data);
                } else {
                    markStepError('publish', res.data.message);
                    showAlert(res.data.message || sabData.strings.error, 'error');
                }
                finishGeneration(res.success);
            });
        });

        $('#sab-btn-cancel-dup').on('click', function () {
            $result.empty().hide();
            $btnGenerate.prop('disabled', false).html('<span>⚡</span> Generate &amp; Publish');
            $btnPreview.prop('disabled', false);
        });
    }

    // =========================================================================
    // Key Switch Notice
    // =========================================================================
    function showKeySwitchNotice(msg) {
        $keySwitchText.text(msg);
        $keySwitchNotice.show();
        setTimeout(() => $keySwitchNotice.fadeOut(600), 6000);
    }

    // =========================================================================
    // Alert Helper
    // =========================================================================
    function showAlert(msg, type) {
        const $alert = $('<div class="sab-alert sab-alert-' + type + '">' + msg + '</div>');
        $('.sab-content').prepend($alert);
        setTimeout(() => $alert.fadeOut(400, function () { $(this).remove(); }), 6000);
    }

    // =========================================================================
    // Utility
    // =========================================================================
    function numberFormat(n) {
        return n ? n.toLocaleString() : '0';
    }

    // =========================================================================
    // 🏓 Ping Key — Settings Page
    // =========================================================================

    // Per-key ping buttons
    $(document).on('click', '.sab-btn-ping', function () {
        const $btn   = $(this);
        const idx    = $btn.data('key-index');
        const $row   = $btn.closest('tr');

        $btn.html('<span class="sab-spinner" style="width:12px;height:12px;"></span>')
            .prop('disabled', true);

        $.post(sabData.ajaxUrl, {
            action:    'sab_ping_key',
            nonce:     sabData.nonce,
            key_index: idx,
        }, function (res) {
            $btn.html('🏓').prop('disabled', false);

            if (!res.success) {
                showPingToast('Error: ' + (res.data.message || 'Ping failed.'), 'error');
                return;
            }

            const d = res.data;
            updateKeyRow($row, d);
            showPingToast(
                'Key #' + (parseInt(idx) + 1) + ': ' + d.message,
                d.status === 'active' ? 'success' : (d.status === 'invalid' ? 'error' : 'warning')
            );
        }).fail(function () {
            $btn.html('🏓').prop('disabled', false);
            showPingToast('Ping request failed.', 'error');
        });
    });

    // Ping All Keys button
    $('#sab-btn-ping-all').on('click', function () {
        const $btn = $(this);
        $btn.html('<span class="sab-spinner" style="width:12px;height:12px;"></span> Testing...')
            .prop('disabled', true);

        $.post(sabData.ajaxUrl, {
            action: 'sab_ping_all_keys',
            nonce:  sabData.nonce,
        }, function (res) {
            $btn.html('🏓 Ping All Keys').prop('disabled', false);

            if (!res.success) {
                showPingToast('Ping all failed.', 'error');
                return;
            }

            const results = res.data.results;
            const summary = res.data.summary;

            // Update each row
            $.each(results, function (idx, d) {
                const $row = $('[data-key-index="' + idx + '"]');
                if ($row.length) updateKeyRow($row, d);
            });

            showPingToast(
                'Ping complete — ' + summary.active + ' active, ' +
                summary.exhausted + ' exhausted, ' + summary.invalid + ' invalid.',
                summary.active === summary.total ? 'success' : 'warning'
            );
        }).fail(function () {
            $btn.html('🏓 Ping All Keys').prop('disabled', false);
            showPingToast('Request failed.', 'error');
        });
    });

    /**
     * Updates a key table row in-place after a ping result.
     */
    function updateKeyRow($row, d) {
        const statusMap = {
            active:    '<span class="sab-status-badge sab-status-active">✅ Active</span>',
            exhausted: '<span class="sab-status-badge sab-status-exhausted">🔴 Exhausted</span>',
            invalid:   '<span class="sab-status-badge sab-status-invalid">⛔ Invalid</span>',
            error:     '<span class="sab-status-badge sab-status-exhausted">⚠️ Error</span>',
        };
        const pingMap = {
            active:    '✅',
            exhausted: '🔴',
            invalid:   '⛔',
        };

        // Status cell
        $row.find('.sab-key-status-cell').html( statusMap[ d.status ] || '' );

        // Countdown cell
        const $countdown = $row.find('.sab-key-countdown-cell');
        if ( d.status === 'exhausted' && d.reset_at_ts ) {
            $row.attr('data-reset-ts', d.reset_at_ts);
            const secsLeft = Math.max(0, d.reset_at_ts - Math.floor(Date.now() / 1000));
            $countdown.html(
                '<span class="sab-countdown" data-reset-ts="' + d.reset_at_ts + '">' +
                '⏱ <span class="sab-countdown-val">' + formatSecs(secsLeft) + '</span></span>'
            );
        } else if ( d.status === 'active' ) {
            $countdown.html('<span class="sab-text-muted">—</span>');
        }

        // Last ping cell — update timestamp + icon
        const now  = new Date();
        const hhmm = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');
        $row.find('.sab-key-ping-cell').html(
            '<span class="sab-ping-badge">' + (pingMap[d.status] || '❓') + '</span> ' +
            '<span class="sab-ping-time">' + hhmm + '</span>'
        );

        // Row highlight
        $row.removeClass('sab-row-exhausted sab-row-invalid');
        if (d.status !== 'active') $row.addClass('sab-row-exhausted');
    }

    // =========================================================================
    // ⏱ Live Countdown Ticker (for all .sab-countdown elements on the page)
    // =========================================================================

    function formatSecs(total) {
        total = Math.max(0, total);
        if (total < 60)   return total + 's';
        if (total < 3600) return Math.floor(total / 60) + 'm ' + (total % 60) + 's';
        const h = Math.floor(total / 3600);
        const m = Math.floor((total % 3600) / 60);
        return h + 'h ' + m + 'm';
    }

    function tickCountdowns() {
        const nowTs = Math.floor(Date.now() / 1000);
        $('.sab-countdown').each(function () {
            const resetTs  = parseInt($(this).data('reset-ts'), 10);
            const secsLeft = Math.max(0, resetTs - nowTs);
            $(this).find('.sab-countdown-val').text(formatSecs(secsLeft));

            if (secsLeft === 0) {
                // Key reset time reached — update row to "Active"
                const $row = $(this).closest('tr');
                $row.find('.sab-key-status-cell').html(
                    '<span class="sab-status-badge sab-status-active">✅ Active (restored)</span>'
                );
                $(this).closest('td').html('<span class="sab-text-muted">—</span>');
                $row.removeClass('sab-row-exhausted');
            }
        });
    }

    // Start ticker if there are any countdowns on the page
    if ($('.sab-countdown').length) {
        setInterval(tickCountdowns, 1000);
        tickCountdowns(); // immediate first tick
    }

    // Ping toast notification
    function showPingToast(msg, type) {
        const cls = { success: '#22c55e', warning: '#f59e0b', error: '#ef4444' }[type] || '#94a3b8';
        const $toast = $(
            '<div class="sab-ping-toast" style="border-left-color:' + cls + '">' + msg + '</div>'
        );
        $('body').append($toast);
        setTimeout(() => $toast.addClass('sab-ping-toast-show'), 50);
        setTimeout(() => {
            $toast.removeClass('sab-ping-toast-show');
            setTimeout(() => $toast.remove(), 400);
        }, 4000);
    }



    const $uploadZone    = $('#sab-upload-zone');
    const $uploadIdle    = $('#sab-upload-idle');
    const $uploadPreview = $('#sab-upload-preview');
    const $refImgInput   = $('#sab-ref-img-input');
    const $refImgThumb   = $('#sab-ref-img-thumb');
    const $refImgName    = $('#sab-ref-img-name');
    const $btnClearRef   = $('#sab-btn-clear-ref');

    const MAX_SIZE = 4 * 1024 * 1024; // 4MB

    // Click anywhere on idle zone to open file picker
    $uploadZone.on('click', function (e) {
        if ($(e.target).closest('#sab-btn-clear-ref').length) return;
        if ($(e.target).closest('label').length) return;
        if ($uploadIdle.is(':visible')) {
            $refImgInput.trigger('click');
        }
    });

    // File input change
    $refImgInput.on('change', function () {
        const file = this.files[0];
        if (file) loadRefImage(file);
    });

    // Drag & Drop
    $uploadZone.on('dragover dragenter', function (e) {
        e.preventDefault();
        $(this).addClass('dragging');
    });

    $uploadZone.on('dragleave drop', function (e) {
        e.preventDefault();
        $(this).removeClass('dragging');
        if (e.type === 'drop') {
            const file = e.originalEvent.dataTransfer.files[0];
            if (file) loadRefImage(file);
        }
    });

    // Clear reference image
    $btnClearRef.on('click', function (e) {
        e.stopPropagation();
        clearRefImage();
    });

    function loadRefImage(file) {
        if (!file.type.startsWith('image/')) {
            showAlert('Please upload an image file (JPG, PNG, WEBP).', 'warning');
            return;
        }
        if (file.size > MAX_SIZE) {
            showAlert('Image must be under 4MB.', 'warning');
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            const dataUrl  = e.target.result;
            // Split "data:image/jpeg;base64,XXXXX" → mime + b64
            const parts    = dataUrl.split(';base64,');
            const mime     = parts[0].replace('data:', '');
            const b64      = parts[1];

            state.refImageB64  = b64;
            state.refImageMime = mime;

            $refImgThumb.attr('src', dataUrl);
            $refImgName.text(file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)');
            $uploadIdle.hide();
            $uploadPreview.show();
        };
        reader.readAsDataURL(file);
    }

    function clearRefImage() {
        state.refImageB64  = '';
        state.refImageMime = '';
        $refImgInput.val('');
        $uploadPreview.hide();
        $uploadIdle.show();
    }

    // =========================================================================
    // Reference Image Upload — Settings Page (Default)
    // =========================================================================

    const $settingsZone          = $('#sab-settings-upload-zone');
    const $settingsIdle          = $('#sab-settings-upload-idle');
    const $settingsPreview       = $('#sab-settings-upload-preview');
    const $settingsInput         = $('#sab-settings-ref-input');
    const $settingsRefPreview    = $('#sab-settings-ref-preview');
    const $settingsRefName       = $('#sab-settings-ref-name');
    const $btnSettingsSave       = $('#sab-btn-settings-save-ref');
    const $btnSettingsClear      = $('#sab-btn-settings-clear-ref');
    const $btnSettingsDelete     = $('#sab-btn-settings-delete-ref');
    const $settingsMsg           = $('#sab-settings-ref-msg');

    let settingsRefB64  = '';
    let settingsRefMime = '';

    if ($settingsZone.length) {

        $settingsZone.on('click', function (e) {
            if ($(e.target).closest('button,label').length) return;
            if ($settingsIdle.is(':visible')) $settingsInput.trigger('click');
        });

        $settingsInput.on('change', function () {
            const file = this.files[0];
            if (file) loadSettingsRef(file);
        });

        $settingsZone.on('dragover dragenter', function (e) {
            e.preventDefault();
            $(this).addClass('dragging');
        });

        $settingsZone.on('dragleave drop', function (e) {
            e.preventDefault();
            $(this).removeClass('dragging');
            if (e.type === 'drop') {
                const file = e.originalEvent.dataTransfer.files[0];
                if (file) loadSettingsRef(file);
            }
        });

        $btnSettingsClear.on('click', function () {
            settingsRefB64  = '';
            settingsRefMime = '';
            $settingsInput.val('');
            $settingsPreview.hide();
            $settingsIdle.show();
        });

        $btnSettingsSave.on('click', function () {
            if (!settingsRefB64) return;
            $btnSettingsSave.prop('disabled', true).html('<span class="sab-spinner"></span> Saving...');

            $.post(sabData.ajaxUrl, {
                action:     'sab_save_reference_image',
                nonce:      sabData.nonce,
                image_b64:  settingsRefB64,
                image_mime: settingsRefMime,
            }, function (res) {
                $btnSettingsSave.prop('disabled', false).html('💾 Save as Default');
                if (res.success) {
                    showSettingsMsg('✅ Default reference image saved! It will be used for all new thumbnails.', 'success');
                    // Refresh to show the new saved image preview
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showSettingsMsg('❌ ' + (res.data.message || 'Failed to save image.'), 'error');
                }
            }).fail(function () {
                $btnSettingsSave.prop('disabled', false).html('💾 Save as Default');
                showSettingsMsg('❌ Request failed. Please try again.', 'error');
            });
        });

        $btnSettingsDelete.on('click', function () {
            if (!confirm('Remove the default reference image?')) return;
            $btnSettingsDelete.prop('disabled', true).html('<span class="sab-spinner"></span>');

            $.post(sabData.ajaxUrl, {
                action: 'sab_delete_reference_image',
                nonce:  sabData.nonce,
            }, function (res) {
                $btnSettingsDelete.prop('disabled', false).html('🗑 Remove Default Image');
                if (res.success) {
                    $('#sab-settings-ref-current').fadeOut(300);
                    showSettingsMsg('✅ Default reference image removed.', 'success');
                } else {
                    showSettingsMsg('❌ Failed to remove image.', 'error');
                }
            });
        });
    }

    function loadSettingsRef(file) {
        if (!file.type.startsWith('image/')) {
            showSettingsMsg('Please upload an image file.', 'error');
            return;
        }
        if (file.size > MAX_SIZE) {
            showSettingsMsg('Image must be under 4MB.', 'error');
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            const dataUrl   = e.target.result;
            const parts     = dataUrl.split(';base64,');
            settingsRefMime = parts[0].replace('data:', '');
            settingsRefB64  = parts[1];

            $settingsRefPreview.attr('src', dataUrl);
            $settingsRefName.text(file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)');
            $settingsIdle.hide();
            $settingsPreview.show();
        };
        reader.readAsDataURL(file);
    }

    function showSettingsMsg(msg, type) {
        $settingsMsg.html('<div class="sab-alert sab-alert-' + type + '">' + msg + '</div>').show();
        setTimeout(() => $settingsMsg.fadeOut(400), 5000);
    }

    // =========================================================================
    // Provider Select Toggles — Settings Page
    // =========================================================================

    // Add Key provider toggle
    $('#sab-key-provider-select').on('change', function () {
        const val   = $(this).val();
        const $input = $('#sab-key-input-field');
        const $hint  = $('#sab-add-key-hint');

        if (val === 'openai') {
            $input.attr('placeholder', 'Paste OpenAI API key here (sk-...)');
            $hint.html('Get your OpenAI API key from <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI API Keys →</a>');
        } else {
            $input.attr('placeholder', 'Paste Gemini API key here (AIza...)');
            $hint.html('Get your free Gemini API key from <a href="https://aistudio.google.com/app/apikey" target="_blank">Google AI Studio →</a>');
        }
    });

    // Active Provider toggle
    $('#sab-active-provider-select').on('change', function () {
        const val = $(this).val();
        const $geminiText = $('#sab-field-gemini-model');
        const $openaiText = $('#sab-field-openai-model');
        const $geminiImg  = $('#sab-field-gemini-image');

        if (val === 'openai') {
            $geminiText.hide();
            $openaiText.show();
            $geminiImg.hide();
        } else {
            $geminiText.show();
            $openaiText.hide();
            $geminiImg.show();
        }
    });

    // Title to Image options toggles (Settings and Generate page)
    function initT2IToggles() {
        // Toggles in Settings Page
        $('#sab_thumb_type').on('change', function() {
            const val = $(this).val();
            if (val === 'text_to_image') {
                $('.sab-t2i-only').show();
                toggleSettingsT2IBg();
            } else {
                $('.sab-t2i-only').hide();
            }
        });
        $('#sab_t2i_bg_type').on('change', toggleSettingsT2IBg);

        function toggleSettingsT2IBg() {
            const bgType = $('#sab_t2i_bg_type').val();
            if (bgType === 'gradient') {
                $('#sab-t2i-gradient-field').show();
                $('#sab-t2i-solid-field').hide();
            } else if (bgType === 'solid') {
                $('#sab-t2i-gradient-field').hide();
                $('#sab-t2i-solid-field').show();
            } else {
                $('#sab-t2i-gradient-field').hide();
                $('#sab-t2i-solid-field').hide();
            }
        }

        // Toggles in Generate Page
        $('#sab-thumb-type').on('change', function() {
            const val = $(this).val();
            if (val === 'text_to_image') {
                $('.sab-t2i-only').show();
                $('.sab-ai-thumb-only').hide();
                toggleGenerateT2IBg();
            } else {
                $('.sab-t2i-only').hide();
                $('.sab-ai-thumb-only').show();
            }
        });
        $('#sab-t2i-bg-type').on('change', toggleGenerateT2IBg);

        function toggleGenerateT2IBg() {
            const bgType = $('#sab-t2i-bg-type').val();
            if (bgType === 'gradient') {
                $('#sab-t2i-gradient-group').show();
                $('#sab-t2i-solid-group').hide();
            } else if (bgType === 'solid') {
                $('#sab-t2i-gradient-group').hide();
                $('#sab-t2i-solid-group').show();
            } else {
                $('#sab-t2i-gradient-group').hide();
                $('#sab-t2i-solid-group').hide();
            }
        }

        // Toggles in Bulk Planner Page
        $('#sab-planner-thumb-type').on('change', function() {
            const val = $(this).val();
            if (val === 'text_to_image') {
                $('.sab-t2i-only').show();
                togglePlannerT2IBg();
            } else {
                $('.sab-t2i-only').hide();
            }
        });
        $('#sab-planner-t2i-bg-type').on('change', togglePlannerT2IBg);

        function togglePlannerT2IBg() {
            const bgType = $('#sab-planner-t2i-bg-type').val();
            if (bgType === 'gradient') {
                $('#sab-planner-t2i-gradient-group').show();
                $('#sab-planner-t2i-solid-group').hide();
            } else if (bgType === 'solid') {
                $('#sab-planner-t2i-gradient-group').hide();
                $('#sab-planner-t2i-solid-group').show();
            } else {
                $('#sab-planner-t2i-gradient-group').hide();
                $('#sab-planner-t2i-solid-group').hide();
            }
        }

        // Trigger on load
        if ($('#sab_thumb_type').length) {
            $('#sab_thumb_type').trigger('change');
            toggleSettingsT2IBg();
        }
        if ($('#sab-thumb-type').length) {
            $('#sab-thumb-type').trigger('change');
            toggleGenerateT2IBg();
        }
        if ($('#sab-planner-thumb-type').length) {
            $('#sab-planner-thumb-type').trigger('change');
            togglePlannerT2IBg();
        }
    }
    initT2IToggles();

    // =========================================================================
    // Bulk Planner JS Logic
    // =========================================================================

    const $btnPlannerFind  = $('#sab-btn-planner-find');
    const $plannerNiche    = $('#sab-planner-niche');
    const $plannerLang     = $('#sab-planner-lang');
    const $plannerDefCat   = $('#sab-planner-default-cat');
    const $plannerPanel    = $('#sab-planner-results-panel');
    const $plannerBody     = $('#sab-planner-table-body');
    const $btnSaveTasks    = $('#sab-btn-save-tasks');

    if ($('#sab-planner-mode').length) {
        $('#sab-planner-mode').on('change', function() {
            if ($(this).val() === 'silo') {
                $('#sab-planner-count-wrapper').hide();
            } else {
                $('#sab-planner-count-wrapper').show();
            }
        });
    }

    if ($btnPlannerFind.length) {
        $btnPlannerFind.on('click', function () {
            const niche  = $plannerNiche.val().trim();
            const lang   = $plannerLang.val();
            const defCat = $plannerDefCat.val();
            const mode   = $('#sab-planner-mode').val() || 'standard';
            const count  = $('#sab-planner-count').val() || '20';

            if (!niche) {
                alert('Please enter a niche first.');
                return;
            }

            $btnPlannerFind.html('<span class="sab-spinner"></span> Generating Plan...').prop('disabled', true);
            $plannerPanel.hide();
            $plannerBody.empty();

            $.post(sabData.ajaxUrl, {
                action:   'sab_generate_planner_titles',
                nonce:    sabData.nonce,
                niche:    niche,
                language: lang,
                mode:     mode,
                count:    parseInt(count, 10),
            }, function (res) {
                $btnPlannerFind.html('🔍 Generate Plan').prop('disabled', false);

                if (!res.success) {
                    alert('Error: ' + (res.data.message || 'Failed to fetch titles.'));
                    return;
                }

                const catHtml = $('#sab-cat-template-source').html();

                if (res.data.mode === 'silo') {
                    // Render Pillar Row First
                    const pillarTitle = res.data.pillar;
                    const pillarRow = `
                        <tr class="sab-silo-pillar-row" style="background:#f0f6fc; border-left: 4px solid var(--sab-primary);">
                            <td><input type="checkbox" class="sab-planner-checkbox" checked></td>
                            <td><strong>PILLAR</strong></td>
                            <td>
                                <input type="text" class="sab-input sab-planner-title-input" value="${pillarTitle.replace(/"/g, '&quot;')}" style="width:100%; font-weight:bold;">
                                <span class="sab-badge sab-badge-gemini" style="margin-top:4px;">Main Pillar Article</span>
                            </td>
                            <td>
                                <select class="sab-select sab-planner-cat-select" style="width:100%;">
                                    ${catHtml}
                                </select>
                            </td>
                        </tr>
                    `;
                    const $pillarRow = $(pillarRow);
                    if (defCat) {
                        $pillarRow.find('.sab-planner-cat-select').val(defCat);
                    }
                    $plannerBody.append($pillarRow);

                    // Render Supporting Cluster Rows
                    const titles = res.data.titles;
                    titles.forEach(function (title, i) {
                        const row = `
                            <tr class="sab-silo-cluster-row" style="border-left: 4px solid #ccd0d4;">
                                <td><input type="checkbox" class="sab-planner-checkbox" checked></td>
                                <td>${i + 1}</td>
                                <td>
                                    <input type="text" class="sab-input sab-planner-title-input" value="${title.replace(/"/g, '&quot;')}" style="width:100%;">
                                    <span class="sab-badge sab-badge-default" style="margin-top:4px;">Supporting Cluster Article</span>
                                </td>
                                <td>
                                    <select class="sab-select sab-planner-cat-select" style="width:100%;">
                                        ${catHtml}
                                    </select>
                                </td>
                            </tr>
                        `;
                        const $row = $(row);
                        if (defCat) {
                            $row.find('.sab-planner-cat-select').val(defCat);
                        }
                        $plannerBody.append($row);
                    });
                } else {
                    // Standard Row Rendering
                    const titles = res.data.titles;
                    titles.forEach(function (title, i) {
                        const row = `
                            <tr>
                                <td><input type="checkbox" class="sab-planner-checkbox" checked></td>
                                <td>${i + 1}</td>
                                <td><input type="text" class="sab-input sab-planner-title-input" value="${title.replace(/"/g, '&quot;')}" style="width:100%;"></td>
                                <td>
                                    <select class="sab-select sab-planner-cat-select" style="width:100%;">
                                        ${catHtml}
                                    </select>
                                </td>
                            </tr>
                        `;
                        const $row = $(row);
                        if (defCat) {
                            $row.find('.sab-planner-cat-select').val(defCat);
                        }
                        $plannerBody.append($row);
                    });
                }

                $plannerPanel.fadeIn(400);
            }).fail(function () {
                $btnPlannerFind.html('🔍 Generate Plan').prop('disabled', false);
                alert('Request failed. Please try again.');
            });
        });

        // Select All / Deselect All
        $('#sab-btn-select-all').on('click', function () {
            $('.sab-planner-checkbox').prop('checked', true);
            $('#sab-check-master').prop('checked', true);
        });

        $('#sab-btn-deselect-all').on('click', function () {
            $('.sab-planner-checkbox').prop('checked', false);
            $('#sab-check-master').prop('checked', false);
        });

        $('#sab-check-master').on('change', function () {
            $('.sab-planner-checkbox').prop('checked', $(this).is(':checked'));
        });

        // Save selected titles as tasks
        $btnSaveTasks.on('click', function () {
            const niche = $plannerNiche.val().trim();
            const lang  = $plannerLang.val();
            const mode  = $('#sab-planner-mode').val() || 'standard';
            const tasks = [];

            $plannerBody.find('tr').each(function () {
                const $row = $(this);
                if ($row.find('.sab-planner-checkbox').is(':checked')) {
                    tasks.push({
                        title:    $row.find('.sab-planner-title-input').val().trim(),
                        category: $row.find('.sab-planner-cat-select').val()
                    });
                }
            });

            // Gather Title-to-Image choices for bulk task enqueuing
            const thumbType = $('#sab-planner-thumb-type').val();
            const t2iBgType = $('#sab-planner-t2i-bg-type').val();
            const t2iBgVal  = t2iBgType === 'gradient'
                ? $('#sab-planner-t2i-bg-val-gradient').val()
                : $('#sab-planner-t2i-bg-val-solid').val();
            const t2iSize   = $('#sab-planner-t2i-size').val();

            const metaOpts = {
                thumb_type:  thumbType,
                bg_type:     t2iBgType,
                bg_val:      t2iBgVal,
                size:        t2iSize
            };

            $btnSaveTasks.html('<span class="sab-spinner"></span> Saving tasks...').prop('disabled', true);

            $.post(sabData.ajaxUrl, {
                action:    'sab_save_planner_tasks',
                nonce:     sabData.nonce,
                niche:     niche,
                language:  lang,
                mode:      mode,
                tasks:     tasks,
                tag_count: parseInt($('#sab-planner-tag-count').val() || '0', 10),
                meta:      metaOpts,
            }, function (res) {
                $btnSaveTasks.html('💾 Save Selected as Background Tasks').prop('disabled', false);
                if (res.success) {
                    alert(res.data.message);
                    
                    // Remove successfully enqueued rows from the planner results table instead of redirecting!
                    $plannerBody.find('tr').each(function () {
                        const $row = $(this);
                        if ($row.find('.sab-planner-checkbox').is(':checked')) {
                            $row.fadeOut(300, function() {
                                $(this).remove();
                                // If no rows left in the table, hide the planner results panel
                                if ($plannerBody.find('tr').length === 0) {
                                    $plannerPanel.fadeOut(300);
                                }
                            });
                        }
                    });
                } else {
                    alert('Error: ' + res.data.message);
                }
            }).fail(function () {
                $btnSaveTasks.html('💾 Save Selected as Background Tasks').prop('disabled', false);
                alert('Failed to save tasks.');
            });
        });
    }

    // =========================================================================
    // Live Queue Runner Console Logic
    // =========================================================================

    const $btnRunQueue     = $('#sab-btn-run-queue');
    const $queueConsole    = $('#sab-queue-console');
    const $consoleStatus   = $('#sab-queue-console-status');
    const $consoleLogs     = $('#sab-queue-console-logs');

    let queueRunning = false;

    if ($btnRunQueue.length) {
        $btnRunQueue.on('click', function () {
            if (queueRunning) {
                // Pause runner
                queueRunning = false;
                $btnRunQueue.html('🚀 Run Queue Now').removeClass('sab-btn-danger').addClass('sab-btn-secondary');
                $consoleStatus.text('Paused').css('color', '#fbbf24');
                appendConsoleLog('Queue processor paused by user.');
            } else {
                // Start runner
                queueRunning = true;
                $btnRunQueue.html('🛑 Pause Queue Runner').removeClass('sab-btn-secondary').addClass('sab-btn-danger');
                $queueConsole.slideDown(300);
                $consoleStatus.text('Running...').css('color', '#34d399');
                $consoleLogs.empty();
                appendConsoleLog('Queue processor started.');
                processNextQueueItem();
            }
        });
    }

    function appendConsoleLog(msg) {
        const time = new Date().toLocaleTimeString();
        $consoleLogs.append(`<div>[${time}] ${msg}</div>`);
        $consoleLogs.scrollTop($consoleLogs[0].scrollHeight);
    }

    function processNextQueueItem() {
        if (!queueRunning) return;

        appendConsoleLog('Fetching next task from queue...');

        // Visually mark the first queued item in the table as processing
        const $nextQueuedRow = $('#sab-queue-table tbody tr.sab-queue-row-queued').first();
        if ($nextQueuedRow.length) {
            $nextQueuedRow.removeClass('sab-queue-row-queued').addClass('sab-queue-row-processing');
            $nextQueuedRow.find('.sab-status-badge')
                .removeClass('sab-status-queued')
                .addClass('sab-status-processing')
                .html('⚙️ Processing');
        }

        $.post(sabData.ajaxUrl, {
            action: 'sab_process_queue_item',
            nonce:  sabData.nonce,
        }, function (res) {
            if (!queueRunning) return;

            if (res.success) {
                if (res.data.processed === false) {
                    // Queue empty
                    appendConsoleLog('✅ ' + res.data.message);
                    $btnRunQueue.trigger('click'); // Stop runner
                } else {
                    // Processed successfully: update table row status live!
                    const qId = res.data.id;
                    const $row = $('#sab-queue-row-' + qId);
                    if ($row.length) {
                        $row.removeClass('sab-queue-row-queued sab-queue-row-processing')
                            .addClass('sab-queue-row-published');
                        $row.find('.sab-status-badge')
                            .removeClass('sab-status-queued sab-status-processing sab-status-failed')
                            .addClass('sab-status-published')
                            .html('✅ Published');
                        $row.find('td').last().html('—'); // Clear action buttons since it is published
                    }

                    appendConsoleLog(`🎉 Success: Generated post "${res.data.title}"`);
                    if (res.data.url) {
                        appendConsoleLog(`🔗 View Post: <a href="${res.data.url}" target="_blank" style="color:#60a5fa; text-decoration:underline;">Link</a>`);
                    }
                    // Wait 3 seconds, then process next item
                    appendConsoleLog('Waiting 3 seconds before next task...');
                    setTimeout(processNextQueueItem, 3000);
                }
            } else {
                // Process failed (e.g. rate limit, api error): update table row status live!
                const qId = res.data && res.data.id ? res.data.id : null;
                if (qId) {
                    const $row = $('#sab-queue-row-' + qId);
                    if ($row.length) {
                        $row.removeClass('sab-queue-row-queued sab-queue-row-processing')
                            .addClass('sab-queue-row-failed');
                        $row.find('.sab-status-badge')
                            .removeClass('sab-status-queued sab-status-processing sab-status-published')
                            .addClass('sab-status-failed')
                            .html('❌ Failed');
                    }
                } else {
                    // Fallback: reset the temporary processing row back to queued or failed
                    const $procRow = $('#sab-queue-table tbody tr.sab-queue-row-processing').first();
                    if ($procRow.length) {
                        $procRow.removeClass('sab-queue-row-processing').addClass('sab-queue-row-failed');
                        $procRow.find('.sab-status-badge')
                            .removeClass('sab-status-processing')
                            .addClass('sab-status-failed')
                            .html('❌ Failed');
                    }
                }

                const errorMsg = res.data.message || 'Unknown error';
                appendConsoleLog(`❌ Error: ${errorMsg}`);

                if (errorMsg.indexOf('exhausted') !== -1 || errorMsg.indexOf('rate limit') !== -1) {
                    appendConsoleLog('⏳ Rate limits exceeded. Autoretry in 30 seconds (Auto-continue enabled)...');
                    setTimeout(processNextQueueItem, 30000);
                } else {
                    // Other error: try next item anyway after 5 seconds
                    appendConsoleLog('Retrying next item in queue in 5 seconds...');
                    setTimeout(processNextQueueItem, 5000);
                }
            }
        }).fail(function () {
            if (!queueRunning) return;
            // Connection failed: reset processing indicator to failed
            const $procRow = $('#sab-queue-table tbody tr.sab-queue-row-processing').first();
            if ($procRow.length) {
                $procRow.removeClass('sab-queue-row-processing').addClass('sab-queue-row-failed');
                $procRow.find('.sab-status-badge')
                    .removeClass('sab-status-processing')
                    .addClass('sab-status-failed')
                    .html('❌ Failed');
            }
            appendConsoleLog('❌ Connection failed. Retrying in 10 seconds...');
            setTimeout(processNextQueueItem, 10000);
        });
    }

    // =========================================================================
    // Thumbnail Manager Ajax Handler
    // =========================================================================
    $(document).on('click', '.sab-btn-gen-thumb-ai, .sab-btn-gen-thumb-t2i', function () {
        const $btn = $(this);
        const postId = $btn.data('post-id');
        const engine = $btn.hasClass('sab-btn-gen-thumb-ai') ? 'ai' : 'text_to_image';
        const originalText = $btn.html();

        $btn.html('<span class="sab-spinner"></span> Generating...').prop('disabled', true);

        $.post(sabData.ajaxUrl, {
            action:  'sab_generate_pending_thumbnail',
            nonce:   sabData.nonce,
            post_id: postId,
            engine:  engine
        }, function (res) {
            if (res.success) {
                $btn.html('✅ Done!').css('background', '#10b981');
                setTimeout(function () {
                    location.reload();
                }, 1000);
            } else {
                $btn.html(originalText).prop('disabled', false);
                alert('Error generating thumbnail: ' + (res.data && res.data.message ? res.data.message : 'Unknown error'));
            }
        }).fail(function () {
            $btn.html(originalText).prop('disabled', false);
            alert('Server request failed.');
        });
    });

    // =========================================================================
    // Thumbnail Manager — Select All / Checkbox Toggle
    // =========================================================================

    $('#sab-thumb-select-all').on('change', function () {
        const checked = $(this).is(':checked');
        $('.sab-thumb-checkbox').prop('checked', checked);
        updateSelectedThumbBtn();
    });

    $(document).on('change', '.sab-thumb-checkbox', function () {
        updateSelectedThumbBtn();
        const total = $('.sab-thumb-checkbox').length;
        const checked = $('.sab-thumb-checkbox:checked').length;
        $('#sab-thumb-select-all').prop('checked', total > 0 && checked === total);
    });

    function updateSelectedThumbBtn() {
        const count = $('.sab-thumb-checkbox:checked').length;
        const $btn = $('#sab-btn-generate-selected-thumbs');
        if (count > 0) {
            $btn.prop('disabled', false).html('🖼️ Generate Selected (' + count + ')');
        } else {
            $btn.prop('disabled', true).html('🖼️ Generate Selected Thumbnails');
        }
    }

    // =========================================================================
    // Thumbnail Manager — Bulk Generate (All / Selected)
    // =========================================================================

    function runBulkThumbGeneration(postIds) {
        if (!postIds.length) return;

        const engine = $('#sab-bulk-thumb-engine').val() || 'ai';
        const total = postIds.length;
        let current = 0;
        let successCount = 0;
        let failCount = 0;

        // Show progress, disable buttons
        $('#sab-bulk-thumb-progress').slideDown(200);
        $('#sab-btn-generate-all-thumbs, #sab-btn-generate-selected-thumbs').prop('disabled', true);
        $('#sab-bulk-thumb-status').text('Processing...');
        $('#sab-bulk-thumb-count').text('0 / ' + total);
        $('#sab-bulk-thumb-bar').css('width', '0%');

        function processNext() {
            if (current >= total) {
                $('#sab-bulk-thumb-status').html('✅ Complete! ' + successCount + ' succeeded, ' + failCount + ' failed.');
                $('#sab-btn-generate-all-thumbs, #sab-btn-generate-selected-thumbs').prop('disabled', false);
                if (successCount > 0) {
                    setTimeout(() => location.reload(), 2000);
                }
                return;
            }

            const postId = postIds[current];
            const $row = $('#sab-thumb-row-' + postId);

            $row.css({ background: 'rgba(99,102,241,0.08)' });
            $('#sab-bulk-thumb-status').text('Generating thumbnail for Post #' + postId + '...');

            $.post(sabData.ajaxUrl, {
                action:  'sab_generate_pending_thumbnail',
                nonce:   sabData.nonce,
                post_id: postId,
                engine:  engine
            }).done(function (res) {
                if (res.success) {
                    successCount++;
                    $row.css({ background: 'rgba(16,185,129,0.08)' });
                    $row.find('.sab-status-badge').removeClass('sab-status-exhausted').html('✅ Generated');
                    $row.find('.sab-thumb-checkbox').prop('checked', false).prop('disabled', true);
                } else {
                    failCount++;
                    $row.css({ background: 'rgba(239,68,68,0.08)' });
                }
            }).fail(function () {
                failCount++;
                $row.css({ background: 'rgba(239,68,68,0.08)' });
            }).always(function () {
                current++;
                const pct = Math.round((current / total) * 100);
                $('#sab-bulk-thumb-bar').css('width', pct + '%');
                $('#sab-bulk-thumb-count').text(current + ' / ' + total);
                processNext();
            });
        }

        processNext();
    }

    // Generate All button
    $('#sab-btn-generate-all-thumbs').on('click', function () {
        const postIds = [];
        $('.sab-thumb-checkbox').each(function () {
            if (!$(this).prop('disabled')) {
                postIds.push($(this).data('post-id'));
            }
        });
        if (!postIds.length) {
            alert('No pending thumbnails to generate.');
            return;
        }
        if (!confirm('Generate thumbnails for all ' + postIds.length + ' pending posts?')) return;
        runBulkThumbGeneration(postIds);
    });

    // Generate Selected button
    $('#sab-btn-generate-selected-thumbs').on('click', function () {
        const postIds = [];
        $('.sab-thumb-checkbox:checked').each(function () {
            postIds.push($(this).data('post-id'));
        });
        if (!postIds.length) {
            alert('Please select at least one post.');
            return;
        }
        runBulkThumbGeneration(postIds);
    });

    // Bulk Tags Action
    $(document).on('click', '#sab-btn-apply-tag-qty-all', function () {
        const val = $('#sab-bulk-tag-qty').val();
        $('.sab-tag-qty-select').val(val);
    });

    $(document).on('click', '.sab-btn-gen-tags', function () {
        const $btn = $(this);
        const postId = $btn.data('post-id');
        const $row = $btn.closest('tr');
        const tagQty = $row.find('.sab-tag-qty-select').val() || '5';
        const originalText = $btn.html();

        $btn.html('<span class="sab-spinner"></span> Generating...').prop('disabled', true);

        $.post(sabData.ajaxUrl, {
            action:    'sab_generate_tags',
            nonce:     sabData.nonce,
            post_id:   postId,
            tag_count: parseInt(tagQty, 10)
        }, function (res) {
            if (res.success) {
                $btn.html('✅ Done!').css('background', '#10b981');
                
                // Update tag badges dynamically on the screen!
                const $container = $row.find('.sab-tags-container');
                $container.fadeOut(300, function() {
                    $container.empty();
                    if (res.data.tags && res.data.tags.length > 0) {
                        res.data.tags.forEach(function(tag) {
                            $container.append(`
                                <span class="sab-status-badge" style="background:#f1f5f9; border:1px solid #e2e8f0; color:#475569; font-size:10px; font-weight:600; padding:2px 6px; border-radius:4px; margin-right:5px; margin-bottom:5px;">
                                    #${tag}
                                </span>
                            `);
                        });
                    } else {
                        $container.html('<span style="color:#94a3b8; font-style:italic; font-size:11px;">— No tags</span>');
                    }
                    $container.fadeIn(300);
                });

                setTimeout(function () {
                    $btn.html(originalText).css('background', '').prop('disabled', false);
                }, 2000);
            } else {
                $btn.html(originalText).prop('disabled', false);
                alert('Error generating tags: ' + (res.data.message || 'Unknown error'));
            }
        }).fail(function () {
            $btn.html(originalText).prop('disabled', false);
            alert('Server request failed.');
        });
    });

    // -------------------------------------------------------------------------
    // Bulk Translator Logic
    // -------------------------------------------------------------------------
    function logTrans(msg, type = 'info') {
        const $log = $('#sab-trans-log');
        const color = type === 'error' ? '#ef4444' : (type === 'success' ? '#10b981' : '#cbd5e1');
        $log.append(`<div style="color: ${color}">[${new Date().toLocaleTimeString()}] ${msg}</div>`);
        $log.scrollTop($log[0].scrollHeight);
    }

    // Header checkbox sync
    $('#sab-translator-select-all, #sab-translator-table-header-select').on('change', function () {
        const checked = $(this).prop('checked');
        $('#sab-translator-select-all, #sab-translator-table-header-select').prop('checked', checked);
        $('.sab-trans-post-checkbox:not(:disabled)').prop('checked', checked);
    });

    $('#sab-btn-translate-selected').on('click', function () {
        const selectedIds = [];
        $('.sab-trans-post-checkbox:checked').each(function () {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            alert('Please select at least one post to translate.');
            return;
        }

        const targetLang = $('#sab-translator-target-lang').val();
        const postStatus = $('#sab-translator-status').val();
        const $btn = $(this);
        const originalText = $btn.html();

        $btn.html('🌐 Translating...').prop('disabled', true);
        $('#sab-translator-progress-container').slideDown();
        $('#sab-trans-log').html('<div>[Batch translation started]</div>');

        let currentIndex = 0;
        const total = selectedIds.length;

        function updateProgress() {
            const pct = Math.round((currentIndex / total) * 100);
            $('#sab-trans-progress-bar').css('width', pct + '%');
            $('#sab-trans-progress-percent').html(pct + '%');
            $('#sab-trans-progress-text').html(`Translating article ${currentIndex + 1} of ${total}...`);
        }

        function processNext() {
            if (currentIndex >= total) {
                logTrans('🎉 Batch translation completed successfully!', 'success');
                $('#sab-trans-progress-text').html('Translation batch completed!');
                $btn.html('✅ Completed').css('background', '#10b981');
                setTimeout(function () {
                    $btn.html(originalText).css('background', '').prop('disabled', false);
                }, 3000);
                return;
            }

            updateProgress();
            const postId = selectedIds[currentIndex];
            logTrans(`Starting translation for Post #${postId} into ${targetLang}...`, 'info');

            $.post(sabData.ajaxUrl, {
                action:      'sab_translate_post',
                nonce:       sabData.nonce,
                post_id:     postId,
                target_lang: targetLang,
                status:      postStatus
            }, function (res) {
                if (res.success) {
                    logTrans(`✅ Success: Post #${postId} translated! New Post: "${res.data.translated_title}" (ID: #${res.data.translated_id})`, 'success');
                    // Add badge dynamically to the row
                    const $row = $(`#sab-trans-row-${postId}`);
                    $row.find('.sab-trans-post-checkbox').prop('checked', false).prop('disabled', true);
                } else {
                    logTrans(`❌ Error on Post #${postId}: ${res.data.message || 'Unknown error'}`, 'error');
                }
                currentIndex++;
                processNext();
            }).fail(function () {
                logTrans(`❌ Connection failed for Post #${postId}`, 'error');
                currentIndex++;
                processNext();
            });
        }

        processNext();
    });

    // =========================================================================
    // Google Indexing Tool — Request Indexing Button
    // =========================================================================

    $(document).on('click', '.sab-btn-request-indexing', function () {
        const $btn    = $(this);
        const postId  = $btn.data('post-id');
        const $row    = $('#sab-gsc-row-' + postId);
        const origTxt = $btn.html();

        $btn.prop('disabled', true).html('<span class="sab-spinner-small"></span> Submitting...');

        $.post(ajaxurl, {
            action: 'sab_request_indexing',
            _ajax_nonce: sab_admin.nonce,
            post_id: postId
        }).done(function (res) {
            if (res.success) {
                $btn.html('✅ Submitted!').css({ background: '#166534' });
                const now = new Date();
                const dateStr = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ', ' + now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
                $row.find('.sab-gsc-ping-cell').html(
                    '<span class="sab-status-badge" style="background:#dcfce7; color:#166534; font-size:10px;">✅ ' + dateStr + '</span>'
                );
                setTimeout(() => { $btn.html(origTxt).css({ background: '#059669' }).prop('disabled', false); }, 3000);
            } else {
                $btn.html('❌ Failed').css({ background: '#dc2626' });
                alert('Error: ' + (res.data && res.data.message ? res.data.message : 'Unknown error'));
                setTimeout(() => { $btn.html(origTxt).css({ background: '#059669' }).prop('disabled', false); }, 3000);
            }
        }).fail(function () {
            $btn.html('❌ Connection Error').css({ background: '#dc2626' });
            setTimeout(() => { $btn.html(origTxt).css({ background: '#059669' }).prop('disabled', false); }, 3000);
        });
    });

    // =========================================================================
    // AI Article Rewriter — Preview + Save
    // =========================================================================

    $(document).on('click', '.sab-btn-rewrite-post', function () {
        const $btn    = $(this);
        const postId  = $btn.data('post-id');
        const save    = $btn.data('save') || 'preview';
        const $row    = $('#sab-rewriter-row-' + postId);
        const $instrField = $('#sab-rewrite-instructions-' + postId);
        const instructions = $instrField ? $instrField.val() : '';
        const origTxt = $btn.html();

        $btn.prop('disabled', true).html('<span class="sab-spinner-small"></span> Rewriting...');

        $.post(sabData.ajaxUrl, {
            action: 'sab_rewrite_post',
            _ajax_nonce: sabData.nonce,
            post_id: postId,
            instructions: instructions,
            save: save
        }).done(function (res) {
            if (res.success) {
                if (save === 'save') {
                    $btn.html('✅ Saved!').css({ background: '#166534' });
                    setTimeout(() => { $btn.html(origTxt).css({ background: '' }).prop('disabled', false); }, 3000);
                    const $preview = $('#sab-rewrite-preview-' + postId);
                    if ($preview.length) $preview.slideUp(200);
                } else {
                    const $previewContainer = $('#sab-rewrite-preview-' + postId);
                    if ($previewContainer.length) {
                        $previewContainer.html(
                            '<div class="sab-rewrite-preview-content">' +
                            '<div class="sab-panel-header"><h3 class="sab-panel-title" style="font-size:13px;">📝 Rewrite Preview</h3>' +
                            '<button type="button" class="sab-btn sab-btn-primary sab-btn-small sab-btn-rewrite-post" data-post-id="' + postId + '" data-save="save" style="background:#059669; border-color:#059669;">💾 Save to Post</button></div>' +
                            '<div style="max-height:400px; overflow-y:auto; padding:15px; background:#1a1a2e; border-radius:8px; margin-top:10px; font-size:13px; color:#e2e8f0; line-height:1.8;">' +
                            res.data.preview +
                            '</div></div>'
                        ).slideDown(300);
                    }
                    $btn.html(origTxt).prop('disabled', false);
                }
            } else {
                $btn.html('❌ Error').css({ background: '#dc2626' });
                alert('Error: ' + (res.data && res.data.message ? res.data.message : 'Unknown error'));
                setTimeout(() => { $btn.html(origTxt).css({ background: '' }).prop('disabled', false); }, 3000);
            }
        }).fail(function () {
            $btn.html('❌ Failed').css({ background: '#dc2626' });
            setTimeout(() => { $btn.html(origTxt).css({ background: '' }).prop('disabled', false); }, 3000);
        });
    });
    // =========================================================================
    // Google Service Account JSON Tab Switcher & File Uploader
    // =========================================================================

    $(document).on('click', '.sab-gsc-tab-btn', function() {
        const $btn = $(this);
        const tab  = $btn.data('tab');
        
        $('.sab-gsc-tab-btn').removeClass('active').css({ background: 'transparent', color: '#94a3b8' });
        $btn.addClass('active').css({ background: 'rgba(255,255,255,0.1)', color: '#fff' });
        
        $('.sab-gsc-tab-content').hide();
        $('#sab-gsc-tab-' + tab).show();
    });

    $(document).on('click', '#sab-gsc-drag-drop-zone', function() {
        $('#sab-gsc-file-input').trigger('click');
    });

    $(document).on('dragover', '#sab-gsc-drag-drop-zone', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).css('border-color', 'var(--sab-primary)');
    });

    $(document).on('dragleave', '#sab-gsc-drag-drop-zone', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).css('border-color', 'rgba(255,255,255,0.1)');
    });

    $(document).on('drop', '#sab-gsc-drag-drop-zone', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).css('border-color', 'rgba(255,255,255,0.1)');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files && files.length) {
            const file = files[0];
            handleGscJsonFile(file);
        }
    });

    $(document).on('change', '#sab-gsc-file-input', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        handleGscJsonFile(file);
    });

    function handleGscJsonFile(file) {
        if (!file.name.endsWith('.json')) {
            $('#sab-gsc-file-status').text('❌ Only .json files are allowed.').css({ color: '#f87171' });
            return;
        }
        const reader = new FileReader();
        reader.onload = function(evt) {
            try {
                const json = JSON.parse(evt.target.result);
                $('#sab_gsc_json_textarea').val(JSON.stringify(json, null, 2));
                $('#sab-gsc-file-status').text('✅ JSON file loaded successfully! Click Save to apply.').css({ color: '#34d399' });
            } catch (err) {
                $('#sab-gsc-file-status').text('❌ Invalid JSON file. Please try again.').css({ color: '#f87171' });
            }
        };
        reader.readAsText(file);
    }


    // =========================================================================
    // Scheduler Queue Checkbox and Delete Handler
    // =========================================================================
    const selectAll = document.getElementById('sab-select-all-queue');
    const checkboxes = document.querySelectorAll('.sab-queue-checkbox');
    const deleteBtn = document.getElementById('sab-btn-delete-selected');

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
        const checked = document.querySelectorAll('.sab-queue-checkbox:checked');
        if (deleteBtn) {
            if (checked.length > 0) {
                deleteBtn.style.display = 'inline-block';
                deleteBtn.textContent = '✕ Delete Selected (' + checked.length + ')';
            } else {
                deleteBtn.style.display = 'none';
            }
        }
    }

    window.sabDeleteSelectedQueue = function() {
        if (!confirm('Are you sure you want to delete the selected queue items?')) return;
        const checked = document.querySelectorAll('.sab-queue-checkbox:checked');
        const ids = Array.from(checked).map(cb => cb.value);
        document.getElementById('sab-hidden-delete-queue-ids').value = ids.join(',');
        document.getElementById('sab-form-delete-selected-queue').submit();
    };

});

