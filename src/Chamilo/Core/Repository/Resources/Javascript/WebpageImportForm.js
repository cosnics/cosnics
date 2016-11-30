(function($) {
	function setDocumentTypeField() {
		var documentType = $('input[name="webpage_type"]:checked');

		if (documentType.val() == 0) {
			$('div#document_upload').show();
			$('div#document_link').hide();
		} else {
			$('div#document_upload').hide();
			$('div#document_link').show();
		}
	}

	$(document).ready(
			function() {
				$(document).on('change', 'input[name="webpage_type"]',
						setDocumentTypeField);
				setDocumentTypeField();
			});
})(jQuery);