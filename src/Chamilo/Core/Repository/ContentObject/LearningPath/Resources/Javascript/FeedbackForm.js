(function($)
{
    function checkFeedback(ev, ui)
    {
        var feedbackSummary = $('input[name=feedback_summary]:checkbox:checked').val();
        var feedbackPerPage = $('input[name=feedback_per_page]:checkbox:checked').val();
        var selectFeedbackType = $('select[name=feedback_type]');
        var row = selectFeedbackType.parent().parent().parent();

        if (feedbackSummary == 1 || feedbackPerPage == 1)
        {
            row.show();
        }
        else if(feedbackSummary != 1 && feedbackPerPage != 1)
        {
            row.hide();
        }
    }

    $(document).ready(function()
    {
        $(document).on('click', 'input[name=feedback_summary]:checkbox, input[name=feedback_per_page]:checkbox', checkFeedback);
        checkFeedback();
    });

})(jQuery);