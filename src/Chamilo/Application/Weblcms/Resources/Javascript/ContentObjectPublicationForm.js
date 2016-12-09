(function ($) {

    var leaveMessage = getTranslation('LeavePublicationPage', [], 'Chamilo\\Application\\Weblcms');

    var leaveHandler = function(e) {
        e.returnValue = leaveMessage;
        return leaveMessage;
    };

    var disableMailIfNecessary = function() {
        var isLimited = $('#limited').prop('checked');
        var isHidden = $('.hidden_publication').prop('checked');
        var inDate = true;

        var sendByEmail = $('.send_by_email');
        var emailNotPossible = $('.email-not-possible');

        if(isLimited) {
            var fromDate = new Date(
                $('select[name="from_date[Y]"]').val(),
                $('select[name="from_date[F]"]').val() - 1,
                $('select[name="from_date[d]"]').val(),
                $('select[name="from_date[H]"]').val(),
                $('select[name="from_date[i]"]').val()
            );

            var toDate = new Date(
                $('select[name="to_date[Y]"]').val(),
                $('select[name="to_date[F]"]').val() - 1,
                $('select[name="to_date[d]"]').val(),
                $('select[name="to_date[H]"]').val(),
                $('select[name="to_date[i]"]').val(),
                59
            );

            var currentDate = new Date();

            var inDate = fromDate <= currentDate && toDate >= currentDate;
        }

        if(isHidden || !inDate) {
            sendByEmail.prop('disabled', true);
            sendByEmail.prop('checked', false);

            emailNotPossible.removeClass('hidden');
        }
        else {
            sendByEmail.prop('disabled', false);
            emailNotPossible.addClass('hidden');
        }
    };

    $(document).ready(function() {

        $(window).on("beforeunload", leaveHandler);

        $('[type="submit"]').on("click", function() { $(window).unbind("beforeunload", leaveHandler) });


        $('#forever, #limited, .hidden_publication').on('click', disableMailIfNecessary);
        $('.from_date, .to_date').on('change', disableMailIfNecessary);
    });

})(jQuery);

var start_time_changed = function() {
    $('select[name="from_date[Y]"]').trigger('change');
};