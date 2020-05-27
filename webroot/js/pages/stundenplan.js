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
    var out = "";
    var lastDay = new Date().getDate();
    var first = true;
    var printedToday = false;
    var printedTomorrow = false;
    var printedLater = false;
    for (var e in msg) {
        var event = msg[e];
        var html = "";


        if (!printedToday && event['custom']['today']) {
            html += "<h3 style='text-align: center'>Heute</h3>";
            printedToday = true;
        }
        if (!printedTomorrow && event['custom']['tomorrow']) {
            html += "<h3 style='text-align: center'>Morgen</h3>";
            printedTomorrow = true;
        }
        if (!event['custom']['tomorrow'] && !event['custom']['today'] && new Date(event['custom']['begin']['date']).getDay() != lastDay) {
            if (!first) html += "<hr>";
            html += "<h3 style='text-align: center'>" + new Date(event['custom']['begin']['date']).toLocaleDateString() + "</h3>";
        }
        if (first) lastDay = new Date(event['custom']['begin']['date']).getDay();
        if (new Date(event['custom']['begin']['date']).getDay() != lastDay && !first) {
            lastDay = new Date(event['custom']['begin']['date']).getDay();
        }
        html += "<blockquote class='" + ((event['custom']['isSeminar']) ? "seminar" : "") + ((event['custom']['isKlausur']) ? "klausur" : "") + ((event['custom']['isOnline']) ? " online" : "") + "'><div class=\"row row-top\">" +
            "<span class=\"column column-20\">" + event['SUMMARY'] + "</span><span\n" +
            "                    class=\"column-offset-50 column-30 column\" style='text-align: right'>" + ((event['custom']['isOnline']) ? "Online<br><s>" + event['LOCATION'] + "</s>" : event['LOCATION']) + "</span></div>" +
            "<div class='row'><small class='column'>" + event['DESCRIPTION'] + "</small></div><br>";

        if (event['custom']['note']) {
            html += "<div class='message'>" + event['custom']['note'] + "</div>";
        }
        if (event['custom']['loggedInNote']) {
            html += "<div class='message'>" + event['custom']['loggedInNote'] + "</div>";
        }
        if (event['custom']['can_edit']) {
            if (event['custom']['note']) {
                html += "<a class='button button-outline' href='/stundenplan/edit/" + event['custom']['can_edit'] + "'>Notiz bearbeiten</a>";
            } else {
                html += "<a class='' href='/stundenplan/edit/" + event['custom']['can_edit'] + "'>📝</a>";
            }
        }
        if (event['custom']['can_delete']) {
            if (event['custom']['can_edit']) {
                html += "<br>";
            }
            html += event['custom']['can_delete'] + "<br><br>";
        }

        html += "<div class='row mobile-margin-down'>\n" +
            "                    <div class='column column-20'>Beginn:</div>\n" +
            "                    <div class='column column-80'>" + event['custom']['begin']['nice'] + "\n" +
            "                        (" + event['custom']['begin']['words'] + ")\n" +
            "                    </div></div><div class='row'> \n" +
            "                    <div class='column column-20'>Ende:</div>\n" +
            "                    <div class='column column-80'>" + event['custom']['end']['nice'] + ((event['custom']['begin']['isPast']) ? " (" + event['custom']['end']['words'] + ")" : "") + "</div></div>\n";
        if (event['custom']['current']) {
            var percent = Number(((Date.now() / 1000) - event['custom']['begin']['timestamp']) / (event['custom']['end']['timestamp'] - event['custom']['begin']['timestamp'])) * 100;
            html += "                        <br><div class='column'>\n" +
                "                            <div class=\"progress\">\n" +
                "                                <div class=\"progress-value\" data-percent='" + Math.round(percent) + "' style=\"width: " + percent + "%\">" + percent.toFixed(0) + "%</div>\n" +
                "                            </div>\n" +
                "                        </div>\n";
        }

        out += html + "</blockquote>";
        first = false;
    }
    $('#list').html(out);
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
