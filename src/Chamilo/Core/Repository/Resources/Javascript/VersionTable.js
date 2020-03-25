(function ($) {

    var checkboxes;

    function checkCompareCheckboxes(e, ui) {
        var checkboxCount = $("#version_table tbody input:checkbox:checked").size();

        if (checkboxCount >= 2) {
            $("#version_table tbody input:checkbox:not(:checked)").attr('disabled', true);
        }
        else {
            $("#version_table tbody input:checkbox").removeAttr('disabled');
        }
    }

    $(document).ready(function () {
        $("#version_table thead input:checkbox:not(:checked)").attr('disabled', true);
        $(document).on('click', "#version_table tbody input:checkbox", checkCompareCheckboxes);
    });

})(jQuery);