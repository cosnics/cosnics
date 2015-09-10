(function($)
{
    function showLinkHint(ev, ui)
    {
        var ajaxUri = getPath('WEB_PATH') + 'index.php';

        var hintId = $(this).attr('id');
        var hintType = (hintId.substr(0, 1) == 'h' ? 1 : 2);
        var hintIdentifier = (hintType == 1 ? hintId.replace('hint_', '') : hintId.replace('answer_hint_', ''));
        var hintIdentifierParts = hintIdentifier.split('_');

        var result = doAjaxPost(ajaxUri, {
            'application' : 'Chamilo\\Core\\Repository\\ContentObject\\FillInBlanksQuestion\\Ajax',
            'go' : 'hint',
            'hint_type' : hintType,
            'hint_identifier' : hintIdentifier
        });
        result = eval('(' + result + ')');

        switch (hintType)
        {
            case 1:
                $(this).remove();
                $('input[name="' + hintIdentifier + '"]').val(result.properties.hint);

                var table = $(this).parent().parent().parent().parent();
                var hintButtonCount = $('a[id^="hint_"]', table).length;
                var $columnLength = false;

                $('tr', table).each(function(index)
                {
                    $columnLength += $('td:nth-child(3)', $(this)).html().length();
                });

                if (hintButtonCount == 1 && $columnLength == 0)
                {
                    $('th:nth-child(3)', table).remove();
                    $('tr', table).each(function(index)
                    {
                        $('td:nth-child(3)', $(this)).remove();
                    });
                }

                break;
            case 2:
                var parentElement = $(this).parent();

                if (parentElement.is('div'))
                {
                    var table = $('table#hint_table_' + hintIdentifierParts[0]);
                    var number = parseInt(hintIdentifierParts[1]) + 1;
                    table.show();
                    $('tbody', table).append('<tr><td>' + number + '.</td><td>' + result.properties.hint + '</td></tr>');
                    $(this).after('<span class="inline_answer_hint">Hint ' + number + '</span>').remove();
                }
                else
                {
                    $(this).after(result.properties.hint).remove();
                }
                
                break;
        }
        
        var hintQuestion = $('input[name="hint_question[' + hintIdentifierParts[0] + ']"]');
        hintQuestion.val(parseInt(hintQuestion.val()) + 1);
    }

    $(document).ready(function()
    {
        $(document).on('click', 'a.blanks_hint_button', showLinkHint);
    });

})(jQuery);