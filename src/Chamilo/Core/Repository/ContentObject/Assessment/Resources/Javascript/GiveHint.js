(function($) {
    function showLinkHint(ev, ui) {
        var hintIdentifier = $(this).attr('id').replace('hint_', ''), ajaxUri = getPath('WEB_PATH') + 'index.php';

        var result = doAjaxPost(ajaxUri, {
            'application' : 'Chamilo\\Core\\Repository\\ContentObject\\Assessment\\Ajax',
            'go' : 'hint',
            'hint_identifier' : hintIdentifier,
            'user_id' : getMemory('_uid')
        });
        result = eval('(' + result + ')');

        $(this).after(result.properties.hint).remove();

        var hintQuestion = $('input[name="hint_question[' + hintIdentifier + ']"]');
        hintQuestion.val(parseInt(hintQuestion.val()) + 1);
    }

    $(document).ready(function() {
        $('a.hint_button').on('click', showLinkHint);
    });

})(jQuery);