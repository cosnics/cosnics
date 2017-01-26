$(function () {
	
	$(document).ready(function () {
		$("#new_password").jpassword({
			length: 7,
			flat: true,
			onShow: function(jInput, jTooltip){ jTooltip.slideDown(); },
			onHide: function(jInput, jTooltip){ jTooltip.slideUp(); },
		});
	});

});