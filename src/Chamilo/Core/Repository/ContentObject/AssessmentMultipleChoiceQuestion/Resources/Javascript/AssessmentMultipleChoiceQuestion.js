/*
 * global $, document, renderFckEditor, getPath, getTranslation, getTheme,
 * setMemory, doAjaxPost
 */

$(function() {
	var skippedOptions = 0;

	function getDeleteIcon() {
		return $('.data_table > tbody > tr:first td:last .remove_option').attr(
				'src').replace('_na.png', '.png');
	}

	function processOptions() {
		var deleteImage, deleteField, rows;

		deleteImage = '<img class="remove_option" src="'
				+ getDeleteIcon().replace('.png', '_na.png') + '"/>';
		deleteField = '<input id="remove_$option_number" class="remove_option" type="image" src="'
				+ getDeleteIcon() + '" name="remove[$option_number]" />';

		rows = $('.data_table > tbody > tr');

		if (rows.size() <= 2) {
			deleteField = deleteImage;
		}

		rows.each(function() {
			var weightField, weightFieldName, id, appendField;

			weightField = $('input[name*="score"]', this);
			weightFieldName = weightField.attr('name');
			id = weightFieldName.substr(6, weightFieldName.length - 7);
			appendField = deleteField.replace(/\$option_number/g, id);

			$('.remove_option', this).remove();
			$('td:last', this).append(appendField);
		});
	}

	function convertType(ev, ui) {
		ev.preventDefault();

		var answerType = $('#mc_answer_type').val(), newLabel = getTranslation(
				'SwitchToCheckboxes', null,
				'repository/content_object/assessment_multiple_choice_question'), newType = 1, counter = 0;

		if (answerType == 1) {
			newType = 2;
			newLabel = getTranslation('SwitchToRadioButtons', null,
					'repository/content_object/assessment_multiple_choice_question');
		}

		$('.value').each(
				function() {
					var id, correct, value, newField, parent, type;

					id = $(this).attr('id');
					correct = 'correct[' + counter + ']';
					value = 1;

					if (newType == 1) {
						correct = 'correct';
						value = counter;
						type = 'radio';
					} else {
						type = 'checkbox';
					}

					newField = '<input id="' + id + '" class="value" type="'
							+ type + '" value="' + value + '" name="' + correct
							+ '" />';
					parent = $(this).parent();
					parent.empty();
					parent.append(newField);
					counter += 1;

				});

		$('#mc_answer_type').val(newType);
		setMemory('mc_answer_type', newType);

		$('.switch').val(newLabel);
		$('.switch').text(newLabel);
	}

	function removeOption(ev, ui) {
		ev.preventDefault();

		var tableBody = $(this).parent().parent().parent(), id = $(this).attr(
				'id'), row = 0, answer_type = $('#mc_answer_type').val(), rows;

		id = id.replace('remove_', '');
		destroyHtmlEditor('value[' + id + ']');
		destroyHtmlEditor('feedback[' + id + ']');
		$('tr#option_' + id, tableBody).remove();

		rows = $('tr', tableBody);

		doAjaxPost(
				"./repository/content_object/assessment_multiple_choice_question/php/ajax/assessment_multiple_choice_question.php",
				{
					action : 'skip_option',
					value : id
				});

		rows.each(function() {
			var row_class = row % 2 === 0 ? 'row_even' : 'row_odd';
			$(this).attr('class', row_class);
			row += 1;
		});

		skippedOptions += 1;

		processOptions();
	}

	function addOption(ev, ui) {
		ev.preventDefault();

		var numberOfOptions = $('#mc_number_of_options').val(), newNumber = (parseInt(
				numberOfOptions, 10) + 1), mcAnswerType = $('#mc_answer_type')
				.val(), rowClass = (numberOfOptions - skippedOptions) % 2 === 0 ? 'row_even'
				: 'row_odd', name = 'correct[' + numberOfOptions + ']', id = name, value = 1, fieldOption, fieldAnswer, fieldComment, fieldScore, fieldDelete, string, parameters, editorNameAnswer, editorNameComment, type;

		setMemory('mc_number_of_options', newNumber);

		$('#mc_number_of_options').val(newNumber);

		if (mcAnswerType == 1) {
			name = 'correct';
			value = numberOfOptions;
			type = 'radio';
		} else {
			type = 'checkbox';
		}

		parameters = {
			"width" : "100%",
			"height" : "65",
			"toolbar" : "RepositoryQuestion",
			"collapse_toolbar" : true
		};
		editorNameAnswer = 'value[' + numberOfOptions + ']';
		editorNameComment = 'feedback[' + numberOfOptions + ']';

		fieldOption = '<input id="' + id + '" class="value" type="' + type
				+ '" value="' + value + '" name="' + name + '" />';
		fieldAnswer = renderHtmlEditor(editorNameAnswer, parameters);
		fieldComment = renderHtmlEditor(editorNameComment, parameters);
		fieldScore = '<input class="input_numeric" type="text" value="0" name="score['
				+ numberOfOptions + ']" size="2" />';
		fieldDelete = '<input id="remove_' + numberOfOptions
				+ '" class="remove_option" type="image" src="'
				+ getDeleteIcon() + '" name="remove[' + numberOfOptions
				+ ']" />';

		string = '<tr id="option_' + numberOfOptions + '" class="' + rowClass
				+ '"><td>' + fieldOption + '</td><td>' + fieldAnswer
				+ '</td><td>' + fieldComment + '</td><td>' + fieldScore
				+ '</td><td>' + fieldDelete + '</td></tr>';

		$('.data_table > tbody').append(string);

		processOptions();
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
				$(document).on('click', '.change_answer_type', convertType);
				$(document).on('click', '.remove_option', removeOption);
				$(document).on('click', '.add_option', addOption);
				$(document).on('click', 'input[name="recalculate_weight"]',
						lockWeight);
			});

});