$(document).ready(function () {
    $('#protocol').change(function () {
        $('#http-form').hide();
        if ($('#protocol').val().startsWith('http')) {
            $('#http-form').show();
        }
    });
    $("#protocol").trigger("change");
});
