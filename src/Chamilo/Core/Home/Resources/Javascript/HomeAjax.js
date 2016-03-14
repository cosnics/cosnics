/*global $, addBlock, bindIcons, blocksDraggable, tabsDroppable, columnsResizable, columnsSortable, confirm, document, editTab, filterComponents, jQuery, showAllComponents, tabsSortable */

$(function()
{
    
    var availableBlocks;
    var ajaxContext = 'Chamilo\\Core\\Home\\Ajax';
    var ajaxUri = getPath('WEB_PATH') + 'index.php';
    var translationContext = 'Chamilo\\Core\\Home';
    
    $(document).ready(function()
    {
        initialize();
        
        bindPortalActions();
        makeColumnsResizable();
        makeBlocksSortable();
        makeTabsSortable();
    });
    
    function initialize()
    {
        getAvailableBlocks();
        renderAvailablePackages();
        renderAvailableComponents();
    }
    
    function bindPortalActions()
    {
        $(document).on('click', "a.portal-add-block:not(.dropdown-toggle)", displayBlockScreen);
        $(document).on('click', "a.portal-package-hide", hideBlockScreen);
        $(document).on('click', "a.portal-add-column", addColumn);
        $(document).on('click', "a.portal-add-tab", addTab);
        $(document).on('click', ".portal-package-blocks a", addBlock);
        
        $(document).on('click', ".portal-action-block-delete", deleteBlock);
        $(document).on('click', ".portal-action-column-delete", deleteColumn);
        $(document).on('click', ".portal-action-tab-delete", deleteTab);
        $(document).on('click', ".portal-action-block-show", showBlock);
        $(document).on('click', ".portal-action-block-hide", hideBlock);
        
        $(document).on('input', "#portal-package-name", filterComponents);
        $(document).on('change', "#portal-package-context", filterComponents);
    }
    
    function getAvailableBlocks()
    {
        
        var parameters = {
            'application' : ajaxContext,
            'go' : 'block_list'
        };
        
        var response = $.ajax({
            type : "POST",
            url : ajaxUri,
            data : parameters,
            async : false
        }).success(function(json)
        {
            availableBlocks = json.properties.blocks;
        });
    }
    
    function displayBlockScreen(e, ui)
    {
        e.preventDefault();
        $(".portal-package-container").switchClass('hidden', 'show');
    }
    
    function hideBlockScreen(e, ui)
    {
        e.preventDefault();
        $(".portal-package-container").switchClass('show', 'hidden');
    }
    
    function renderAvailablePackages()
    {
        var packagesList = $("#portal-package-context");
        var ordering = [];
        
        for ( var i in availableBlocks)
        {
            ordering[i] = availableBlocks[i].name;
        }
        
        ordering = asort(ordering);
        
        for ( var i in ordering)
        {
            var availablePackage = $('<option></option>');
            availablePackage.attr('value', i);
            
            packageImage = $('<img />');
            packageImage.prop('src', availableBlocks[i].image);
            availablePackage.append(packageImage);
            
            availablePackage.append(availableBlocks[i].name);
            
            packagesList.append(availablePackage);
        }
    }
    
    function renderAvailableComponents()
    {
        var componentsList = $(".portal-package-blocks");
        
        componentsList.empty();
        
        for ( var i in availableBlocks)
        {
            for ( var j in availableBlocks[i].components)
            {
                var blockName = availableBlocks[i].components[j].name;
                
                var column = $('<div />');
                column.addClass('col-xs-12 col-sm-4 col-md-3 col-lg-2');
                
                var availableComponent = $('<a />');
                column.append(availableComponent);
                
                availableComponent.addClass('btn btn-default btn-block');
                availableComponent.css('cursor', 'pointer');
                availableComponent.attr('data-block', availableBlocks[i].components[j].id);
                availableComponent.prop('title', availableBlocks[i].components[j].name);
                
                componentImage = $('<img />');
                componentImage.css('width', '16px');
                componentImage.css('height', '16px');
                componentImage.prop('src', availableBlocks[i].components[j].image);
                availableComponent.append(componentImage);
                
                availableComponent.append(availableBlocks[i].components[j].name);
                
                componentsList.append(column);
            }
        }
    }
    
    function addBlock(e, ui)
    {
        e.preventDefault();
        
        var column, columnId, order, isEmpty;
        
        column = $(".portal-tab:visible .portal-column:last");
        columnId = column.data('element-id');
        isEmpty = $(".portal-column-empty:visible", column).length != 0;
        
        var parameters = {
            'application' : ajaxContext,
            'go' : 'BlockAdd',
            'block' : $(this).data('block'),
            'column' : columnId
        };
        
        var response = $.ajax({
            type : "POST",
            url : ajaxUri,
            data : parameters
        }).success(
                function(json)
                {
                    if (json.result_code == 200)
                    {
                        column.prepend(json.properties.block);
                        $('.portal-column[data-element-id="' + columnId + '"] .portal-column-empty').switchClass(
                                'show', 'hidden');
                    }
                });
    }
    
    function addColumn(e, ui)
    {
        e.preventDefault();
        
        var tab, tabId;
        
        tab = $(".portal-tab:visible");
        tabId = tab.data('element-id');
        
        var parameters = {
            'application' : ajaxContext,
            'go' : 'ColumnAdd',
            'tab' : tabId
        };
        
        var response = $.ajax({
            type : "POST",
            url : ajaxUri,
            data : parameters
        }).success(
                function(json)
                {
                    var columnHtml, newWidths;
                    
                    newWidths = json.properties.width;
                    
                    $.each(newWidths, function(index, value)
                    {
                        $('.portal-column[data-element-id="' + index + '"]').prop('class',
                                'col-xs-12 col-md-' + value + ' portal-column');
                    });
                    
                    tab.append(json.properties.html);
                    $(".portal-tab:visible .portal-action-column-delete").switchClass('hidden', 'show');
                });
        
    }
    
    function addTab(e, ui)
    {
        e.preventDefault();
        
        var parameters = {
            'application' : ajaxContext,
            'go' : 'TabAdd'
        };
        
        var response = $.ajax({
            type : "POST",
            url : ajaxUri,
            data : parameters
        }).success(function(json)
        {
            
            $('.portal-nav-tabs li.active').removeClass('active');
            $('.portal-tab.show').switchClass('show', 'hidden');
            
            $(".portal-tabs .portal-tab:last").after(json.properties.html);
            $(".portal-nav-tabs .portal-nav-tab:last").after(json.properties.title);
            $(".portal-action-tab-delete").switchClass('hidden', 'show');
        });
    }
    
    function deleteBlock(e, ui)
    {
        e.preventDefault();
        
        var block = $(this).parent().parent().parent();
        var blockId = block.data('element-id');
        var columnId = block.data('column-id');
        
        var parameters = {
            'application' : ajaxContext,
            'go' : 'BlockDelete',
            'block' : blockId
        };
        
        var response = $.ajax({
            type : "POST",
            url : ajaxUri,
            data : parameters
        });
        
        $('.portal-block[data-element-id="' + blockId + '"]').remove();
        var isEmpty = $('.portal-column[data-element-id="' + columnId + '"] .portal-block:visible').length == 0;
        
        if (isEmpty)
        {
            $('.portal-column[data-element-id="' + columnId + '"] .portal-column-empty').switchClass('hidden', 'show');
        }
    }
    
    function deleteColumn(e, ui)
    {
        e.preventDefault();
        
        var column = $(this).parent().parent().parent().parent();
        var columnId = column.data('element-id');
        var tabId = column.data('tab-id');
        
        var columnCount = $(".portal-tab:visible .portal-column").length;
        
        if (columnCount > 1)
        {
            var parameters = {
                'application' : ajaxContext,
                'go' : 'ColumnDelete',
                'column' : columnId
            };
            
            var response = $.ajax({
                type : "POST",
                url : ajaxUri,
                data : parameters
            }).success(function(json)
            {
                if (json.result_code == 200)
                {
                    column.remove();
                    columnCount = $(".portal-tab:visible .portal-column").length;
                    
                    if (columnCount == 1)
                    {
                        $(".portal-tab:visible .portal-action-column-delete").switchClass('show', 'hidden');
                    }
                }
            });
        }
    }
    
    function deleteTab(e, ui)
    {
        e.preventDefault();
        
        var tab = $(this).parent().parent();
        var tabId = tab.data('tab-id');
        
        var tabCount = $(".portal-tab").length;
        var isActiveTab = tab.hasClass('active');
        
        if (tabCount > 1)
        {
            var deleteIsConfirmed = confirm(getTranslation('ConfirmTabDelete', null, translationContext));
            
            if (deleteIsConfirmed)
            {
                var parameters = {
                    'application' : ajaxContext,
                    'go' : 'TabDelete',
                    'tab' : tabId
                };
                
                var response = $.ajax({
                    type : "POST",
                    url : ajaxUri,
                    data : parameters
                }).success(function(json)
                {
                    if (json.result_code == 200)
                    {
                        $('.portal-nav-tab[data-tab-id="' + tabId + '"]').remove();
                        $('.portal-tab[data-element-id="' + tabId + '"]').remove();
                        
                        if (isActiveTab)
                        {
                            $('li.portal-nav-tab:first').trigger("click");
                        }
                        
                        if ($(".portal-tab").length == 1)
                        {
                            $(".portal-action-tab-delete").switchClass('show', 'hidden');
                        }
                    }
                });
            }
        }
    }
    
    function makeColumnsResizable()
    {
        try
        {
            $(".portal-column").resizable("destroy");
        }
        catch (error)
        {
        }
        
        var containerWidth = $('.portal-tabs').width();
        var gridWidth = ((containerWidth + 30) / 12);
        
        $(".portal-column").resizable({
            handles : 'e',
            autoHide : true,
            preventDefault : true,
            helper : 'ui-state-highlight',
            grid : gridWidth,
            stop : columnResizableStopped
        });
    }
    
    function columnResizableStopped(e, ui)
    {
        var column = $(this);
        var columnId = column.data('element-id');
        
        var proposedColumnWidth = getColumnGridValue(column.width());
        var currentColumnWidth = column.data('element-width');
        
        var totalColumnWidth = determineTotalColumnWidth($('.portal-tab:visible').data('element-id'));
        var proposedTotalColumnWidth = totalColumnWidth - currentColumnWidth + proposedColumnWidth;
        
        if (proposedTotalColumnWidth > 12)
        {
            proposedColumnWidth = proposedColumnWidth - (proposedTotalColumnWidth - 12);
        }
        
        column.css({
            width : "",
            height : ""
        });
        
        var parameters = {
            'application' : ajaxContext,
            'go' : 'ColumnWidth',
            'column' : columnId,
            'width' : proposedColumnWidth
        };
        
        var response = $.ajax({
            type : "POST",
            url : ajaxUri,
            data : parameters
        }).success(function(json)
        {
            if (json.result_code == 200)
            {
                column.removeClass(function(index, css)
                {
                    return (css.match(/(^|\s)col-md-\S+/g) || []).join(' ');
                });
                
                column.addClass('col-md-' + proposedColumnWidth);
            }
        });
    }
    
    function determineTotalColumnWidth(tabId)
    {
        var totalWidth = 0;
        
        $('.portal-tab[data-element-id="' + tabId + '"] .portal-column').each(function(i)
        {
            var currentColumn = $(this);
            var currentWidth = currentColumn.data('element-width');
            
            totalWidth += currentWidth;
        });
        
        return totalWidth;
    }
    
    function getColumnGridValue(currentWidth)
    {
        var containerWidth = $('.portal-tabs').width();
        var gridWidth = ((containerWidth + 30) / 12);
        
        return Math.round((currentWidth + 30) / gridWidth);
    }
    
    function toggleBlock(blockNode)
    {
        var block = $(blockNode).parent().parent().parent();
        var visibility = $('.portal-block-content', block).hasClass('hidden');
        
        var parameters = {
            'application' : ajaxContext,
            'go' : 'BlockVisibility',
            'block' : block.data('element-id'),
            'visibility' : visibility
        };
        
        var response = $.ajax({
            type : "POST",
            url : ajaxUri,
            data : parameters
        }).success(function(json)
        {
            if (json.result_code == 200)
            {
                $('.portal-block-content', block).toggleClass('hidden');
                $('.panel-heading', block).toggleClass('panel-heading-without-content');
                
                $('.portal-action-block-show', block).toggleClass('hidden');
                $('.portal-action-block-hide', block).toggleClass('hidden');
            }
        });
    }
    
    function hideBlock(e, ui)
    {
        e.preventDefault();
        toggleBlock(this);
    }
    
    function showBlock(e, ui)
    {
        e.preventDefault();
        toggleBlock(this);
    }
    
    function makeBlocksSortable()
    {
        try
        {
            $(".portal-column").sortable("destroy");
        }
        catch (error)
        {
        }
        
        $(".portal-column").sortable({
            handle : '.panel-heading',
            cancel : 'a,input',
            opacity : 0.8,
            forcePlaceholderSize : true,
            cursor : 'move',
            placeholder : 'portal-block-sortable-helper',
            scroll : true,
            connectWith : '.portal-column',
            update : blocksSortableUpdate
        });
    }
    
    function blocksSortableUpdate(e, ui)
    {
        var column, order;
        column = $(this).data('element-id');
        order = $(this).sortable("serialize", {
            key : "order[]",
            attribute : "data-element-id",
            expression : /([0-9]+)/
        });
        
        var parameters = {
            'application' : ajaxContext,
            'go' : 'BlockSort',
            'column' : column,
            'order' : order
        };
        
        var response = $.ajax({
            type : "POST",
            url : ajaxUri,
            data : parameters
        });
    }
    
    function makeTabsSortable()
    {
        try
        {
            $(".portal-nav-tabs").sortable("destroy");
        }
        catch (error)
        {
        }
        
        $(".portal-nav-tabs").sortable({
            cancel : 'span.portal-action-tab-delete',
            opacity : 0.8,
            forcePlaceholderSize : true,
            cursor : 'move',
            placeholder : 'bg-info',
            helper : function(event, element)
            {
                var tab = $(element);
                tab.width(tab.width() + 2);
                return tab;
            },
            scroll : true,
            update : tabsSortableUpdate
        });
    }
    
    function tabsSortableUpdate(e, ui)
    {
        var column, order;
        column = $(this).data('element-id');
        order = $(this).sortable("serialize", {
            key : "order[]",
            attribute : "data-tab-id",
            expression : /([0-9]+)/
        });
        
        var parameters = {
            'application' : ajaxContext,
            'go' : 'TabSort',
            'order' : order
        };
        
        var response = $.ajax({
            type : "POST",
            url : ajaxUri,
            data : parameters
        });
    }
    
    function filterComponents(e, ui)
    {
        var searchText = $('#portal-package-name').val();
        var searchContext = $('#portal-package-context').val();
        
        $('.portal-package-blocks div a.btn')
                .each(
                        function(i)
                        {
                            var component = $(this);
                            var block = component.data('block');
                            var title = component.prop('title');
                            
                            var isInSelectedContext = (searchContext.length == 0 || (searchContext.length > 0 && block
                                    .indexOf(searchContext) != -1));
                            var isTitleInQuery = (searchText.length == 0 || (searchText.length > 0 && title
                                    .indexOf(searchText) != -1));
                            
                            if (isInSelectedContext && isTitleInQuery)
                            {
                                component.parent().show();
                            }
                            else
                            {
                                component.parent().hide();
                            }
                        });
    }
});