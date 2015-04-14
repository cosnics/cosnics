(function($) {
	var ajaxUri = getPath('WEB_PATH') + 'index.php';

	function processOrder(e) {
		var id = $(this).attr('id').replace('order_', '');
		var order = $('option:selected', this).attr('value');
		var parameters = {
			"application" : "Chamilo\\Core\\Repository\\ContentObject\\Survey\\Page\\Builder\\Ajax",
			"go" : "process_order",
			"id" : id,
			"order" : order
		};

		var save_answer = $.ajax({
			type : "POST",
			url : ajaxUri,
			data : parameters,
			async : false
		}).success(

				function(json) {

					if (json.result_code == 200) {
						window.location.reload();
					} else {
						$(
								"#order_" + id + " option")
								.removeAttr('selected');
						$(
								"#order_" + id + " option[value='"
										+ json.properties.display_order + "']")
								.attr('selected', 'selected');
					}
				});
	}

	$(document).ready(function() {
		$(document).on('change', ".order", processOrder);

	});

})(jQuery);