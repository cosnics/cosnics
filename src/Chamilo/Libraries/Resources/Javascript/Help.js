(function($) {
    var handle_help = function(ev, ui) {
	var href = $(this).attr("href");

	var loadingHTML = '<iframe id="iframe-help" style="margin: 20px; width: 95%; height: 95%;" src="' + href
		+ '" frameborder="0">';
	loadingHTML += '</iframe>';

	$.modal(loadingHTML, {
	    overlayId : 'help-modal-overlay',
	    containerId : 'help-modal-container',
	    closeClass : 'help-modal-close',
	    opacity : 75
	});

        //mozilla height fix
        if($.browser.mozilla)
        {
            calculate_height();
            $(window).resize(function()
            {
                calculate_height();
            });
        }
	return false;
    }

    $(document).ready(function() {
	$(".help").bind('click', handle_help);

    });
    var calculate_height = function(){
        var height = 0.95 * $('#help-modal-container').height();
        $('#iframe-help').css('height', height + 'px');
    }

})(jQuery);
