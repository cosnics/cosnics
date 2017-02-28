$(function()
{
    var skippedOptions = 0;
    var ajaxContext = 'Chamilo\\Core\\Repository\\ContentObject\\AssessmentMultipleChoiceQuestion\\Ajax';
    var ajaxUri = getPath('WEB_PATH') + 'index.php';
    var translationContext = 'Chamilo\\Core\\Repository\\ContentObject\\AssessmentMultipleChoiceQuestion';
    
    function processOptions()
    {
        var deleteImage, deleteField, rows;
        
        rows = $('.table.table-assessment-question-form > tbody > tr');
        
        if (rows.size() <= 2)
        {
            $('.option-remove', rows).addClass('text-muted');
            $('.option-remove', rows).removeClass('text-danger');
        }
        else
        {
            $('.option-remove', rows).removeClass('text-muted');
            $('.option-remove', rows).addClass('text-danger');
        }
    }
    
    function convertType(ev, ui)
    {
        ev.preventDefault();
        
        var answerType = $('#mc_answer_type').val(), newLabel, newType, counter = 0;
        
        if (answerType == 1)
        {
            newType = 2;
            newLabel = getTranslation('SwitchToRadioButtons', null, translationContext);
        }
        else
        {
            newType = 1;
            newLabel = getTranslation('SwitchToCheckboxes', null, translationContext)
        }
        
        $('.option-value').each(function()
        {
            var inputField = $(this);
            var inputContainer = inputField.parent();
            
            if (newType == 1)
            {
                inputContainer.removeClass('checkbox');
                inputContainer.addClass('radio');
                
                inputField.attr('name', 'correct');
                inputField.attr('value', counter);
                inputField.attr('type', 'radio');
            }
            else
            {
                inputContainer.removeClass('radio');
                inputContainer.addClass('checkbox');
                
                inputField.attr('name', 'correct[' + counter + ']');
                inputField.attr('value', 1);
                inputField.attr('type', 'checkbox');
            }
            
            counter += 1;
        });
        
        $('#mc_answer_type').val(newType);
        setMemory('mc_answer_type', newType);
        
        $('.change-answer-type').attr('title', newLabel);
        $('.change-answer-type span:not(.inline-glyph)').text(newLabel);
    }
    
    function removeOption(ev, ui)
    {
        ev.preventDefault();
        
        var isConfirmed = confirm(getTranslation('ConfirmOptionDelete', null, translationContext));
        
        if (!isConfirmed)
        {
            return false;
        }
        
        var deleteButton = $(this);
        var optionIdentifier = deleteButton.data('option-id');
        
        destroyHtmlEditor('value[' + optionIdentifier + ']');
        destroyHtmlEditor('feedback[' + optionIdentifier + ']');
        $('.table.table-assessment-question-form tr[data-option-id="' + optionIdentifier + '"]').remove();
        
        var parameters = {
            'application' : ajaxContext,
            'go' : 'SkipOption',
            'option-number' : optionIdentifier
        };
        
        var response = $.ajax({
            type : "POST",
            url : ajaxUri,
            data : parameters
        }).success(function(json)
        {
            if (json.result_code == 200)
            {
                skippedOptions += 1;
                processOptions();
            }
        });
    }
    
    function addOption(ev, ui)
    {
        ev.preventDefault();
        
        var numberOfOptions = $('#mc_number_of_options').val(), newNumber = (parseInt(numberOfOptions, 10) + 1), mcAnswerType = $(
                '#mc_answer_type').val(), name, value, fieldOption, fieldAnswer, fieldComment, fieldScore, fieldDelete, string, htmlEditorOptions, editorNameAnswer, editorNameComment, type;
        
        htmlEditorOptions = {
            "width" : "100%",
            "height" : "65",
            "toolbar" : "RepositoryQuestion",
            "collapse_toolbar" : true
        };
        
        setMemory('mc_number_of_options', newNumber);
        $('#mc_number_of_options').val(newNumber);
        
        if (mcAnswerType == 1)
        {
            selectionName = 'correct';
            selectionValue = numberOfOptions;
            selectionType = 'radio';
        }
        else
        {
            selectionName = 'correct[' + numberOfOptions + ']';
            selectionValue = 1;
            selectionType = 'checkbox';
        }
        
        fieldSelection = '<div class="' + selectionType + '">';
        fieldSelection += '<input class="option-value" type="' + selectionType + '" value="' + selectionValue
                + '" name="' + selectionName + '" data-option-id="' + numberOfOptions + '" />';
        fieldSelection += '<label></label>';
        fieldSelection += '</div>';
        
        fieldAnswer = renderHtmlEditor('value[' + numberOfOptions + ']', htmlEditorOptions);

        var feedbackLabel = getTranslation('Feedback', null, translationContext);
        var scoreLabel = getTranslation('Score', null, translationContext);
        var deleteLabel = getTranslation('Delete', null, 'Chamilo\\Libraries');

        fieldFeedback = '<div class="option-feedback-field form-assessment-extra-container" data-element="feedback['
                + numberOfOptions + ']">';
        fieldFeedback += '<label>' + feedbackLabel + '</label>';
        fieldFeedback += renderHtmlEditor('feedback[' + numberOfOptions + ']', htmlEditorOptions);
        fieldFeedback += '</div>';
        
        fieldScore = '<div class="option-score-field form-assessment-extra-container form-inline" data-element="score['
                + numberOfOptions + ']">';
        fieldScore += '<label>' + scoreLabel + ':</label> ';
        fieldScore += '<input size="2" class="input_numeric form-control" name="score[' + numberOfOptions
                + ']" value="0" type="text">';
        fieldScore += '</div>';
        
        fieldActions = '<span title="' + feedbackLabel + '" data-option-id="' + numberOfOptions
                + '" class="option-action option-feedback fa fa-comment text-primary"></span>';
        fieldActions += '<br>';
        fieldActions += '<span title="' + scoreLabel + '" data-option-id="' + numberOfOptions
                + '" class="option-action option-score fa fa-percent text-primary"></span>';
        fieldActions += '<br>';
        fieldActions += '<span title="' + deleteLabel + '" data-option-id="' + numberOfOptions
                + '" class="option-action option-remove fa fa-trash text-danger"></span>';
        
        tableRow = '<tr data-option-id="' + numberOfOptions + '">';
        tableRow += '<td class="table-cell-selection cell-stat-x2">' + fieldSelection + '</td>';
        tableRow += '<td>' + fieldAnswer + fieldFeedback + fieldScore + '</td>';
        tableRow += '<td class="table-cell-action cell-stat-x2 text-right">' + fieldActions + '</td>';
        tableRow += '</tr>';
        
        $('.table.table-assessment-question-form > tbody').append(tableRow);
        
        processOptions();
    }
    
    function lockWeight(ev, ui)
    {
        var checked = $(this).prop('checked');
        $('input[name="weight"]').prop('disabled', (checked) ? true : false);
    }
    
    function toggleElement(element, fieldName)
    {
        var optionIdentifier = element.data('option-id');
        var row = $('.table.table-assessment-question-form > tbody > tr[data-option-id="' + optionIdentifier + '"]');
        
        var fieldContainer = $('div.option-' + fieldName + '-field', row);
        
        if (fieldContainer.is(':visible'))
        {
            fieldContainer.hide();
        }
        else
        {
            fieldContainer.show();
        }
    }
    
    function setDefaultScores()
    {
        $('input.option-value').each(function()
        {
            var inputField = $(this);
            var fieldIdentifier = inputField.data('option-id');
            var scoreField = $('input[name="score[' + fieldIdentifier + ']"]');
            var scoreValue = scoreField.val();
            var inputFieldIsChecked = inputField.is(':checked');
            
            if (inputFieldIsChecked && scoreValue <= 0)
            {
                scoreField.val(1);
            }
            else if (!inputFieldIsChecked && scoreValue > 0)
            {
                scoreField.val(0);
            }
        });
    }
    
    $(document).ready(function()
    {
        $(document).on('click', '.change-answer-type', convertType);
        $(document).on('click', '.add-option', addOption);
        $(document).on('click', 'input[name="recalculate_weight"]', lockWeight);
        $(document).on('click', 'input.option-value', setDefaultScores);
        
        // Options actions
        $(document).on('click', '.table.table-assessment-question-form .option-remove.text-danger', removeOption);
        
        $(document).on('click', '.table.table-assessment-question-form .option-feedback', function(ev, ui)
        {
            toggleElement($(this), 'feedback');
        });
        
        $(document).on('click', '.table.table-assessment-question-form .option-score', function(ev, ui)
        {
            toggleElement($(this), 'score');
        });
        
    });
    
});