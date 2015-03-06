$(function() {

    function toggleInstallationStep(e, ui) {
	var step = $(this);

	if (step.hasClass('installation-step-collapsed')) {
	    step.removeClass('installation-step-collapsed');
	    $('div', step).show();
	}
	else
	    {
	    step.addClass('installation-step-collapsed');
	    $('div', step).hide();
	    }
    }

    $(document).ready(function() {
	$(document).on('click', ".installation-step", toggleInstallationStep);
	
	$(".installation-step:last").removeClass('installation-step-collapsed');
	$('div', $(".installation-step:last")).show();
    });

});