$(function()
{
    
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
    }
    
    $(document).ready(function()
    {
        $('input[name=file]').parent().parent().parent().hide();
        $("div#file-upload").dropzone({
            url : getPath('WEB_PATH') + 'index.php?application=Chamilo\\Libraries\\Ajax&go=UploadTemporaryFile',
            init : function()
            {
                this.on("success", processUploadedFile)
            }
        });
    });
    
});