(function($) {
	function resetRating(ev, ui) {
		$(".rating_question_low_value").val(0);
		$(".rating_question_high_value").val(100);
	}

	$(document).ready(function() {
		$(document).on("click", "#ratingtype_percentage", resetRating);
	});
})(jQuery);
