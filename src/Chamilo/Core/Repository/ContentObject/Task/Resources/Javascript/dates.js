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

        $('#start_date').datetimepicker({
            timeFormat : 'HH:mm',
            dateFormat : 'dd-mm-yy',
            controlType : 'select',
            separator : '  ',
            onClose : function(dateText, inst) {
                var endDateTextBox = $('#due_date');
                if (endDateTextBox.val() != '') {
                var testStartDate = new Date(dateText);
                var testEndDate = new Date(endDateTextBox.val());

                if (testStartDate > testEndDate)
                    endDateTextBox.val(dateText);
                } else {
                endDateTextBox.val(dateText);
                }
            },
            onSelect : function(selectedDateTime) {
                var start = $(this).datetimepicker('getDate');
                var instance = $(this).data("datepicker");
                var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDateTime, instance.settings);
                $('#due_date').datetimepicker("option", "minDate", date);
            }
        });
        $('#due_date').datetimepicker({
            timeFormat : 'HH:mm',
            dateFormat : 'dd-mm-yy',
            controlType : 'select',
            separator : '  ',
            onClose : function(dateText, inst) {
                var startDateTextBox = $('#start_date');
                if (startDateTextBox.val() != '') {
                var testStartDate = new Date(startDateTextBox.val());
                var testEndDate = new Date(dateText);
                if (testStartDate > testEndDate)
                    startDateTextBox.val(dateText);
                } else {
                startDateTextBox.val(dateText);
                }
            },
            onSelect : function(selectedDateTime) {
                var end = $(this).datetimepicker('getDate');
                var instance = $(this).data("datepicker");
                var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datetimepicker._defaults.dateFormat, selectedDateTime, instance.settings);
                $('#start_date').datetimepicker("option", "maxDate", date);
            }
        });

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
