$(function ()
{
    $(document).ready(function ()
    {
        $(document).on('click', '.specific_rights_selector', function() { $('.specific_rights_selector_box').show(); } );
        $(document).on('click', '.inherit_rights_selector', function() { $('.specific_rights_selector_box').hide(); } );

        $(document).on('click', '.other_option_selected',  function() { $('.entity_selector_box',
            $(this).closest('.right')).hide(); } );

        $(document).on('click', '.entity_option_selected', function() { $('.entity_selector_box',
            $(this).closest('.right')).show(); } );

	$('.specific_rights_selector').each(function()
	{
	   if($(this).prop('checked'))
	   {
	       $('.specific_rights_selector_box').show();
	   }
	});

	$('.entity_option_selected').each(function()
	{
	    if($(this).prop('checked'))
	    {
		$('.entity_selector_box', $(this).closest('.right')).show();
	    }
	});
    });

});