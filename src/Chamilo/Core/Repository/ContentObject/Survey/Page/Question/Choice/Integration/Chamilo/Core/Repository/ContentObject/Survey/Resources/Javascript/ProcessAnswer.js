(function($) {

	var ajaxUri = getPath('WEB_PATH') + 'index.php';

	function procesAnswer() {
		
		var inputElement = $(this);
		var questionDiv = inputElement.parents('.question.Choice');
		var nodeId = questionDiv.data('node_id');
		var complexQuestionId = questionDiv.data('complex_question_id');
		
		if ($(this).attr('checked')) {
			deleteAnswer(inputElement);
		} else {
			saveAnswer(inputElement);
		}

		procesSurveyVisibility(nodeId);
	}

	function saveAnswer($input, attribute) {
		$input.attr('checked', 'checked');
		var parameters = getSurveyParameters();
		parameters.node_id = $input.data('node_id');
		parameters.complex_question_id = $input.data('complex_question_id');
		parameters.answer_id = $input.attr('name');
		parameters.answer_value = $input.attr('value');
		parameters.go = "SaveAnswer";
		doAjaxPost(ajaxUri, parameters);
	}

	function deleteAnswer($input) {
		$input.removeAttr('checked');
		var answerId = $input.attr('name');
		var value = $input.attr('value');
		var parameters = getSurveyParameters();
		parameters.answer_id = answerId;
		parameters.answer_value = value;
		parameters.go = "DeleteAnswer";
		parameters.node_id = $input.data('node_id');
		doAjaxPost(ajaxUri, parameters);
	}

	$(document).ready(function() {

		$(document).on('change', '.question.Choice  input', procesAnswer);

	});

})(jQuery);