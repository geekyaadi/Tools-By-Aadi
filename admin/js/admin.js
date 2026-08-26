/**
 * Tools By Aadi — Admin JavaScript
 * Step-by-step generation pipeline with live progress, key-switch notices, preview, and duplicate handling.
 * Includes: reference image upload, drag-drop, base64 conversion, and Settings default image management.
 */

/* global aapData, jQuery */
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
    const $nicheInput     = $('#tba-niche-input');
    const $btnFindTitles  = $('#tba-btn-find-titles');
    const $titlesList     = $('#tba-titles-list');
    const $stepTitles     = $('#tba-step-titles');
    const $stepOptions    = $('#tba-step-options');
    const $btnGenerate    = $('#tba-btn-generate');
    const $btnPreview     = $('#tba-btn-preview');
    const $progressIdle   = $('#tba-progress-idle');
    const $progressSteps  = $('#tba-progress-steps');
    const $keySwitchNotice= $('#tba-key-switch-notice');
    const $keySwitchText  = $('#tba-key-switch-text');
    const $result         = $('#tba-result');
    const $previewPanel   = $('#tba-preview-panel');
    const $btnConfirmPublish = $('#tba-btn-confirm-publish');
    const $btnCancelPreview  = $('#tba-btn-cancel-preview');
    const $sessionId      = $('#tba-session-id');
    const $postStatus     = $('#tba-post-status');

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
        state.sessionId = 'tba_' + Date.now();
        $sessionId.val(state.sessionId);

        $btnFindTitles.html('<span class="tba-spinner"></span> Finding Titles...');
        $btnFindTitles.prop('disabled', true);
        $titlesList.html('<div class="tba-titles-placeholder"><span class="tba-spinner"></span> Asking Gemini for title ideas...</div>');

        $.post(aapData.ajaxUrl, {
            action:         'tba_get_titles',
            nonce:          aapData.nonce,
            niche:          niche,
            focus_keywords: $('#tba-keywords-input').val() || '',
            session_id:     state.sessionId,
        }, function (res) {
            $btnFindTitles.html('<span class="tba-btn-icon">🔍</span> Find Titles').prop('disabled', false);

            if (!res.success) {
                showAlert(res.data.message || aapData.strings.error, 'error');
                $titlesList.html('<div class="tba-titles-placeholder">Failed to fetch titles. Please try again.</div>');
                return;
            }

            renderTitles(res.data.titles);
            $stepTitles.addClass('tba-step-unlocked').removeClass('tba-step-locked');
            $stepOptions.addClass('tba-step-unlocked').removeClass('tba-step-locked');

            if (res.data.switched) {
                showKeySwitchNotice('Key switched during title generation.');
            }
        }).fail(function () {
            $btnFindTitles.html('<span class="tba-btn-icon">🔍</span> Find Titles').prop('disabled', false);
            showAlert(aapData.strings.error, 'error');
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
            $titlesList.html('<div class="tba-titles-placeholder">No titles returned. Try a different niche.</div>');
            return;
        }

        titles.forEach(function (title, i) {
            const id    = 'tba-title-' + i;
            const $item = $('<div class="tba-title-option"></div>');
            const $radio = $('<input type="radio" name="tba_title">')
                .attr('id', id).val(title);
            const $label = $('<label></label>').attr('for', id).text(title);

            $item.append($radio, $label);
            $titlesList.append($item);

            $item.on('click', function () {
                $('.tba-title-option').removeClass('selected');
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
        $('.tba-progress-step').each(function () {
            $(this).removeClass('running done error');
            $(this).find('.tba-pstep-dot').attr('class', 'tba-pstep-dot waiting');
            $(this).find('.tba-pstep-meta').text('');
        });

        $btnGenerate.prop('disabled', true).html('<span class="tba-spinner"></span> Generating...');
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
            action:         'tba_generate_post',
            nonce:          aapData.nonce,
            session_id:     state.sessionId,
            title:          state.title,
            niche:          state.niche,
            step:           step,
            post_status:    state.postStatus,
            focus_keywords: $('#tba-keywords-input').val() || '',
            preview_only:   isPublish && state.previewOnly ? 1 : 0,
            tag_count:      parseInt($('#tba-tag-count').val() || '0', 10),
            category:       $('#tba-post-category').val() || '',
        };

        // Attach reference image only for thumbnail step (AI Generated Only)
        if (step === 'thumbnail') {
            const method = $('#tba-thumb-type').val() || 'ai';
            if (method === 'ai' && state.refImageB64) {
                payload.ref_image_b64  = state.refImageB64;
                payload.ref_image_mime = state.refImageMime;
            } else if (method === 'text_to_image') {
                payload.thumb_type  = 'text_to_image';
                payload.t2i_bg_type = $('#tba-t2i-bg-type').val();
                payload.t2i_bg_val  = $('#tba-t2i-bg-type').val() === 'gradient'
                    ? $('#tba-t2i-bg-val-gradient').val()
                    : $('#tba-t2i-bg-val-solid').val();
                payload.t2i_size    = $('#tba-t2i-size').val();
            }
        }

        // Attach Title-to-Image options for OG Image step if selected
        if (step === 'og_image') {
            const method = $('#tba-thumb-type').val() || 'ai';
            if (method === 'text_to_image') {
                payload.thumb_type  = 'text_to_image';
                payload.t2i_bg_type = $('#tba-t2i-bg-type').val();
                payload.t2i_bg_val  = $('#tba-t2i-bg-type').val() === 'gradient'
                    ? $('#tba-t2i-bg-val-gradient').val()
                    : $('#tba-t2i-bg-val-solid').val();
            }
        }

        $.post(aapData.ajaxUrl, payload, function (res) {

            if (!res.success) {
                // If thumbnail or og_image step fails, mark it but continue to next step instead of getting stuck
                if (step === 'thumbnail' || step === 'og_image') {
                    markStepError(step, res.data.message || 'Failed/Skipped');
                    state.currentStep++;
                    runNextStep();
                    return;
                }
                
                markStepError(step, res.data.message);
                showAlert('Error at step "' + step + '": ' + (res.data.message || aapData.strings.error), 'error');
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
            showAlert(aapData.strings.error, 'error');
            finishGeneration(false);
        });
    }

    // =========================================================================
    // Step UI Helpers
    // =========================================================================
    function markStepRunning(step) {
        const $step = $('#tba-pstep-' + step);
        $step.addClass('running').removeClass('done error');
        $step.find('.tba-pstep-dot').attr('class', 'tba-pstep-dot running');
        $step.find('.tba-pstep-meta').text('Generating...');
    }

    function markStepDone(step, data) {
        const $step = $('#tba-pstep-' + step);
        $step.addClass('done').removeClass('running error');
        $step.find('.tba-pstep-dot').attr('class', 'tba-pstep-dot done');

        let meta = '✓ Done';
        if (data) {
            if (data.cached)         meta = '⚡ Cached (resumed)';
            if (data.count)          meta += ' — ' + data.count + ' generated';
            if (data.used_reference) meta += ' · 🖼️ styled from reference';
            if (data.key_used && !data.cached) meta += ' — Key: ' + data.key_used;
        }
        $step.find('.tba-pstep-meta').text(meta);
    }

    function markStepError(step, msg) {
        const $step = $('#tba-pstep-' + step);
        $step.addClass('error').removeClass('running done');
        $step.find('.tba-pstep-dot').attr('class', 'tba-pstep-dot error');
        $step.find('.tba-pstep-meta').text('❌ ' + (msg || 'Error'));
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
            <div class="tba-result-title">🎉 Post ${statusLabel} Successfully!</div>
            <div class="tba-result-meta">
                Estimated tokens: ~${numberFormat(data.token_est)} &nbsp;|&nbsp;
                Status: <strong>${statusLabel}</strong>
            </div>
            <div class="tba-result-links">
                <a href="${data.edit_url}" target="_blank" class="tba-btn tba-btn-secondary">✏️ Edit Post</a>
                ${data.post_status === 'publish' ? `<a href="${data.post_url}" target="_blank" class="tba-btn tba-btn-primary">👁 View Post</a>` : ''}
            </div>
        `).show();
    }

    // =========================================================================
    // Preview Panel
    // =========================================================================
    function showPreview(data) {
        $('#tba-preview-title').text(data.title);
        $('#tba-preview-category').text(data.category || '');
        $('#tba-preview-meta-desc').text(data.meta || '');
        $('#tba-preview-content').html(data.article || '');
        $('#tba-preview-tags').text(data.tags || '');
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
        $btnGenerate.prop('disabled', true).html('<span class="tba-spinner"></span> Publishing...');
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
            <div class="tba-dup-warning">
                <p>⚠️ A similar post already exists: <a href="${data.dup_url}" target="_blank">"${data.dup_title}"</a></p>
                <div class="tba-dup-actions">
                    <button id="tba-btn-force-publish" class="tba-btn tba-btn-primary">✅ Publish Anyway</button>
                    <button id="tba-btn-cancel-dup" class="tba-btn tba-btn-ghost">✕ Cancel</button>
                </div>
            </div>
        `;
        $result.html(html).show();

        $('#tba-btn-force-publish').on('click', function () {
            $result.empty().hide();
            state.running = true;
            state.currentStep = state.steps.indexOf('publish');
            // Use force_publish step
            markStepRunning('publish');
            $.post(aapData.ajaxUrl, {
                action:      'tba_generate_post',
                nonce:       aapData.nonce,
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
                    showAlert(res.data.message || aapData.strings.error, 'error');
                }
                finishGeneration(res.success);
            });
        });

        $('#tba-btn-cancel-dup').on('click', function () {
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
        const $alert = $('<div class="tba-alert tba-alert-' + type + '">' + msg + '</div>');
        $('.tba-content').prepend($alert);
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
    $(document).on('click', '.tba-btn-ping', function () {
        const $btn   = $(this);
        const idx    = $btn.data('key-index');
        const $row   = $btn.closest('tr');

        $btn.html('<span class="tba-spinner" style="width:12px;height:12px;"></span>')
            .prop('disabled', true);

        $.post(aapData.ajaxUrl, {
            action:    'tba_ping_key',
            nonce:     aapData.nonce,
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
    $('#tba-btn-ping-all').on('click', function () {
        const $btn = $(this);
        $btn.html('<span class="tba-spinner" style="width:12px;height:12px;"></span> Testing...')
            .prop('disabled', true);

        $.post(aapData.ajaxUrl, {
            action: 'tba_ping_all_keys',
            nonce:  aapData.nonce,
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
            active:    '<span class="tba-status-badge tba-status-active">✅ Active</span>',
            exhausted: '<span class="tba-status-badge tba-status-exhausted">🔴 Exhausted</span>',
            invalid:   '<span class="tba-status-badge tba-status-invalid">⛔ Invalid</span>',
            error:     '<span class="tba-status-badge tba-status-exhausted">⚠️ Error</span>',
        };
        const pingMap = {
            active:    '✅',
            exhausted: '🔴',
            invalid:   '⛔',
        };

        // Status cell
        $row.find('.tba-key-status-cell').html( statusMap[ d.status ] || '' );

        // Countdown cell
        const $countdown = $row.find('.tba-key-countdown-cell');
        if ( d.status === 'exhausted' && d.reset_at_ts ) {
            $row.attr('data-reset-ts', d.reset_at_ts);
            const secsLeft = Math.max(0, d.reset_at_ts - Math.floor(Date.now() / 1000));
            $countdown.html(
                '<span class="tba-countdown" data-reset-ts="' + d.reset_at_ts + '">' +
                '⏱ <span class="tba-countdown-val">' + formatSecs(secsLeft) + '</span></span>'
            );
        } else if ( d.status === 'active' ) {
            $countdown.html('<span class="tba-text-muted">—</span>');
        }

        // Last ping cell — update timestamp + icon
        const now  = new Date();
        const hhmm = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');
        $row.find('.tba-key-ping-cell').html(
            '<span class="tba-ping-badge">' + (pingMap[d.status] || '❓') + '</span> ' +
            '<span class="tba-ping-time">' + hhmm + '</span>'
        );

        // Row highlight
        $row.removeClass('tba-row-exhausted tba-row-invalid');
        if (d.status !== 'active') $row.addClass('tba-row-exhausted');
    }

    // =========================================================================
    // ⏱ Live Countdown Ticker (for all .tba-countdown elements on the page)
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
        $('.tba-countdown').each(function () {
            const resetTs  = parseInt($(this).data('reset-ts'), 10);
            const secsLeft = Math.max(0, resetTs - nowTs);
            $(this).find('.tba-countdown-val').text(formatSecs(secsLeft));

            if (secsLeft === 0) {
                // Key reset time reached — update row to "Active"
                const $row = $(this).closest('tr');
                $row.find('.tba-key-status-cell').html(
                    '<span class="tba-status-badge tba-status-active">✅ Active (restored)</span>'
                );
                $(this).closest('td').html('<span class="tba-text-muted">—</span>');
                $row.removeClass('tba-row-exhausted');
            }
        });
    }

    // Start ticker if there are any countdowns on the page
    if ($('.tba-countdown').length) {
        setInterval(tickCountdowns, 1000);
        tickCountdowns(); // immediate first tick
    }

    // Ping toast notification
    function showPingToast(msg, type) {
        const cls = { success: '#22c55e', warning: '#f59e0b', error: '#ef4444' }[type] || '#94a3b8';
        const $toast = $(
            '<div class="tba-ping-toast" style="border-left-color:' + cls + '">' + msg + '</div>'
        );
        $('body').append($toast);
        setTimeout(() => $toast.addClass('tba-ping-toast-show'), 50);
        setTimeout(() => {
            $toast.removeClass('tba-ping-toast-show');
            setTimeout(() => $toast.remove(), 400);
        }, 4000);
    }



    const $uploadZone    = $('#tba-upload-zone');
    const $uploadIdle    = $('#tba-upload-idle');
    const $uploadPreview = $('#tba-upload-preview');
    const $refImgInput   = $('#tba-ref-img-input');
    const $refImgThumb   = $('#tba-ref-img-thumb');
    const $refImgName    = $('#tba-ref-img-name');
    const $btnClearRef   = $('#tba-btn-clear-ref');

    const MAX_SIZE = 4 * 1024 * 1024; // 4MB

    // Click anywhere on idle zone to open file picker
    $uploadZone.on('click', function (e) {
        if ($(e.target).closest('#tba-btn-clear-ref').length) return;
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

    const $settingsZone          = $('#tba-settings-upload-zone');
    const $settingsIdle          = $('#tba-settings-upload-idle');
    const $settingsPreview       = $('#tba-settings-upload-preview');
    const $settingsInput         = $('#tba-settings-ref-input');
    const $settingsRefPreview    = $('#tba-settings-ref-preview');
    const $settingsRefName       = $('#tba-settings-ref-name');
    const $btnSettingsSave       = $('#tba-btn-settings-save-ref');
    const $btnSettingsClear      = $('#tba-btn-settings-clear-ref');
    const $btnSettingsDelete     = $('#tba-btn-settings-delete-ref');
    const $settingsMsg           = $('#tba-settings-ref-msg');

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
            $btnSettingsSave.prop('disabled', true).html('<span class="tba-spinner"></span> Saving...');

            $.post(aapData.ajaxUrl, {
                action:     'tba_save_reference_image',
                nonce:      aapData.nonce,
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
            $btnSettingsDelete.prop('disabled', true).html('<span class="tba-spinner"></span>');

            $.post(aapData.ajaxUrl, {
                action: 'tba_delete_reference_image',
                nonce:  aapData.nonce,
            }, function (res) {
                $btnSettingsDelete.prop('disabled', false).html('🗑 Remove Default Image');
                if (res.success) {
                    $('#tba-settings-ref-current').fadeOut(300);
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
        $settingsMsg.html('<div class="tba-alert tba-alert-' + type + '">' + msg + '</div>').show();
        setTimeout(() => $settingsMsg.fadeOut(400), 5000);
    }

    // =========================================================================
    // Provider Select Toggles — Settings Page
    // =========================================================================

    // Add Key provider toggle
    $('#tba-key-provider-select').on('change', function () {
        const val   = $(this).val();
        const $input = $('#tba-key-input-field');
        const $hint  = $('#tba-add-key-hint');

        if (val === 'openai') {
            $input.attr('placeholder', 'Paste OpenAI API key here (sk-...)');
            $hint.html('Get your OpenAI API key from <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI API Keys →</a>');
        } else {
            $input.attr('placeholder', 'Paste Gemini API key here (AIza...)');
            $hint.html('Get your free Gemini API key from <a href="https://aistudio.google.com/app/apikey" target="_blank">Google AI Studio →</a>');
        }
    });

    // Active Provider toggle
    $('#tba-active-provider-select').on('change', function () {
        const val = $(this).val();
        const $geminiText = $('#tba-field-gemini-model');
        const $openaiText = $('#tba-field-openai-model');
        const $geminiImg  = $('#tba-field-gemini-image');

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
        $('#tba_thumb_type').on('change', function() {
            const val = $(this).val();
            if (val === 'text_to_image') {
                $('.tba-t2i-only').show();
                toggleSettingsT2IBg();
            } else {
                $('.tba-t2i-only').hide();
            }
        });
        $('#tba_t2i_bg_type').on('change', toggleSettingsT2IBg);

        function toggleSettingsT2IBg() {
            const bgType = $('#tba_t2i_bg_type').val();
            if (bgType === 'gradient') {
                $('#tba-t2i-gradient-field').show();
                $('#tba-t2i-solid-field').hide();
            } else if (bgType === 'solid') {
                $('#tba-t2i-gradient-field').hide();
                $('#tba-t2i-solid-field').show();
            } else {
                $('#tba-t2i-gradient-field').hide();
                $('#tba-t2i-solid-field').hide();
            }
        }

        // Toggles in Generate Page
        $('#tba-thumb-type').on('change', function() {
            const val = $(this).val();
            if (val === 'text_to_image') {
                $('.tba-t2i-only').show();
                $('.tba-ai-thumb-only').hide();
                toggleGenerateT2IBg();
            } else {
                $('.tba-t2i-only').hide();
                $('.tba-ai-thumb-only').show();
            }
        });
        $('#tba-t2i-bg-type').on('change', toggleGenerateT2IBg);

        function toggleGenerateT2IBg() {
            const bgType = $('#tba-t2i-bg-type').val();
            if (bgType === 'gradient') {
                $('#tba-t2i-gradient-group').show();
                $('#tba-t2i-solid-group').hide();
            } else if (bgType === 'solid') {
                $('#tba-t2i-gradient-group').hide();
                $('#tba-t2i-solid-group').show();
            } else {
                $('#tba-t2i-gradient-group').hide();
                $('#tba-t2i-solid-group').hide();
            }
        }

        // Toggles in Bulk Planner Page
        $('#tba-planner-thumb-type').on('change', function() {
            const val = $(this).val();
            if (val === 'text_to_image') {
                $('.tba-t2i-only').show();
                togglePlannerT2IBg();
            } else {
                $('.tba-t2i-only').hide();
            }
        });
        $('#tba-planner-t2i-bg-type').on('change', togglePlannerT2IBg);

        function togglePlannerT2IBg() {
            const bgType = $('#tba-planner-t2i-bg-type').val();
            if (bgType === 'gradient') {
                $('#tba-planner-t2i-gradient-group').show();
                $('#tba-planner-t2i-solid-group').hide();
            } else if (bgType === 'solid') {
                $('#tba-planner-t2i-gradient-group').hide();
                $('#tba-planner-t2i-solid-group').show();
            } else {
                $('#tba-planner-t2i-gradient-group').hide();
                $('#tba-planner-t2i-solid-group').hide();
            }
        }

        // Trigger on load
        if ($('#tba_thumb_type').length) {
            $('#tba_thumb_type').trigger('change');
            toggleSettingsT2IBg();
        }
        if ($('#tba-thumb-type').length) {
            $('#tba-thumb-type').trigger('change');
            toggleGenerateT2IBg();
        }
        if ($('#tba-planner-thumb-type').length) {
            $('#tba-planner-thumb-type').trigger('change');
            togglePlannerT2IBg();
        }
    }
    initT2IToggles();

    // =========================================================================
    // Bulk Planner JS Logic
    // =========================================================================

    const $btnPlannerFind  = $('#tba-btn-planner-find');
    const $plannerNiche    = $('#tba-planner-niche');
    const $plannerLang     = $('#tba-planner-lang');
    const $plannerDefCat   = $('#tba-planner-default-cat');
    const $plannerPanel    = $('#tba-planner-results-panel');
    const $plannerBody     = $('#tba-planner-table-body');
    const $btnSaveTasks    = $('#tba-btn-save-tasks');

    if ($('#tba-planner-mode').length) {
        $('#tba-planner-mode').on('change', function() {
            if ($(this).val() === 'silo') {
                $('#tba-planner-count-wrapper').hide();
            } else {
                $('#tba-planner-count-wrapper').show();
            }
        });
    }

    if ($btnPlannerFind.length) {
        $btnPlannerFind.on('click', function () {
            const niche  = $plannerNiche.val().trim();
            const lang   = $plannerLang.val();
            const defCat = $plannerDefCat.val();
            const mode   = $('#tba-planner-mode').val() || 'standard';
            const count  = $('#tba-planner-count').val() || '20';

            if (!niche) {
                alert('Please enter a niche first.');
                return;
            }

            $btnPlannerFind.html('<span class="tba-spinner"></span> Generating Plan...').prop('disabled', true);
            $plannerPanel.hide();
            $plannerBody.empty();

            $.post(aapData.ajaxUrl, {
                action:   'tba_generate_planner_titles',
                nonce:    aapData.nonce,
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

                const catHtml = $('#tba-cat-template-source').html();

                if (res.data.mode === 'silo') {
                    // Render Pillar Row First
                    const pillarTitle = res.data.pillar;
                    const pillarRow = `
                        <tr class="tba-silo-pillar-row" style="background:#f0f6fc; border-left: 4px solid var(--tba-primary);">
                            <td><input type="checkbox" class="tba-planner-checkbox" checked></td>
                            <td><strong>PILLAR</strong></td>
                            <td>
                                <input type="text" class="tba-input tba-planner-title-input" value="${pillarTitle.replace(/"/g, '&quot;')}" style="width:100%; font-weight:bold;">
                                <span class="tba-badge tba-badge-gemini" style="margin-top:4px;">Main Pillar Article</span>
                            </td>
                            <td>
                                <select class="tba-select tba-planner-cat-select" style="width:100%;">
                                    ${catHtml}
                                </select>
                            </td>
                        </tr>
                    `;
                    const $pillarRow = $(pillarRow);
                    if (defCat) {
                        $pillarRow.find('.tba-planner-cat-select').val(defCat);
                    }
                    $plannerBody.append($pillarRow);

                    // Render Supporting Cluster Rows
                    const titles = res.data.titles;
                    titles.forEach(function (title, i) {
                        const row = `
                            <tr class="tba-silo-cluster-row" style="border-left: 4px solid #ccd0d4;">
                                <td><input type="checkbox" class="tba-planner-checkbox" checked></td>
                                <td>${i + 1}</td>
                                <td>
                                    <input type="text" class="tba-input tba-planner-title-input" value="${title.replace(/"/g, '&quot;')}" style="width:100%;">
                                    <span class="tba-badge tba-badge-default" style="margin-top:4px;">Supporting Cluster Article</span>
                                </td>
                                <td>
                                    <select class="tba-select tba-planner-cat-select" style="width:100%;">
                                        ${catHtml}
                                    </select>
                                </td>
                            </tr>
                        `;
                        const $row = $(row);
                        if (defCat) {
                            $row.find('.tba-planner-cat-select').val(defCat);
                        }
                        $plannerBody.append($row);
                    });
                } else {
                    // Standard Row Rendering
                    const titles = res.data.titles;
                    titles.forEach(function (title, i) {
                        const row = `
                            <tr>
                                <td><input type="checkbox" class="tba-planner-checkbox" checked></td>
                                <td>${i + 1}</td>
                                <td><input type="text" class="tba-input tba-planner-title-input" value="${title.replace(/"/g, '&quot;')}" style="width:100%;"></td>
                                <td>
                                    <select class="tba-select tba-planner-cat-select" style="width:100%;">
                                        ${catHtml}
                                    </select>
                                </td>
                            </tr>
                        `;
                        const $row = $(row);
                        if (defCat) {
                            $row.find('.tba-planner-cat-select').val(defCat);
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
        $('#tba-btn-select-all').on('click', function () {
            $('.tba-planner-checkbox').prop('checked', true);
            $('#tba-check-master').prop('checked', true);
        });

        $('#tba-btn-deselect-all').on('click', function () {
            $('.tba-planner-checkbox').prop('checked', false);
            $('#tba-check-master').prop('checked', false);
        });

        $('#tba-check-master').on('change', function () {
            $('.tba-planner-checkbox').prop('checked', $(this).is(':checked'));
        });

        // Save selected titles as tasks
        $btnSaveTasks.on('click', function () {
            const niche = $plannerNiche.val().trim();
            const lang  = $plannerLang.val();
            const mode  = $('#tba-planner-mode').val() || 'standard';
            const tasks = [];

            $plannerBody.find('tr').each(function () {
                const $row = $(this);
                if ($row.find('.tba-planner-checkbox').is(':checked')) {
                    tasks.push({
                        title:    $row.find('.tba-planner-title-input').val().trim(),
                        category: $row.find('.tba-planner-cat-select').val()
                    });
                }
            });

            // Gather Title-to-Image choices for bulk task enqueuing
            const thumbType = $('#tba-planner-thumb-type').val();
            const t2iBgType = $('#tba-planner-t2i-bg-type').val();
            const t2iBgVal  = t2iBgType === 'gradient'
                ? $('#tba-planner-t2i-bg-val-gradient').val()
                : $('#tba-planner-t2i-bg-val-solid').val();
            const t2iSize   = $('#tba-planner-t2i-size').val();

            const metaOpts = {
                thumb_type:  thumbType,
                bg_type:     t2iBgType,
                bg_val:      t2iBgVal,
                size:        t2iSize
            };

            $btnSaveTasks.html('<span class="tba-spinner"></span> Saving tasks...').prop('disabled', true);

            $.post(aapData.ajaxUrl, {
                action:    'tba_save_planner_tasks',
                nonce:     aapData.nonce,
                niche:     niche,
                language:  lang,
                mode:      mode,
                tasks:     tasks,
                tag_count: parseInt($('#tba-planner-tag-count').val() || '0', 10),
                meta:      metaOpts,
            }, function (res) {
                $btnSaveTasks.html('💾 Save Selected as Background Tasks').prop('disabled', false);
                if (res.success) {
                    alert(res.data.message);
                    
                    // Remove successfully enqueued rows from the planner results table instead of redirecting!
                    $plannerBody.find('tr').each(function () {
                        const $row = $(this);
                        if ($row.find('.tba-planner-checkbox').is(':checked')) {
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

    const $btnRunQueue     = $('#tba-btn-run-queue');
    const $queueConsole    = $('#tba-queue-console');
    const $consoleStatus   = $('#tba-queue-console-status');
    const $consoleLogs     = $('#tba-queue-console-logs');

    let queueRunning = false;

    if ($btnRunQueue.length) {
        $btnRunQueue.on('click', function () {
            if (queueRunning) {
                // Pause runner
                queueRunning = false;
                $btnRunQueue.html('🚀 Run Queue Now').removeClass('tba-btn-danger').addClass('tba-btn-secondary');
                $consoleStatus.text('Paused').css('color', '#fbbf24');
                appendConsoleLog('Queue processor paused by user.');
            } else {
                // Start runner
                queueRunning = true;
                $btnRunQueue.html('🛑 Pause Queue Runner').removeClass('tba-btn-secondary').addClass('tba-btn-danger');
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
        const $nextQueuedRow = $('#tba-queue-table tbody tr.tba-queue-row-queued').first();
        if ($nextQueuedRow.length) {
            $nextQueuedRow.removeClass('tba-queue-row-queued').addClass('tba-queue-row-processing');
            $nextQueuedRow.find('.tba-status-badge')
                .removeClass('tba-status-queued')
                .addClass('tba-status-processing')
                .html('⚙️ Processing');
        }

        $.post(aapData.ajaxUrl, {
            action: 'tba_process_queue_item',
            nonce:  aapData.nonce,
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
                    const $row = $('#tba-queue-row-' + qId);
                    if ($row.length) {
                        $row.removeClass('tba-queue-row-queued tba-queue-row-processing')
                            .addClass('tba-queue-row-published');
                        $row.find('.tba-status-badge')
                            .removeClass('tba-status-queued tba-status-processing tba-status-failed')
                            .addClass('tba-status-published')
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
                    const $row = $('#tba-queue-row-' + qId);
                    if ($row.length) {
                        $row.removeClass('tba-queue-row-queued tba-queue-row-processing')
                            .addClass('tba-queue-row-failed');
                        $row.find('.tba-status-badge')
                            .removeClass('tba-status-queued tba-status-processing tba-status-published')
                            .addClass('tba-status-failed')
                            .html('❌ Failed');
                    }
                } else {
                    // Fallback: reset the temporary processing row back to queued or failed
                    const $procRow = $('#tba-queue-table tbody tr.tba-queue-row-processing').first();
                    if ($procRow.length) {
                        $procRow.removeClass('tba-queue-row-processing').addClass('tba-queue-row-failed');
                        $procRow.find('.tba-status-badge')
                            .removeClass('tba-status-processing')
                            .addClass('tba-status-failed')
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
            const $procRow = $('#tba-queue-table tbody tr.tba-queue-row-processing').first();
            if ($procRow.length) {
                $procRow.removeClass('tba-queue-row-processing').addClass('tba-queue-row-failed');
                $procRow.find('.tba-status-badge')
                    .removeClass('tba-status-processing')
                    .addClass('tba-status-failed')
                    .html('❌ Failed');
            }
            appendConsoleLog('❌ Connection failed. Retrying in 10 seconds...');
            setTimeout(processNextQueueItem, 10000);
        });
    }

    // =========================================================================
    // Thumbnail Manager Ajax Handler
    // =========================================================================
    $(document).on('click', '.tba-btn-gen-thumb-ai, .tba-btn-gen-thumb-t2i', function () {
        const $btn = $(this);
        const postId = $btn.data('post-id');
        const engine = $btn.hasClass('tba-btn-gen-thumb-ai') ? 'ai' : 'text_to_image';
        const originalText = $btn.html();

        $btn.html('<span class="tba-spinner"></span> Generating...').prop('disabled', true);

        $.post(aapData.ajaxUrl, {
            action:  'tba_generate_pending_thumbnail',
            nonce:   aapData.nonce,
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

    $('#tba-thumb-select-all').on('change', function () {
        const checked = $(this).is(':checked');
        $('.tba-thumb-checkbox').prop('checked', checked);
        updateSelectedThumbBtn();
    });

    $(document).on('change', '.tba-thumb-checkbox', function () {
        updateSelectedThumbBtn();
        const total = $('.tba-thumb-checkbox').length;
        const checked = $('.tba-thumb-checkbox:checked').length;
        $('#tba-thumb-select-all').prop('checked', total > 0 && checked === total);
    });

    function updateSelectedThumbBtn() {
        const count = $('.tba-thumb-checkbox:checked').length;
        const $btn = $('#tba-btn-generate-selected-thumbs');
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

        const engine = $('#tba-bulk-thumb-engine').val() || 'ai';
        const total = postIds.length;
        let current = 0;
        let successCount = 0;
        let failCount = 0;

        // Show progress, disable buttons
        $('#tba-bulk-thumb-progress').slideDown(200);
        $('#tba-btn-generate-all-thumbs, #tba-btn-generate-selected-thumbs').prop('disabled', true);
        $('#tba-bulk-thumb-status').text('Processing...');
        $('#tba-bulk-thumb-count').text('0 / ' + total);
        $('#tba-bulk-thumb-bar').css('width', '0%');

        function processNext() {
            if (current >= total) {
                $('#tba-bulk-thumb-status').html('✅ Complete! ' + successCount + ' succeeded, ' + failCount + ' failed.');
                $('#tba-btn-generate-all-thumbs, #tba-btn-generate-selected-thumbs').prop('disabled', false);
                if (successCount > 0) {
                    setTimeout(() => location.reload(), 2000);
                }
                return;
            }

            const postId = postIds[current];
            const $row = $('#tba-thumb-row-' + postId);

            $row.css({ background: 'rgba(99,102,241,0.08)' });
            $('#tba-bulk-thumb-status').text('Generating thumbnail for Post #' + postId + '...');

            $.post(aapData.ajaxUrl, {
                action:  'tba_generate_pending_thumbnail',
                nonce:   aapData.nonce,
                post_id: postId,
                engine:  engine
            }).done(function (res) {
                if (res.success) {
                    successCount++;
                    $row.css({ background: 'rgba(16,185,129,0.08)' });
                    $row.find('.tba-status-badge').removeClass('tba-status-exhausted').html('✅ Generated');
                    $row.find('.tba-thumb-checkbox').prop('checked', false).prop('disabled', true);
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
                $('#tba-bulk-thumb-bar').css('width', pct + '%');
                $('#tba-bulk-thumb-count').text(current + ' / ' + total);
                processNext();
            });
        }

        processNext();
    }

    // Generate All button
    $('#tba-btn-generate-all-thumbs').on('click', function () {
        const postIds = [];
        $('.tba-thumb-checkbox').each(function () {
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
    $('#tba-btn-generate-selected-thumbs').on('click', function () {
        const postIds = [];
        $('.tba-thumb-checkbox:checked').each(function () {
            postIds.push($(this).data('post-id'));
        });
        if (!postIds.length) {
            alert('Please select at least one post.');
            return;
        }
        runBulkThumbGeneration(postIds);
    });

    // Bulk Tags Action
    $(document).on('click', '#tba-btn-apply-tag-qty-all', function () {
        const val = $('#tba-bulk-tag-qty').val();
        $('.tba-tag-qty-select').val(val);
    });

    $(document).on('click', '.tba-btn-gen-tags', function () {
        const $btn = $(this);
        const postId = $btn.data('post-id');
        const $row = $btn.closest('tr');
        const tagQty = $row.find('.tba-tag-qty-select').val() || '5';
        const originalText = $btn.html();

        $btn.html('<span class="tba-spinner"></span> Generating...').prop('disabled', true);

        $.post(aapData.ajaxUrl, {
            action:    'tba_generate_tags',
            nonce:     aapData.nonce,
            post_id:   postId,
            tag_count: parseInt(tagQty, 10)
        }, function (res) {
            if (res.success) {
                $btn.html('✅ Done!').css('background', '#10b981');
                
                // Update tag badges dynamically on the screen!
                const $container = $row.find('.tba-tags-container');
                $container.fadeOut(300, function() {
                    $container.empty();
                    if (res.data.tags && res.data.tags.length > 0) {
                        res.data.tags.forEach(function(tag) {
                            $container.append(`
                                <span class="tba-status-badge" style="background:#f1f5f9; border:1px solid #e2e8f0; color:#475569; font-size:10px; font-weight:600; padding:2px 6px; border-radius:4px; margin-right:5px; margin-bottom:5px;">
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
        const $log = $('#tba-trans-log');
        const color = type === 'error' ? '#ef4444' : (type === 'success' ? '#10b981' : '#cbd5e1');
        $log.append(`<div style="color: ${color}">[${new Date().toLocaleTimeString()}] ${msg}</div>`);
        $log.scrollTop($log[0].scrollHeight);
    }

    // Header checkbox sync
    $('#tba-translator-select-all, #tba-translator-table-header-select').on('change', function () {
        const checked = $(this).prop('checked');
        $('#tba-translator-select-all, #tba-translator-table-header-select').prop('checked', checked);
        $('.tba-trans-post-checkbox:not(:disabled)').prop('checked', checked);
    });

    $('#tba-btn-translate-selected').on('click', function () {
        const selectedIds = [];
        $('.tba-trans-post-checkbox:checked').each(function () {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            alert('Please select at least one post to translate.');
            return;
        }

        const targetLang = $('#tba-translator-target-lang').val();
        const postStatus = $('#tba-translator-status').val();
        const $btn = $(this);
        const originalText = $btn.html();

        $btn.html('🌐 Translating...').prop('disabled', true);
        $('#tba-translator-progress-container').slideDown();
        $('#tba-trans-log').html('<div>[Batch translation started]</div>');

        let currentIndex = 0;
        const total = selectedIds.length;

        function updateProgress() {
            const pct = Math.round((currentIndex / total) * 100);
            $('#tba-trans-progress-bar').css('width', pct + '%');
            $('#tba-trans-progress-percent').html(pct + '%');
            $('#tba-trans-progress-text').html(`Translating article ${currentIndex + 1} of ${total}...`);
        }

        function processNext() {
            if (currentIndex >= total) {
                logTrans('🎉 Batch translation completed successfully!', 'success');
                $('#tba-trans-progress-text').html('Translation batch completed!');
                $btn.html('✅ Completed').css('background', '#10b981');
                setTimeout(function () {
                    $btn.html(originalText).css('background', '').prop('disabled', false);
                }, 3000);
                return;
            }

            updateProgress();
            const postId = selectedIds[currentIndex];
            logTrans(`Starting translation for Post #${postId} into ${targetLang}...`, 'info');

            $.post(aapData.ajaxUrl, {
                action:      'tba_translate_post',
                nonce:       aapData.nonce,
                post_id:     postId,
                target_lang: targetLang,
                status:      postStatus
            }, function (res) {
                if (res.success) {
                    logTrans(`✅ Success: Post #${postId} translated! New Post: "${res.data.translated_title}" (ID: #${res.data.translated_id})`, 'success');
                    // Add badge dynamically to the row
                    const $row = $(`#tba-trans-row-${postId}`);
                    $row.find('.tba-trans-post-checkbox').prop('checked', false).prop('disabled', true);
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

    $(document).on('click', '.tba-btn-request-indexing', function () {
        const $btn    = $(this);
        const postId  = $btn.data('post-id');
        const $row    = $('#tba-gsc-row-' + postId);
        const origTxt = $btn.html();

        $btn.prop('disabled', true).html('<span class="tba-spinner-small"></span> Submitting...');

        $.post(ajaxurl, {
            action: 'tba_request_indexing',
            _ajax_nonce: tba_admin.nonce,
            post_id: postId
        }).done(function (res) {
            if (res.success) {
                $btn.html('✅ Submitted!').css({ background: '#166534' });
                const now = new Date();
                const dateStr = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ', ' + now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
                $row.find('.tba-gsc-ping-cell').html(
                    '<span class="tba-status-badge" style="background:#dcfce7; color:#166534; font-size:10px;">✅ ' + dateStr + '</span>'
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

    $(document).on('click', '.tba-btn-rewrite-post', function () {
        const $btn    = $(this);
        const postId  = $btn.data('post-id');
        const save    = $btn.data('save') || 'preview';
        const $row    = $('#tba-rewriter-row-' + postId);
        const $instrField = $('#tba-rewrite-instructions-' + postId);
        const instructions = $instrField ? $instrField.val() : '';
        const origTxt = $btn.html();

        $btn.prop('disabled', true).html('<span class="tba-spinner-small"></span> Rewriting...');

        $.post(aapData.ajaxUrl, {
            action: 'tba_rewrite_post',
            _ajax_nonce: aapData.nonce,
            post_id: postId,
            instructions: instructions,
            save: save
        }).done(function (res) {
            if (res.success) {
                if (save === 'save') {
                    $btn.html('✅ Saved!').css({ background: '#166534' });
                    setTimeout(() => { $btn.html(origTxt).css({ background: '' }).prop('disabled', false); }, 3000);
                    const $preview = $('#tba-rewrite-preview-' + postId);
                    if ($preview.length) $preview.slideUp(200);
                } else {
                    const $previewContainer = $('#tba-rewrite-preview-' + postId);
                    if ($previewContainer.length) {
                        $previewContainer.html(
                            '<div class="tba-rewrite-preview-content">' +
                            '<div class="tba-panel-header"><h3 class="tba-panel-title" style="font-size:13px;">📝 Rewrite Preview</h3>' +
                            '<button type="button" class="tba-btn tba-btn-primary tba-btn-small tba-btn-rewrite-post" data-post-id="' + postId + '" data-save="save" style="background:#059669; border-color:#059669;">💾 Save to Post</button></div>' +
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

    $(document).on('click', '.tba-gsc-tab-btn', function() {
        const $btn = $(this);
        const tab  = $btn.data('tab');
        
        $('.tba-gsc-tab-btn').removeClass('active').css({ background: 'transparent', color: '#94a3b8' });
        $btn.addClass('active').css({ background: 'rgba(255,255,255,0.1)', color: '#fff' });
        
        $('.tba-gsc-tab-content').hide();
        $('#tba-gsc-tab-' + tab).show();
    });

    $(document).on('click', '#tba-gsc-drag-drop-zone', function() {
        $('#tba-gsc-file-input').trigger('click');
    });

    $(document).on('dragover', '#tba-gsc-drag-drop-zone', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).css('border-color', 'var(--tba-primary)');
    });

    $(document).on('dragleave', '#tba-gsc-drag-drop-zone', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).css('border-color', 'rgba(255,255,255,0.1)');
    });

    $(document).on('drop', '#tba-gsc-drag-drop-zone', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).css('border-color', 'rgba(255,255,255,0.1)');
        
        const files = e.originalEvent.dataTransfer.files;
        if (files && files.length) {
            const file = files[0];
            handleGscJsonFile(file);
        }
    });

    $(document).on('change', '#tba-gsc-file-input', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        handleGscJsonFile(file);
    });

    function handleGscJsonFile(file) {
        if (!file.name.endsWith('.json')) {
            $('#tba-gsc-file-status').text('❌ Only .json files are allowed.').css({ color: '#f87171' });
            return;
        }
        const reader = new FileReader();
        reader.onload = function(evt) {
            try {
                const json = JSON.parse(evt.target.result);
                $('#tba_gsc_json_textarea').val(JSON.stringify(json, null, 2));
                $('#tba-gsc-file-status').text('✅ JSON file loaded successfully! Click Save to apply.').css({ color: '#34d399' });
            } catch (err) {
                $('#tba-gsc-file-status').text('❌ Invalid JSON file. Please try again.').css({ color: '#f87171' });
            }
        };
        reader.readAsText(file);
    }

});

