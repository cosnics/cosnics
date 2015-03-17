$(function ()
{
	var tool = $("#tool_bar").attr('class');
	var originalWidth = $("#tool_bar").outerWidth();
	
	function toggleButtons()
	{
		$("#tool_bar_hide").toggle();
		$("#tool_bar_show").toggle();
	}
	
	function hideBlockScreen()
	{
		$("#tool_bar_hide_container").attr('class', 'show');
		
		switch(tool)
		{
			case 'tool_bar tool_bar_left':
				$("div.tool_bar_left").animate({left: "-" + originalWidth + "px"}, 300, function(){
					$("#tool_browser_left").animate({marginLeft: "10px"}, 300, toggleButtons);
				});
				break;
			case 'tool_bar tool_bar_icon_left':
				$("div.tool_bar_icon_left").animate({left: "-" + originalWidth + "px"}, 300, function(){
					$("#tool_browser_icon_left").animate({marginLeft: "0px"}, 300, toggleButtons);
				});
				break;
			case 'tool_bar tool_bar_right':
				$("div.tool_bar_right").animate({right: "-" + originalWidth + "px"}, 300, function(){
					$("#tool_browser_right").animate({marginRight: "0px"}, 300, toggleButtons);
				});
				break;
			case 'tool_bar tool_bar_icon_right':
				$("div.tool_bar_icon_right").animate({right: "-" + originalWidth + "px"}, 300, function(){
					$("#tool_browser_icon_right").animate({marginRight: "0px"}, 300, toggleButtons);
				});
				break;
		}
		
		$.ajax({
			type: "POST",
			url: "./libraries/ajax/toolbar_memory.php",
			data: { state: 'hide'},
			async: false
		})
	}
	
	function immediateHideBlockScreen()
	{
		$("#tool_bar_hide_container").attr('class', 'show');
		
		switch(tool)
		{
			case 'tool_bar tool_bar_left':
				$("div.tool_bar_left").css("left", "-" + originalWidth + "px");
				$("#tool_browser_left").css("margin-left", "10px");
				break;
			case 'tool_bar tool_bar_icon_left':
				$("div.tool_bar_icon_left").css("left", "-" + originalWidth + "px");
				$("#tool_browser_icon_left").css("margin-left", "0px");
				break;
			case 'tool_bar tool_bar_right':
				$("div.tool_bar_right").css("right", "-" + originalWidth + "px");
				$("#tool_browser_right").css("margin-right", "0px");
				break;
			case 'tool_bar tool_bar_icon_right':
				$("div.tool_bar_icon_right").css("right", "-" + originalWidth + "px");
				$("#tool_browser_icon_right").css("margin-right", "0px");
				break;
		}
		
		toggleButtons();
		
		$.ajax({
			type: "POST",
			url: "./libraries/ajax/toolbar_memory.php",
			data: { state: 'hide'},
			async: false
		})
	}
	
	function showBlockScreen()
	{
		$("#tool_bar_hide_container").attr('class', 'hide');
		
		switch(tool)
		{
			case 'tool_bar tool_bar_left':
				$("#tool_browser_left").animate({marginLeft: originalWidth + "px"}, 300, function(){
					$("div.tool_bar_left").animate({left: "0px"}, 300, toggleButtons);
				});
				break;
			case 'tool_bar tool_bar_icon_left':
				$("#tool_browser_icon_left").animate({marginLeft: originalWidth + "px"}, 300, function(){
					$("div.tool_bar_icon_left").animate({left: "0px"}, 300, toggleButtons);
				});
				break;
			case 'tool_bar tool_bar_right':
				$("#tool_browser_right").animate({marginRight: originalWidth + "px"}, 300, function(){
					$("div.tool_bar_right").animate({right: "0px"}, 300, toggleButtons);
				});
				break;
			case 'tool_bar tool_bar_icon_right':
				$("#tool_browser_right").animate({marginRight: originalWidth + "px"}, 300, function(){
					$("div.tool_bar_icon_right").animate({right: "0px"}, 300, toggleButtons);
				});
				break;
		}
		
		$.ajax({
			type: "POST",
			url: "./libraries/ajax/toolbar_memory.php",
			data: { state: 'show'},
			async: false
		})
	}
	
	function setBrowserWidth()
	{
		switch(tool)
		{
			case 'tool_bar tool_bar_left': 
				$("#tool_browser_left").css('margin-left', originalWidth + "px");
				break;
			case 'tool_bar tool_bar_icon_left':
				$("#tool_browser_icon_left").css('margin-left', originalWidth + "px");
				break;
			case 'tool_bar tool_bar_right':
				$("#tool_browser_right").css('margin-right', originalWidth + "px");
				break;
			case 'tool_bar tool_bar_icon_right':
				$("#tool_browser_icon_right").css('margin-right', originalWidth + "px");
				break;
		}
	}
	
	$(document).ready(function ()
	{
		setBrowserWidth();
		$("#tool_bar_hide_container").toggle();
		
		$("#tool_bar_hide").bind("click", hideBlockScreen);
		$("#tool_bar_show").bind("click", showBlockScreen);

		if (typeof hide !== 'undefined')
		{
			if(hide == 'true')
			{
				immediateHideBlockScreen();
			}
		}
	});

});