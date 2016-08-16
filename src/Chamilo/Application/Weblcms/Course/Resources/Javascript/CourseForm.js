$(function() {
	function reloadForm(evt, ui) {
		$('input.select_course_type', $(this).closest('form')).click();
	}

	$(document).ready(function() {
		$(document).on('change', '.course_type_selector', reloadForm);
	});

});