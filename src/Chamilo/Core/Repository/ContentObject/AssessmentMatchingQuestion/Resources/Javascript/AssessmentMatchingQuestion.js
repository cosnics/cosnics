/*global $, document, FCKeditor, renderFckEditor, getPath, getTranslation, getTheme, doAjaxPost, setMemory */

$(function() {
	var skippedOptions = 0, skippedMatches = 0, labels = [ 'A', 'B', 'C', 'D',
			'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q',
			'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z' ];

	function getDeleteIconMatches() {
		return $('.data_table.matches > tbody > tr:first td:last .remove_match')
				.attr('src').replace('_na.png', '.png');
	}

	function getDeleteIconOptions() {
		return $(
				'.data_table.options > tbody > tr:first td:last .remove_option')
				.attr('src').replace('_na.png', '.png');
	}

	function getSelectOptions() {
		return $(
				'.data_table.options > tbody > tr:first select[name*="matches_to"]')
				.html();
	}

	function processMatches() {
		var deleteImage, deleteField, rows, counter = 0;

		deleteImage = '<img class="remove_match" src="'
				+ getDeleteIconMatches().replace('.png', '_na.png') + '"/>';
		deleteField = '<input id="remove_match_$option_number" class="remove_match" type="image" src="'
				+ getDeleteIconMatches()
				+ '" name="remove_match[$option_number]" />';
		rows = $('.data_table.matches > tbody > tr');

		if (rows.size() <= 2) {
			deleteField = deleteImage;
		}

		rows.each(function() {
			var labelField, labelFieldName, id, appendField;

			labelField = $('input[name*="match_label"]', this);
			labelFieldName = labelField.attr('name');
			id = labelFieldName.substr(12, labelFieldName.length - 13);

			appendField = deleteField.replace(/\$option_number/g, id);

			$('.remove_match', this).remove();
			$('td:last', this).append(appendField);
			$('td:first', this).html(
					labels[counter] + '<input type="hidden" value="'
							+ labels[counter] + '" name="' + labelFieldName
							+ '" />');

			counter += 1;
		});
	}

	function processOptions() {
		var deleteImage, deleteField, rows, counter = 1;

		deleteImage = '<img class="remove_option" src="'
				+ getDeleteIconOptions().replace('.png', '_na.png') + '"/>';
		deleteField = '<input id="remove_option_$option_number" class="remove_option" type="image" src="'
				+ getDeleteIconOptions()
				+ '" name="remove_option[$option_number]" />';
		rows = $('.data_table.options > tbody > tr');

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
			$('td:first', this).html(counter);

			counter += 1;
		});
	}

	function removeOption(ev, ui) {
		ev.preventDefault();

		var tableBody = $(this).parent().parent().parent(), id = $(this).attr(
				'id').replace('remove_option_', ''), row = 0, rows;

		destroyHtmlEditor('value[' + id + ']');
		destroyHtmlEditor('feedback[' + id + ']');
		$('tr#option_' + id, tableBody).remove();
		doAjaxPost(
				"./repository/content_object/assessment_matching_question/php/ajax/assessment_matching_question.php",
				{
					action : 'skip_option',
					value : id
				});

		rows = $('tr', tableBody);
		rows.each(function() {
			var rowClass = row % 2 === 0 ? 'row_even' : 'row_odd';
			$(this).attr('class', rowClass);
			row += 1;
		});

		skippedOptions += 1;
		processOptions();
	}

	function addOption(ev, ui) {
		ev.preventDefault();

		var numberOfOptions = $('#mq_number_of_options').val(), numberOfMatches = $(
				'#mq_number_of_matches').val(), newNumber = (parseInt(
				numberOfOptions, 10) + 1), rowClass = ((numberOfOptions - skippedOptions) % 2 === 0 ? 'row_even'
				: 'row_odd'), fieldOption = newNumber, fieldAnswer, fieldMatches, fieldComment, fieldScore, fieldDelete, string, parameters, editorNameAnswer, editorNameComment, counter = 0;

		setMemory('mq_number_of_options', newNumber);

		$('#mq_number_of_options').val(newNumber);

		parameters = {
			"width" : "100%",
			"height" : "65",
			"toolbar" : "RepositoryQuestion",
			"collapse_toolbar" : true
		};
		editorNameAnswer = 'value[' + numberOfOptions + ']';
		editorNameComment = 'feedback[' + numberOfOptions + ']';

		fieldMatches = '<select name="matches_to[' + numberOfOptions + ']">'
				+ getSelectOptions() + '</select>';
		fieldAnswer = renderHtmlEditor(editorNameAnswer, parameters);
		fieldComment = renderHtmlEditor(editorNameComment, parameters);
		fieldScore = '<input class="input_numeric" type="text" value="1" name="score['
				+ numberOfOptions + ']" size="2" />';
		fieldDelete = '<input id="remove_option_' + numberOfOptions
				+ '" class="remove_option" type="image" src="'
				+ getDeleteIconOptions() + '" name="remove_option['
				+ numberOfOptions + ']" />';

		string = '<tr id="option_' + numberOfOptions + '" class="' + rowClass
				+ '"><td>' + fieldOption + '</td><td>' + fieldAnswer
				+ '</td><td>' + fieldMatches + '</td><td>' + fieldComment
				+ '</td><td>' + fieldScore + '</td><td>' + fieldDelete
				+ '</td></tr>';

		$('.data_table.options > tbody').append(string);

		processOptions();
	}

	function removeMatch(ev, ui) {
		ev.preventDefault();

		var tableBody = $(this).parent().parent().parent(), id = $(this).attr(
				'id').replace('remove_match_', ''), row = 0, rows, selectBox;

		destroyHtmlEditor('match[' + id + ']');
		$('tr#match_' + id, tableBody).remove();

		doAjaxPost(
				"./repository/content_object/assessment_matching_question/php/ajax/assessment_matching_question.php",
				{
					action : 'skip_match',
					value : id
				});

		rows = $('tr', tableBody);
		rows.each(function() {
			var rowClass = row % 2 === 0 ? 'row_even' : 'row_odd';
			$(this).attr('class', rowClass);
			row += 1;
		});

		selectBox = $('.data_table.options select[name*="matches_to"]');
		$('option[value="' + id + '"]', selectBox).remove();

		selectBox.each(function() {
			var counter = 0;
			$('option', this).each(function() {
				$(this).text(labels[counter]);
				counter += 1;
			});
		});

		skippedMatches += 1;
		processMatches();
	}

	function addMatch(ev, ui) {
		ev.preventDefault();

		var numberOfMatches = $('#mq_number_of_matches').val(), newNumber = (parseInt(
				numberOfMatches, 10) + 1), rowClass = ((numberOfMatches - skippedMatches) % 2 === 0 ? 'row_even'
				: 'row_odd'), fieldOption, fieldAnswer, fieldDelete, string, selectBox, editorName, parameters;

		setMemory('mq_number_of_matches', newNumber);
		$('#mq_number_of_matches').val(newNumber);

		parameters = {
			"width" : "100%",
			"height" : "65",
			"toolbar" : "RepositoryQuestion",
			"collapse_toolbar" : true
		};
		editorName = 'match[' + numberOfMatches + ']';

		fieldOption = labels[newNumber] + '<input type="hidden" value="'
				+ labels[newNumber] + '" name="match_label[' + numberOfMatches
				+ ']" />';
		fieldAnswer = renderHtmlEditor(editorName, parameters);
		fieldDelete = '<input id="remove_match_' + numberOfMatches
				+ '" class="remove_match" type="image" src="'
				+ getDeleteIconMatches() + '" name="remove_match['
				+ numberOfMatches + ']" />';
		string = '<tr id="match_' + numberOfMatches + '" class="' + rowClass
				+ '"><td>' + fieldOption + '</td><td>' + fieldAnswer
				+ '</td><td>' + fieldDelete + '</td></tr>';

		$('.data_table.matches > tbody').append(string);

		selectBox = $('.data_table.options select[name*="matches_to"]');
		selectBox.append('<option value="' + numberOfMatches + '">'
				+ labels[numberOfMatches - skippedMatches] + '</option>');

		processMatches();
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
				$(document).on('click', '.remove_option', removeOption);
				$(document).on('click', '#add_option', addOption);

				$(document).on('click', '.remove_match', removeMatch);
				$(document).on('click', '#add_match', addMatch);

				$(document).on('click', 'input[name="recalculate_weight"]',
						lockWeight);
			});

});