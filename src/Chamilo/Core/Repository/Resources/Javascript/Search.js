(function($)
{

    function clear()
    {

        var parameter = $(this).parent();

        var ajaxUri = getPath('WEB_PATH') + 'index.php';

        var parameters = {
            'application' : 'Chamilo\\Core\\Repository\\Ajax',
            'go' : 'clear_parameter',
            'parameter' : parameter.attr('id'),
            'url' : location.href,
            'current_workspace_id' : parameter.parents('#search-parameters').data('current-workspace-id')
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
                parameter.remove();
                location.assign(json.properties.url);
            }
        });

    }

    $(document)
        .ready(
        function()
        {
            var creationDates = $("#creation_date_from,#creation_date_to")
                .datepicker(
                {
                    dateFormat : 'dd-mm-yy',
                    firstDay : 1,
                    changeMonth : true,
                    changeYear : true,
                    onSelect : function(selectedDate)
                    {
                        var option = (this.id == "creation_date_from") ? "minDate" : "maxDate", instance = $(
                            this).data("datepicker"), date = $.datepicker.parseDate(
                            instance.settings.dateFormat
                            || $.datepicker._defaults.dateFormat, selectedDate,
                            instance.settings);
                        creationDates.not(this).datepicker("option", option, date);
                    }
                });

            var modificationDates = $("#modification_date_from,#modification_date_to")
                .datepicker(
                {
                    dateFormat : 'dd-mm-yy',
                    firstDay : 1,
                    changeMonth : true,
                    changeYear : true,
                    onSelect : function(selectedDate)
                    {
                        var option = (this.id == "modification_date_from") ? "minDate"
                            : "maxDate", instance = $(this).data("datepicker"), date = $.datepicker
                            .parseDate(instance.settings.dateFormat
                            || $.datepicker._defaults.dateFormat, selectedDate,
                            instance.settings);
                        modificationDates.not(this).datepicker("option", option, date);
                    }
                });

            $(document).on("click", "div#search-parameters span.glyphicon.glyphicon-remove", clear);

        })
})(jQuery);
