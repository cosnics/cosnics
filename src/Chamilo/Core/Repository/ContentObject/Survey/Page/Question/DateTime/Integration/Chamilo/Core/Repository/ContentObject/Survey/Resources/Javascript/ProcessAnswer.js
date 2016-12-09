(function($) {

	var ajaxUri = getPath('WEB_PATH') + 'index.php';

	function procesAnswer() {

		var selectElement = $(this);
		var questionDiv = selectElement.parents('.question.DateTime');
		var nodeId = questionDiv.data('node_id');
		var complexQuestionId = questionDiv.data('complex_question_id');
		saveAnswer(nodeId, complexQuestionId, selectElement);

		procesSurveyVisibility(nodeId);
	}

	function saveAnswer(nodeId, complexQuestionId, selectElement) {

		var parameters = getSurveyParameters();
		parameters.node_id = nodeId;
		parameters.complex_question_id = complexQuestionId;
		var oldOptionElement = selectElement.children('option[selected]');
		oldOptionElement.removeAttr('selected');
		var optionElement = selectElement.find(":selected");
		optionElement.attr('selected', 'selected');
		var answers = getOtherSelectAnswers(selectElement);
		
		var answerId =selectElement.attr('name');
		var key = getAnswerIdKey(answerId);
		var answer = optionElement.attr('value');
		answers[key] = answer;
		
		parameters.answer_id= getAnswerIdName(answerId);
		parameters.answer_value = answers;
		parameters.go = "SaveAnswer";
		doAjaxPost(ajaxUri, parameters);
	}


	function getOtherSelectAnswers(selectElement) {
		var answers = {};
		selectElement.siblings('select').each(function() {
			var answerId = $(this).attr('name');
			var key = getAnswerIdKey(answerId);
			var answer = $(this).find(":selected").attr('value');
			answers[key] = answer

		});
		return answers;
	}

	function getAnswerIdKey(answerId) {
		var res = answerId.split("[");	
		var res2 = res[1].split("]");
		return res2[0];
	}

	function getAnswerIdName(answerId) {
		var res = answerId.split("[");	
		return res[0];
	}
	
	$(document).ready(function() {

		$(document).on('change', '.question.DateTime select', procesAnswer);

	});

})(jQuery);