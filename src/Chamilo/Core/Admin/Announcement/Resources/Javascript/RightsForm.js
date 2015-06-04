$(function() {
	$(document).ready(
			function() {

				$(document).on('click', '.entity_option_selected[value="2"]',
						function() {
							$('.entity_selector_box').show();

						});

				$(document).on('click', '.other_option_selected', function() {

					$('.entity_selector_box').hide();
				});
			});

});