(function($) {
    function resetAttempts(ev, ui) {
        $("#attempts").val(0);
    }

    function startAttempts(ev, ui) {
        $("#attempts").val(1);
    }

    function resetQuestions(ev, ui) {
        $("#questions").val(0);
    }

    function resetTime(ev, ui) {
        $("#time").val(0);
    }

    function resetRandom(ev, ui) {
        $("#number_random").val(0);
    }

    $(document).ready(function() {
        $("#unlimited_attempts").on("click", resetAttempts);
        $("#limited_attempts").on("click", startAttempts);
        $("#all_questions").on("click", resetQuestions);
        $("#unlimited_time").on("click", resetTime);
        $("#random").on("click", resetRandom);
    });
})(jQuery);
