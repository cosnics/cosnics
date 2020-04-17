(function ($) {
    var formTimePeriodSelector = '.form-time-period';
    var formTimePeriodDatesSelector = '.form-time-period-dates';

    function handleFormTimePeriod(radioElement) {
        var containerElement = radioElement.closest(formTimePeriodSelector);

        if (radioElement.val() == 1) {
            $(formTimePeriodDatesSelector, containerElement).addClass('hidden');
        }
        else {
            $(formTimePeriodDatesSelector, containerElement).removeClass('hidden');
        }
    }

    $(document).ready(function () {
        $(formTimePeriodSelector).on('click', ':radio', [], function () {
            handleFormTimePeriod($(this));
        });

        $(formTimePeriodSelector).each(function () {
            handleFormTimePeriod($(':radio:checked', $(this)));
        });
    });

})(jQuery);