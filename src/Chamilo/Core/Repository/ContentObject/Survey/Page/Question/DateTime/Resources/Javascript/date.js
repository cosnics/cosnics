(function($) {

	$(document).ready(function() {

		$("[id^=datepicker]").datepicker({
			changeMonth : true,
			changeYear : true,
			firstDay : 1
		});

	});
})(jQuery);
