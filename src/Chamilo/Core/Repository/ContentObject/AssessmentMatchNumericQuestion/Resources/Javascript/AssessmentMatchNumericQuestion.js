$(function()
{
    var skippedOptions = 0;
    var ajaxContext = 'Chamilo\\Core\\Repository\\ContentObject\\AssessmentMatchTextQuestion\\Ajax';
    var ajaxUri = getPath('WEB_PATH') + 'index.php';
    var translationContext = 'Chamilo\\Core\\Repository\\ContentObject\\AssessmentMatchTextQuestion';
    
    function processOptions()
    {
        var deleteImage, deleteField, rows;
        
        rows = $('.table.table-assessment-question-form > tbody > tr');
        
        if (rows.size() <= 1)
        {
            $('.option-remove', rows).addClass('text-muted');
            $('.option-remove', rows).removeClass('text-danger');
        }
        else
        {
            $('.option-remove', rows).removeClass('text-muted');
            $('.option-remove', rows).addClass('text-danger');
        }
        
        rows.each(function(index)
        {
            var row = $(this);
            
            $('.table-cell-selection', row).text((parseInt(index, 10) + 1) + '.');
        });
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
        
        var numberOfOptions = $('#match_number_of_options').val(), newNumber = (parseInt(numberOfOptions, 10) + 1), name, value, fieldOption, fieldAnswer, fieldComment, fieldScore, fieldDelete, string, htmlEditorOptions, editorNameAnswer, editorNameComment, type;
        
        htmlEditorOptions = {
            "width" : "100%",
            "height" : "65",
            "toolbar" : "RepositoryQuestion",
            "collapse_toolbar" : true
        };
        
        setMemory('match_number_of_options', newNumber);
        $('#match_number_of_options').val(newNumber);
        
        fieldSelection = newNumber + '.';
        
        fieldAnswer = '<textarea class="form-control" style="height: 80px;" name="value[' + numberOfOptions
                + ']"></textarea>';
        
        fieldFeedback = '<div class="option-feedback-field form-assessment-extra-container" data-element="feedback['
                + numberOfOptions + ']">';
        fieldFeedback += '<label>' + getTranslation('Feedback', null, translationContext) + '</label>';
        fieldFeedback += renderHtmlEditor('feedback[' + numberOfOptions + ']', htmlEditorOptions);
        fieldFeedback += '</div>';
        
        fieldScore = '<div class="option-score-field assessment_match_question_score_container form-inline" data-element="score['
                + numberOfOptions + ']">';
        fieldScore += '<label>' + getTranslation('Score', null, translationContext) + ':</label> ';
        fieldScore += '<input size="2" class="input_numeric form-control" name="score[' + numberOfOptions
                + ']" value="1" type="text">';
        fieldScore += '</div>';
        
        fieldTolerance = '<div class="option-tolerance-field form-assessment-extra-container form-inline" data-element="score['
                + numberOfOptions + ']">';
        fieldTolerance += '<label>' + getTranslation('Tolerance', null, translationContext) + ':</label> ';
        fieldTolerance += '<input size="2" class="input_numeric form-control" name="score[' + numberOfOptions
                + ']" value="0" type="text">';
        fieldTolerance += '</div>';
        
        fieldActions = '<span data-option-id="' + numberOfOptions
                + '" class="option-action option-feedback fa fa-comment text-primary"></span>&nbsp;&nbsp;';
        fieldActions += '<span data-option-id="' + numberOfOptions
                + '" class="option-action option-tolerance fa fa-magnet text-primary"></span>&nbsp;&nbsp;';
        fieldActions += '<span data-option-id="' + numberOfOptions
                + '" class="option-action option-remove fa fa-trash text-danger"></span>';
        
        tableRow = '<tr data-option-id="' + numberOfOptions + '">';
        tableRow += '<td class="table-cell-selection cell-stat-x3">' + fieldSelection + '</td>';
        tableRow += '<td>' + fieldAnswer + fieldScore + fieldFeedback + fieldTolerance + '</td>';
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
    
    $(document).ready(function()
    {
        $(document).on('click', '.add-option', addOption);
        $(document).on('click', 'input[name="recalculate_weight"]', lockWeight);
        
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
        
        $(document).on('click', '.table.table-assessment-question-form .option-tolerance', function(ev, ui)
        {
            toggleElement($(this), 'tolerance');
        });
        
    });
    
});
