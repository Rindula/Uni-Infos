function loadData() {
    $.ajax({
        method: "GET",
        url: "/stundenplan/ajax",
        dataType: "json"
    })
        .done(function (msg) {
            var out = "";
            for (var e in msg) {
                var event = msg[e];
                var html = "<blockquote><div class=\"row row-top" + ((event['custom']['current']) ? " active" : "") + ((event['custom']['today'] && !event['custom']['current']) ? " indigo lighten-2" : "") + ((event['custom']['tomorrow']) ? " indigo lighten-4" : "") + ((event['custom']['isKlausur'] && !event['custom']['current']) ? " red accent-1" : "") + "\">" +
                    "<span class=\"column column-20\">" + event['SUMMARY'] + "</span><span\n" +
                    "                    class=\"column-offset-75 column-20 column\">" + event['LOCATION'] + "</span></div>" +
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

                out += html + "</blockquote><hr>";
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
