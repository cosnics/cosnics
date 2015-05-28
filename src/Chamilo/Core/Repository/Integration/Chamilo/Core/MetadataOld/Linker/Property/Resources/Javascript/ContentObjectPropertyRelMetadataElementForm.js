    function changeContentObjectType()
    {
        var context = $('option:selected', this).attr('value');

        var property_names_select_box = $('.property_name_selector');
        property_names_select_box.html('');

        var context_property_names = property_names[context];
        for (var property_name in context_property_names)
        {
            var translation = context_property_names[property_name];
            var option = $('<option value="' + property_name + '">' + translation + '</option>');
            property_names_select_box.append(option);
        }

    }

    $(document).ready(function()
    {
        $('.type_selector').on('change', changeContentObjectType);
    });