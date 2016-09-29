dropzoneCallbacks.chamilo.core.repository.importAttachment = {
	processUploadedFile : function(environment, file, serverResponse) {
		dropzoneCallbacks.chamilo.core.repository.importWithElementFinder.processUploadedFile(
			'attachments', environment, file, serverResponse
		);
	},
	prepareRequest : function(environment, file, xhrObject, formData)
	{
		dropzoneCallbacks.chamilo.core.repository.importWithElementFinder.prepareRequest(
			environment, file, xhrObject, formData
		);
	},
	deleteUploadedFile: function(environment, file, serverResponse) {
		dropzoneCallbacks.chamilo.core.repository.importWithElementFinder.deleteUploadedFile(
			'attachments', environment, file, serverResponse
		);
	}
};

$(function() {

	function showNewCategory(e, ui) {
		e.preventDefault();
		$("div#new_category").show();
		$("input#add_category").hide();
	}

	$(document)
		.ready(
			function() {
				$("div#new_category").hide();
				$("input#add_category").show();
				$("input#add_category").on('click', showNewCategory);
			});

});