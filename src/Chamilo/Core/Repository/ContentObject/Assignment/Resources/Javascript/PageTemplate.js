(function ($) {

    function activatePageTemplateWhenPageSelected() {
        var pageId = $('.inactive_elements .type_page').attr('id');
        console.log(pageId);

        var activeTypes = $('input[name="active_hidden_allowed_types"]').val();
        var includesPage = activeTypes.includes(pageId);
        if(includesPage) {
            $('.page_template').show();
        } else {
            $('.page_template').hide();
        }
    }

    $(document)
        .ready(
            function () {
                $('input[name="activate_allowed_types"]').on('click', function() {
                    setTimeout(activatePageTemplateWhenPageSelected, 100);
                });

                $('input[name="deactivate_allowed_types"]').on('click', function() {
                    setTimeout(activatePageTemplateWhenPageSelected, 100);
                });

                activatePageTemplateWhenPageSelected();
            }

        )
})(jQuery);
