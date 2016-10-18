(function($) {
	var default_size = 20, tableElement, tableBody;
	var timer;
	var questionPattern = /\[([^[\]]+)\](?:\{([^[}]+)\})?/g;
	/*
	 * Javascript seems to parse the regex somewhat differently into groups ->
	 * thus shifting the final 2 indexes compared with PHP The parts are parsed
	 * to give the following groups 0 => entire string 1 => value itself 2 =>
	 * size given by regex question (null otherwise) 4 => feedback 5 => score
	 */
	var answersPattern = /(\{regex(?:=([0-9]+))?\}.+?\{\/regex\}|(?!\{regex(?:=([0-9]+))?\}).+?(?!\{\/regex\}))(?:\((.+?)\))?(?:=([+-]?[0-9]+))?(?:\||$)/g;

	function answerChanged(ev, ui) {
		var value = $(".answer").prop('value');
		var data = $("input[name=answer_data]");
		var rows = $(".table-data > tbody > tr");
		var css_class = 'row_even';

		var i = 0;
		var index = 0;

		tableElement.css('display', 'block');
		while (question = questionPattern.exec(value)) {
			var hint = (typeof question[2] == 'undefined') ? '' : question[2];

			while (answer = answersPattern.exec(question[1])) {
				var answertext = (typeof answer[1] == 'undefined') ? ''
						: answer[1];
				var feedback = (typeof answer[4] == 'undefined') ? ''
						: answer[4];
				var score = isNaN(answer[5]) ? '' : answer[5];

				if (index < rows.size()) {
					update_row(rows.eq(index), i + 1, answertext, feedback,
							hint, score, css_class);
				} else {
					insert_row(i + 1, answertext, feedback, hint, score,
							css_class);
				}

				index++;
			}

			css_class = css_class == 'row_even' ? 'row_odd' : 'row_even';

			i++;
		}

		while (rows.size() > index) {
			rows.eq(rows.size() - 1).remove();
			rows = $(".table-data > tbody > tr");
		}

		if (index == 0) {
			tableBody.empty();
			tableElement.css('display', 'none');
		}

		return true;
	}

	function update_row(row, index, answer, feedback, hint, score, css_class) {
		row.attr('class', css_class);
		cells = $('td', row);
		cells.eq(0).text(index);
		cells.eq(1).text(answer.replace('\n', '<br/>'));
		cells.eq(2).html(feedback.replace('\n', '<br/>'));
		cells.eq(3).html(hint.replace('\n', '<br/>'));
		cells.eq(4).text(score);
	}

	function insert_row(index, answer, feedback, hint, score, css_class) {
		var html = '';
		html += '<tr class="' + css_class + '">';
		html += '<td>' + index + '</td>';
		html += '<td>' + answer + '</td>';
		html += '<td>' + feedback + '</td>';
		html += '<td>' + hint + '</td>';
		html += '<td>' + score + '</td>';
		html += '</tr>';
		tableBody.append(html);
	}

	function lockWeight(ev, ui) {
		var checked = $(this).prop('checked');
		if (checked) {
			$('input[name="weight"]').prop('disabled', true);
		} else {
			$('input[name="weight"]').prop('disabled', false);
		}
	}

	$(document).ready(
			function() {
				// ADDITIONAL PROPERTIES
				$(document).on('click', '.type_0_option_selector', function() {
					$('.type_0_options_box').show();
					$('.type_1_options_box').hide();
				});
				$(document).on('click', '.type_1_option_selector', function() {
					$('.type_0_options_box').hide();
					$('.type_1_options_box').show();
				});
				$(document).on('click', '.type_2_option_selector', function() {
					$('.type_0_options_box').hide();
					$('.type_1_options_box').hide();
				});

				$('.type_0_option_selector').each(function() {
					if ($(this).prop('checked')) {
						$('.type_0_options_box').show();
						$('.type_1_options_box').hide();
					}
				});

				$('.type_1_option_selector').each(function() {
					if ($(this).prop('checked')) {
						$('.type_0_options_box').hide();
						$('.type_1_options_box').show();
					}
				});

				$('.type_2_option_selector').each(function() {
					if ($(this).prop('checked')) {
						$('.type_0_options_box').hide();
						$('.type_1_options_box').hide();
					}
				});

				// PARSE ANSWER
				tableElement = $("#answers_table");
				tableBody = $("tbody", tableElement);
				$(".answer").keyup(function() {
					// Avoid searches being started after every character
					clearTimeout(timer);
					timer = setTimeout(answerChanged, 750);
				});

				$(".add_matches").toggle();

				$(document).on('click', 'input[name="recalculate_weight"]',
						lockWeight);
			});

})(jQuery);