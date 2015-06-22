(function($) {

	var ajaxUri = getPath('WEB_PATH') + 'index.php';

	function procesVisibility(answerElement) {

		var parameters = getParameters();
		parameters.node_id = answerElement.data('node_id');
		parameters.go = "GetVisibility";
		var respons = {};
		var respons = $.parseJSON(doAjaxPost(ajaxUri, parameters));

		if (respons.properties.question_visibility != null) {
			$.each(respons.properties.question_visibility, function(node_id,
					visible) {
				if (visible) {
					if ($("div#" + node_id).attr("style")) {
						$("div#" + node_id).removeAttr("style");
					}
				} else {

					if (!$("div#" + node_id).attr("style")) {
						$("div#" + node_id).hide();
						deleteAnswers(node_id);
					}
				}
			});
		}
	}

	function getParameters() {

		var parameters = {};
		var $params = $('input[type="hidden"]', window.parent.document);
		$params.each(function() {
			parameters[$(this).attr('name').replace('param_', '')] = $(this)
					.attr('value');
		});

		parameters.application = 'Chamilo\\Core\\Repository\\ContentObject\\Survey\\Display';
		parameters.display_action = "Ajax";
		return parameters;
	}

})(jQuery);