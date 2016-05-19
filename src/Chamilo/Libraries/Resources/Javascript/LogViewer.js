$(function() {
	function typeChanged(evt, ui) {
		var value = $(this).attr('value');
		$("#chamilo_type").toggle();
		$("#server_type").toggle();
	}

	$(document).ready(function() {
		$(document).on('change', "#type", typeChanged);

		var value = $("#type").attr('value');
		if (value == 'server')
			$("#chamilo_type").toggle();
		else
			$("#server_type").toggle();
	});

});