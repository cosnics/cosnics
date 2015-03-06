/**
 * @author Michael Kyndt
 * @author Hans De Bisschop
 */
(function($) {
    $(window).on('beforeunload', function(e)
    {
        if (typeof tracker != 'undefined')
        {
        	var ajaxUri = getPath('WEB_PATH') + 'index.php';
        	
        	parameters = new Object();
        	parameters.application = 'Chamilo\\Core\\User\\Ajax';
        	parameters.go = 'Leave';
        	parameters.tracker = tracker;

        	$.ajax({
        		type : "POST",
        		url : ajaxUri,
        		data : parameters,
        		async : false
        	});
        	
        //.responseText <- this triggers an error in ie
        }
    });
})(jQuery);