(function ($) {

    function showOptions(event, ui) {
        var frequency = $(this).val();
        $('.frequency').hide();
        $('.range').hide();
        $('#frequency_' + frequency).show();
        if (frequency != 0) {
            $('.range').show();
        }
    }

    $(document).ready(function () {
        $('.frequency').hide();
        $('.range').hide();
        $(document).on('click', 'input[name=frequency]:radio', showOptions);
        var frequency = $('input[name=frequency]:radio:checked').val();
        $('#frequency_' + frequency).show();
        if (frequency != 0) {
            $('.range').show();
        }

    })
})(jQuery);
