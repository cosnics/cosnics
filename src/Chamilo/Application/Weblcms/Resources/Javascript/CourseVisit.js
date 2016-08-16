(function($)
{
    $(document).ready( function()
    {
        $(window).on('beforeunload', function(e)
        {
            if (typeof course_visit_tracker != 'undefined')
            {
                $.ajax(
                    {
                        type: "POST",
                        url: "index.php",
                        data: {
                            'application': 'Chamilo\\Application\\Weblcms\\Ajax',
                            'go': 'leave_course',
                            'course_visit_tracker_id': course_visit_tracker
                        },
                        async: false
                    }
                );
            }

        });
    });

})(jQuery);