(function($) {
    function checkCorrection(ev, ui) {

        var feedbackShowCorrection = $('input[name=show_correction]:checkbox:checked').val();

        var inputSolution = $('input[name=show_solution]:checkbox');
        var inputSolutionParent = inputSolution.parent().parent().parent();
        var inputAnswerFeedback = $('input[name=answer_feedback_option]:checkbox');
        var inputAnswerFeedbackParent = inputAnswerFeedback.parent().parent().parent();

        if (feedbackShowCorrection == 1) {
            inputSolutionParent.show();
            inputAnswerFeedbackParent.show();
        } else {
            inputSolution.prop('checked', false);
            inputSolutionParent.hide();
            inputAnswerFeedback.prop('checked', false);
            inputAnswerFeedbackParent.hide();
            $('select[name=show_answer_feedback]').val(1);
        }

        checkAnswerFeedback();
    }

    function checkFeedback(ev, ui) {
        var feedbackShowScore = $('input[name=show_score]:checkbox:checked').val();

        var feedbackShowCorrection = $('input[name=show_correction]:checkbox:checked').val();

        var feedbackShowSolution = $('input[name=show_solution]:checkbox:checked').val();

        var feedbackShowAnswerFeedback = $('input[name=answer_feedback_option]:checkbox:checked').val();

        var selectFeedbackLocation = $('select[name=feedback_location]');
        var row = selectFeedbackLocation.parent().parent().parent();

        if (feedbackShowScore == 1 || feedbackShowCorrection == 1 || feedbackShowSolution == 1 || feedbackShowAnswerFeedback == 1) {
            row.show();
        } else if (feedbackShowScore != 1 && feedbackShowCorrection != 1 && feedbackShowSolution != 1 && feedbackShowAnswerFeedback != 1) {

            selectFeedbackLocation.val(1);
            row.hide();
        }
    }

    function checkAnswerFeedback(ev, ui) {
        var feedbackShowAnswerFeedback = $('input[name=answer_feedback_option]:checkbox:checked').val();

        if (feedbackShowAnswerFeedback == 1) {
            $("span#answer_feedback_enabled").show();
        } else if (feedbackShowAnswerFeedback != 1) {
            $("span#answer_feedback_enabled").hide();
        }
    }

    function checkSolution(ev, ui) {
        var feedbackShowSolution = $('input[name=show_solution]:checkbox:checked').val();

        if (feedbackShowSolution == 1) {
            $('select[name=show_answer_feedback] option').show();
        } else if (feedbackShowSolution != 1) {
            $('select[name=show_answer_feedback]').val(1);
            $('select[name=show_answer_feedback] option[value!="1"][value!="2"]').hide();
        }
    }

    $(document).ready(function() {
        $(document).on('click', 'input[name=show_correction]:checkbox', checkCorrection);
        checkCorrection();

        $(document).on('click', 'input[name=show_score]:checkbox, input[name=show_correction]:checkbox, input[name=show_solution]:checkbox, input[name=answer_feedback_option]:checkbox', checkFeedback);
        checkFeedback();

        $(document).on('click', 'input[name=answer_feedback_option]:checkbox', checkAnswerFeedback);
        checkAnswerFeedback();

        $(document).on('click', 'input[name=show_solution]:checkbox', checkSolution);
        checkSolution();
    });

})(jQuery);