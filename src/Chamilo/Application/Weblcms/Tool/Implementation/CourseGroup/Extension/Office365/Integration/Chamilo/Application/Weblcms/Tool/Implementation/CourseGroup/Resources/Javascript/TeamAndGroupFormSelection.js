$(function () {
    function selectGroup() {
        var checkboxElement = $(this);

        if (checkboxElement.is(':checked')) {
            $('input[name^="use_group["]').prop('checked', true);
        }
    }

    $(document).ready(function () {
        $(document).on('change', 'input[name^="use_group_and_team["]', selectGroup);
    });

});