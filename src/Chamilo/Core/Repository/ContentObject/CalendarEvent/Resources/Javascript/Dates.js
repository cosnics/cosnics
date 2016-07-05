(function($) {

    function showOptions(event, ui) {
        var frequency = $(this).val();
        $('.frequency').hide();
        $('.range').hide();
        $('#frequency_' + frequency).show();
        if (frequency != 0) {
        $('.range').show();
        }

    }

    $(document).ready(function() {

        var startDateTextBox = $('#start_date');
        var endDateTextBox = $('#end_date');

        $.timepicker.datetimeRange(
            startDateTextBox,
            endDateTextBox,
            {
                firstDay: 1,
                minInterval: (1000*60*60), // 1hr
                dateFormat: 'dd-mm-yy', 
                timeFormat: 'HH:mm',
                start: {controlType: 'select'}, // start picker options
                end: {controlType: 'select'} // end picker options                  
            }
        );

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
