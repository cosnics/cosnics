$(function() {

    function selectAllRights() {
        var checkboxElement = $(this);

        if (checkboxElement.is(':checked')) {
        $('input[name="right_mail"]').prop('checked', true);
        $('input[name="right_report"]').prop('checked', true);
//        $('input[name="right_view"][value="15"]').prop('checked', true);
        }
    }

    $(document).ready(function() {

        $(document).on('change', 'input[name="right_manage"]', selectAllRights);
    });

});