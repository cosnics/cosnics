(function($) {

	var ajaxUri = getPath('WEB_PATH') + 'index.php';

	function procesAnswer() {

		var selectElement = $(this);
		var questionDiv = selectElement.parents('.question.Matching');
		var nodeId = questionDiv.data('node_id');
		saveAnswer(nodeId, selectElement);
		procesSurveyVisibility(nodeId);
	}

	function saveAnswer(nodeId, selectElement) {
	
		var parameters = getSurveyParameters();
		parameters.node_id = nodeId;
		var oldOptionElement = selectElement.children('option[selected]');
		oldOptionElement.removeAttr('selected');
		var optionElement = selectElement.find(":selected");
		optionElement.attr('selected', 'selected');
		parameters.answer_id= selectElement.attr('name');
		parameters.answer_value = optionElement.attr('value');
		parameters.go = "SaveAnswer";
		doAjaxPost(ajaxUri, parameters);
	}
	
	$(document).ready(function() {

		$(document).on('change', '.question.Matching select', procesAnswer);

	});

})(jQuery);