Dropzone.autoDiscover = false;

$(function()
{
    var ajaxContext = 'Chamilo\\Libraries\\Ajax';
    var ajaxUri = getPath('WEB_PATH') + 'index.php';
    
    function processUploadedFile(file, serverResponse)
    {
        var fileData = {
            name : file.name,
            temporaryFileName : serverResponse.properties.temporaryFileName
        };
        
        var titleField = $('input[name=title]');
        
        $('input[type=hidden][name=file_upload_data]').val(JSON.stringify(fileData));
        
        if (titleField.val() == '')
        {
            titleField.val(file.name);
        }
        
        $('div#file-upload .panel').hide();
        $('div#file-upload #previews .thumbnail .progress').hide();
        $('div#file-upload #previews .thumbnail button.cancel').hide();
    }
    
    function deleteUploadedFile(file, serverResponse)
    {
        var parameters = {
            'application' : ajaxContext,
            'go' : 'DeleteTemporaryFile',
            'file' : $('input[type=hidden][name=file_upload_data]').val()
        };
        
        var response = $.ajax({
            type : "POST",
            url : ajaxUri,
            data : parameters
        }).success(function(json)
        {
            var titleField = $('input[name=title]');
            titleField.val('');
            
            $('input[type=hidden][name=file_upload_data]').val('');
            $('div#file-upload .panel').show();
        });
    }
    
    $(document).ready(function()
    {
        $('input[name=file]').parent().parent().parent().hide();
        
        var previewNode = $("#template");
        previewNode.prop('id', '');
        var previewTemplate = previewNode.parent().html();
        previewNode.remove();
        
        $("div#file-upload").dropzone({
            maxFiles : 1,
            previewTemplate : previewTemplate,
            previewsContainer : "#previews",
            clickable : ".fileinput-button",
            maxFilesize : 7000,
            filesizeBase : 1024,
            thumbnailWidth : 240,
            thumbnailHeight : 200,
            url : ajaxUri + '?application=Chamilo\\Libraries\\Ajax&go=UploadTemporaryFile',
            init : function()
            {
                this.on("success", processUploadedFile);
                this.on("removedfile", deleteUploadedFile);
                this.on("canceled", cancelFileUpload);
            }
        });
    });
    
});