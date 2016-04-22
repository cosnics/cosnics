$(function()
{
    function toggleMenu(e, ui)
    {
        e.preventDefault();
        
        var displayContainer = $(this).closest('.learning-path-display');
        var menuContainer = getMenuContainer(displayContainer);
        
        if (menuContainer.is(':visible'))
        {
            hideMenu(displayContainer);
        }
        else
        {
            showMenu(displayContainer);
        }
    }
    
    function getMenuContainer(displayContainer)
    {
        return $('.learning-path-tree-menu-container', displayContainer);
    }
    
    function getMenuActionContainer(displayContainer)
    {
        return $('.learning-path-action-menu', displayContainer);
    }
    
    function hideMenu(displayContainer)
    {
        var menuContainer = getMenuContainer(displayContainer);
        var menuActionContainer = getMenuActionContainer(displayContainer);
        
        menuContainer.addClass('learning-path-tree-menu-container-hidden');
        $('.learning-path-content', displayContainer).addClass('learning-path-content-full-screen');
        
        $('.learning-path-action-menu-hide', menuActionContainer).hide();
        $('.learning-path-action-menu-show', menuActionContainer).show();
        
        setMemory('learningPathMenuIsHidden', 'true');
    }
    
    function showMenu(displayContainer)
    {
        var menuContainer = getMenuContainer(displayContainer);
        var menuActionContainer = getMenuActionContainer(displayContainer);
        
        $('.learning-path-content', displayContainer).removeClass('learning-path-content-full-screen');
        menuContainer.removeClass('learning-path-tree-menu-container-hidden');
        
        $('.learning-path-action-menu-show', menuActionContainer).hide();
        $('.learning-path-action-menu-hide', menuActionContainer).show();
        
        setMemory('learningPathMenuIsHidden', 'false');
    }
    
    function processMenuStatus()
    {
        var isMenuHidden = getMemory('learningPathMenuIsHidden');
        
        if (isMenuHidden == 'true')
        {
            $('.learning-path-display').each(function(index)
            {
                var displayContainer = $(this);
                var menuContainer = getMenuContainer(displayContainer);
                
                if (!menuContainer.hasClass('learning-path-tree-menu-container-hidden'))
                {
                    hideMenu(displayContainer);
                }
            });
        }
    }
    
    $(document).ready(function()
    {
        processMenuStatus();
        
        $(document).on('click', ".learning-path-display .learning-path-action-menu", toggleMenu);
    });
});