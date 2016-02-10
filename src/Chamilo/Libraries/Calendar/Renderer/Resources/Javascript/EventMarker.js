$(function() {

    $(document).ready(function() {
        $("div.event_marker").tooltip({
            items : "div",
            content : function() {
                var eventsContent = '';
                var element = $(this);

                $("div.event", element.parent()).each(function(i) {
                    eventsContent += $('<div>').append($(this).clone().show()).remove().html();
                });
                return eventsContent;
            }
        });

    });

});