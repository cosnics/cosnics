(function($) {
    function form_submitted(evt, ui) {
        var table_name = $(this).attr('name');
        table_name = table_name.substr(5);

        var result = true;

        if (!any_checkbox_checked(table_name))
            return false;

        var actions = $('#actions_' + table_name);
        var selectedOption = $('option:selected', actions);
        var selectedClass = selectedOption.attr('class');

        if (selectedClass == 'confirm') {
        var confirmMessage = selectedOption.data('message');
        return confirm(confirmMessage);
        }
    }

    function any_checkbox_checked(table_name) {
        var result = false;
        $('.' + table_name + '_id:checked').each(function() {
            result = true;
            return false;
        });

        return result;
    }

    function ucfirst(string) {
        var f = string.charAt(0).toUpperCase();
        return f + string.substr(1);
    }

    function underscores_to_camelcase(string) {
        var array = string.split('_');
        var str = '';

        for (i = 0; i < array.length; i++) {
        str += ucfirst(array[i]);
        }

        return str;
    }

    /**
     * List of selected checkbox per table
     */
    var lastSelectedCheckboxes = new Array();

    /**
     * Allow multiple selects
     */
    function checkboxClicked(e) {
        var selectedTable = $(this).closest('.data_table');
        var selectedTableId = selectedTable.attr('id');

        var lastSelectedCheckbox = lastSelectedCheckboxes[selectedTableId];

        /**
         * When shift is pressed then we start the multiple select process
         */
        if (e.shiftKey && lastSelectedCheckbox) {
        // Clear all the selected elements in the box
        var allCheckboxes = $(':checkbox', selectedTable);

        var currentCheckboxIndex = allCheckboxes.index($(this));
        var lastSelectedCheckboxIndex = allCheckboxes.index(lastSelectedCheckbox);

        // Determine the lowest and the highest selected index
        var start = Math.min(currentCheckboxIndex, lastSelectedCheckboxIndex);
        var end = Math.max(currentCheckboxIndex, lastSelectedCheckboxIndex);

        var lastChecked = lastSelectedCheckbox.attr('checked');

        // Add the selected class to all the items between the current and
        // last selected element
        for (var i = start; i <= end; i++) {
        allCheckboxes[i].checked = lastChecked;
        }
        }

        // Add the last selected element to the cache for this box
        lastSelectedCheckboxes[selectedTableId] = $(this);
    }

    function selectAll(e, ui) {
        e.preventDefault();
        $(':checkbox', $(this).parentsUntil('form').parent()).attr('checked', true);
    }

    function selectNone(e, ui) {
        e.preventDefault();
        $(':checkbox', $(this).parentsUntil('form').parent()).attr('checked', false);
    }

    function changeAction(e, ui) {
        $(this).closest('form').attr('action', $(this).val());
    }

    $(document).ready(function() {
        $(document).on('submit', '.table_form', form_submitted);
        $('.data_table').on('click', ':checkbox', checkboxClicked);
        $(document).on('click', 'a.sortable_table_select_all', selectAll);
        $(document).on('click', 'a.sortable_table_select_none', selectNone);
        $(document).on('change', 'select', changeAction);
    });

})(jQuery);