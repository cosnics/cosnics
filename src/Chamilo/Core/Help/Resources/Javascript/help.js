( function($) 
{
	var handle_help = function(ev, ui) 
	{ 
	    var href = $(this).attr("href");
	   
	    var loadingHTML  = '<iframe style="margin: 20px; width: 760px; height: 560px;" src="' + href + '" frameborder="0">';
	    loadingHTML += '</iframe>';
	   
	    $.modal(loadingHTML, {
			overlayId: 'modalOverlay',
		  	containerId: 'modalContainer',
		  	opacity: 75
		});
		
		return false;
	} 

	$(document).ready( function() 
	{
		$(".help").bind('click', handle_help);
		
	});
	
})(jQuery);