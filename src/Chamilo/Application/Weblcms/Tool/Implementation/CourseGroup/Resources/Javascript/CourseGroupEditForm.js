/**
 * Additional JavaScript/jQuery functions used by the course group editing form.
 */
(function($) {

	/**
	 * Event handler for the (un)checking of any tool checkboxes.
	 * 
	 * Displays a warning message when the tool checkbox has been unchecked, and
	 * hides it again when the tool checkbox has been rechecked.
	 * 
	 */
	function toggleToolUncheckedWarning() {
		var el = $("#tool_unchecked_warning_" + this.name)
		if ($(this).attr("checked") && el) {
			el.hide();
		} else {
			el.show();
		}
	}

	$(document).ready(
			function() {
				$(doucment).on("change", "[name^='document_category_id']",
						toggleToolUncheckedWarning); // Note the '^',
														// indicates that name
														// must start with the
														// given value.
				$(doucment).on("change", "[name^='forum_category_id']",
						toggleToolUncheckedWarning); // Note the '^',
														// indicates that name
														// must start with the
														// given value.
			});

})(jQuery);
