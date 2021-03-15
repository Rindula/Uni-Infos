function loadData() {
    $.ajax({
        method: "GET",
        url: "/stundenplan/api/" + $('#courseSelector').val() + '/0/0/1/' + (($('#onlineOnly')[0].checked) ? 1 : 0),
        dataType: "json"
    })
        .done(function (msg) {
            setData(msg);
            setTimeout(loadData, 5000);
        })
        .fail(function (msg) {
            setTimeout(loadData, 15000);
        })
}

function setData(msg) {
    var out = [];
    var lastDay = new Date().getDate();
    var first = true;
    var printedToday = false;
    var printedTomorrow = false;
    var template = document.getElementById("lessonTemplate");
    for (var e in msg) {
        var clone = template.content.cloneNode(true);
        var event = msg[e];
        var html = null;


        if (!printedToday && event['custom']['today']) {
            html = document.createElement("h3");
            html.innerHTML = "Heute";
            // html = "<h3 style='text-align: center'>Heute</h3>";
            printedToday = true;
        }
        if (!printedTomorrow && event['custom']['tomorrow']) {
            html = document.createElement("h3");
            html.innerHTML = "Morgen";
            // html = "<h3 style='text-align: center'>Morgen</h3>";
            printedTomorrow = true;
        }
        if (!event['custom']['tomorrow'] && !event['custom']['today'] && new Date(event['custom']['begin']['date']).getDay() !== lastDay) {
            html = document.createElement("h3");
            html.innerHTML = new Date(event['custom']['begin']['date']).toLocaleDateString();
            // html = "<h3 style='text-align: center'>" + new Date(event['custom']['begin']['date']).toLocaleDateString() + "</h3>";
        }
        if (first) lastDay = new Date(event['custom']['begin']['date']).getDay();
        if (new Date(event['custom']['begin']['date']).getDay() !== lastDay && !first) {
            lastDay = new Date(event['custom']['begin']['date']).getDay();
            out.push(document.createElement("hr"));
        }

        if (html != null) {
            html.setAttribute("style", "text-align: center;");
            out.push(html);
        }

        var blockquote = clone.querySelector("blockquote");
        if (event['custom']['isKlausur']) {
            blockquote.classList.add("klausur")
        }
        if (event['custom']['isSeminar']) {
            blockquote.classList.add("seminar")
        }
        if (event['custom']['isOnline']) {
            blockquote.classList.add("online")
        }

        clone.querySelector("[data-role=\"title\"]").innerText = event['SUMMARY'];
        clone.querySelector("[data-role=\"description\"]").innerText = event['DESCRIPTION'];
        clone.querySelector("[data-role=\"location\"]").innerText = ((event['custom']['isOnline']) ? "Online" : event['LOCATION']);
        var notes = clone.querySelector("[data-role=\"notes\"]");
        if (event['custom']['note']) {
            notes.innerHTML = event['custom']['note'];
        } else {
            notes.innerHTML = "";
            notes.setAttribute("style", "display: none;");
        }
        var loggedInNotes = clone.querySelector("[data-role=\"loggedinnotes\"]")
        if (event['custom']['loggedInNote']) {
            loggedInNotes.innerHTML = event['custom']['loggedInNote'];
        } else {
            loggedInNotes.innerHTML = "";
            loggedInNotes.setAttribute("style", "display: none;");
        }
        var editLink = clone.querySelector("[data-role=\"editnotes\"]");
        if (event['custom']['can_edit']) {
            editLink.setAttribute("href", "/stundenplan/edit/" + event['custom']['can_edit']);
        } else {
            editLink.setAttribute("style", "display: none;");
        }
        var deleteLink = clone.querySelector("[data-role=\"deletenotes\"]");
        if (event['custom']['can_delete']) {
            var links = deleteLink.getElementsByTagName("a");
            if (!event['custom']['note']) {
                links[0].setAttribute("style", "display: none;");
            }
            if (!event['custom']['loggedInNote']) {
                links[1].setAttribute("style", "display: none;");
            }
            if (!event['custom']['loggedInNote'] || !event['custom']['note']) {
                links[2].setAttribute("style", "display: none;");
            }
            for (const link in links) {
                var link1 = links[link];
                try {
                    var attribute = link1.getAttribute("href");
                    var replace = attribute.replace("__UID__", event['UID']);
                    link1.setAttribute("href", replace);
                } catch (e) {
                }
            }
        } else {
            deleteLink.remove();
        }

        clone.querySelector("[data-role=\"beginntime\"]").innerText = event['custom']['begin']['nice'] + " (" + event['custom']['begin']['words'] + ")";
        clone.querySelector("[data-role=\"endtime\"]").innerText = event['custom']['end']['nice'] + " (" + event['custom']['end']['words'] + ")";

        if (event['custom']['current']) {
            var percent = Number(((Date.now() / 1000) - event['custom']['begin']['timestamp']) / (event['custom']['end']['timestamp'] - event['custom']['begin']['timestamp'])) * 100;
            var progressbar = clone.querySelector("[data-role=\"progressbar\"]");
            progressbar.setAttribute("data-percent", Math.round(percent) + "")
            progressbar.setAttribute("style", "width: " + percent + "%")
            progressbar.innerHTML = percent.toFixed(0) + "%";
        } else {
            clone.querySelector("[data-role=\"progress-wrapper\"]").remove()
        }
        // "<div class=\"progress-value\" data-percent='" + Math.round(percent) + "' style=\"width: " + percent + "%\">" + percent.toFixed(0) + "%</div>\n";
        first = false;
        out.push(clone);
    }
    $('#list').html("");
    for (const outKey of out) {
        $('#list').append(outKey);
    }
}

$('#courseSelector').on('change', function () {
    setCookie('selectedCourse', $('#courseSelector').val(), 365);
    $.ajax({
        method: "GET",
        url: "/stundenplan/api/" + $('#courseSelector').val() + '/0/0/1/' + (($('#onlineOnly')[0].checked) ? 1 : 0),
        dataType: "json"
    })
        .done(function (msg) {
            setData(msg);
        });
});

$('#onlineOnly').on('change', function () {
    setCookie('selectedCourse', $('#courseSelector').val(), 365);
    $.ajax({
        method: "GET",
        url: "/stundenplan/api/" + $('#courseSelector').val() + '/0/0/1/' + (($('#onlineOnly')[0].checked) ? 1 : 0),
        dataType: "json"
    })
        .done(function (msg) {
            setData(msg);
        });
});

loadData();
