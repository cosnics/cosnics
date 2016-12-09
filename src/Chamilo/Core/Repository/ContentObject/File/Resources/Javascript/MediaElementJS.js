$(function()
{
    $(document).ready(function()
    {
        $('video.media-element-js-element,audio.media-element-js-element').mediaelementplayer({
            error : function(errorElement)
            {
                if ('currentTarget' in errorElement)
                {
                    var currentTarget = $(errorElement.currentTarget);
                    var currentContainer = currentTarget.closest('.media-element-js-container');
                }
                else
                {
                    var currentContainer = $(errorElement).closest('.media-element-js-container');
                }
                
                $('.mejs-container', currentContainer).hide();
                $('.media-element-js-playback-error', currentContainer).removeClass('hidden');
                $('.media-element-js-download', currentContainer).hide();
            }
        });
    });
});