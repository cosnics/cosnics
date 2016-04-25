$(function()
{
    function maximizeViewer(e, ui)
    {
        e.preventDefault();

        var viewerContainer = $(this).closest('.office-viewer-container');
        var viewerIFrame = $('iframe', viewerContainer).get(0);

        if (viewerIFrame.requestFullscreen)
        {
            viewerIFrame.requestFullscreen();
        } else if (viewerIFrame.webkitRequestFullscreen)
        {
            viewerIFrame.webkitRequestFullscreen();
        } else if (viewerIFrame.mozRequestFullScreen)
        {
            viewerIFrame.mozRequestFullScreen();
        } else if (viewerIFrame.msRequestFullscreen)
        {
            viewerIFrame.msRequestFullscreen();
        } else
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
                $(document).on('click', ".office-viewer-container .btn-office-viewer-full-screen", maximizeViewer);
                $(document).on('click', ".office-viewer-container .office-viewer-sidebar .btn-office-viewer-minimize",
                        minimizeViewer);
            });
});