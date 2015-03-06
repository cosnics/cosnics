$(document).ready(function()
{
	var menuHeight = $("div.action_bar_left").height();
	var newMarginTop = '-' + (menuHeight / 2) + 'px';
	
	$("div.action_bar_left").css('margin-top', newMarginTop);
	
	$(".action_bar_left_hide_container").toggle();
	
	$(".action_bar_left_hide").bind("click", hideBlockScreenLeft);
	$(".action_bar_left_show").bind("click", showBlockScreenLeft);
	
	$(".action_bar_browser").css('margin-left', '230px');
	
	function hideBlockScreenLeft()
	{
		var id = $(this).attr('id').replace('_action_bar_left_hide', '');;
		
		$("#"+ id +"_action_bar_left_hide_container").attr('class', 'action_bar_left_hide_container show');
		$("#"+ id +"_action_bar_left").animate(
			{
				left: "-231px"
			}
			, 300, function()
				{
					$("#"+ id +"_action_bar_left_hide").toggle();
					$("#"+ id +"_action_bar_left_show").toggle();
				}
		);
		$("#"+ id +"_action_bar_browser").animate({marginLeft: "0px"}, 300);
		
		return false;
	}
	
	function showBlockScreenLeft()
	{
		var id = $(this).attr('id').replace('_action_bar_left_show', '');
		
		$("#"+ id +"_action_bar_left_hide_container").attr('class', 'action_bar_left_hide_container hide');
		$("#"+ id +"_action_bar_left").animate(
			{
				left: "0px"
			}
			, 300, function()
				{
					$("#"+ id +"_action_bar_left_hide").toggle();
					$("#"+ id +"_action_bar_left_show").toggle();
				}
		);
		
		$("#"+ id +"_action_bar_browser").animate({marginLeft: "230px"}, 300);
		
		return false;
	}
});