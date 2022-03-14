$(function()
{
    function maximizeViewer(e, ui)
    {
        e.preventDefault();
        
        var viewerContainer = $(this).closest('.office-viewer-container');
        var viewerIFrame = $('iframe', viewerContainer);
        
        if ($.fullscreen.isNativelySupported())
        {
            viewerIFrame.fullscreen();
        }
        else
        {
            var vierwerFrame = $('.office-viewer-frame', viewerContainer);
            var viewerSidebar = $('.office-viewer-sidebar', viewerContainer);
            
            vierwerFrame.addClass('office-viewer-frame-full-screen');
            viewerSidebar.show();
        }
    }
    
    function minimizeViewer(e, ui)
    {
        e.preventDefault();
        
        var viewerContainer = $(this).closest('.office-viewer-container');
        var vierwerFrame = $('.office-viewer-frame', viewerContainer);
        var viewerSidebar = $('.office-viewer-sidebar', viewerContainer);
        
        vierwerFrame.removeClass('office-viewer-frame-full-screen');
        viewerSidebar.hide();
    }
    
    $(document).ready(
            function()
            {
                /*if (window.matchMedia('(min-width: 768px)').matches)
                {
                    $('iframe.office-viewer-frame').each(function(index)
                    {
                        var iframeElement = $(this);
                        iframeElement.attr('src', iframeElement.data('url'));
                    });
                }*/
                
                $(document).on('click', ".office-viewer-container .btn-office-viewer-full-screen", maximizeViewer);
                $(document).on('click', ".office-viewer-container .office-viewer-sidebar .btn-office-viewer-minimize",
                        minimizeViewer);
            });
});