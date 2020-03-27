if (typeof dropzoneCallbacks != 'undefined') {

    if (typeof dropzoneCallbacks.chamilo.core === 'undefined') {
        dropzoneCallbacks.chamilo.core = {'repository': {}};
    }

    dropzoneCallbacks.chamilo.core.repository.importAttachment = {
        processUploadedFile: function (environment, file, serverResponse) {
            dropzoneCallbacks.chamilo.core.repository.importWithElementFinder.processUploadedFile(
                'attachments', environment, file, serverResponse
            );
        },
        prepareRequest: function (environment, file, xhrObject, formData) {
            dropzoneCallbacks.chamilo.core.repository.importWithElementFinder.prepareRequest(
                environment, file, xhrObject, formData
            );
        },
        deleteUploadedFile: function (environment, file, serverResponse) {
            dropzoneCallbacks.chamilo.core.repository.importWithElementFinder.deleteUploadedFile(
                'attachments', environment, file, serverResponse
            );
        }
    };
}

$(function () {

    function showNewCategory(e, ui) {
        e.preventDefault();
        $("#new_category").show();
        $("#add_category").hide();
    }

    $(document)
        .ready(
            function () {
                $("#new_category").hide();
                $("#add_category").show();
                $("#add_category").on('click', showNewCategory);
            });

});
