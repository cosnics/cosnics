/*
 * global $, document, FCKeditor, renderFckEditor, getPath, getTranslation,
 * getTheme
 */

$(function() {
	var skippedOptions = 0, baseWebPath = getPath('WEB_PATH'), currentNumberOfOptions;

	function getDeleteIcon() {
		return $('.table-data > tbody > tr:first > td:last .remove_option')
				.attr('src').replace('_na.png', '.png');
	}

	function getSelectOptions() {
		return $('.table-data > tbody > tr:first select[name*="order"]').html();
	}

	function processItems() {
		var deleteImage, deleteField, rows;

		deleteImage = '<img class="remove_option" src="'
				+ getDeleteIcon().replace('.png', '_na.png') + '"/>';
		deleteField = '<input id="remove_$option_number" class="remove_option" type="image" src="'
				+ getDeleteIcon() + '" name="remove[$option_number]" />';
		rows = $('.table-data > tbody > tr');

		if (rows.size() <= 2) {
			deleteField = deleteImage;
		}

		rows.each(function() {
			var orderField, orderFieldName, id, appendField;

			orderField = $('select[name*="order"]', this);

			if (rows.size() > currentNumberOfOptions) {
				orderField.append($('<option value="' + rows.size() + '">'
						+ rows.size() + '</option>'));
			} else {
				$('option:last', orderField).remove();
			}
			orderFieldName = orderField.attr('name');
			id = orderFieldName.substr(6, orderFieldName.length - 7);
			appendField = deleteField.replace(/\$option_number/g, id);

			$('.remove_option', this).remove();
			$('td:last', this).append(appendField);
		});

		currentNumberOfOptions = rows.size();
	}

	function removeOption(ev, ui) {
		ev.preventDefault();

		var tableBody, id, rows, row, response;

		tableBody = $(this).parent().parent().parent();
		id = $(this).attr('id');
		id = id.replace('remove_', '');
		destroyHtmlEditor('value[' + id + ']');
		destroyHtmlEditor('feedback[' + id + ']');
		$('tr#option_' + id, tableBody).remove();

		rows = $('.table-data > tbody > tr');

		row = 0;

		response = $.ajax({
			type : "POST",
			url : baseWebPath + "libraries/ajax/memory.php",
			data : {
				action : 'skip_option',
				value : id
			},
			async : false
		}).responseText;

		rows.each(function() {
			var rowClass = row % 2 === 0 ? 'row_even' : 'row_odd';
			$(this).attr('class', rowClass);
			row += 1;
		});

		skippedOptions += 1;

		processItems();
	}

	function addOption(ev, ui) {
		ev.preventDefault();

		var numberOfOptions, newNumber, response, rowClass, id, fieldAnswer, fieldOrder, fieldDelete, string, parameters, editorName, highestOptionValue;

		numberOfOptions = $('#ordering_number_of_options').val();
		newNumber = parseInt(numberOfOptions, 10) + 1;

		setMemory('ordering_number_of_options', newNumber);

		$('#ordering_number_of_options').val(newNumber);

		rowClass = (numberOfOptions - skippedOptions) % 2 === 0 ? 'row_even'
				: 'row_odd';
		id = 'correct[' + numberOfOptions + ']';

		parameters = {
			"width" : "100%",
			"height" : "65",
			"collapse_toolbar" : true
		};

		fieldAnswer = renderHtmlEditor('value[' + numberOfOptions + ']',
				parameters);
		fieldOrder = '<select name="order[' + numberOfOptions + ']">'
				+ getSelectOptions() + '</select>';
		fieldFeedback = renderHtmlEditor('feedback[' + numberOfOptions + ']',
				parameters);
		fieldScore = '<input class="input_numeric" type="text" value="1" name="score['
				+ numberOfOptions + ']" size="2" />';
		fieldDelete = '<input id="remove_' + numberOfOptions
				+ '" class="remove_option" type="image" src="'
				+ getDeleteIcon() + '" name="remove[' + numberOfOptions
				+ ']" />';

		string = '<tr id="option_' + numberOfOptions + '" class="' + rowClass
				+ '"><td>' + fieldAnswer + '</td><td>' + fieldOrder
				+ '</td><td>' + fieldFeedback + '</td><td>' + fieldScore
				+ '</td><td>' + fieldDelete + '</td></tr>';

		$('.table-data > tbody').append(string);

		processItems();

		highestOptionValue = $(
				'.table-data tbody tr:first select[name*="order"] option:last')
				.val();
		$('.table-data > tbody > tr:last select[name*="order"]').val(
				highestOptionValue);
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
				currentNumberOfOptions = $('.table-data tbody tr').size();
				$(document).on('click', '.remove_option', removeOption);
				$(document).on('click', '#add_option', addOption);
				$(document).on('click', 'input[name="recalculate_weight"]',
						lockWeight);
			});

});