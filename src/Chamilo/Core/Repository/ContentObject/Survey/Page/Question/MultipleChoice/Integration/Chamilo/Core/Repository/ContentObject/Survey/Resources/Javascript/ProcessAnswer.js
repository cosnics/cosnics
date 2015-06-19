(function($) {

	var ajaxUri = getPath('WEB_PATH') + 'index.php';

	function procesSelectAnswer() {

		var selectElement = $(this);
		var questionDiv = selectElement.parents('.question.MultipleChoice');
		var nodeId = questionDiv.data('node_id');
		saveSelectAnswer(nodeId, selectElement);
		procesSurveyVisibility(nodeId);
	}

	function saveSelectAnswer(nodeId, selectElement) {

		var parameters = getSurveyParameters();
		parameters.node_id = nodeId;
		var oldOptionElement = selectElement.children('option[selected]');
		oldOptionElement.removeAttr('selected');
		var optionElement = selectElement.find(":selected");
		optionElement.attr('selected', 'selected');
		parameters.answer_id = selectElement.attr('name');
		parameters.answer_value = optionElement.attr('value');
		parameters.go = "SaveAnswer";
		doAjaxPost(ajaxUri, parameters);
	}

	function procesAnswer() {

		var inputElement = $(this);
		var questionDiv = inputElement.parents('.question.MultipleChoice');
		var nodeId = questionDiv.data('node_id');

		if ($(this).attr('checked')) {
			deleteAnswer(inputElement);
		} else {
			saveAnswer(inputElement);
		}

		procesSurveyVisibility(nodeId);
	}

	function saveAnswer($input) {
		$input.attr('checked', 'checked');
		var parameters = getSurveyParameters();
		parameters.node_id = $input.data('node_id');
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

	function procesSpecialFSelectAnswer() {

		var selectElement = $(this);
		var questionDiv = selectElement.parents('.question.MultipleChoice');
		var nodeId = questionDiv.data('node_id');
		
		
		var optionElement = $(selectElement).find(":selected");
		var optionValue = $(optionElement).attr('value');
		var selectTElement = questionDiv.find('select[id$="-t"]');
		var optionElements = selectTElement.children('option');
		var answerValue = [];
		answerValue.push(optionValue);
		optionElements.each(function(index, optionElement) {
			var value = $(optionElement).attr('value');
			if(value!=""){
				answerValue.push(value);
			}
		});
		var answerId = selectTElement.attr('name').replace("-t[]", "");
		saveSpecialSelectAnswer(nodeId, answerId, answerValue);
		procesSurveyVisibility(nodeId);
	}

	function procesSpecialTSelectAnswer() {

		var selectElement = $(this);
		var questionDiv = selectElement.parents('.question.MultipleChoice');
		var nodeId = questionDiv.data('node_id');
		var optionElement = $(selectElement).find(":selected");
		var optionValue = $(optionElement).attr('value');
		var selectTElement = questionDiv.find('select[id$="-t"]');
		var optionElements = selectTElement.children('option');
		var answerValue = [];
		optionElements.each(function(index, optionElement) {
			var value = $(optionElement).attr('value');
			if(value != optionValue){
				if(value!=""){
					answerValue.push(value);
				}
			}
		});
		var answerId = selectTElement.attr('name').replace("-t[]", "");
		saveSpecialSelectAnswer(nodeId, answerId, answerValue);
		procesSurveyVisibility(nodeId);
		
	}
	
	function saveSpecialSelectAnswer(nodeId, answerId, answerValue) {

		var parameters = getSurveyParameters();
		parameters.node_id = nodeId;
		parameters.answer_id = answerId;
		parameters.answer_value = answerValue
		parameters.go = "SaveAnswer";
		doAjaxPost(ajaxUri, parameters);
	}

	$(document).ready(
			function() {

				$(document).on('change',
						'div[data-display-type="mcnormal"] select',
						procesSelectAnswer);
				$(document)
						.on('change',
								'div[data-display-type="mcnormal"] input',
								procesAnswer);
				$(document).on('change',
						'div[data-display-type="mcspecial"] select[id$="-f"]',
						procesSpecialFSelectAnswer);
				$(document).on('change',
						'div[data-display-type="mcspecial"] select[id$="-t"]',
						procesSpecialTSelectAnswer);
				
			});

})(jQuery);