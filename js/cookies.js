document.addEventListener("DOMContentLoaded", () => {
    if (!localStorage.getItem("cookieConsent")) {
        document.getElementById("cookie-popup").style.display = "block";
    }
    function saveConsent(type) {
        localStorage.setItem("cookieConsent", type);
        // Enregistre anonymement en base
        fetch("../config/save_consent.php", {
            method: "POST",
            headers: {"Content-Type": "application/x-www-form-urlencoded"},
            body: "type=" + encodeURIComponent(type)
        });
        document.getElementById("cookie-popup").style.display = "none";
    }
    document.getElementById("acceptCookies").onclick = () => saveConsent("accepté");
    document.getElementById("declineCookies").onclick = () => saveConsent("refusé");
});