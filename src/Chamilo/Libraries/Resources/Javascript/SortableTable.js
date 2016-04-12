(function($)
{
    /**
     * List of selected checkbox per table
     */
    var lastSelectedCheckboxes = new Array();
    
    /**
     * Allow multiple selects
     */
    function checkboxClicked(e)
    {
        var selectedTable = $(this).closest('.table');
        var selectedTableId = selectedTable.attr('id');
        
        var lastSelectedCheckbox = lastSelectedCheckboxes[selectedTableId];
        
        var allCheckboxes = $(':checkbox:not(.sortableTableSelectToggle)', selectedTable);
        
        /**
         * When shift is pressed then we start the multiple select process
         */
        if (e.shiftKey && lastSelectedCheckbox)
        {
            
            var currentCheckboxIndex = allCheckboxes.index($(this));
            var lastSelectedCheckboxIndex = allCheckboxes.index(lastSelectedCheckbox);
            
            // Determine the lowest and the highest selected index
            var start = Math.min(currentCheckboxIndex, lastSelectedCheckboxIndex);
            var end = Math.max(currentCheckboxIndex, lastSelectedCheckboxIndex);
            
            var lastChecked = lastSelectedCheckbox.prop('checked');
            
            // Add the selected class to all the items between the current and
            // last selected element
            for (var i = start; i <= end; i++)
            {
                allCheckboxes[i].checked = lastChecked;
            }
        }
        
        // Add the last selected element to the cache for this box
        lastSelectedCheckboxes[selectedTableId] = $(this);
        
        var allChecked = true;
        allCheckboxes.each(function()
        {
            console.log($(this).prop('checked'));
            if (!$(this).prop('checked'))
            {
                allChecked = false;
                return false;
            }
        });
        
        var form = $(this).closest('form');
        $('input.sortableTableSelectToggle', form).prop('checked', allChecked);
        
    }
    
    function selectToggle(event, userInterface)
    {
        var tableCheckbox = $(this);
        var newState = (tableCheckbox.prop('checked') == true) ? true : false;
        
        $('tbody :checkbox', $(this).parentsUntil('form').parent()).prop('checked', newState);
    }
    
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
    
    $(document)
            .ready(
                    function()
                    {
                        $('.table').on('click', ':checkbox:not(.sortableTableSelectToggle)', checkboxClicked);
                        $(document).on('click', 'input.sortableTableSelectToggle', selectToggle);
                        $(document)
                                .on(
                                        'click',
                                        'form.form-table a.btn.btn-table-action:not(.dropdown-toggle), form.form-table ul.dropdown-menu.btn-table-action > li > a',
                                        executeAction);
                    });
    
})(jQuery);