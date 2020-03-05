function loadData() {
    $.ajax({
        method: "GET",
        url: "/stundenplan/ajax/" + $('#courseSelector').val(),
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

        if (new Date(event['custom']['begin']['date']).getDate() != lastDay && new Date(event['custom']['begin']['date']).toDateString() != new Date().toDateString() && !first) {
            html += "<hr>";
            lastDay = new Date(event['custom']['begin']['date']).getDate();
        }
        if (!printedToday && event['custom']['today']) {
            html += "<h3 style='text-align: center'>Heute</h3>";
            printedToday = true;
        }
        if (!printedTomorrow && event['custom']['tomorrow']) {
            html += "<h3 style='text-align: center'>Morgen</h3>";
            printedTomorrow = true;
        }
        if (!printedLater && !event['custom']['tomorrow'] && !event['custom']['today']) {
            html += "<h3 style='text-align: center'>Später</h3>";
            printedLater = true;
        }
        html += "<blockquote class='" + ((event['custom']['isSeminar']) ? "seminar" : "") + ((event['custom']['isKlausur']) ? "klausur" : "") + "'><div class=\"row row-top\">" +
            "<span class=\"column column-20\">" + event['SUMMARY'] + "</span><span\n" +
            "                    class=\"column-offset-50 column-33 column\" style='text-align: right'>" + event['LOCATION'] + "</span></div>" +
            "<div class='row'><small class='column'>" + event['DESCRIPTION'] + "</small></div><br>";

        if (event['custom']['note']) {
            html += "<p class='message'>" + event['custom']['note'] + "</p>";
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
                "                                <div class=\"progress-value\" style=\"width: " + percent + "%\">" + percent.toFixed(0) + "%</div>\n" +
                "                            </div>\n" +
                "                        </div>\n";
        }

        out += html + "</blockquote>";
        lastDate = new Date(event['custom']['begin']['date']);
        first = false;
    }
    $('#list').html(out);
}

$('#courseSelector').on('change', function () {
    setCookie('selectedCourse', $('#courseSelector').val(), 30);
    $.ajax({
        method: "GET",
        url: "/stundenplan/ajax/" + $('#courseSelector').val(),
        dataType: "json"
    })
        .done(function (msg) {
            setData(msg);
        });
});

loadData();
