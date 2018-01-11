if(typeof dropzoneCallbacks != 'undefined') {

    if(typeof dropzoneCallbacks.chamilo.core === 'undefined') {
        dropzoneCallbacks.chamilo.core = {'repository': {}};
    }

    dropzoneCallbacks.chamilo.core.repository.feedback = {};

    dropzoneCallbacks.chamilo.core.repository.feedback.importAttachment = {
        processUploadedFile: function (environment, file, serverResponse) {
            dropzoneCallbacks.chamilo.core.repository.importWithHiddenField.processUploadedFile(
                'attachments', environment, file, serverResponse
            );
        },
        prepareRequest: function (environment, file, xhrObject, formData) {
            dropzoneCallbacks.chamilo.core.repository.importWithHiddenField.prepareRequest(
                environment, file, xhrObject, formData
            );
        },
        deleteUploadedFile: function (environment, file, serverResponse) {
            dropzoneCallbacks.chamilo.core.repository.importWithHiddenField.deleteUploadedFile(
                'attachments', environment, file, serverResponse
            );
        }
    };
}