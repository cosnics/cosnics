dropzoneCallbacks.chamilo = {
    core : {
        repository : {
            import : {
                processUploadedFile : function(environment, file, serverResponse)
                {
                    var viewButton = $(serverResponse.properties.viewButton);
                    var contentObjectId = serverResponse.properties.contentObjectId;
                    var previewElement = $(file.previewElement);
                    
                    previewElement.data('content-object-id', contentObjectId);
                    $('.file-upload-buttons', previewElement).prepend(viewButton);
                },
                prepareRequest : function(environment, file, xhrObject, formData)
                {
                    var selectedCategory = $('#parent_id').val();
                    formData.append('parentId', selectedCategory);
                },
                deleteUploadedFile : function(environment, file, serverResponse)
                {
                    var contentObjectId = $(file.previewElement).data('content-object-id');
                    
                    var ajaxUri = getPath('WEB_PATH') + 'index.php';
                    var temporaryFileName = $(file.previewElement).data('temporary-file-name');
                    
                    var parameters = {
                        'application' : 'Chamilo\\Core\\Repository\\Ajax',
                        'go' : 'DeleteFile',
                        'content_object_id' : contentObjectId
                    };
                    
                    var response = $.ajax({
                        type : "POST",
                        url : ajaxUri,
                        data : parameters
                    });
                }
            }
        }
    }
};

(function($)
{
    function setDocumentTypeField()
    {
        var documentType = $('input[name="document_type"]:checked');
        
        if (documentType.val() == 0)
        {
            $('div#document_upload').show();
            $('div#document_link').hide();
            $('#import_button').hide();
        }
        else
        {
            $('div#document_upload').hide();
            $('div#document_link').show();
            $('#import_button').show();
        }
    }
    
    $(document).ready(function()
    {
        $(document).on('change', 'input[name="document_type"]', setDocumentTypeField);
        setDocumentTypeField();
    });
})(jQuery);