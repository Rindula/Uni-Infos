function loadData() {
    $.ajax({
        method: "GET",
        url: "/stundenplan/ajax",
        dataType: "json"
    })
        .done(function (msg) {
            var out = "";
            var lastDay = new Date().getDay()
            for (var e in msg) {
                var event = msg[e];
                var html = "";
                if (new Date(event['custom']['begin']['date']).getDate() != lastDay) {
                    html += "<hr>";
                    lastDay = new Date(event['custom']['begin']['date']).getDate();
                }
                html += "<blockquote class='" + ((event['custom']['current']) ? " current" : "") + ((event['custom']['today'] && !event['custom']['current']) ? " today" : "") + ((event['custom']['tomorrow']) ? " tomorrow" : "") + ((event['custom']['isKlausur'] && !event['custom']['current']) ? " klausur" : "") + "'><div class=\"row row-top\">" +
                    "<span class=\"column column-20\">" + event['SUMMARY'] + "</span><span\n" +
                    "                    class=\"column-offset-67 column-20 column\">" + event['LOCATION'] + "</span></div>" +
                    "<div class='row'><small class='column'>" + event['DESCRIPTION'] + "</small></div><br>" +
                    "<div class='row'>\n" +
                    "                    <div class='column column-50'>Beginn:</div>\n" +
                    "                    <div class='column column-50'>" + event['custom']['begin']['nice'] + "\n" +
                    "                        (" + event['custom']['begin']['words'] + ")\n" +
                    "                    </div></div><div class='row'> \n" +
                    "                    <div class='column column-50'>Ende:</div>\n" +
                    "                    <div class='column column-50'>" + event['custom']['end']['nice'] + ((event['custom']['begin']['isPast']) ? " (" + event['custom']['end']['words'] + ")" : "") + "</div></div>\n";
                if (event['custom']['current']) {
                    var percent = new Number(((Date.now() / 1000) - event['custom']['begin']['timestamp']) / (event['custom']['end']['timestamp'] - event['custom']['begin']['timestamp'])) * 100;
                    html += "                        <br><div class='column'>\n" +
                        "                            <div class=\"progress\">\n" +
                        "                                <div class=\"progress-value\" style=\"width: " + percent + "%\">"+percent.toFixed(2)+"%</div>\n" +
                        "                            </div>\n" +
                        "                        </div>\n";
                }

                out += html + "</blockquote>";
            }

            //
            //    ""
            $('#list').html(out);
            setTimeout(loadData, 5000);
        })
        .fail(function (msg) {
            setTimeout(loadData, 15000);
        });
};

loadData();
