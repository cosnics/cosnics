$(function() {

    function switchVisibility(calendarEvent) {
        var ajaxUri = getPath('WEB_PATH') + 'index.php';
        var eventContainer = $('span.event-container', $(calendarEvent));
        var eventClasses = eventContainer.attr('class').split(" ");
        var eventSourceClass = determineEventSourceClass(eventClasses);

        var parameters = {
            'application' : calendarVisibilityContext,
            'go' : 'CalendarEventVisibility',
            'source' : eventContainer.data('source')
        };

        var response = $.ajax({
            type : "POST",
            url : ajaxUri,
            data : parameters,
            async : false
        }).success(function(json) {
            if (json.result_code == 200) {
            var typeClass = $(calendarEvent).attr('class');
            var typeClasses = typeClass.split(" ");

            var eventBoxes = $('.table-calendar .' + eventSourceClass);

            $(eventBoxes).each(function(index, item) {
                var eventBox = $(item);
                if (eventBox.parent().hasClass('list-event-item-data')) {
                var listItem = eventBox.parent().parent().parent();
                listItem.toggleClass('event-container-hidden');

                var visibleListItems = $('.list-group-item:visible', listItem.parent());

                listItem.parent().parent().parent().show();

                if (visibleListItems.length == 0) {
                listItem.parent().parent().parent().hide();
                }

                } else {
                eventBox.toggleClass('event-container-hidden');
                }
            });

            $(calendarEvent).toggleClass('disabled');
            $(calendarEvent).toggleClass('event-container-source-faded');
            }
        });
    }

    function determineEventSourceClass(eventClasses) {
        var eventClass = '';

        $.each(eventClasses, function(index, value) {

            if (value.indexOf('event-container-source-') > -1 && value != 'event-container-source-faded') {
            eventClass = value;
            }
        });

        return eventClass;
    }

    $(document).ready(function() {

        $(document).on('click', '.table-calendar-legend .event-source', function(event) {
            switchVisibility(this);
        });
    });
});