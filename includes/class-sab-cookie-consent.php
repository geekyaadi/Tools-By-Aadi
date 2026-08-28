<?php
/**
 * Cookie Consent Controller Engine for Soniji Auto Blogging
 * Supports Optional Reject Button & Custom Text
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class SAB_Cookie_Consent {

    public static function init() {
        if ( get_option( 'sab_cookie_enable', '1' ) === '1' ) {
            add_action( 'wp_footer', [ __CLASS__, 'render_cookie_consent' ] );
            add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_cookie_consent_scripts' ] );
        }
    }

    public static function enqueue_cookie_consent_scripts() {
        if ( is_admin() ) return;
        wp_enqueue_script( 'sab-cookie-consent', SAB_PLUGIN_URL . 'admin/js/cookie-consent.js', [], SAB_VERSION, true );
    }

    public static function render_cookie_consent() {
        if ( is_admin() ) return;

        $style         = get_option( 'sab_cookie_style', 'bottom_banner' );
        $text          = get_option( 'sab_cookie_text', 'We use cookies to personalize content, ads, and analyze traffic for maximum user experience.' );
        $btn_text      = get_option( 'sab_cookie_btn_text', 'Accept All Cookies 🍪' );
        $show_reject   = get_option( 'sab_cookie_enable_reject', '0' ) === '1';
        $reject_text   = get_option( 'sab_cookie_reject_btn_text', 'Decline Non-Essential' );
        $privacy_url   = get_option( 'sab_cookie_privacy_url', home_url('/privacy-policy/') );

        // CSS Styles by Choice
        $css = '';
        if ( $style === 'bottom_banner' ) {
            $css = 'position:fixed; bottom:0; left:0; right:0; background:#0f172a; color:#f8fafc; padding:16px 24px; box-shadow:0 -5px 25px rgba(0,0,0,0.15); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; z-index:999999; font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif; border-top:2px solid #3b82f6;';
        } elseif ( $style === 'floating_card' ) {
            $css = 'position:fixed; bottom:20px; left:20px; max-width:380px; background:#ffffff; color:#1e293b; padding:20px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.2); border:1px solid #e2e8f0; z-index:999999; font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;';
        } elseif ( $style === 'glassmorphism' ) {
            $css = 'position:fixed; bottom:20px; left:50%; transform:translateX(-50%); width:90%; max-width:700px; background:rgba(15, 23, 42, 0.85); backdrop-filter:blur(10px); color:#ffffff; padding:16px 24px; border-radius:16px; border:1px solid rgba(255,255,255,0.1); box-shadow:0 10px 30px rgba(0,0,0,0.3); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px; z-index:999999; font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;';
        } else { // dark_pill
            $css = 'position:fixed; bottom:20px; right:20px; max-width:420px; background:#18181b; color:#f4f4f5; padding:16px 22px; border-radius:50px; box-shadow:0 10px 30px rgba(0,0,0,0.3); border:1px solid #27272a; display:flex; justify-content:space-between; align-items:center; gap:15px; z-index:999999; font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;';
        }

        ?>
        <div id="sab-cookie-banner" style="<?php echo esc_attr($css); ?>">
            <div style="font-size:13px; line-height:1.5; flex:1;">
                <?php echo esc_html($text); ?>
                <?php if ( ! empty($privacy_url) ): ?>
                    <a href="<?php echo esc_url($privacy_url); ?>" target="_blank" style="color:#60a5fa; text-decoration:underline; margin-left:5px;">Privacy Policy</a>
                <?php endif; ?>
            </div>
            <div style="display:flex; gap:8px; align-items:center;">
                <?php if ( $show_reject ): ?>
                    <button onclick="sabDismissCookies()" style="background:transparent; color:#cbd5e1; border:1px solid #475569; padding:8px 14px; font-size:12px; font-weight:500; border-radius:6px; cursor:pointer; white-space:nowrap;">
                         <?php echo esc_html($reject_text); ?>
                    </button>
                <?php endif; ?>
                <button onclick="sabAcceptCookies()" style="background:#2563eb; color:#ffffff; border:none; padding:9px 16px; font-size:13px; font-weight:600; border-radius:6px; cursor:pointer; white-space:nowrap; transition:background 0.2s;">
                    <?php echo esc_html($btn_text); ?>
                </button>
            </div>
        </div>
        <?php
    }
}
