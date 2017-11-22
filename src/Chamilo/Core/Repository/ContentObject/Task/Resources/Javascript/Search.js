(function($) {
    $(document).ready(function() {
        var creationDates = $("#start_date,#due_date").datepicker({
            dateFormat : 'dd-mm-yy',
            firstDay : 1,
            changeMonth : true,
            changeYear : true,
            onSelect : function(selectedDate) {
                var option = (this.id == "start_date") ? "minDate" : "maxDate", instance = $(this).data("datepicker"), date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
                creationDates.not(this).datepicker("option", option, date);
            }
        });

    })
})(jQuery);