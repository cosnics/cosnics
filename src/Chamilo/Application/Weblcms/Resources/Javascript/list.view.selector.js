$(function()
{
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
        $('.pid:checkbox').prop('checked', 'checked');
        toggleSelectButtons();
    }
    
    function unselectAllItems(e, ui)
    {
        e.preventDefault();
        $('.pid:checkbox').prop('checked', '');
        toggleSelectButtons();
    }
    
    $(document).ready(function()
    {
        toggleSelectButtons();
        $(document).on('click', ".btn.select-all", selectAllItems);
        $(document).on('click', ".btn.select-none", unselectAllItems);
        
        $(document).on('change', "form.form-list-view input:checkbox", toggleSelectButtons);
    });
});