$(function()
{
    var ajaxContext = 'Chamilo\\Core\\Home\\Ajax';
    var ajaxUri = getPath('WEB_PATH') + 'index.php';
    var translationContext = 'Chamilo\\Core\\Home';

    $(document).ready(function()
    {
        bindGeneralModeActions();
    });

    function bindGeneralModeActions()
    {
        $(document).on('click', ".portal-action-block-configure-target-entities", configureTargetEntities);
        $(document).on('click', ".portal-block-target-entities-form .btn[name=submit]", saveTargetEntities);
        $(document).on('click', ".portal-block-target-entities-form .btn[name=cancel]", cancelTargetEntities);
    }

    function configureTargetEntities(e, ui)
    {
        e.preventDefault();

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

    function cancelTargetEntities(e, ui)
    {
        e.preventDefault();

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

    function saveTargetEntities(e, ui)
    {
        e.preventDefault();

        // var form = $(this).parent().parent().parent().parent();
        // var block = form.parent().parent().parent();
        // var blockId = block.data('element-id');
        //
        // var submittedData = {};
        //
        // $(':input', form).each(function(index)
        // {
        //     var inputElement = $(this);
        //
        //     if (inputElement.attr('type') != 'radio' && inputElement.attr('type') != 'checkbox')
        //     {
        //         submittedData[inputElement.attr('name')] = inputElement.val();
        //     }
        //     else if (inputElement.attr('type') != 'radio' && inputElement.prop('checked') == true)
        //     {
        //         submittedData[inputElement.attr('name')] = inputElement.val();
        //     }
        //     else if (inputElement.attr('type') == 'checkbox')
        //     {
        //         if (inputElement.prop('checked') == true)
        //         {
        //             submittedData[inputElement.attr('name')] = inputElement.val();
        //         }
        //         else
        //         {
        //             submittedData[inputElement.attr('name')] = 0;
        //         }
        //     }
        // });
        //
        // var parameters = {
        //     'application' : ajaxContext,
        //     'go' : 'BlockConfig',
        //     'block' : blockId,
        //     'data' : submittedData
        // };
        //
        // var response = $.ajax({
        //     type : "POST",
        //     url : ajaxUri,
        //     data : parameters,
        //     async : false
        // }).success(function(json)
        // {
        //     if (json.result_code == 200)
        //     {
        //         block.before(json.properties.block).remove();
        //     }
        // });
    }
});