(function ($) {
    $(document)
        .ready(
            function () {
                var creationTimes = $("#start_time,#end_time")
                    .datepicker(
                        {
                            dateFormat: 'dd-mm-yy',
                            firstDay: 1,
                            changeMonth: true,
                            changeYear: true,
                            onSelect: function (selectedDate) {
                                var option = (this.id == "start_time") ? "minDate"
                                    : "maxDate", instance = $(
                                    this)
                                    .data("datepicker"), date = $.datepicker
                                    .parseDate(
                                        instance.settings.dateFormat
                                        || $.datepicker._defaults.dateFormat,
                                        selectedDate,
                                        instance.settings
                                    );
                                creationTimes.not(this)
                                    .datepicker("option",
                                        option, date
                                    );
                            }
                        });

            })
})(jQuery);