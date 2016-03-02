( function($) 
{
	function setValue(e, ui)
	{
		var currentValue = ui.value;
		var sliderId = $(this).attr('id');
		var selectName = sliderId.replace('slider_', '');
		$("select[name=" + selectName + "]").val(currentValue);
		$("#slider_caption_" + selectName).html(currentValue);
	}
	
	function processValue(e, ui)
	{
		var currentValue = ui.value;
		var sliderId = $(this).attr('id');
		var selectName = sliderId.replace('slider_', '');
		$("select[name=" + selectName + "]").trigger('change');
	}
	
	function addSlider()
	{
		var id = $(this).attr("name");
		var minValue = parseInt($('option:first', this).val());		
		var maxValue = parseInt($('option:last', this).val());
		var slider = $('<div class="slider" id="slider_' + id + '"></div>');
		var caption = $('<div class="caption" id="slider_caption_' + id + '"></div>');
		$(this).after(caption).after(slider);
		$(this).toggle();
		
		$(slider).slider({
			animate: true,
			min: minValue,
			max: maxValue,
			stop: setValue,
			slide: setValue,
			change : processValue,
			value: $(this).val()
			});
		$("#slider_caption_" + id).html($(this).val());

	}
	
	function saveOldQuestionValue(event, userInterface) {
		oldQuestionValue = $('#question').val();
	}

	function synchronizeTitle(event, userInterface) {
		var questionValue = $('#question').val();
		var titleValue = $('#title').val();

		if (!titleValue || titleValue == oldQuestionValue) {
			$('#title').val(questionValue);
			$("#title").trigger('change');
		}
	}
	
	$(document).ready( function() 
	{
		$("select.rating_slider").each(addSlider);
		$(document).on('focusin', '#question', saveOldQuestionValue);
		$(document).on('focusout', '#question', synchronizeTitle);
	});
	
})(jQuery);