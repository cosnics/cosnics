Dropzone.autoDiscover = false;

var dropzoneCallbacks = {};

(function($)
{
    $.fn.extend({
                fileUpload : function(options)
                {
                    var ajaxUri = getPath('WEB_PATH') + 'index.php';
                    
                    // Settings list and the default values
                    var defaults = {
                        name : 'file',
                        maxFileSize : 100,
                        thumbnailWidth : 200,
                        thumbnailHeight : 150,
                        maxFiles : null,
                        uploadUrl : ajaxUri + '?application=Chamilo\\Libraries\\Ajax&go=UploadTemporaryFile',
                        successCallbackFunction : null,
                        sendingCallbackFunction : null,
                        removedfileCallbackFunction : null
                    };
                    
                    var settings = $.extend(defaults, options);
                    var self = $(this), myDropzone, previewNode, previewTemplate;
                    var environment = {
                        container : self,
                        dropzone : myDropzone,
                        settings : settings
                    };
                    
                    function getCallbackFunctionName(functionName)
                    {
                        var namespaces = functionName.split(".");
                        return namespaces.pop();
                    }
                    
                    function getCallbackFunctionNamespace(functionName)
                    {
                        var namespaces = functionName.split(".");
                        var func = namespaces.pop();
                        var context = dropzoneCallbacks;
                        
                        for (var i = 0; i < namespaces.length; i++)
                        {
                            context = context[namespaces[i]];
                        }
                        
                        return context;
                    }
                    
                    function processUploadedFile(file, serverResponse)
                    {
                        var previewElement = $(file.previewElement);
                        var previewSpan = $('span.preview', previewElement);
                        var previewImage = $('img', previewSpan);
                        var successCallbackFunction = settings.successCallbackFunction;
                        
                        if (settings.maxFiles != null && getCurrentNumberOfFiles() >= settings.maxFiles)
                        {
                            $('.panel', self).hide();
                        }
                        
                        $('.progress', previewElement).hide();
                        $(' button.cancel', previewElement).hide();
                        
                        if (successCallbackFunction != null)
                        {
                            var callbackFunctionNamespace = getCallbackFunctionNamespace(successCallbackFunction);
                            var callbackFunctionName = getCallbackFunctionName(successCallbackFunction);
                            
                            callbackFunctionNamespace[callbackFunctionName](environment, file, serverResponse);
                        }
                        
                        if (previewImage.prop('src') == '')
                        {
                            previewSpan
                                    .append('<div class="file-upload-no-preview"><span class="glyphicon glyphicon-file"></span></div>');
                        }
                    }
                    
                    function getCurrentNumberOfFiles()
                    {
                        return myDropzone.files.length;
                    }
                    
                    function prepareRequest(file, xhrObject, formData)
                    {
                        var sendingCallbackFunction = settings.sendingCallbackFunction;
                        
                        formData.append('filePropertyName', settings.name);
                        
                        if (settings.sendingCallbackFunction != null)
                        {
                            var callbackFunctionNamespace = getCallbackFunctionNamespace(sendingCallbackFunction);
                            var callbackFunctionName = getCallbackFunctionName(sendingCallbackFunction);
                            
                            callbackFunctionNamespace[callbackFunctionName](environment, file, xhrObject, formData);
                        }
                    }
                    
                    function deleteUploadedFile(file, serverResponse)
                    {
                        var removedfileCallbackFunction = settings.removedfileCallbackFunction;
                        
                        if (removedfileCallbackFunction != null)
                        {
                            var callbackFunctionNamespace = getCallbackFunctionNamespace(removedfileCallbackFunction);
                            var callbackFunctionName = getCallbackFunctionName(removedfileCallbackFunction);
                            
                            callbackFunctionNamespace[callbackFunctionName](environment, file, serverResponse);
                        }
                        
                        if (settings.maxFiles != null && getCurrentNumberOfFiles() < settings.maxFiles)
                        {
                            $('.panel', self).show();
                        }
                    }
                    
                    function determineTemplate()
                    {
                        previewNode = $("#" + settings.name + "-template");
                        previewNode.removeAttr('id');
                        previewTemplate = previewNode.parent().html();
                        previewNode.remove();
                    }
                    
                    function hideLegacyInput()
                    {
                        $('#' + settings.name + '-upload-input', self).hide();
                    }
                    
                    function init()
                    {
                        hideLegacyInput();
                        determineTemplate();
                        
                        myDropzone = new Dropzone("#" + settings.name + "-upload", {
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
                                this.on("sending", prepareRequest);
                                this.on("removedfile", deleteUploadedFile);
                            }
                        });
                    }
                    
                    return this.each(init);
                }
            });
})(jQuery);
