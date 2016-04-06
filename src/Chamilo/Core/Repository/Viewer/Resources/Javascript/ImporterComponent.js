$(document).ready(function()
{
    function publishContentObjects(e, ui)
    {
        e.preventDefault();
        
        var previewContainers = $('.file-upload .thumbnail');
        var contentObjectIdentifiers = {
            'viewer_object_id' : []
        };
        
        if (previewContainers.length == 0)
        {
            return;
        }
        
        previewContainers.each(function(index)
        {
            var previewContainer = $(this);
            contentObjectIdentifiers.viewer_object_id.push(previewContainer.data('content-object-id'));
        });
        
        var currentQuery = $.query.set('viewer_object_id', contentObjectIdentifiers.viewer_object_id);
        
        window.location.replace(currentQuery.toString());
    }
    
    $('#publish-button').toggleClass('hidden');
    $("#publish-button").on('click', publishContentObjects);
});