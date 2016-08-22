$(function()
{
    var ajaxUri = getPath('WEB_PATH') + 'index.php';
    var translationContext = 'Chamilo\\Core\\Home';

    $(document).ready(function()
    {
        bindGeneralModeActions();
    });

    function bindGeneralModeActions()
    {
        $(document).on('click', ".portal-action-block-configure-target-entities", showTargetEntitiesConfigurationForm);
        $(document).on('click', ".portal-block-target-entities-form .btn[name=submit]", saveTargetEntities);
        $(document).on('click', ".portal-block-target-entities-form .btn[name=cancel]", hideTargetEntitiesConfigurationForm);
    }

    function showTargetEntitiesConfigurationForm(event)
    {
        if(event) {
            event.preventDefault();
        }

        var block = $(this).parent().parent().parent();
        blockId = block.data('element-id');

        var portalBlockForm = $('.portal-block-target-entities-form', block);
        var contentIsHidden = $('.portal-action-block-hide', block).hasClass('hidden');

        block.removeClass('panel-default').addClass('panel-info');
        $('.portal-action', block).hide();
        portalBlockForm.toggleClass('hidden');

        if (!contentIsHidden)
        {
            $('.portal-block-content', block).toggleClass('hidden');
        }
        else
        {
            $('.panel-heading', block).removeClass('panel-heading-without-content');
        }

        $('.panel-heading .panel-title', block).prepend(
            $('<span class="panel-title-target-entities">'
                + getTranslation('SelectTargetUsersGroupsFor', null, translationContext) + '</span>'));

    }

    function hideTargetEntitiesConfigurationForm(event)
    {
        if(event) {
            event.preventDefault();
        }

        var form = $(this).parent().parent().parent().parent();

        var portalBlockForm = form.parent().parent();
        var panel = portalBlockForm.parent();
        var contentIsHidden = $('.portal-action-block-hide', panel).hasClass('hidden');

        $(':reset', form).trigger("click");
        panel.removeClass('panel-info').addClass('panel-default');
        portalBlockForm.toggleClass('hidden');

        if (!contentIsHidden)
        {
            $('.portal-block-content', panel).toggleClass('hidden');
        }
        else
        {
            $('.panel-heading', panel).addClass('panel-heading-without-content');
        }

        $('.portal-action', panel).show();
        $('.panel-title-target-entities', panel).remove();

    }

    function saveTargetEntities(event)
    {
        if(event) {
            event.preventDefault();
        }

        var form = $(this).parent().parent().parent().parent();
        var formId = form.attr('id');

        var block = form.parent().parent().parent();
        var blockId = block.data('element-id');

        var selectedEntitiesField = $('input[name="active_hidden_' + formId + '_rights"]', form);

        var parameters = {
            'application' : 'Chamilo\\Core\\Home\\Rights\\Ajax',
            'go' : 'SetElementTargetEntities',
            'elementId' : blockId,
            'targetEntities' : selectedEntitiesField.val()
        };

        var response = $.ajax({
            type : "POST",
            url : ajaxUri,
            data : parameters,
            async : false
        }).success(function(json)
        {
            if (json.result_code == 200)
            {
                block.before(json.properties.block).remove();
            }
        });
    }
});