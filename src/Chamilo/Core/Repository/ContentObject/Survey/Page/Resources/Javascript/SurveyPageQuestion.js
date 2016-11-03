(function($) {
	function processAnswers(e, ui) {

		var surveyPageId, checkedQuestions, checkedQuestionResults, answers, displayResult, ajaxUri = getPath('WEB_PATH')
				+ 'index.php';

		surveyPageId = $("input[name=survey_page]").val();
		userId = $("input[name=user_id]").val();

		checkedQuestions = $(".question input:checked");

		checkedQuestionResults = {};
		answers = {};

		checkedQuestions.each(function(i) {
			// checkedQuestionResults[$(this).attr('type') + '_'
			// + $(this).attr('name')] = $(this).val();
			checkedQuestionResults[$(this).attr('name')] = $(this).val();
			var ids = $(this).attr('name').split('_');
			var question_id = ids[0];
			if (!answers[question_id]) {
				answers[question_id] = {};
			}
			// alert(question_id+" "+$(this).attr('name')+" "+$(this).val());
			answers[question_id][$(this).attr('name')] = $(this).val();
		});

		selectQuestions = $(".question select option:selected");

		selectQuestions.each(function(i) {
			name = $(this).parent().attr('name')
			checkedQuestionResults[name] = $(this).val();
			var ids = name.split('_');
			var question_id = ids[0];
			if (!answers[question_id]) {
				answers[question_id] = {};
			}
			// alert(question_id+" "+name+" "+$(this).val());
			answers[question_id][name] = $(this).val();
		});

		var parameters = {
			"application" : "Chamilo\\Core\\Repository\\ContentObject\\Survey\\Page\\Ajax",
			"go" : "proces_preview_answer",
			"survey_page" : surveyPageId,
			"results" : checkedQuestionResults
		};

		var save_answer = $
				.ajax({
					type : "POST",
					url : ajaxUri,
					data : parameters,
					async : false
				})
				.success(function(json){
					if (json.result_code == 200){
						$.each(json.properties.question_visibility,	function(questionId,questionVisible){
							if (!questionVisible){
											
							var uncheckquestions = $("div#"+"survey_question_"+ questionId+" input:checked");
							uncheckquestions.each(function(i){
								$(this).attr('checked', false);
							});

							var cleartextarea = $("textarea.html_editor[name="+ questionId+"]");
							cleartextarea.each(function(i){
								$(this).empty();
							});

							$("div#"+"survey_question_"+questionId).hide();

					}else{
						$("div#"+ "survey_question_"+ questionId).removeAttr("style");
					}
				
			}
		});

	}

	$(document).ready(function() {
		$(document).on('click', ".question input", processAnswers);

		$(document).on('change', ".question select", processAnswers);

	});

})(jQuery);