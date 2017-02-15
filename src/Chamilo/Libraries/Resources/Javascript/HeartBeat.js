( function($) 
{
	var heartBeat = function() {
        $.post( 'index.php?application=Chamilo\\Libraries\\Ajax&go=HeartBeat' );
	};

	$(document).ready( function() 
	{
        setInterval(heartBeat, 600000);
	});
	
})(jQuery);