function sabAcceptCookies() {
    document.cookie = "sab_cookie_consent=accepted; max-age=" + (365*24*60*60) + "; path=/";
    var banner = document.getElementById("sab-cookie-banner");
    if (banner) banner.style.display = "none";
}
function sabDismissCookies() {
    document.cookie = "sab_cookie_consent=declined; max-age=" + (30*24*60*60) + "; path=/";
    var banner = document.getElementById("sab-cookie-banner");
    if (banner) banner.style.display = "none";
}
(function() {
    document.addEventListener("DOMContentLoaded", function() {
        if (document.cookie.indexOf("sab_cookie_consent=") !== -1) {
            var banner = document.getElementById("sab-cookie-banner");
            if (banner) banner.style.display = "none";
        }
    });
})();
