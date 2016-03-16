function procesSurveyVisibility(nodeId){

	var parameters = getSurveyParameters();
	parameters.node_id=nodeId;
	parameters.go = "GetVisibility";
	var respons = {};
	var respons = $.parseJSON(doAjaxPost(ajaxUri, parameters));

	if (respons.properties.question_visibility != null) {
		$.each(respons.properties.question_visibility, function(nodeId,
				visible) {
			if (visible) {
				if ($("div#" + nodeId).attr("style")) {
					$("div#" + nodeId).removeAttr("style");
				}
			} else {

				if (!$("div#" + nodeId).attr("style")) {
					$("div#" + nodeId).hide();
					deleteAnswers(nodeId);
				}
			}
		});
	}
}

function deleteAnswers(nodeId) {
	var parameters = getSurveyParameters();
	parameters.go = "DeleteAnswer";
	parameters.node_id = nodeId;
	doAjaxPost(ajaxUri, parameters);
}

function getSurveyParameters() {

	var parameters = {};
	var $params = $('input[type="hidden"]', window.parent.document);
	$params.each(function() {
		parameters[$(this).attr('name').replace('param_', '')] = $(this).attr(
				'value');
	});

	parameters.application = 'Chamilo\\Core\\Repository\\ContentObject\\Survey\\Display';
	parameters.display_action = "Ajax";
	return parameters;
}
