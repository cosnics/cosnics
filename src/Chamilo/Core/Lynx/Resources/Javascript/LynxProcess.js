$(function() {

	function toggleUpgradeStep(e, ui) {
		var step = $(this);

		if (step.hasClass('upgrade-step-collapsed')) {
			step.removeClass('upgrade-step-collapsed');
			$('div', step).show();
		} else {
			step.addClass('upgrade-step-collapsed');
			$('div', step).hide();
		}
	}

	$(document).ready(function() {
		$(document).on('click', ".package_upgrade", toggleUpgradeStep);

		$(".package_upgrade:last").removeClass('upgrade-step-collapsed');
		$('div', $(".package_upgrade:last")).show();
	});

});