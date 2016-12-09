(function($) {
		
	function getAnswer(){
		
		var parameters = getParameters();
		var ajaxUri = getPath('WEB_PATH') + 'index.php';
    	var context = parameters.ajax_context;
    	var complex_question_id=$.trim($(this).attr('id'));
    	parameters.complex_question_id=complex_question_id
    	var respons = {};
		var ajax_parameters = {
			"application" : context,
			"go" : "get_answer",
			"parameters" : parameters,
			"results" : respons
		};

		var respons = $.parseJSON(doAjaxPost(ajaxUri, ajax_parameters));
		
		if(respons.properties.result.answer != null){
			var $question = $(this);
			$.each(respons.properties.result.answer, function(answer_id, answer_value){
				var input = 'input[name="'+answer_id+'"][value="'+answer_value+'"]';
				$question.find(input).attr('checked', 'checked');
			});
		}
		
		proces_visibility();
	}
	
	function proces_answer(){
		
		var answerElement;
		var id = $(this).attr('id'); 
		
		if(id.substring(0, 'timepicker'.length) === 'timepicker'){
			var timeElement = $(this).find('.ui_tpicker_time');
			timeElement.attr('name', $(this).attr('name'));
			timeElement.attr('value', timeElement.text());
			answerElement = timeElement;
		}else if(id.substring(0, 'datepicker'.length) === 'datepicker'){
			var dateElement = $(this).find('a.ui-state-active');
			dateElement.attr('name', $(this).attr('name'));
			var year =  dateElement.parent().attr('data-year');
			var month = parseInt(dateElement.parent().attr('data-month'))+1;
			var day = dateElement.text();
			var date = day+'/'+month+'/'+year;
			dateElement.attr('value', date);
			answerElement = dateElement;
		}else{
			answerElement = $(this);
		}
				
		if($(this).attr('checked')){
			deleteAnswer($(this));
		}else{
			saveAnswer(answerElement);
		}
		
		proces_visibility();
	}
	
	function proces_visibility(){
		
		var parameters = getParameters();
		var ajaxUri = getPath('WEB_PATH') + 'index.php';
    	var context = parameters.ajax_context;
    	var respons = {};
		var ajax_parameters = {
			"application" : context,
			"go" : "get_visibility",
			"parameters" : parameters,
			"results" : respons
		};

		var respons = $.parseJSON(doAjaxPost(ajaxUri, ajax_parameters));
		
		if(respons.properties.question_visibility != null){
			$.each(respons.properties.question_visibility, function(complex_question_id, visible){
				if(visible){
					if($("div#"+ complex_question_id).attr("style")){
						$("div#"+ complex_question_id).removeAttr("style");
					}
				}else{
					
					if(!$("div#"+ complex_question_id).attr("style")){
						$("div#"+complex_question_id).hide();
						deleteAnswers(complex_question_id);
					}
				}
			});
		}
	}
	
	function saveAnswer($input){
		
		$input.attr('checked', 'checked')
		var name = $input.attr('name');
		var ids = name.split('_');
		var complex_quetsion_id = ids[0];
		var match_id = $input.attr('value');
		var answer = {};
		answer.complex_question_id = complex_quetsion_id;
		answer.answer_id = name;
		answer.answer_value = match_id;
		
		var parameters = getParameters();
		parameters.answer=answer;
		var ajaxUri = getPath('WEB_PATH') + 'index.php';
    	var context = parameters.ajax_context;
    	
    	parameters.complex_question_id=complex_quetsion_id;
    	var result = {};
		var ajax_parameters = {
			"application" : context,
			"go" : "save_answer",
			"parameters" : parameters,
			"results" : result
		};
		
		doAjaxPost(ajaxUri, ajax_parameters)
	}
	
	function deleteAnswer($input){
		
		$input.removeAttr('checked')
		var name = $input.attr('name');
		
		var ids = name.split('_');
		var complex_question_id = ids[0];
		var match_id = $input.attr('value');
		var answer = {};
		answer.complex_question_id = complex_question_id;
		answer.answer_id = name;
		answer.answer_value = match_id;
		
		var parameters = getParameters();
		parameters.answer=answer;
		var ajaxUri = getPath('WEB_PATH') + 'index.php';
    	var context = parameters.ajax_context;
    	
    	parameters.complex_question_id=complex_question_id;
    	var result = {};
		var ajax_parameters = {
			"application" : context,
			"go" : "delete_answer",
			"parameters" : parameters,
			"results" : result
		};
		
		doAjaxPost(ajaxUri, ajax_parameters)
	}
	
	function deleteAnswers(complex_question_id){
		
		var answer = {};
		answer.complex_question_id = complex_question_id;
		answer.answer_id = null;
		answer.answer_value = null;
		
		var parameters = getParameters();
		parameters.answer=answer;
		var ajaxUri = getPath('WEB_PATH') + 'index.php';
    	var context = parameters.ajax_context;
    	
    	parameters.complex_question_id=complex_question_id;
    	var result = {};
		var ajax_parameters = {
			"application" : context,
			"go" : "delete_answer",
			"parameters" : parameters,
			"results" : result
		};
		
		doAjaxPost(ajaxUri, ajax_parameters)
	}
	
	function getParameters(){
		
		var parameters = {};
		var $params = $('input[type="hidden"]'  , window.parent.document);
			$params.each(function(){
				parameters[$(this).attr('name').replace('param_', '')] = $(this).attr('value');
			});
		
			return parameters;
	}
	
	$(document).ready(function() {

		$(document).on('change', '.question input', proces_answer);
		$(document).on('click', '[id^=timepicker]', proces_answer);
		$(document).on('click', '[id^=datepicker]', proces_answer);

	});

})(jQuery);