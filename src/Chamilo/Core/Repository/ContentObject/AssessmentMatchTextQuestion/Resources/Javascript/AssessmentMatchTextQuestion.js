/*global $, document, FCKeditor, renderFckEditor, getPath, getTranslation, getTheme */

var skippedOptions = 0, baseWebPath = getPath('WEB_PATH'), currentNumberOfOptions;

function getDeleteIcon() {
	return $('.table-data > tbody > tr:first > td:last .remove_option').attr(
			'src').replace('_na.png', '.png');
}

function getSelectOptions() {
	return $('.table-data > tbody > tr:first select[name*="option_order"]')
			.html();
}

function processItems() {
	var deleteImage, deleteField, rows;

	deleteImage = '<img class="remove_option" src="'
			+ getDeleteIcon().replace('.png', '_na.png') + '"/>';
	deleteField = '<input id="remove_$option_number" class="remove_option" type="image" src="'
			+ getDeleteIcon() + '" name="remove[$option_number]" />';
	rows = $('.table-data > tbody > tr');

	if (rows.size() <= 1) {
		deleteField = deleteImage;
	}

	var i = 1;

	rows.each(function() {
		var weightField, weightFieldName, id, appendField;

		weightField = $('input[name*="option_weight"]', this);
		weightFieldName = weightField.attr('name');
		id = weightFieldName.substr(14, weightFieldName.length - 15);
		appendField = deleteField.replace(/\$option_number/g, id);

		$('.remove_option', this).remove();
		$('td:last', this).append(appendField);
		$('td:first', this).empty();
		$('td:first', this).append(i);

		i++;
	});

	currentNumberOfOptions = rows.size();
}

function removeOption(ev, ui) {
	ev.preventDefault();

	var tableBody, id, rows, row, response;

	tableBody = $(this).parent().parent().parent();
	id = $(this).attr('id');
	id = id.replace('remove_', '');
	destroyHtmlEditor('comment[' + id + ']');
	$('tr#option_' + id, tableBody).remove();

	rows = $('tr', tableBody);

	row = 0;

	response = $.ajax({
		type : "POST",
		url : baseWebPath + "libraries/ajax/match_question.php",
		data : {
			action : 'skip_match',
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

	var numberOfOptions, newNumber, response, rowClass, id, fieldAnswer, fieldFeedback, fieldWeight, fieldDelete, string, parameters, editorName, highestOptionValue;

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
	fieldFeedback = renderHtmlEditor(editorName, parameters);
	fieldWeight = '<input class="input_numeric" type="text" value="0" name="option_weight['
			+ numberOfOptions + ']" size="2" />';
	fieldDelete = '<input id="remove_' + numberOfOptions
			+ '" class="remove_option" type="image" src="' + getDeleteIcon()
			+ '" name="remove[' + numberOfOptions + ']" />';
	string = '<tr id="option_' + numberOfOptions + '" class="' + rowClass
			+ '"><td>' + visibleNumber + '</td><td>' + fieldAnswer
			+ '</td><td>' + fieldFeedback + '</td><td>' + fieldWeight
			+ '</td><td>' + fieldDelete + '</td></tr>';

	$('.table-data > tbody').append(string);

	processItems();

	highestOptionValue = $(
			'.table-data tbody tr:first select[name*="option_order"] option:last')
			.val();
	$('.table-data > tbody > tr:last select[name*="option_order"]').val(
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
}

function lockWeight(ev, ui) {
	var checked = $(this).prop('checked');
	if (checked) {
		$('input[name="weight"]').prop('disabled', true);
	} else {
		$('input[name="weight"]').prop('disabled', false);
	}
}

$(document).ready(function() {
	currentNumberOfOptions = $('.table-data tbody tr').size();
	$(document).on('click', '.remove_option', removeOption);
	$(document).on('click', '#add_option', addOption);
	$(document).on('click', 'input[name="recalculate_weight"]', lockWeight);
});
