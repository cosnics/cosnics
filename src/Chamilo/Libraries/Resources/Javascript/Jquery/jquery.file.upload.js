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
                maxFilesize : 100,
                thumbnailWidth : 200,
                thumbnailHeight : 150,
                maxFiles : null,
                uploadUrl : ajaxUri + '?application=Chamilo\\Libraries\\Ajax&go=UploadTemporaryFile',
                successCallbackFunction : null,
                sendingCallbackFunction : null,
                removedfileCallbackFunction : null,
                acceptCallbackFunction: null,
                initCallbackFunction: null,
                autoProcessQueue: true,
                acceptedFiles: ''
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
                var successCallbackFunction = settings.successCallbackFunction;

                $('.progress', previewElement).hide();
                $(' button.cancel', previewElement).hide();

                if (successCallbackFunction != null)
                {
                    var callbackFunctionNamespace = getCallbackFunctionNamespace(successCallbackFunction);
                    var callbackFunctionName = getCallbackFunctionName(successCallbackFunction);

                    callbackFunctionNamespace[callbackFunctionName](environment, file, serverResponse);
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

            function acceptFile(file, doneCallback) {
                var acceptCallbackFunction = settings.acceptCallbackFunction;

                if (settings.maxFiles != null && getCurrentNumberOfFiles() >= settings.maxFiles)
                {
                    $('.panel', self).hide();
                }

                if (acceptCallbackFunction != null)
                {
                    var callbackFunctionNamespace = getCallbackFunctionNamespace(acceptCallbackFunction);
                    var callbackFunctionName = getCallbackFunctionName(acceptCallbackFunction);

                    callbackFunctionNamespace[callbackFunctionName](environment, file, doneCallback);
                }
                else {
                    doneCallback();
                }
            }

            function processThumbnail(file, dataUrl)
            {
                var previewElement = $(file.previewElement);
                var previewSpan = $('.preview', previewElement);
                $('.file-upload-no-preview', previewSpan).hide();
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
                    clickable : "#" + settings.name + "-upload .panel-body",
                    maxFilesize : settings.maxFilesize,
                    filesizeBase : 1024,
                    thumbnailWidth : settings.thumbnailWidth,
                    thumbnailHeight : settings.thumbnailHeight,
                    url : settings.uploadUrl,
                    parallelUploads : 2,
                    autoProcessQueue: settings.autoProcessQueue === true || settings.autoProcessQueue === 'true',
                    acceptedFiles: settings.acceptedFiles,
                    init : function()
                    {
                        this.on("success", processUploadedFile);
                        this.on("sending", prepareRequest);
                        this.on("removedfile", deleteUploadedFile);
                        this.on("thumbnail", processThumbnail);
                    },
                    accept: acceptFile
                });

                environment.dropzone = myDropzone;

                var initCallbackFunction = settings.initCallbackFunction;

                if (initCallbackFunction != null) {
                    var callbackFunctionNamespace = getCallbackFunctionNamespace(initCallbackFunction);
                    var callbackFunctionName = getCallbackFunctionName(initCallbackFunction);

                    callbackFunctionNamespace[callbackFunctionName](environment);
                }
            }

            return this.each(init);
        }
    });
})(jQuery);
