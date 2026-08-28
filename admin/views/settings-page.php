<?php if ( ! defined( 'ABSPATH' ) ) exit;
$keys          = SAB_Key_Manager::get_all_keys();
$reset_minutes = (int) get_option( 'sab_key_reset_minutes', 60 );
$msg_map       = [
    'key_added'      => ['type'=>'success','text'=>'✅ API key added successfully.'],
    'key_exists'     => ['type'=>'warning','text'=>'⚠️ This API key already exists.'],
    'key_empty'      => ['type'=>'error',  'text'=>'❌ API key cannot be empty.'],
    'key_deleted'    => ['type'=>'success','text'=>'✅ API key deleted.'],
    'key_reset'      => ['type'=>'success','text'=>'✅ API key reset to active.'],
    'saved'          => ['type'=>'success','text'=>'✅ Settings saved.'],
    'settings_reset' => ['type'=>'success','text'=>'✅ All plugin settings have been reset to factory defaults.'],
    'data_cleared'   => ['type'=>'success','text'=>'✅ Plugin cache, temporary transients, queue, and history logs have been cleared.'],
];
$msg = isset( $_GET['msg'] ) ? sanitize_key( $_GET['msg'] ) : ( isset($_GET['saved']) ? 'saved' : '' );

// Default Prompts for Prefilling
$default_prompt_titles  = "Generate exactly {count} unique, highly engaging, CTR-optimized SEO blog post title ideas for the niche/topic: \"{niche}\". Write in the language: \"{language}\". Return ONLY a numbered list (1. Title, 2. Title, etc.).";
$default_prompt_article = "Write a comprehensive, 100% unique, human-like, SEO-optimized blog post titled: \"{title}\". Write in language: \"{language}\". Target length: {word_count} words. Tone: {tone}. {focus_clause}. MODERN SEO: Use clean HTML hierarchy (h2, h3, h4), include at least ONE HTML Data Table (<table>), lists (ul, ol), bold key terms (strong). NO AI intros (\"In today's digital world...\") or outros (\"In conclusion...\"). NO robotic buzzwords (delve, tapestry, testament, crucial, furthermore).";
$default_prompt_meta    = "Write an SEO-optimized meta description (max 160 characters) for a blog post titled: \"{title}\". Language: {language}.";
$default_prompt_tags    = "Generate exactly {tag_count} relevant, specific SEO tags for a blog post titled: \"{title}\". Language: {language}. Return as a JSON array.";
$default_prompt_faq     = "Generate exactly {faq_count} relevant Frequently Asked Questions with detailed answers for a blog post titled: \"{title}\". Language: {language}. Return as a JSON array of objects with \"question\" and \"answer\" keys.";

$val_prompt_titles  = get_option( 'sab_prompt_titles', '' );
if ( empty( $val_prompt_titles ) ) $val_prompt_titles = $default_prompt_titles;

$val_prompt_article = get_option( 'sab_prompt_article', '' );
if ( empty( $val_prompt_article ) ) $val_prompt_article = $default_prompt_article;

$val_prompt_meta    = get_option( 'sab_prompt_meta', '' );
if ( empty( $val_prompt_meta ) ) $val_prompt_meta = $default_prompt_meta;

$val_prompt_tags    = get_option( 'sab_prompt_tags', '' );
if ( empty( $val_prompt_tags ) ) $val_prompt_tags = $default_prompt_tags;

$val_prompt_faq     = get_option( 'sab_prompt_faq', '' );
if ( empty( $val_prompt_faq ) ) $val_prompt_faq = $default_prompt_faq;
?>
<div class="sab-wrap">
    <div class="sab-header">
        <div class="sab-header-inner">
            <div class="sab-logo">
                <img src="<?php echo esc_url( SAB_PLUGIN_URL . 'admin/soniji-auto-blogging-by-aadi.png' ); ?>" alt="Logo" style="height:32px; width:auto; vertical-align:middle; margin-right:10px; border-radius:4px;">
                <span class="sab-logo-badge">Settings (v<?php echo esc_html( SAB_VERSION ); ?>)</span>
            </div>
                        <div class="sab-header-nav">
    <a href="<?php echo esc_url( admin_url('admin.php?page=soniji-auto-blogging') ); ?>" class="sab-nav-link">Dashboard</a>
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
    <a href="<?php echo esc_url( admin_url('admin.php?page=sab-settings') ); ?>" class="sab-nav-link active">Settings</a>
