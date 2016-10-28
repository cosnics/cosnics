
$(document).ready(function()
{
    //$('.collapsed .row').hide();
    $('div.collapsible .category a').bind('click', me_toggle);

    function me_toggle()
    {
        var div = $(this).parent().parent();
        div.find('.collapsible .row').toggle();

        if(div.is('.collapsed')){
            div.removeClass('collapsed');
            div.addClass('expanded');
        }else{
            div.addClass('collapsed');
            div.removeClass('expanded');
        }

        return false;
    }

});