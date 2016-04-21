/*global $, document, renderFckEditor, getPath, getTranslation, getTheme, setMemory, doAjaxPost */

$(function() {

    var deleteInactive = '<button value="RemoveOption" name="remove[$option_number]" class="remove_option mini negative delete-na remove">RemoveOption</button>';
    var deleteActive = '<button type="submit" value="RemoveOption" name="remove[$option_number]" id="remove_$option_number" class="remove_option mini negative delete remove">RemoveOption</button>';
    var oldQuestionValue;

    function processOptions() {
        var deleteField, rows = $('.table-data > tbody > tr'), row = 0;

        rows = $('.table-data > tbody > tr');

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

    function convertType(ev, ui) {
        ev.preventDefault();

        var answerType = parseInt($('#answer_type').val(), 10), newLabel = getTranslation('SwitchToCheckboxes',
                'repository', 'repository\\content_object\\survey_multiple_choice_question'), newType = 1, counter = 0;

        if (answerType === 1) {
            newType = 2;
            newLabel = getTranslation('SwitchToRadioButtons', 'repository',
                    'repository\\content_object\\survey_multiple_choice_question');
        }

        $('#answer_type').val(newType);

        $('.switch.change_answer_type').val(newLabel);
        $('.switch.change_answer_type').text(newLabel);
    }

    function convertDisplayType(ev, ui) {
        ev.preventDefault();

        var displayType = parseInt($('#display_type').val(), 10), newLabel = getTranslation('SwitchToSelect',
                'repository', 'repository\\content_object\\survey_multiple_choice_question'), newType = 'select', counter = 0;

        if (displayType === 'select') {
            newType = 'table';
            newLabel = getTranslation('SwitchToTable', 'repository',
                    'repository\\content_object\\survey_multiple_choice_question');
        }

        $('#display_type').val(newType);

        $('.switch.change_display_type').val(newLabel);
        $('.switch.change_display_type').text(newLabel);
    }

    function removeOption(ev, ui) {
        ev.preventDefault();

        var tableBody = $(this).parent().parent().parent(), id = $(this).attr('id').replace('remove_', ''), rows, numberOfOptions = $(
                '#number_of_options').val(), question_id = $('#mc_id').val();

        var skippedOptions = unserialize($('#skipped_options').val())
        skippedOptions.push(id);

        $('tr#option_' + id, tableBody).remove();
        $('input[name="option[' + id + '][id]"]').remove();

        $('#skipped_options').val(serialize(skippedOptions));

        processOptions();
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
        fieldId = '<input type="hidden" name="option[' + numberOfOptions + '][id]">';
        fieldDisplayOrder = '<input type="hidden" name="option[' + numberOfOptions + '][display_order]">';

        fieldOptionValue = '<input type="text" name="option[' + numberOfOptions
                + '][option_value]" style="width: 95%" size="100">';

        fieldDelete = deleteActive;
        fieldDelete.replace(/\$option_number/g, numberOfOptions);

        string = '<tr id="option_' + numberOfOptions + '" class="row_even"><td>' + numberOfOptions + fieldDisplayOrder
                + '</td><td>' + fieldId + fieldOptionValue + '</td><td>' + fieldDelete + '</td></tr>';

        $('.table-data > tbody').append(string);

        processOptions();
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

        $(document).on('click', '.change_answer_type', convertType);
        $(document).on('click', '.change_display_type', convertDisplayType);
        $(document).on('click', '.remove_option', removeOption);
        $(document).on('click', '.add_option', addOption);
        $(document).on('focusin', '#question', saveOldQuestionValue);
        $(document).on('focusout', '#question', synchronizeTitle);
    });

});