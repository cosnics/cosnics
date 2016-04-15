$(function()
{
    
    function executeAction(event)
    {
        if (event.isDefaultPrevented())
        {
            return false;
        }
        
        event.preventDefault();
        
        var form = $(this).closest('form');
        
        if (!$('input:checked', form).length > 0)
        {
            
            return false;
        }
        
        form.prop('action', $(this).prop('href'));
        $('input[type="submit"]', form).click();
    }
    
    function toggleSelectButtons()
    {
        $("form.form-list-view").each(function()
        {
            var form = $(this);
            var hasUncheckedItems = $('input:checkbox:not(:checked)').length > 0;
            
            if (hasUncheckedItems)
            {
                $('.btn.select-none').hide();
                $('.btn.select-all').show();
            }
            else
            {
                $('.btn.select-all').hide();
                $('.btn.select-none').show();
            }
        });
    }
    
    function selectAllItems(e, ui)
    {
        e.preventDefault();
        $('.publication-select:checkbox').prop('checked', 'checked');
        toggleSelectButtons();
    }
    
    function unselectAllItems(e, ui)
    {
        e.preventDefault();
        $('.publication-select:checkbox').prop('checked', '');
        toggleSelectButtons();
    }
    
    $(document)
            .ready(
                    function()
                    {
                        toggleSelectButtons();
                        $(document).on('click', ".btn.select-all", selectAllItems);
                        $(document).on('click', ".btn.select-none", unselectAllItems);
                        
                        $(document).on('change', "form.form-list-view input:checkbox", toggleSelectButtons);
                        
                        $(document)
                                .on(
                                        'click',
                                        'form.form-list-view a.btn.btn-table-action:not(.dropdown-toggle):not(.select-all):not(.select-none), form.form-list-view ul.dropdown-menu.btn-table-action > li > a',
                                        executeAction);
                    });
});