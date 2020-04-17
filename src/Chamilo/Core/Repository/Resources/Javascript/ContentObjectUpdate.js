(function ($) {
    function contentObjectUpdate(ev, ui) {
        var new_version = $('input[type=checkbox].version').attr('checked');
        if (new_version == 1) {
            return true;
        }

        new_version = $('input[type=hidden].version').attr('value');
        if (new_version == 1) {
            return true;
        }

        var modification_date = $('input[type=hidden].modification_date').attr('value');
        var content_object_id = $('input[type=hidden].content_object_id').attr('value');

        var ajaxUri = getPath('WEB_PATH') + 'index.php';

        var result = doAjaxPost(
            ajaxUri,
            {
                'application': 'Chamilo\\Core\\Repository\\Ajax',
                'go': 'content_object_update',
                'content_object_id': content_object_id,
                'modification_date': modification_date
            }
        );
        result = eval('(' + result + ')');

        var allow_update = result.properties.allow_update;
        if (!allow_update) {
            var confirm_result = confirm(getTranslation('ContentObjectUpdateOutOfDate', null, 'core\\repository'));
            if (confirm_result) {
                $('input[type=checkbox].version').attr('checked', 'checked');
                return true;
            }
            else {
                return false;
            }
        }

        return true;
    }

    $(document).ready(function () {
        $(document).on('click', 'input:checkbox.version', function () {
            $('.content-object-version-comment').toggleClass('hidden');
        });

        $(document).on('click', 'button.update', contentObjectUpdate);
    });

})(jQuery);