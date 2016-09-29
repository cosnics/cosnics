dropzoneCallbacks.chamilo.core.repository.importFeedbackAttachment = {
	processUploadedFile : function(environment, file, serverResponse) {
		dropzoneCallbacks.chamilo.core.repository.importWithElementFinder.processUploadedFile(
			'select_attachment', environment, file, serverResponse
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
			'select_attachment', environment, file, serverResponse
		);
	}
};
