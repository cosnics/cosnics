(function($)
{
    
    function selectAll(event, userInterface)
    {
        event.preventDefault();
        $('input:checkbox[name^="target"]').prop('checked', true);
        $('input:checkbox.select-all').prop('checked', true);
    }
    
    function selectNone(event, userInterface)
    {
        event.preventDefault();
        $('input:checkbox[name^="target"]').prop('checked', false);
        $('input:checkbox.select-all').prop('checked', false);
    }
    
    function selectTable(event, userInterface)
    {
        var checkboxElement = $(this);
        var tableElement = checkboxElement.parent().parent().parent().parent().parent();
        
        if ($(this).prop('checked'))
        {
            $('input:checkbox[name^="target"]', tableElement).prop('checked', true);
        }
        else
        {
            $('input:checkbox[name^="target"]', tableElement).prop('checked', false);
        }
    }
    
    $(document).ready(function()
    {
        $(document).on("click", "a.select-all-checkboxes", selectAll);
        $(document).on("click", "a.select-no-checkboxes", selectNone);
        $(document).on("click", "input:checkbox.select-all", selectTable);
    })
})(jQuery);