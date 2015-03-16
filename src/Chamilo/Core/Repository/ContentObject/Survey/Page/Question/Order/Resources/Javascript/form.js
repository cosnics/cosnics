/*global $, document, renderFckEditor, getPath, getTranslation, getTheme, setMemory, doAjaxPost */

$(function() {

	var deleteInactive = '<button value="RemoveOption" name="remove[$option_number]" class="remove_option mini negative delete-na remove">RemoveOption</button>';
	var deleteActive = '<button type="submit" value="RemoveOption" name="remove[$option_number]" id="remove_$option_number" class="remove_option mini negative delete remove">RemoveOption</button>';
	var oldQuestionValue;

	function processOptions() {
		var deleteField, rows = $('.data_table > tbody > tr'), row = 0;

		rows = $('.data_table > tbody > tr');

		if (rows.size() <= 2) {
			deleteField = deleteInactive;
		} else {
			deleteField = deleteActive;
		}

		rows.each(function() {
			var rowName, id, appendField;

			var id = $(this).attr('id').replace('option_', '');

			// Replace the remove button if necessary
			appendField = deleteField.replace(/\$option_number/g, row);
			$('.remove_option', this).remove();
			$('td:last', this).append(appendField);

			// Set the CSS class
			var rowClass = row % 2 === 0 ? 'row_even' : 'row_odd';
			$(this).attr('class', rowClass);

			// Set the display order
			var displayOrderCell = $('td', this).first();
			var displayOrderField = $('input', displayOrderCell);

			displayOrderField.val(row + 1);
			displayOrderCell.html(row + 1);
			displayOrderCell.append(displayOrderField);

			row += 1;
		});
	}

	function convertDisplayType(ev, ui) {
		ev.preventDefault();

		var displayType = $('#display_type').val(), newLabel = getTranslation(
				'SwitchToOneColumn', 'repository',
				'repository\\content_object\\survey_order_question'), newType = 'two_column', counter = 0, order_limit = $('#order_limit');

		if (displayType === 'two_column') {
			newType = 'one_column';
			newLabel = getTranslation('SwitchToTwoColumn', 'repository',
					'repository\\content_object\\survey_order_question');
		}

		$('#display_type').val(newType);

		$('.switch.change_display_type').val(newLabel);
		$('.switch.change_display_type').text(newLabel);
	}

	function processOrderLimit() {

		var order_limit = $('select[name="order_limit"]'), number_of_options = $(
				'.data_table > tbody > tr').size();
		count = 0;

		var selected_id = order_limit.children('[selected="selected"]').val();

		alert(selected_id);

		order_limit.children().remove();
		while (count <= number_of_options) {
			var option;
			if (selected_id == count) {
				option = '<option selected="selected" value="' + count + '">'
						+ count + '</option>';
			} else {
				option = '<option value="' + count + '">' + count + '</option>';
			}
			order_limit.append(option);
			count++;
		}

	}

	function changeSelect(ev, ui){
		
		alert('test');
		
	}
	
	function removeOption(ev, ui) {
		ev.preventDefault();

		var tableBody = $(this).parent().parent().parent(), id = $(this).attr(
				'id').replace('remove_', ''), rows, numberOfOptions = $(
				'#number_of_options').val(), question_id = $('#mc_id').val();

		var skippedOptions = unserialize($('#skipped_options').val())
		skippedOptions.push(id);

		$('tr#option_' + id, tableBody).remove();
		$('input[name="option[' + id + '][id]"]').remove();

		$('#skipped_options').val(serialize(skippedOptions));

		processOptions();
		processOrderLimit();
	}

	function addOption(ev, ui) {
		ev.preventDefault();

		var numberOfOptions, newNumberOfOptions, fieldId, fieldDisplayOrder, fieldOptionValue, fieldDelete, string;

		// Determine the new number of options
		numberOfOptions = $('#number_of_options').val();
		newNumberOfOptions = (parseInt(numberOfOptions, 10) + 1);

		// Set the new number op options in the form
		$('#number_of_options').val(newNumberOfOptions);

		// Build the new form row and add it
		fieldId = '<input type="hidden" name="option[' + numberOfOptions
				+ '][id]">';
		fieldDisplayOrder = '<input type="hidden" name="option['
				+ numberOfOptions + '][display_order]">';

		fieldOptionValue = '<input type="text" name="option[' + numberOfOptions
				+ '][option_value]" style="width: 95%" size="100">';

		fieldDelete = deleteActive;
		fieldDelete.replace(/\$option_number/g, numberOfOptions);

		string = '<tr id="option_' + numberOfOptions
				+ '" class="row_even"><td>' + numberOfOptions
				+ fieldDisplayOrder + '</td><td>' + fieldId + fieldOptionValue
				+ '</td><td>' + fieldDelete + '</td></tr>';

		$('.data_table > tbody').append(string);

		processOptions();
		processOrderLimit();
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

	$(document).ready(function() {

		$(document).on('click', '.change_display_type', convertDisplayType);
		$(document).on('click', '.remove_option', removeOption);
		$(document).on('click', '.add_option', addOption);
		$(document).on('focusin', '#question', saveOldQuestionValue);
		$(document).on('focusout', '#question', synchronizeTitle);
		$(document).on('change', 'select[name="order_limit"]', changeSelect);
	});

});