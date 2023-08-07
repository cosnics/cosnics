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

				$(document).ready(function(){
					var entityOptionSelected = document.getElementsByClassName('entity_option_selected');
					if (entityOptionSelected[0].checked)
					{
						$('.entity_selector_box').show();
					}
				});
			});

});