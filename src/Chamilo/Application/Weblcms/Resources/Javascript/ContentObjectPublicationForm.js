(function ($) {

    $(document).ready(function() {

        var leaveMessage = getTranslation('LeavePublicationPage', [], 'Chamilo\\Application\\Weblcms');

        var leaveHandler = function(e) {
            e.returnValue = leaveMessage;
            return leaveMessage;
        };

        $(window).on("beforeunload", leaveHandler);

        $('[type="submit"]').on("click", function() { $(window).unbind("beforeunload", leaveHandler) });

        var disableMailIfNecessary = function() {
            var isLimited = $('#limited').prop('checked');
            var isHidden = $('.hidden_publication').prop('checked');

            var sendByEmail = $('.send_by_email');
            var emailNotPossible = $('.email-not-possible');

            if(isLimited || isHidden) {
                sendByEmail.prop('disabled', true);
                sendByEmail.prop('checked', false);

                emailNotPossible.removeClass('hidden');
            }
            else {
                sendByEmail.prop('disabled', false);
                emailNotPossible.addClass('hidden');
            }
        };

        $('#forever, #limited, .hidden_publication').on('click', disableMailIfNecessary)
    });

})(jQuery);