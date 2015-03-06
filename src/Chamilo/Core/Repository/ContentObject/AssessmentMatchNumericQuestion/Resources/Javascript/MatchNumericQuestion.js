/*global $, document, FCKeditor, renderFckEditor, getPath, getTranslation, getTheme */

$(function() {
	function addNumericOption(ev, ui) {
		ev.preventDefault();

		var numberOfOptions, newNumber, response, rowClass, id, fieldAnswer, fieldFeedback, fieldTolerance, fieldWeight, fieldDelete, string, parameters, editorName, highestOptionValue;

		numberOfOptions = $('#match_number_of_options').val();
		newNumber = parseInt(numberOfOptions, 10) + 1;

		$('#match_number_of_options').val(newNumber);

		rowClass = (numberOfOptions - skippedOptions) % 2 === 0 ? 'row_even'
				: 'row_odd';
		id = 'correct[' + numberOfOptions + ']';

		var visibleNumber = numberOfOptions - skippedOptions + 1;

		parameters = {
			"width" : "100%",
			"height" : "65",
			"toolbar" : "RepositoryQuestion",
			"collapse_toolbar" : true
		};
		editorName = 'comment[' + numberOfOptions + ']';

		fieldAnswer = '<textarea name="option[' + numberOfOptions
				+ ']" style="width: 100%; height: 65px;"></textarea>';
		fieldTolerance = '<input class="input_numeric" type="text" value="1" name="tolerance['
				+ numberOfOptions + ']" size="2" />';
		fieldFeedback = renderHtmlEditor(editorName, parameters);
		fieldWeight = '<input class="input_numeric" type="text" value="0" name="option_weight['
				+ numberOfOptions + ']" size="2" />';
		fieldDelete = '<input id="remove_' + numberOfOptions
				+ '" class="remove_option" type="image" src="'
				+ getDeleteIcon() + '" name="remove[' + numberOfOptions
				+ ']" />';
		string = '<tr id="option_' + numberOfOptions + '" class="' + rowClass
				+ '"><td>' + visibleNumber + '</td><td>' + fieldAnswer
				+ '</td><td>' + fieldTolerance + '</td><td>' + fieldFeedback
				+ '</td><td>' + fieldWeight + '</td><td>' + fieldDelete
				+ '</td></tr>';

		$('.data_table > tbody').append(string);

		processItems();

		highestOptionValue = $(
				'.data_table tbody tr:first select[name*="option_order"] option:last')
				.val();
		$('.data_table > tbody > tr:last select[name*="option_order"]').val(
				highestOptionValue);

		response = $.ajax({
			type : "POST",
			url : baseWebPath + "libraries/ajax/memory.php",
			data : {
				action : 'set',
				variable : 'match_number_of_options',
				value : newNumber
			},
			async : false
		}).responseText;

		return false;
	}

	function lockWeight(ev, ui) {
		var checked = $(this).attr('checked');
		if (checked == 'checked') {
			$('input[name="weight"]').attr('disabled', 'disabled');
		} else {
			$('input[name="weight"]').removeAttr('disabled');
		}
	}

	$(document).ready(
			function() {
				$(document).on('click', '#add_numeric_option',
						addNumericOption);
				$(document).on('click', 'input[name="recalculate_weight"]',
						lockWeight);
			});

});