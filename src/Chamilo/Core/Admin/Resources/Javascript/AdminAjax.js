/*global $, document, jQuery, window */

$(function () {
	
	var windowHeight = getWindowHeight(), resizeTimer = null;
	
	function handleResize() {
		var currentHeight = getWindowHeight();
		
		if (resizeTimer)
		{
			clearTimeout(resizeTimer);
		}
		
		if (windowHeight != currentHeight)
		{
			resizeTimer = setTimeout(reinit, 100);
		}
	}
	
	function getWindowHeight()
	{
		if (window.innerHeight)
		{
			return window.innerHeight;
		}
		else if (document.documentElement)
		{
			return document.documentElement.offsetHeight;
		}
	}
	
	function reinit() {	
		windowHeight = getWindowHeight();
		destroy();
	}
	
	function destroy() {
		$(window).unbind('resize', handleResize);
	}
	
	// Extension to jQuery selectors which only returns visible elements
	$.extend($.expr[':'], {
	    visible: function (a) {
	        return $(a).css('display') !== 'none';
	    }
	});
	
	function placeFooter()
	{
		htmlHeight = $("body").outerHeight();
		
		if (htmlHeight > windowHeight)
		{
			$("#footer").css("position", "static");
			$("#footer").css("bottom", "");
			$("#footer").css("left", "");
			$("#footer").css("right", "");
			
			$("#main").css("margin-bottom", '0px;');
		}
		else
		{
			$("#footer").css("position", "fixed");
			$("#footer").css("bottom", "0px");
			$("#footer").css("left", "0px");
			$("#footer").css("right", "0px");
			
			$("#main").css("margin-bottom", '30px;');
		}
	}

//	$(document).ready(function ()
//	{
//		$("#admin_tabs ul").css('display', 'block');
//		$("#admin_tabs h2").hide();
//		$("#admin_tabs").tabs();
//		var tabs = $('#admin_tabs').tabs('paging', { cycle: false, follow: false, nextButton : "", prevButton : "" } );
//		tabs.tabs('select', tabnumber);
//	});

});