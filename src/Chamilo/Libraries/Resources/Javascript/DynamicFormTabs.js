$(function() {
	
	var $tabs = $('#' + element + '_tabs').tabs();
	var selected = $tabs.tabs('option', 'selected');
	
	$(document).ready(function() {
		
		$(".previous").bind("click", function(e)
		{
			e.preventDefault();
			selected = selected - 1;
			$tabs.tabs("select",selected);

		});
		
		$(".next").bind("click", function(e)
		{
			e.preventDefault();
			selected = selected + 1;
			$tabs.tabs("select",selected);
		});
	});

});