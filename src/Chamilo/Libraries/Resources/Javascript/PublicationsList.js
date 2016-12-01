(function ($)
{
	function form_submitted(evt, ui)
	{
		var result = true;
		
		if(!any_checkbox_checked())
			return false;
		
		var actions = $('#tool_actions');
		var selectedOption = $('option:selected', actions);
		var selectedClass = selectedOption.attr('class');
		
		var selectedValue = selectedOption.attr('value');
		selectedValue = underscores_to_camelcase(selectedValue);

		if(selectedClass == 'confirm')
		{
			return confirm(translation(selectedValue + 'Confirm'));
		}
	}
	
	function any_checkbox_checked()
	{
		var result = false;
		$('.pid:checked').each(function () 
		{ 
            result = true;
            return false;
        });
	
		return result;
	}
	
	$(document).ready(function () 
    {
		$(document).on('submit', '.publication_list', form_submitted);
        $(document).on('change', 'form.publication_list select', changeAction);
	});
	

    function changeAction(e, ui) {
        $(this).closest('form').attr('action', $(this).val());
    }


    function translation(string, application) {
		var translated_string = $.ajax({
			type: "POST",
			url: "./libraries/ajax/translation.php",
			data: { string: string, application: application },
			async: false
		}).responseText;

		return translated_string;
	}
    
    function ucfirst(string)
    {
    	var f = string.charAt(0).toUpperCase();
		return f + string.substr(1);
    }
    
    function underscores_to_camelcase(string)
    {
    	var array = string.split('_');
    	var str = '';
    	
    	for(i = 0; i < array.length; i++)
    	{
    		str += ucfirst(array[i]);
    	}
    	
    	return str;
    }

})(jQuery);