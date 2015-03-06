$(function () {
    $(document).on("change keyup", "#sel", function () {
        $("#select_form").submit();
    });
});