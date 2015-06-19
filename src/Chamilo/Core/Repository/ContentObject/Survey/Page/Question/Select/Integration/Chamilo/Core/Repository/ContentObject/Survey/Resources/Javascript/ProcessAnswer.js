(function($) {

	var ajaxUri = getPath('WEB_PATH') + 'index.php';

	function procesAnswer() {
		
		answerElement = $(this);

		if ($(this).attr('checked')) {
			deleteAnswer(answerElement);
		} else {
			saveAnswer(answerElement);
		}

		procesVisibility(answerElement);
	}

//	function procesVisibility(answerElement) {
//
//		var parameters = getParameters();
//		parameters.node_id = answerElement.data('node_id');
//		parameters.go = "GetVisibility";
//		var respons = {};
//		var respons = $.parseJSON(doAjaxPost(ajaxUri, parameters));
//
//		if (respons.properties.question_visibility != null) {
//			$.each(respons.properties.question_visibility, function(node_id,
//					visible) {
//				if (visible) {
//					if ($("div#" + node_id).attr("style")) {
//						$("div#" + node_id).removeAttr("style");
//					}
//				} else {
//
//					if (!$("div#" + node_id).attr("style")) {
//						$("div#" + node_id).hide();
//						deleteAnswers(node_id);
//					}
//				}
//			});
//		}
//	}

	function saveAnswer($input, attribute) {
		$input.attr('checked', 'checked');
		var parameters = getParameters();
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
		var parameters = getParameters();
		parameters.answer_id = answerId;
		parameters.answer_value = value;
		parameters.go = "DeleteAnswer";
		parameters.node_id = $input.data('node_id');
		doAjaxPost(ajaxUri, parameters);
	}

	function deleteAnswers(nodeId) {
		var question = $("div#" + nodeId);
		question.find("input").removeAttr('checked');
		var parameters = getParameters();
		parameters.go = "DeleteAnswer";
		parameters.node_id = nodeId;
		doAjaxPost(ajaxUri, parameters);
	}

//	function getParameters() {
//
//		var parameters = {};
//		var $params = $('input[type="hidden"]', window.parent.document);
//		$params.each(function() {
//			parameters[$(this).attr('name').replace('param_', '')] = $(this)
//					.attr('value');
//		});
//
//		parameters.application = 'Chamilo\\Core\\Repository\\ContentObject\\Survey\\Display';
//		parameters.display_action = "Ajax";
//		return parameters;
//	}

	$(document).ready(function() {

//		$(document).on('change', '.question .matrix input', procesAnswer);

	});

})(jQuery);