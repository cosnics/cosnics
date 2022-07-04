$(function () {

    function switchVisibility(calendarEvent) {
        var eventSourceKey = $(calendarEvent).data('source-key');
        var eventSource = $(calendarEvent).data('source');

        var response = $.ajax({
            type: "POST",
            url: getPath('WEB_PATH') + 'index.php',
            data: {
                'application': calendarVisibilityContext,
                'go': 'CalendarEventVisibility',
                'source': eventSource
            },
            async: false
        }).success(function (json) {
            if (json.result_code == 200) {
                $(".table-calendar [data-source-key='" + eventSourceKey + "']").toggleClass('event-container-hidden');

                $('.table-calendar-list-events').each(function (index) {
                    var eventContainers = $('ul.list-group .event-container', $(this)).length;
                    var hiddenEventContainers = $('ul.list-group .event-container-hidden', $(this)).length;

                    if (hiddenEventContainers == eventContainers &&
                        !$(this).parent().hasClass('event-container-hidden')) {
                        $(this).parent().addClass('event-container-hidden');
                    }
                    else if (hiddenEventContainers < eventContainers &&
                        $(this).parent().hasClass('event-container-hidden')) {
                        $(this).parent().removeClass('event-container-hidden');
                    }
                });

                $(calendarEvent).toggleClass('event-container-source-faded');
            }
        });
    }

    $(document).ready(function () {
        $(document).on('click', '.table-calendar-legend .event-source', function (event) {
            switchVisibility(this);
        });
    });
});