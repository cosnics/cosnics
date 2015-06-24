$(function() {
    $(document).ready(function() {
        $('.feedback-history-toggle-visible').click(function() {
            $(".feedback-history").show();
            $(".feedback-history-toggle-visible").hide();
            $(".feedback-history-toggle-invisible").show();
        });

        $('.feedback-history-toggle-invisible').click(function() {
            $(".feedback-history").hide();
            $(".feedback-history-toggle-visible").show();
            $(".feedback-history-toggle-invisible").hide();
        });
    });

});