</div>
        </div>
    </div>
    <div class="sab-content">

        <?php if ( $msg && isset($msg_map[$msg]) ): ?>
        <div class="sab-alert sab-alert-<?php echo esc_attr( $msg_map[$msg]['type'] ); ?>">
            <?php echo esc_html($msg_map[$msg]['text']); ?>
        </div>
        <?php endif; ?>

        <?php
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $retrofitted_count = isset( $_GET['retrofitted'] ) ? (int) $_GET['retrofitted'] : 0;
        if ( $retrofitted_count > 0 ): ?>
        <div class="sab-alert sab-alert-success" style="margin-bottom:20px;">
            🎉 <strong>Retrofit Processing Complete!</strong> Successfully updated <?php echo (int) $retrofitted_count; ?> old/existing posts with Auto-Internal Links, High-DA Outbound Links, and Table of Contents (TOC).
        </div>
        <?php endif; ?>

        <!-- ============================================================ -->
        <!-- API KEYS -->
        <!-- ============================================================ -->
        <div class="sab-panel">
            <div class="sab-panel-header">
                <h2 class="sab-panel-title">🔑 API Key Pool</h2>
                <div class="sab-key-badge">
                    <?php $stats = SAB_Key_Manager::get_stats(); ?>
                    <span class="sab-key-dot <?php echo $stats['active']>0?'active':'inactive'; ?>"></span>
                    <?php echo (int) $stats['active']; ?> Active / <?php echo (int) $stats['total']; ?> Total
                </div>
            </div>

            <!-- Add key -->
            <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" class="sab-add-key-form">
                <?php wp_nonce_field('sab_add_key'); ?>
                <input type="hidden" name="action" value="sab_add_key">
                <div class="sab-add-key-inputs">
                    <div class="sab-field-provider">
                        <select name="api_key_provider" class="sab-select" id="sab-key-provider-select">
                            <option value="gemini">Google Gemini</option>
                            <option value="openai">OpenAI ChatGPT</option>
                        </select>
                    </div>
                    <div class="sab-field-key-input">
                        <input type="text" name="api_key" class="sab-input" id="sab-key-input-field" placeholder="Paste Gemini API key here (AIza...)" autocomplete="off">
                    </div>
                    <button type="submit" class="sab-btn sab-btn-primary">➕ Add Key</button>
                </div>
                <div class="sab-hint" id="sab-add-key-hint">Get your free Gemini API key from <a href="https://aistudio.google.com/app/apikey" target="_blank">Google AI Studio →</a></div>
            </form>

            <!-- Key table -->
            <?php if ( empty($keys) ): ?>
            <div class="sab-empty-state">No API keys added yet.</div>
            <?php else: ?>
            <div class="sab-key-table-toolbar">
                <span class="sab-hint"><?php echo count($keys); ?> key(s) configured</span>
                <button type="button" id="sab-btn-ping-all" class="sab-btn sab-btn-secondary sab-btn-sm">
                    🏓 Ping All Keys
                </button>
            </div>
            <table class="sab-table" id="sab-keys-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Provider</th>
                        <th>API Key</th>
                        <th>Status</th>
                        <th>Resets In</th>
                        <th>Requests</th>
                        <th>Tokens Used</th>
                        <th>Last Ping</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $keys as $i => $k ):
                        $reset_secs = SAB_Key_Manager::seconds_until_reset( $k );
                        $status_cls = $k['status'] === 'active' ? 'active' :
                                     ( $k['status'] === 'invalid' ? 'invalid' : 'exhausted' );
                        $provider   = $k['provider'] ?? 'gemini';
                    ?>
                    <tr class="sab-key-row <?php echo $k['status'] !== 'active' ? 'sab-row-exhausted' : ''; ?>"
                        data-key-index="<?php echo (int) $i; ?>"
                        data-reset-ts="<?php echo (int)($k['reset_at_ts'] ?? 0); ?>">
                        <td><?php echo (int) ($i + 1); ?></td>
                        <td>
                            <span class="sab-badge sab-badge-<?php echo esc_attr($provider); ?>">
                                <?php echo $provider === 'openai' ? 'OpenAI' : 'Gemini'; ?>
                            </span>
                        </td>
                        <td><code class="sab-key-masked"><?php echo esc_html(SAB_Key_Manager::mask_key($k['key'])); ?></code></td>
                        <td class="sab-key-status-cell">
                            <span class="sab-status-badge sab-status-<?php echo esc_attr( $status_cls ); ?>">
                                <?php
                                if ( $k['status'] === 'active' )         echo '✅ Active';
                                elseif ( $k['status'] === 'invalid' )    echo '⛔ Invalid';
                                else                                      echo '🔴 Exhausted';
                                ?>
                            </span>
                        </td>
                        <td class="sab-key-countdown-cell">
                            <?php if ( $k['status'] === 'exhausted' && $reset_secs !== null ): ?>
                            <span class="sab-countdown" data-reset-ts="<?php echo (int)$k['reset_at_ts']; ?>">
                                ⏱ <span class="sab-countdown-val"><?php echo esc_html( SAB_Key_Manager::format_seconds($reset_secs) ); ?></span>
                            </span>
                            <?php elseif ( $k['status'] === 'exhausted' ): ?>
                            <span class="sab-text-muted">Unknown</span>
                            <?php else: ?>
                            <span class="sab-text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo number_format((int)($k['requests']??0)); ?></td>
                        <td><?php
                            $t = (int)($k['tokens_used']??0);
                            echo $t > 0 ? '~' . number_format($t) : '—';
                        ?></td>
                        <td class="sab-key-ping-cell">
                            <?php if ( $k['last_ping_at'] ): ?>
                            <span class="sab-ping-badge sab-ping-<?php echo esc_attr($k['last_ping_status']??''); ?>">
                                <?php
                                $ps = $k['last_ping_status'] ?? '';
                                if ($ps === 'active')    echo '✅';
                                elseif ($ps === 'exhausted') echo '🔴';
                                elseif ($ps === 'invalid')   echo '⛔';
                                else echo '❓';
                                ?>
                            </span>
                            <span class="sab-ping-time"><?php echo esc_html(gmdate('H:i', strtotime($k['last_ping_at']))); ?></span>
                            <?php else: ?>
                            <span class="sab-text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="sab-actions">
                            <button type="button"
                                class="sab-btn-small sab-btn-ghost sab-btn-ping"
                                data-key-index="<?php echo (int) $i; ?>"
                                title="Test this key with a minimal API call">
                                🏓
                            </button>
                            <?php if ( in_array($k['status'], ['exhausted','invalid'], true) ): ?>
                            <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" style="display:inline;">
                                <?php wp_nonce_field('sab_reset_key'); ?>
                                <input type="hidden" name="action" value="sab_reset_key">
                                <input type="hidden" name="key_index" value="<?php echo (int) $i; ?>">
                                <button class="sab-btn-small sab-btn-success" type="submit" title="Force reset to active">↺</button>
                            </form>
                            <?php endif; ?>
                            <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" style="display:inline;" onsubmit="return confirm('Delete this API key?')">
                                <?php wp_nonce_field('sab_delete_key'); ?>
                                <input type="hidden" name="action" value="sab_delete_key">
                                <input type="hidden" name="key_index" value="<?php echo (int) $i; ?>">
                                <button class="sab-btn-small sab-btn-danger" type="submit">✕</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <!-- ============================================================ -->
        <!-- SETTINGS FORM (Balanced Double Columns Layout) -->
        <!-- ============================================================ -->
        <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
            <?php wp_nonce_field('sab_save_settings'); ?>
            <input type="hidden" name="action" value="sab_save_settings">

            <div class="sab-settings-layout-grid">
                
                <!-- ── COLUMN 1 (Core Settings, Internal Linking, Filters) ── -->
                <div class="sab-settings-column">
                    
                    <!-- CARD 1: CORE SETTINGS -->
                    <div class="sab-panel">
                        <div class="sab-panel-header">
                            <h2 class="sab-panel-title">⚙️ Core Settings</h2>
                        </div>
                        
                        <div class="sab-settings-grid">
                            <div class="sab-field">
                                <label class="sab-label">Default Post Status</label>
                                <select name="sab_default_status" class="sab-select">
                                    <option value="draft" <?php selected(get_option('sab_default_status','draft'),'draft'); ?>>Draft</option>
                                    <option value="publish" <?php selected(get_option('sab_default_status','draft'),'publish'); ?>>Published</option>
                                </select>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Automatic Background Updates</label>
                                <label class="sab-switch" style="display:inline-block; margin-top:4px;">
                                    <input type="checkbox" name="sab_auto_update_enabled" value="1" <?php checked(get_option('sab_auto_update_enabled','1'),'1'); ?>>
                                    <span class="sab-slider round"></span>
                                </label>
                                <div class="sab-hint">Automatically download &amp; install new releases from GitHub.</div>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Default Author</label>
                                <?php
                                wp_dropdown_users([
                                    'name'             => 'sab_default_author',
                                    'selected'         => get_option('sab_default_author', get_current_user_id()),
                                    'class'            => 'sab-select',
                                    'who'              => 'authors',
                                ]);
                                ?>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Active AI Provider</label>
                                <select name="sab_active_provider" class="sab-select" id="sab-active-provider-select">
                                    <option value="gemini" <?php selected(get_option('sab_active_provider', 'gemini'), 'gemini'); ?>>Google Gemini</option>
                                    <option value="openai" <?php selected(get_option('sab_active_provider', 'gemini'), 'openai'); ?>>OpenAI ChatGPT</option>
                                </select>
                                <div class="sab-hint">Select the active service provider. Make sure keys are added for the active provider.</div>
                            </div>

                            <div class="sab-field" id="sab-field-gemini-model" style="display: <?php echo get_option('sab_active_provider', 'gemini') === 'gemini' ? 'block' : 'none'; ?>;">
                                <label class="sab-label">Active Gemini Text Model</label>
                                <select name="sab_text_model" class="sab-select">
                                    <?php foreach ( SAB_Rate_Limits::MODELS as $id => $m ): if ($m['type'] !== 'text' || ($m['provider'] ?? 'gemini') !== 'gemini') continue; ?>
                                    <option value="<?php echo esc_attr($id); ?>" <?php selected(SAB_Gemini::get_text_model(), $id); ?>>
                                        <?php echo esc_html($m['label']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="sab-hint">Select model. Gemini 3.1 Flash Lite offers the highest daily limit (500 free calls/day).</div>
                            </div>

                            <div class="sab-field" id="sab-field-openai-model" style="display: <?php echo get_option('sab_active_provider', 'gemini') === 'openai' ? 'block' : 'none'; ?>;">
                                <label class="sab-label">Active OpenAI Text Model</label>
                                <select name="sab_openai_model" class="sab-select">
                                    <?php foreach ( SAB_Rate_Limits::MODELS as $id => $m ): if ($m['type'] !== 'text' || ($m['provider'] ?? 'gemini') !== 'openai') continue; ?>
                                    <option value="<?php echo esc_attr($id); ?>" <?php selected(SAB_Gemini::get_text_model(), $id); ?>>
                                        <?php echo esc_html($m['label']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="sab-hint">Select ChatGPT model. GPT-4o Mini is highly recommended.</div>
                            </div>

                            <div class="sab-field" id="sab-field-gemini-image" style="display: <?php echo get_option('sab_active_provider', 'gemini') === 'gemini' ? 'block' : 'none'; ?>;">
                                <label class="sab-label">Active Gemini Image Model</label>
                                <select name="sab_image_model" class="sab-select">
                                    <?php foreach ( SAB_Rate_Limits::MODELS as $id => $m ): if ($m['type'] !== 'image' || ($m['provider'] ?? 'gemini') !== 'gemini') continue; ?>
                                    <option value="<?php echo esc_attr($id); ?>" <?php selected(SAB_Gemini::get_image_model(), $id); ?>>
                                        <?php echo esc_html($m['label']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="sab-hint">Select model for thumbnail/OG generation.</div>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Article Word Count</label>
                                <select name="sab_word_count" class="sab-select">
                                    <?php foreach ([500,750,1000,1500,2000,2500] as $wc): ?>
                                    <option value="<?php echo (int) $wc; ?>" <?php selected(get_option('sab_word_count',1000),$wc); ?>><?php echo (int) $wc; ?> words</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Default Tag Count</label>
                                <select name="sab_tag_count" class="sab-select">
                                    <?php
                                    $saved_tag_count = (int) get_option( 'sab_tag_count', 15 );
                                    foreach ( [5, 10, 15, 20, 25, 30, 40, 50, 75, 100] as $tc ):
                                    ?>
                                    <option value="<?php echo (int) $tc; ?>" <?php selected( $saved_tag_count, $tc ); ?>><?php echo (int) $tc; ?> tags</option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="sab-hint">Default number of SEO tags generated per post.</div>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Content Tone</label>
                                <select name="sab_content_tone" class="sab-select">
                                    <?php foreach (['professional'=>'Professional','casual'=>'Casual','friendly'=>'Friendly','academic'=>'Academic','humorous'=>'Humorous'] as $v=>$l): ?>
                                    <option value="<?php echo esc_attr( $v ); ?>" <?php selected(get_option('sab_content_tone','professional'),$v); ?>><?php echo esc_html( $l ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Key Auto-Reset Interval</label>
                                <div class="sab-input-suffix">
                                    <input type="number" name="sab_key_reset_minutes" class="sab-input" value="<?php echo (int) $reset_minutes; ?>" min="1" max="1440">
                                    <span class="sab-suffix">mins</span>
                                </div>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Human Review Mode</label>
                                <label class="sab-toggle">
                                    <input type="checkbox" name="sab_review_mode" value="1" <?php checked(get_option('sab_review_mode',0),1); ?>>
                                    <span class="sab-toggle-slider"></span>
                                    <span class="sab-toggle-label">Always save as Draft</span>
                                </label>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Table of Contents (TOC) Auto-Generator</label>
                                <label class="sab-toggle">
                                    <input type="checkbox" name="sab_enable_toc" value="1" <?php checked(get_option('sab_enable_toc',1),1); ?>>
                                    <span class="sab-toggle-slider"></span>
                                    <span class="sab-toggle-label">Auto-inject Table of Contents Box</span>
                                </label>
                                <div class="sab-hint">Auto-injects a mobile-friendly TOC before the first H2 heading.</div>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Dynamic FAQ &amp; Schema Generator</label>
                                <label class="sab-toggle">
                                    <input type="checkbox" name="sab_enable_faq" value="1" <?php checked(get_option('sab_enable_faq',1),1); ?>>
                                    <span class="sab-toggle-slider"></span>
                                    <span class="sab-toggle-label">Enable FAQ Section &amp; Schema</span>
                                </label>
                                <div class="sab-hint">Auto-appends FAQ &amp; JSON-LD Schema.</div>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Number of FAQs to Generate</label>
                                <select name="sab_faq_count" class="sab-select">
                                    <?php foreach ([3,4,5] as $fc): ?>
                                    <option value="<?php echo (int) $fc; ?>" <?php selected(get_option('sab_faq_count',3),$fc); ?>><?php echo (int) $fc; ?> FAQs</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.06);">
                            <button type="submit" class="sab-btn sab-btn-primary" style="width: 100%; border-radius: 6px;">💾 Save Core Settings</button>
                        </div>
                    </div>

                    <!-- CARD 2: INTERNAL LINKING & INDEXING -->
                    <div class="sab-panel">
                        <div class="sab-panel-header">
                            <h2 class="sab-panel-title">🔗 Internal Linking &amp; Indexing</h2>
                        </div>
                        <div class="sab-settings-grid">
                            <div class="sab-field">
                                <label class="sab-label">Auto-Internal Linking</label>
                                <label class="sab-toggle">
                                    <input type="checkbox" name="sab_enable_internal_linking" value="1" <?php checked(get_option('sab_enable_internal_linking',0),1); ?>>
                                    <span class="sab-toggle-slider"></span>
                                    <span class="sab-toggle-label">Enable Auto-Internal Linking</span>
                                </label>
                                <div class="sab-hint">Automatically link keyword phrases in new posts to older posts.</div>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Max Links Per Post</label>
                                <input type="number" name="sab_max_internal_links" class="sab-input" value="<?php echo esc_attr(get_option('sab_max_internal_links',3)); ?>" min="1" max="10">
                                <div class="sab-hint">Maximum number of internal links per post (1 to 10).</div>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Internal Link Design Style</label>
                                <select name="sab_internal_link_style" class="sab-select">
                                    <option value="callout_box" <?php selected(get_option('sab_internal_link_style','callout_box'),'callout_box'); ?>>📖 Clean Callout Box (Recommended)</option>
                                    <option value="card" <?php selected(get_option('sab_internal_link_style','callout_box'),'card'); ?>>📌 Modern Gradient Card</option>
                                    <option value="inline" <?php selected(get_option('sab_internal_link_style','callout_box'),'inline'); ?>>🔗 Inline Hyperlink in Text</option>
                                </select>
                                <div class="sab-hint">Choose how internal links are rendered inside generated articles.</div>
                            </div>

                            <!-- 📌 TABLE OF CONTENTS (TOC) ENGINE -->
                            <div class="sab-field sab-field-full" style="border-top: 1px dashed #cbd5e1; padding-top: 15px; margin-top: 10px;">
                                <label class="sab-label">📌 Table of Contents (TOC) Engine</label>
                                <label class="sab-toggle">
                                    <input type="checkbox" name="sab_enable_toc" value="1" <?php checked(get_option('sab_enable_toc', 1), 1); ?>>
                                    <span class="sab-toggle-slider"></span>
                                    <span class="sab-toggle-label">Enable Dynamic Table of Contents</span>
                                </label>
                                <div class="sab-hint">Dynamically renders a Table of Contents box before the first H2 heading across your articles.</div>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">TOC Accordion Default State</label>
                                <select name="sab_toc_default_state" class="sab-select">
                                    <option value="open" <?php selected(get_option('sab_toc_default_state', 'open'), 'open'); ?>>📖 Default Open / Expanded [Hide]</option>
                                    <option value="closed" <?php selected(get_option('sab_toc_default_state', 'open'), 'closed'); ?>>🙈 Default Closed / Collapsed [Show]</option>
                                </select>
                                <div class="sab-hint">Choose whether the Table of Contents box is open or collapsed by default.</div>
                            </div>

                            <!-- 🌐 AUTO OUTBOUND HIGH-DA LINKING ENGINE -->
                            <div class="sab-field sab-field-full" style="border-top: 1px dashed #cbd5e1; padding-top: 15px; margin-top: 10px;">
                                <label class="sab-label">🌐 Auto Outbound High-DA Links Engine</label>
                                <label class="sab-toggle">
                                    <input type="checkbox" name="sab_enable_outbound_linking" value="1" <?php checked(get_option('sab_enable_outbound_linking', 1), 1); ?>>
                                    <span class="sab-toggle-slider"></span>
                                    <span class="sab-toggle-label">Enable High-DA External Outbound Links</span>
                                </label>
                                <div class="sab-hint">Automatically inject contextual links to Wikipedia, MDN, WebMD, Investopedia &amp; Edu/Gov sources for E-E-A-T trust.</div>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Max Outbound Links Per Post</label>
                                <input type="number" name="sab_max_outbound_links" class="sab-input" value="<?php echo esc_attr(get_option('sab_max_outbound_links', 2)); ?>" min="1" max="10">
                                <div class="sab-hint">Maximum external outbound links per post (1 to 10).</div>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Link Open Target</label>
                                <select name="sab_outbound_target" class="sab-select">
                                    <option value="_blank" <?php selected(get_option('sab_outbound_target', '_blank'), '_blank'); ?>>↗️ Open in New Tab (_blank)</option>
                                    <option value="_self" <?php selected(get_option('sab_outbound_target', '_blank'), '_self'); ?>>➡️ Open in Same Tab (_self)</option>
                                </select>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Link Rel Attribute</label>
                                <select name="sab_outbound_rel" class="sab-select">
                                    <option value="nofollow noopener" <?php selected(get_option('sab_outbound_rel', 'nofollow noopener'), 'nofollow noopener'); ?>>🛡️ nofollow noopener (Recommended)</option>
                                    <option value="dofollow noopener" <?php selected(get_option('sab_outbound_rel', 'nofollow noopener'), 'dofollow noopener'); ?>>🔗 dofollow noopener</option>
                                    <option value="sponsored noopener" <?php selected(get_option('sab_outbound_rel', 'nofollow noopener'), 'sponsored noopener'); ?>>💸 sponsored noopener</option>
                                </select>
                            </div>

                            <div class="sab-field sab-field-full">
                                <label class="sab-label">Outbound Domain Blacklist (1 per line)</label>
                                <textarea name="sab_outbound_blacklist" class="sab-textarea" rows="2" placeholder="e.g. competitor.com&#10;spam-domain.com"><?php echo esc_textarea(get_option('sab_outbound_blacklist', '')); ?></textarea>
                                <div class="sab-hint">Domains listed here will never be linked externally.</div>
                            </div>

                            <div class="sab-field sab-field-full">
                                <label class="sab-label">Google Indexing API (GSC)</label>
                                <label class="sab-toggle">
                                    <input type="checkbox" name="sab_enable_gsc_auto_ping" value="1" <?php checked(get_option('sab_enable_gsc_auto_ping',0),1); ?>>
                                    <span class="sab-toggle-slider"></span>
                                    <span class="sab-toggle-label">Auto-ping Google Indexing API</span>
                                </label>
                                <div class="sab-hint">Submits new posts automatically to Google search index on publish.</div>
                            </div>

                            <div class="sab-field sab-field-full">
                                <label class="sab-label">Google Service Account JSON Key</label>
                                <div class="sab-gsc-tabs" style="display: flex; gap: 5px; margin-bottom: 10px; background: #f8fafc; padding: 4px; border-radius: 6px; border: 1px solid #e2e8f0; max-width: 100%;">
                                    <button type="button" class="sab-btn sab-gsc-tab-btn active" data-tab="paste" style="flex: 1; font-size: 11px; padding: 6px; border-radius: 4px; background: #0284c7; border: none; color: #fff; font-weight: 600; cursor: pointer; transition: all 0.2s;">📋 Paste JSON</button>
                                    <button type="button" class="sab-btn sab-gsc-tab-btn" data-tab="upload" style="flex: 1; font-size: 11px; padding: 6px; border-radius: 4px; background: transparent; border: none; color: #475569; font-weight: 600; cursor: pointer; transition: all 0.2s;">📁 Upload file</button>
                                </div>
                                
                                <div class="sab-gsc-tab-content" id="sab-gsc-tab-paste">
                                    <textarea name="sab_gsc_json" id="sab_gsc_json_textarea" class="sab-textarea" rows="4" placeholder="Paste your Google Service Account key file (.json) content here..."><?php echo esc_textarea(get_option('sab_gsc_json','')); ?></textarea>
                                </div>
                                <div class="sab-gsc-tab-content" id="sab-gsc-tab-upload" style="display: none;">
                                    <div class="sab-gsc-upload-area" id="sab-gsc-drag-drop-zone" style="border: 2px dashed #cbd5e1; border-radius: 8px; padding: 20px; text-align: center; background: #f8fafc; cursor: pointer; transition: border-color 0.2s;">
                                        <span style="font-size: 24px; display: block; margin-bottom: 8px;">📄</span>
                                        <span style="font-size: 12px; color: #0f172a; font-weight: 600;">Choose JSON File</span>
                                        <span style="font-size: 11px; color: #64748b; display: block; margin-top: 4px;">Click or drag file here</span>
                                    </div>
                                    <input type="file" id="sab-gsc-file-input" accept=".json" style="opacity: 0; position: absolute; width: 0; height: 0; z-index: -1;">
                                    <div id="sab-gsc-file-status" style="margin-top: 8px; font-size: 11px; font-weight: 600; text-align: center; color: #059669;"></div>
                                </div>
                                <div class="sab-hint">Required for GSC Indexing Tool. Upload or paste the full JSON content from your Google Cloud Console Service Account key.</div>
                            </div>
                        </div>
                        <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.06);">
                            <button type="submit" class="sab-btn sab-btn-primary" style="width: 100%; border-radius: 6px;">💾 Save Indexing Settings</button>
                        </div>
                    </div>

                    <!-- CARD 3: CONTENT FILTERS -->
                    <div class="sab-panel">
                        <div class="sab-panel-header">
                            <h2 class="sab-panel-title">🚫 Content Filters</h2>
                        </div>
                        <div class="sab-field">
                            <label class="sab-label">Blacklist Words / Phrases</label>
                            <textarea name="sab_blacklist_words" class="sab-textarea" rows="3"
                                placeholder="Enter comma-separated words or phrases to exclude from all generated content e.g. casino, gambling, adult content"
                            ><?php echo esc_textarea(get_option('sab_blacklist_words','')); ?></textarea>
                            <div class="sab-hint">These words will be explicitly excluded from articles, tags, titles, and meta descriptions.</div>
                        </div>
                        <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.06);">
                            <button type="submit" class="sab-btn sab-btn-primary" style="width: 100%; border-radius: 6px;">💾 Save Filters</button>
                        </div>
                    </div>

                </div>
                
                <!-- ── COLUMN 2 (Retrofit Old Posts, Thumbnail Generator, Custom Prompts) ── -->
                <div class="sab-settings-column">

                    <!-- CARD 0: RETROFIT OLD POSTS ENGINE -->
                    <div class="sab-panel">
                        <div class="sab-panel-header">
                            <h2 class="sab-panel-title">🔄 Retrofit Old Posts Engine</h2>
                        </div>
                        <p style="color:var(--sab-text-muted); font-size:12px; margin-bottom:15px;">
                            Bulk inject Auto-Internal Links, High-DA Outbound Links, and Table of Contents (TOC) into your <strong>existing / old published posts</strong> in 1-click!
                        </p>
                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                            <?php wp_nonce_field( 'sab_retrofit_nonce' ); ?>
                            <input type="hidden" name="action" value="sab_retrofit_old_posts">

                            <div class="sab-field">
                                <label class="sab-label">Enhancements to Apply</label>
                                <label style="display:block; margin-bottom:6px; font-size:13px;">
                                    <input type="checkbox" name="do_internal" value="1" checked> 🔗 Auto-Internal Links
                                </label>
                                <label style="display:block; margin-bottom:6px; font-size:13px;">
                                    <input type="checkbox" name="do_outbound" value="1" checked> 🌐 High-DA Outbound Links
                                </label>
                                <label style="display:block; margin-bottom:6px; font-size:13px;">
                                    <input type="checkbox" name="do_toc" value="1" checked> 📌 Table of Contents (TOC)
                                </label>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Post Count Limit</label>
                                <select name="post_limit" class="sab-select">
                                    <option value="10">Last 10 Published Posts</option>
                                    <option value="25" selected>Last 25 Published Posts</option>
                                    <option value="50">Last 50 Published Posts</option>
                                    <option value="100">Last 100 Published Posts</option>
                                </select>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Filter Category (Optional)</label>
                                <?php
                                wp_dropdown_categories( [
                                    'show_option_all' => 'All Categories',
                                    'name'            => 'category_id',
                                    'class'           => 'sab-select',
                                    'hide_empty'      => 0,
                                ] );
                                ?>
                            </div>

                            <button type="submit" class="button button-primary" style="width:100%; border-radius:4px; margin-top:5px;" onclick="return confirm('Bulk process selected old posts now?');">🚀 Bulk Enhance Old Posts Now</button>
                        </form>
                    </div>

                    <!-- CARD 1: THUMBNAIL GENERATOR & ENGAGEMENT -->
                    <div class="sab-panel">
                        <div class="sab-panel-header">
                            <h2 class="sab-panel-title">🖼️ Thumbnail Generator &amp; Engagement</h2>
                        </div>
                        <div class="sab-settings-grid">
                            
                            <div class="sab-field">
                                <label class="sab-label">Default Thumbnail Option</label>
                                <select name="sab_thumb_type" id="sab_thumb_type" class="sab-select">
                                    <option value="ai" <?php selected(get_option('sab_thumb_type','ai'), 'ai'); ?>>AI Generated Thumbnail (Gemini)</option>
                                    <option value="text_to_image" <?php selected(get_option('sab_thumb_type','ai'), 'text_to_image'); ?>>Title to Image (Local GD)</option>
                                </select>
                            </div>

                            <div class="sab-field sab-t2i-only" style="display:none;">
                                <label class="sab-label">Background Selection Type</label>
                                <select name="sab_t2i_bg_type" id="sab_t2i_bg_type" class="sab-select">
                                    <option value="gradient" <?php selected(get_option('sab_t2i_bg_type','gradient'), 'gradient'); ?>>Gradient Background</option>
                                    <option value="solid" <?php selected(get_option('sab_t2i_bg_type','gradient'), 'solid'); ?>>Solid Color Background</option>
                                    <option value="image" <?php selected(get_option('sab_t2i_bg_type','gradient'), 'image'); ?>>Default Image Background (admin/default-thumbnail.jpg)</option>
                                    <option value="mix" <?php selected(get_option('sab_t2i_bg_type','gradient'), 'mix'); ?>>🎲 Mix Background (Randomize)</option>
                                </select>
                            </div>

                            <div class="sab-field sab-t2i-only" id="sab-t2i-gradient-field" style="display:none;">
                                <label class="sab-label">Gradient Color Palette</label>
                                <select name="sab_t2i_bg_val_gradient" class="sab-select">
                                    <?php 
                                    $saved_bg = get_option('sab_t2i_bg_val', 'blue_purple');
                                    foreach ( SAB_Text_To_Image::get_gradients() as $key => $g ): 
                                    ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($saved_bg, $key); ?>><?php echo esc_html($g['label']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="sab-field sab-t2i-only" id="sab-t2i-solid-field" style="display:none;">
                                <label class="sab-label">Solid Color Background</label>
                                <select name="sab_t2i_bg_val_solid" class="sab-select">
                                    <?php 
                                    $saved_bg = get_option('sab_t2i_bg_val', 'dark_slate');
                                    foreach ( SAB_Text_To_Image::get_solid_colors() as $key => $s ): 
                                    ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($saved_bg, $key); ?>><?php echo esc_html($s['label']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="sab-field sab-t2i-only" style="display:none;">
                                <label class="sab-label">Image Ratio &amp; Dimension</label>
                                <select name="sab_t2i_size" class="sab-select">
                                    <option value="600x315" <?php selected(get_option('sab_t2i_size','600x315'), '600x315'); ?>>Landscape (2:1) — 600 × 315 px</option>
                                    <option value="1200x630" <?php selected(get_option('sab_t2i_size','600x315'), '1200x630'); ?>>OpenGraph (2:1) — 1200 × 630 px</option>
                                    <option value="500x500" <?php selected(get_option('sab_t2i_size','600x315'), '500x500'); ?>>Square (1:1) — 500 × 500 px</option>
                                    <option value="1000x1000" <?php selected(get_option('sab_t2i_size','600x315'), '1000x1000'); ?>>Square High-Res (1:1) — 1000 × 1000 px</option>
                                </select>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Auto-Comment Generator</label>
                                <label class="sab-toggle">
                                    <input type="checkbox" name="sab_enable_comments" value="1" <?php checked(get_option('sab_enable_comments',0),1); ?>>
                                    <span class="sab-toggle-slider"></span>
                                    <span class="sab-toggle-label">Auto-generate comments for new posts</span>
                                </label>
                                <div class="sab-hint">Generates discussions on newly published articles.</div>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Comments Count</label>
                                <select name="sab_comments_count" class="sab-select">
                                    <?php foreach ([1,2,3,4,5] as $cc): ?>
                                    <option value="<?php echo (int) $cc; ?>" <?php selected(get_option('sab_comments_count',2),$cc); ?>><?php echo (int) $cc; ?> Comments</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Thumbnail Text Overlay</label>
                                <label class="sab-toggle">
                                    <input type="checkbox" name="sab_enable_text_overlay" value="1" <?php checked(get_option('sab_enable_text_overlay',0),1); ?>>
                                    <span class="sab-toggle-slider"></span>
                                    <span class="sab-toggle-label">Overlay title on featured image</span>
                                </label>
                                <div class="sab-hint">Writes the post title directly on the AI-generated thumbnail.</div>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Overlay Position</label>
                                <select name="sab_overlay_position" class="sab-select">
                                    <option value="bottom" <?php selected(get_option('sab_overlay_position','bottom'),'bottom'); ?>>Bottom Banner</option>
                                    <option value="center" <?php selected(get_option('sab_overlay_position','bottom'),'center'); ?>>Centered Box</option>
                                    <option value="top" <?php selected(get_option('sab_overlay_position','bottom'),'top'); ?>>Top Banner</option>
                                </select>
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Text Color (Hex)</label>
                                <input type="text" name="sab_overlay_color" class="sab-input" value="<?php echo esc_attr(get_option('sab_overlay_color','#ffffff')); ?>" placeholder="#ffffff">
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Background Banner Color (Hex)</label>
                                <input type="text" name="sab_overlay_bg_color" class="sab-input" value="<?php echo esc_attr(get_option('sab_overlay_bg_color','#000000')); ?>" placeholder="#000000">
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Overlay Font Size (px)</label>
                                <input type="number" name="sab_overlay_font_size" class="sab-input" value="<?php echo esc_attr(get_option('sab_overlay_font_size',24)); ?>" min="12" max="64">
                            </div>

                            <div class="sab-field">
                                <label class="sab-label">Background Opacity (0–100)</label>
                                <input type="number" name="sab_overlay_bg_opacity" class="sab-input" value="<?php echo esc_attr(get_option('sab_overlay_bg_opacity',60)); ?>" min="0" max="100">
                            </div>
                        </div>
                        <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.06);">
                            <button type="submit" class="sab-btn sab-btn-primary" style="width: 100%; border-radius: 6px;">💾 Save Thumbnail Settings</button>
                        </div>
                    </div>

                    <!-- CARD 2: CUSTOM PROMPT TEMPLATES -->
                    <div class="sab-panel">
                        <div class="sab-panel-header">
                            <h2 class="sab-panel-title">📝 Custom Prompt Templates</h2>
                        </div>
                        <div class="sab-settings-grid">
                            <div class="sab-hint" style="margin-bottom:10px; line-height: 1.4; grid-column: 1 / -1;">
                                Merge tags supported: <code>{title}</code>, <code>{niche}</code>, <code>{keywords}</code>, <code>{language}</code>, <code>{word_count}</code>, <code>{tone}</code>, <code>{tag_count}</code>, <code>{faq_count}</code>.
                            </div>

                            <div class="sab-field sab-field-full">
                                <label class="sab-label">Title Generation Prompt</label>
                                <textarea name="sab_prompt_titles" class="sab-textarea" rows="3"><?php echo esc_textarea($val_prompt_titles); ?></textarea>
                            </div>

                            <div class="sab-field sab-field-full">
                                <label class="sab-label">Article Writing Prompt</label>
                                <textarea name="sab_prompt_article" class="sab-textarea" rows="4"><?php echo esc_textarea($val_prompt_article); ?></textarea>
                            </div>

                            <div class="sab-field sab-field-full">
                                <label class="sab-label">Meta Description Prompt</label>
                                <textarea name="sab_prompt_meta" class="sab-textarea" rows="2"><?php echo esc_textarea($val_prompt_meta); ?></textarea>
                            </div>

                            <div class="sab-field sab-field-full">
                                <label class="sab-label">Tags Prompt</label>
                                <textarea name="sab_prompt_tags" class="sab-textarea" rows="2"><?php echo esc_textarea($val_prompt_tags); ?></textarea>
                            </div>

                            <div class="sab-field sab-field-full">
                                <label class="sab-label">FAQs Prompt</label>
                                <textarea name="sab_prompt_faq" class="sab-textarea" rows="2"><?php echo esc_textarea($val_prompt_faq); ?></textarea>
                            </div>
                        </div>
                        <div style="margin-top: 15px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.06);">
                            <button type="submit" class="sab-btn sab-btn-primary" style="width: 100%; border-radius: 6px;">💾 Save Prompt Templates</button>
                        </div>
                    </div>

                    <!-- CARD 3: DEFAULT THUMBNAIL REFERENCE IMAGE -->
                    <div class="sab-panel" id="thumbnail-ref">
                        <div class="sab-panel-header">
                            <h2 class="sab-panel-title">🖼️ Default Thumbnail Style Reference</h2>
                            <?php $default_ref = SAB_Gemini::get_default_reference_image(); ?>
                            <?php if ( ! empty( $default_ref ) ): ?>
                            <span class="sab-status-badge sab-status-active">✅ Image Set</span>
                            <?php else: ?>
                            <span class="sab-status-badge sab-status-exhausted">No image set</span>
                            <?php endif; ?>
                        </div>

                        <p class="sab-hint" style="margin-bottom:15px;">
                            Upload a reference image here to use as the default style guide for <strong>all</strong> AI-generated thumbnails.
                        </p>

                        <?php if ( ! empty( $default_ref ) ): ?>
                        <!-- Current default reference preview -->
                        <div class="sab-ref-current" id="sab-settings-ref-current" style="margin-bottom:15px;">
                            <div class="sab-ref-current-inner">
                                <img src="data:<?php echo esc_attr($default_ref['mime_type']); ?>;base64,<?php echo esc_attr($default_ref['base64']); ?>"
                                     id="sab-settings-ref-thumb"
                                     alt="Default reference image"
                                     style="max-height:100px;border-radius:8px;border:1px solid var(--sab-border);">
                                <div class="sab-ref-current-actions">
                                    <span class="sab-hint">Current default reference image</span>
                                    <button type="button" id="sab-btn-settings-delete-ref" class="sab-btn sab-btn-secondary sab-btn-small">
                                        🗑 Remove Default Image
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Upload Zone -->
                        <div class="sab-upload-zone sab-upload-zone-settings" id="sab-settings-upload-zone">
                            <div class="sab-upload-idle" id="sab-settings-upload-idle">
                                <span class="sab-upload-icon">📁</span>
                                <span class="sab-upload-text">
                                    Drag & drop image, or
                                    <label for="sab-settings-ref-input" class="sab-upload-link">browse to upload</label>
                                </span>
                                <span class="sab-upload-sub">JPG, PNG, WEBP — max 4MB</span>
                            </div>
                            <div class="sab-upload-preview" id="sab-settings-upload-preview" style="display:none;">
                                <img id="sab-settings-ref-preview" src="" alt="Preview">
                                <div class="sab-upload-preview-info">
                                    <span id="sab-settings-ref-name" class="sab-upload-filename"></span>
                                    <button type="button" id="sab-btn-settings-save-ref" class="sab-btn sab-btn-primary sab-btn-small">
                                        💾 Save
                                    </button>
                                    <button type="button" id="sab-btn-settings-clear-ref" class="sab-btn sab-btn-ghost sab-btn-small">
                                        ✕ Cancel
                                    </button>
                                </div>
                            </div>
                            <input type="file" id="sab-settings-ref-input"
                                   accept="image/jpeg,image/png,image/webp,image/gif"
                                   style="display:none;">
                        </div>

                        <div id="sab-settings-ref-msg" style="margin-top:10px;display:none;"></div>
                    </div>

                </div>

            </div>

            <!-- SAVE SETTINGS FLOATING BAR -->
            <div class="sab-form-actions" style="margin-top:15px; display: flex; justify-content: flex-end;">
                <button type="submit" class="sab-btn sab-btn-primary" style="padding: 10px 24px; font-weight: 600; border-radius: 6px;">💾 Save All Settings</button>
            </div>
        </form>

        <!-- ============================================================ -->
        <!-- SYSTEM MAINTENANCE & DATA CONTROL -->
        <!-- ============================================================ -->
        <div class="sab-panel" style="margin-top:20px; border: 1px solid #f87171; background: #fff5f5;">
            <div class="sab-panel-header" style="border-bottom: 1px solid #fca5a5; padding-bottom: 10px; margin-bottom: 15px;">
                <h2 class="sab-panel-title" style="color: #b91c1c; font-weight: 700;">🛠️ System Maintenance &amp; Data Control</h2>
            </div>
            <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                <div>
                    <div style="font-weight: 700; font-size: 14px; color: #1e293b; margin-bottom: 4px;">Reset Settings or Purge Plugin Cache &amp; Data</div>
                    <div class="sab-hint" style="color: #475569; font-size: 13px;">Reset all options back to plugin defaults or clear temporary transients, queue data, history logs, and system caches.</div>
                </div>
                <div style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                    <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" onsubmit="return confirm('⚠️ Are you sure you want to reset all plugin settings to factory defaults?');" style="margin:0;">
                        <?php wp_nonce_field('sab_reset_settings'); ?>
                        <input type="hidden" name="action" value="sab_reset_settings">
                        <button type="submit" class="sab-btn sab-btn-secondary" style="border: 1px solid #dc2626; color: #dc2626; background: #ffffff; font-weight: 600; padding: 8px 16px;">
                            🔄 Reset Settings
                        </button>
                    </form>

                    <form method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>" onsubmit="return confirm('🧹 Clear all plugin cache, temporary transients, queue, and history logs?');" style="margin:0;">
                        <?php wp_nonce_field('sab_clear_plugin_data'); ?>
                        <input type="hidden" name="action" value="sab_clear_plugin_data">
                        <button type="submit" class="sab-btn sab-btn-danger" style="background: #dc2626; color: #ffffff; font-weight: 600; border: none; padding: 8px 16px;">
                            🧹 Clear Plugin Data &amp; Cache
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div><!-- .sab-content -->
</div>
