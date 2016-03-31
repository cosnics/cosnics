/**
 * Copyright (c) 2010, Sven Vanpoucke, Chamilo tree menu in jQuery plugin
 */

(function($)
{
    $.fn.extend({
        fileUpload : function(options)
        {
            
            // Settings list and the default values
            var defaults = {
                name : 'file',
                maxFileSize : 100,
                thumbnailWidth : 240,
                thumbnailHeight : 200,
                maxFiles : 1,
                ajaxUri : getPath('WEB_PATH') + 'index.php',
                uploadUrl : ajaxUri + '?application=Chamilo\\Libraries\\Ajax&go=UploadTemporaryFile',
                deleteUrl : ajaxUri,
                deleteContext : 'Chamilo\\Libraries\\Ajax',
                deleteComponent : 'DeleteTemporaryFile',
                titleInputName : null
            };
            
            var settings = $.extend(defaults, options), self = $(this);
            
            var elementContainer, previewNode, previewTemplate;
            
            function processUploadedFile(file, serverResponse)
            {
                var fileData = {
                    name : file.name,
                    temporaryFileName : serverResponse.properties.temporaryFileName
                };
                
                if (settings.maxFiles == 1)
                {
                    if (settings.titleInputName != null)
                    {
                        var titleField = $('input[name=' + settings.titleInputName + ']');
                        
                        if (titleField.val() == '')
                        {
                            titleField.val(file.name);
                        }
                    }
                    
                    $('input[type=hidden][name=' + settings.name + '_upload_data]').val(JSON.stringify(fileData));
                }
                
                $('.panel', elementContainer).hide();
                $('.file-previews .thumbnail .progress', elementContainer).hide();
                $('.file-previews .thumbnail button.cancel', elementContainer).hide();
                
                $(file.previewElement).data('temporary-file-name', serverResponse.properties.temporaryFileName);
            }
            
            function deleteUploadedFile(file, serverResponse)
            {
                var temporaryFileName = $(file.previewElement).data('temporary-file-name');
                
                var parameters = {
                    'application' : settings.deleteContext,
                    'go' : settings.deleteComponent,
                    'file' : temporaryFileName
                };
                
                var response = $.ajax({
                    type : "POST",
                    url : ajaxUri,
                    data : parameters
                }).success(function(json)
                {
                    if (settings.maxFiles == 1)
                    {
                        if (settings.titleInputName != null)
                        {
                            var titleField = $('input[name=' + settings.titleInputName + ']');
                            titleField.val('');
                        }
                        
                        $('input[type=hidden][name=' + settings.name + '_upload_data]').val('');
                    }
                    
                    $('.panel', elementContainer).show();
                });
            }
            
            function determineTemplate()
            {
                previewNode = $("#" + settings.name + "-template");
                previewNode.removeAttr('id');
                previewTemplate = previewNode.parent().html();
                previewNode.remove();
            }
            
            function init()
            {
                elementContainer = $(this);
                $('input[name=' + settings.name + ']', elementContainer).parent().parent().parent().hide();
                determineTemplate();
                
                $("#" + settings.name + "-upload", elementContainer).dropzone({
                    paramName : settings.name,
                    maxFiles : settings.maxFiles,
                    previewTemplate : previewTemplate,
                    previewsContainer : "#" + settings.name + "-previews",
                    clickable : ".fileinput-button",
                    maxFilesize : settings.maxFilesize,
                    filesizeBase : 1024,
                    thumbnailWidth : settings.thumbnailWidth,
                    thumbnailHeight : settings.thumbnailHeight,
                    url : settings.uploadUrl,
                    init : function()
                    {
                        this.on("success", processUploadedFile);
                        this.on("removedfile", deleteUploadedFile);
                    }
                });
            }
            
            return this.each(init);
        }
    });
})(jQuery);
