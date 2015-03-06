$(document).ready(function()
{	
	$(".action_bar_hide_container").toggle();
	$(".action_bar .search_menu").css('padding-right', '20px');
	
	$(".action_bar_text").bind("click", showBlockScreen);
	$(".action_bar_hide").bind("click", hideBlockScreen);
	
	function showBlockScreen()
	{
		$(".action_bar_text").hide();
		$(".action_bar").slideToggle(300);
		
		return false;
	}
	
	function hideBlockScreen()
	{		
		$(".action_bar").slideToggle(300, function()
		{
			$(".action_bar_text").show();
		});
		
		return false;
	}
});