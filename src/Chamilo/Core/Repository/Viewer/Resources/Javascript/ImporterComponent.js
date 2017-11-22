$(document).ready(function()
{
    function publishContentObjects(e, ui)
    {
        e.preventDefault();
        
        var previewContainers = $('.file-upload .thumbnail');
        var contentObjectIdentifiers = {
            'viewer_publish_id' : []
        };
        
        if (previewContainers.length == 0)
        {
            return;
        }
        
        previewContainers.each(function(index)
        {
            var previewContainer = $(this);
            contentObjectIdentifiers.viewer_publish_id.push(previewContainer.data('content-object-id'));
        });
        
        var currentQuery = $.query.set('viewer_publish_id', contentObjectIdentifiers.viewer_publish_id);
        
        window.location.replace(currentQuery.toString());
    }

    var fileUploadContainers = $('.file-upload');

    if(fileUploadContainers.length > 0) {
        $('#publish-button').toggleClass('hidden');
        $("#publish-button").on('click', publishContentObjects);
    }
});