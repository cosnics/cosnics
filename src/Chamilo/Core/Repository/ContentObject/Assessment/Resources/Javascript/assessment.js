var timer;

function handle_timer() {
	var value = $('#start_time').val();
	value = parseInt(value);
	value++;
	$('#start_time').val(value);

	var max_time = $('#max_time').val();
	max_time = parseInt(max_time);

	var text = max_time - value;

	if (max_time - value < 10) {
		$('.time_left').attr('class', 'time_left_alert');
	}

	$('.time').html(text);

	if (max_time == 0)
		return;

	if (value >= max_time) {
		alert(getTranslation('TimesUp', null,
				'Chamilo/Core/Repository/ContentObject/Assessment'));
		$(".submit").click();
	} else {
		timer = setTimeout('handle_timer()', 1000);
	}
}

(function($) {
	function highlightRowColumn(ev, ui) {
		var column = $(this).prevUntil('tr', 'td').size();
		$(this).parent().addClass('highlight');
		$(
				'tr td:nth-child(' + (column + 1) + '), tr th:nth-child('
						+ (column + 1) + ')',
				$(this).parent().parent().parent()).addClass('highlight');

	}

	function unhighlightRowColumn(ev, ui) {
		var column = $(this).prevUntil('tr', 'td').size();
		$(this).parent().removeClass('highlight');
		$(
				'tr td:nth-child(' + (column + 1) + '), tr th:nth-child('
						+ (column + 1) + ')',
				$(this).parent().parent().parent()).removeClass('highlight');

	}

	$(document).ready(
			function() {
				handle_timer();

				$("table.take_assessment_matrix_question td").on('mouseover',
						highlightRowColumn);
				$("table.take_assessment_matrix_question td").on('mouseout',
						unhighlightRowColumn);
			});

})(jQuery);
