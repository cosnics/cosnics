$(function()
{
    function getMenuContainer(displayContainer)
    {
        return $('.learning-path-tree-menu-container', displayContainer);
    }

    function getMenuActionContainer(displayContainer)
    {
        return $('.learning-path-action-menu', displayContainer);
    }

    function hideMenu(e, ui)
    {
        e.preventDefault();

        var displayContainer = $(this).closest('.learning-path-display');
        var menuContainer = getMenuContainer(displayContainer);
        var menuActionContainer = getMenuActionContainer(displayContainer);

        menuContainer.addClass('learning-path-tree-menu-container-hidden');
        menuContainer.removeClass('learning-path-tree-menu-container-visible');
        $('.learning-path-content', displayContainer).addClass('learning-path-content-full-screen');

        $('.learning-path-action-menu-hide', menuActionContainer).addClass('hidden');
        $('.learning-path-action-menu-show', menuActionContainer).removeClass('hidden');

        setMemory('learningPathMenuIsHidden', 'true');
    }

    function showMenu(e, ui)
    {
        e.preventDefault();

        var displayContainer = $(this).closest('.learning-path-display');
        var menuContainer = getMenuContainer(displayContainer);
        var menuActionContainer = getMenuActionContainer(displayContainer);

        $('.learning-path-content', displayContainer).removeClass('learning-path-content-full-screen');
        menuContainer.removeClass('learning-path-tree-menu-container-hidden');
        menuContainer.addClass('learning-path-tree-menu-container-visible');

        $('.learning-path-action-menu-show', menuActionContainer).addClass('hidden');
        $('.learning-path-action-menu-hide', menuActionContainer).removeClass('hidden');

        setMemory('learningPathMenuIsHidden', 'false');
    }

    function handleMobileLearningPath(e, ui)
    {
        e.preventDefault();

        var displayContainer = $(this).closest('.learning-path-tree-menu-container');

        console.log(displayContainer.css('position'));

        if (displayContainer.css('position') == 'fixed')
        {
            setMemory('learningPathMenuIsHidden', 'true');
        }

        window.location = this.href;
    }

    function checkFullScreenSupport()
    {
        if ($.fullscreen.isNativelySupported())
        {
            $(".learning-path-display .learning-path-action-menu .learning-path-action-fullscreen").show();
            $(document).on('fscreenchange', handleFullScreenChange);
            $(document).on('click',
                ".learning-path-display .learning-path-action-menu .learning-path-action-fullscreen", goFullScreen);
        }
    }

    function goFullScreen(e, ui)
    {
        e.preventDefault();

        var displayContainer = $(this).closest('.learning-path-display');
        var frame = $('iframe', displayContainer);

        frame.attr('src', window.location + '&full_screen=1');

        $('iframe').fullscreen();
        frame.show();
    }

    function handleFullScreenChange(e, ui)
    {
        if (!$.fullscreen.isFullScreen())
        {
            var frameUrl = $('iframe').contents().get(0).location.href;
            window.location = frameUrl.replace("&full_screen=1", "");
        }
    }

    $(document).ready(
        function()
        {
            checkFullScreenSupport();

            $(document).on('click', ".learning-path-display .learning-path-tree-menu a", handleMobileLearningPath);
            $(document).on('click',
                ".learning-path-display .learning-path-action-menu .learning-path-action-menu-hide", hideMenu);
            $(document).on('click',
                ".learning-path-display .learning-path-action-menu .learning-path-action-menu-show", showMenu);
        });
});