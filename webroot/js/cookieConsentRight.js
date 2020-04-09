window.cookieconsent.initialise({
    "palette": {
        "popup": {
            "background": "#252e39"
        },
        "button": {
            "background": "#14a7d0"
        }
    },
    "theme": "classic",
    "position": "bottom",
    "type": "opt-out",
    "revokable": false,
    "content": {
        "message": "Diese Website verwendet Cookies und Google Analytics um die beste Erfahrung auf dieser Website zu bieten.",
        "dismiss": "Verstanden",
        "deny": "Ablehnen",
        "link": "Mehr erfahren"
    }
});

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
