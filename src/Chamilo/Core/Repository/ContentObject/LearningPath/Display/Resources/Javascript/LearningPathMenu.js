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
    
    $(document).ready(
            function()
            {
                $(document).on('click',
                        ".learning-path-display .learning-path-action-menu .learning-path-action-menu-hide", hideMenu);
                $(document).on('click',
                        ".learning-path-display .learning-path-action-menu .learning-path-action-menu-show", showMenu);
            });
});