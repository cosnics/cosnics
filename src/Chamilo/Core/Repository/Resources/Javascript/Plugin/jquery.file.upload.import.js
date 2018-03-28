dropzoneCallbacks.chamilo = {
    core : {
        repository : {
            import : {
                processUploadedFile : function(environment, file, serverResponse)
                {
                    var viewButton = $(serverResponse.properties.viewButton);
                    var uploadedMessage = $(serverResponse.properties.uploadedMessage);
                    var contentObjectId = serverResponse.properties.contentObjectId;
                    var previewElement = $(file.previewElement);

                    previewElement.data('content-object-id', contentObjectId);
                    var fileUploadButton = $('.file-upload-buttons', previewElement);
                    fileUploadButton.prepend(viewButton);

                    uploadedMessage.insertBefore(fileUploadButton);
                },
                prepareRequest : function(environment, file, xhrObject, formData)
                {
                    var selectedCategory = $('#parent_id').val();
                    formData.append('parentId', selectedCategory);

                    var selectedWorkspace = $('#workspace_id').val();
                    formData.append('workspaceId', selectedWorkspace);
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
            },
            importWithElementFinder: {
                processUploadedFile: function (elementFinderId, environment, file, serverResponse) {
                    dropzoneCallbacks.chamilo.core.repository.import.processUploadedFile(
                        environment, file, serverResponse
                    );

                    var name = serverResponse.properties.contentObjectTitle;
                    var contentObjectId = serverResponse.properties.contentObjectId;

                    $('#' + elementFinderId + '_search_field').val(name);
                    $('#tbl_' + elementFinderId).trigger('update_search');

                    $('#elf_' + elementFinderId + '_inactive').find('#lo_' + contentObjectId).trigger('activate');
                },
                prepareRequest: function (environment, file, xhrObject, formData) {
                    formData.append('parentId', 0);
                },
                deleteUploadedFile: function (elementFinderId, environment, file, serverResponse) {
                    dropzoneCallbacks.chamilo.core.repository.import.deleteUploadedFile(
                        environment, file, serverResponse
                    );

                    var contentObjectId = $(file.previewElement).data('content-object-id');
                    $('#elf_' + elementFinderId + '_active').find('#lo_' + contentObjectId).trigger(
                        'deactivateElement'
                    );
                    setTimeout(
                        function () {
                            $('#tbl_' + elementFinderId).trigger('update_search')
                        }, 500
                    );
                }
            },
            importWithHiddenField: {
                contentObjectIdentifiers: [],
                processUploadedFile: function (hiddenFieldId, environment, file, serverResponse) {
                    dropzoneCallbacks.chamilo.core.repository.import.processUploadedFile(
                        environment, file, serverResponse
                    );

                    var contentObjectId = serverResponse.properties.contentObjectId;

                    this.contentObjectIdentifiers.push(contentObjectId);
                    $('#' + hiddenFieldId).val(JSON.stringify(this.contentObjectIdentifiers));
                },
                prepareRequest: function (environment, file, xhrObject, formData) {
                    formData.append('parentId', 0);
                },
                deleteUploadedFile: function (hiddenFieldId, environment, file, serverResponse) {
                    dropzoneCallbacks.chamilo.core.repository.import.deleteUploadedFile(
                        environment, file, serverResponse
                    );

                    var contentObjectId = $(file.previewElement).data('content-object-id');

                    var index = this.contentObjectIdentifiers.indexOf(contentObjectId);
                    if(index > -1) {
                        this.contentObjectIdentifiers.splice(index, 1);
                        $('#' + hiddenFieldId).val(JSON.stringify(this.contentObjectIdentifiers));
                    }
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
        var buttonContainer = $('#import_button').parent();

        if (documentType.val() == 0)
        {
            $('div#document_upload').show();
            $('div#document_link').hide();
            $('#import_button').hide();
            $('button:not(#import_button)', buttonContainer).show();
        }
        else
        {
            $('div#document_upload').hide();
            $('div#document_link').show();
            $('#import_button').show();
            $('button:not(#import_button)', buttonContainer).hide();
        }
    }

    $(document).ready(function()
    {
        $(document).on('change', 'input[name="document_type"]', setDocumentTypeField);
        setDocumentTypeField();
    });
})(jQuery);