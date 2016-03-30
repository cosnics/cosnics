dropzoneCallbacks.chamilo = {
    core : {
        repository : {
            import : {
                processUploadedFile : function(environment, file, serverResponse)
                {
                    $(file.previewElement).data('content-object-id', serverResponse.properties.contentObjectId);
                    
                    // Add view button
                },
                prepareRequest : function(environment, file, xhrObject, formData)
                {
                    // Retrieve the chosen repository category and add it to the
                    // form data
                },
                deleteUploadedFile : function(environment, file, serverResponse)
                {
                    var contentObjectId = $(file.previewElement).data('content-object-id');
                    alert(contentObjectId);
                    
                    // Do AJAX call to delete content object again
                }
            }
        }
    }
};