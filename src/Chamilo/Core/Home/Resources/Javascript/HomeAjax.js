/*global $, addBlock, bindIcons, bindIconsLegacy, blocksDraggable, tabsDroppable, columnsResizable, columnsSortable, confirm, document, editTab, filterComponents, jQuery, showAllComponents, tabsSortable */

$(
    function () {

        var columns = $(".portal_column");
        var availableBlocks;
        var ajaxContext = 'Chamilo\\Core\\Home\\Ajax';
        var ajaxUri = getPath('WEB_PATH') + 'index.php';
        var translationContext = 'Chamilo\\Core\\Home';

        function checkForEmptyColumns() {
            $("div.portal_tab div.portal_column").each(
                function (i) {
                    var numberOfBlocks, emptyBlock;
                    numberOfBlocks = $(".portal-block", this).length;
                    emptyBlock = $(".empty_portal_column", this);
                    if (numberOfBlocks === 0) {
                        emptyBlock.show();
                    }
                    else {
                        emptyBlock.hide();
                    }
                }
            );

            bindIconsLegacy();
        }

        function sortableStart(e, ui) {
            ui.helper.css("border", "4px solid #c0c0c0");
        }

        function sortableBeforeStop(e, ui) {
            ui.helper.css("border", "0px solid #c0c0c0");
        }

        function sortableStop(e, ui) {
            // Fade the action links / images
            $("div.title a").fadeOut(150);
            checkForEmptyColumns();
        }

        function showTab(e, ui) {
            e.preventDefault();
            var tabId, tab;
            tabId = $(this).attr('id');
            tab = tabId.split("_");

            $("div.portal_tab:not(#portal_tab_" + tab[2] + ")").css('display', 'none');
            $("div #portal_tab_" + tab[2]).css('display', 'block');

            $("#tab_menu ul li.current").removeClass('current');
            $("#tab_menu ul li").addClass('normal');
            $("#tab_menu ul li#tab_select_" + tab[2]).removeClass('normal');
            $("#tab_menu ul li#tab_select_" + tab[2]).addClass('current');

            // $("li.current a.deleteTab").css('display', 'inline');
            // $("li.normal a.deleteTab").css('display', 'none');

            tabsDroppable();
        }

        function sortableUpdate(e, ui) {
            var column, order;
            column = $(this).attr("id");
            order = $(this).sortable("serialize");

            var parameters = {
                'application': ajaxContext,
                'go': 'block_sort',
                'column': column,
                'order': order
            };

            var response = $.ajax(
                {
                    type: "POST",
                    url: ajaxUri,
                    data: parameters,
                    async: false
                }
            );
        }

        function tabsSortableUpdate(e, ui) {
            var order = $(this).sortable("serialize");

            var parameters = {
                'application': ajaxContext,
                'go': 'tab_sort',
                'order': order
            };

            var response = $.ajax(
                {
                    type: "POST",
                    url: ajaxUri,
                    data: parameters,
                    async: false
                }
            ).responseText;
        }

        function resizableStop(e, ui) {
            var columnId, rowId, countColumns, widthBox, widthRow, widthPercentage, widthCurrentTotal, widthSurplus;

            columnId = $(this).attr("id");
            tabId = $(this).parent().attr("id");
            countColumns = $("div.portal_column", $(this).parent()).length;

            widthBox = $(this).width();
            widthRow = $(this).parent().width();
            widthPercentage = (widthBox / widthRow) * 100;
            widthPercentage = widthPercentage.toFixed(0);

            widthCurrentTotal = 0;

            $("#" + tabId + " div.portal_column").each(
                function (i) {
                    var curWidthBox, curWidthPercentage;
                    curWidthBox = $(this).width();
                    curWidthPercentage = (curWidthBox / widthRow) * 100;
                    curWidthPercentage = parseInt(curWidthPercentage.toFixed(0), 10);

                    widthCurrentTotal = widthCurrentTotal + curWidthPercentage;
                }
            );

            widthCurrentTotal = widthCurrentTotal + countColumns - 1;

            if (widthCurrentTotal > 100) {
                widthSurplus = widthCurrentTotal - 100;

                widthPercentage = widthPercentage - widthSurplus;
                widthBox = ((widthRow / 100) * widthPercentage) - 1;
            }

            $(this).css('width', widthPercentage + "%");

            var parameters = {
                'application': ajaxContext,
                'go': 'column_width',
                'column': columnId,
                'width': widthPercentage
            };

            var response = $.ajax(
                {
                    type: "POST",
                    url: ajaxUri,
                    data: parameters,
                    async: false
                }
            );
        }

        function collapseItem(e, ui) {
            e.preventDefault();
            $(this).parent().next(".description").slideToggle(300);

            $(this).children(".invisible").toggle();
            $(this).children(".visible").toggle();

            var parameters = {
                'application': ajaxContext,
                'go': 'block_visibility',
                'block': $(this).parent().parent().attr("id")
            };

            var response = $.ajax(
                {
                    type: "POST",
                    url: ajaxUri,
                    data: parameters,
                    async: false
                }
            );
        }

        function hoverInItem() {
            $(this).children("a").fadeIn(150);
        }

        function hoverOutItem() {
            $(this).children("a").fadeOut(150);
        }

        function deleteItem(e) {
            e.preventDefault();

            var confirmation, columnId, blockId, order;

            confirmation = confirm(getTranslation('Confirm', null, translationContext));
            if (confirmation) {
                columnId = $(this).parent().parent().parent().attr("id");
                blockId = $(this).parent().parent().attr("id");

                $(this).parent().parent().remove();

                var parameters = {
                    'application': ajaxContext,
                    'go': 'block_delete',
                    'block': blockId
                };

                var response = $.ajax(
                    {
                        type: "POST",
                        url: ajaxUri,
                        data: parameters,
                        async: false
                    }
                );

                order = $("#" + columnId).sortable("serialize");

                var parameters = {
                    'application': ajaxContext,
                    'go': 'block_sort',
                    'column': columnId,
                    'order': order
                };

                var response = $.ajax(
                    {
                        type: "POST",
                        url: ajaxUri,
                        data: parameters,
                        async: false
                    }
                );
            }

            checkForEmptyColumns();
        }

        function removeBlockScreen(e, ui) {
            $("#portal-block-container").hide();
            $("#tab_menu li.current").removeClass('current-no-border');
            $("a.addEl").show();
        }

        function getEmptyBlocksContainer() {
            var container = $("<div></div>");
            container.attr('id', 'portal-block-container');

            var listContainer = $("<div></div>");
            listContainer.attr('id', 'list');

            container.append(listContainer);

            var packageContainer = $("<div></div>");
            packageContainer.attr('id', 'packages');

            listContainer.append(packageContainer);

            var packageSearch = $("<input />");
            packageSearch.attr('id', 'package-search');
            packageSearch.attr('type', 'text');
            packageSearch.val(getTranslation('SearchForWidgets', null, translationContext));

            var packagesList = $("<div></div>");
            packagesList.attr('id', 'packages-list');

            packageContainer.append(packageSearch);
            packageContainer.append(packagesList);

            var componentTitle = $("<div></div>");
            componentTitle.attr('id', 'component-title');
            componentTitle.html(getTranslation('BrowseBlocks', null, translationContext));
            listContainer.append(componentTitle);

            var components = $("<div></div>");
            components.attr('id', 'components');
            listContainer.append(components);

            var clear = $("<div></div>");
            clear.addClass('clear');
            clear.html('&nbsp;');

            listContainer.append(clear);

            var closingBar = $('<div></div>');
            closingBar.attr('id', 'remove-block-container');

            container.append(closingBar);

            return container;
        }

        function prepareWidgetSearch(e, ui) {
            var search = $("#portal-block-container input");
            if (search.val() == getTranslation('SearchForWidgets', null, 'Chamilo\\Core\\Home')) {
                search.val('');
                search.addClass('query');
            }
        }

        function clearWidgetSearch(e, ui) {
            var search = $("#portal-block-container input");

            if (search.val() == '') {
                search.val(getTranslation('SearchForWidgets', null, translationContext));
                search.removeClass('query');

                $('#component-title').html(getTranslation('BrowseBlocks', null, translationContext))
            }
        }

        function addPackages() {
            var packagesList = $("#packages-list");
            var ordering = [];

            packagesList.append(availablePackage);

            for (var i in availableBlocks) {
                ordering[i] = availableBlocks[i].name;
            }

            ordering = asort(ordering);

            for (var i in ordering) {
                var availablePackage = $('<span></span>');
                availablePackage.addClass('package');
                availablePackage.attr('data-context', i);
                // availablePackage.css('background-image', 'url(' +
                // availableBlocks[i].image + ')');

                var availablePackageTitle = $('<span></span>');
                availablePackageTitle.addClass('title');
                availablePackageTitle.html(availableBlocks[i].name);

                availablePackage.append(availablePackageTitle);
                availablePackage.append(getRemoveSelectedPackageFilter());
                availablePackage.append(getSelectedPackageIndicator());

                packagesList.append(availablePackage);
            }
        }

        function addComponents() {
            var componentsList = $("#components");

            componentsList.empty();

            for (var i in availableBlocks) {
                for (var j in availableBlocks[i].components) {
                    var blockName = availableBlocks[i].components[j].name;
                    var availableComponent = $('<div></div>');

                    availableComponent.addClass('component');
                    availableComponent.css('cursor', 'pointer');
                    availableComponent.attr('data-block', availableBlocks[i].components[j].id);
                    availableComponent.attr('title', availableBlocks[i].components[j].name);

                    componentButton = $('<div />');
                    componentButton.addClass('button');
                    availableComponent.append(componentButton);

                    componentContent = $('<div />');
                    componentContent.addClass('content');
                    availableComponent.append(componentContent);

                    componentImage = $('<div />');
                    componentImage.addClass('image');
                    componentImage.css('background-image', 'url(' + availableBlocks[i].components[j].image + ')');
                    componentContent.append(componentImage);

                    componentTitle = $('<div />');
                    componentTitle.addClass('title');
                    componentTitle.html(availableBlocks[i].components[j].name);
                    componentContent.append(componentTitle);

                    componentsList.append(availableComponent);
                }
            }

            $('#component-title').html(getTranslation('BrowseBlocks', null, translationContext));
        }

        function showBlockScreen(e, ui) {
            e.preventDefault();

            $("a.addEl").hide();

            var block = $('#addBlock');
            if (block.length > 0) {
                return;
            }

            var blockContainer = $("#portal-block-container");

            if (blockContainer.length == 0) {
                container = getEmptyBlocksContainer();

                $("#tab_menu").after(container);

                addPackages();
                addComponents();

                $("#portal-block-container").show();
                $("#tab_menu li.current").addClass('current-no-border');
                $("#remove-block-container").bind('click', removeBlockScreen);
                $(document).on('click', "#components .component", addBlock);
                // $(".component").css('cursor', 'pointer');

                $("#portal-block-container input").bind('focusin', prepareWidgetSearch);
                $("#portal-block-container input").bind('focusout', clearWidgetSearch);
                $("#portal-block-container input").bind('change', hideComponents);

                $(document).on('click', "#packages-list span.package span.title", filterComponents);

                $(document).on('click', "#packages-list span.package a.remove-package-filter", removePackageFilter);
            }
            else {
                $("#portal-block-container").show();
                $("#tab_menu li.current").addClass('current-no-border');
            }
        }

        function getAvailableBlocks() {

            var parameters = {
                'application': ajaxContext,
                'go': 'block_list'
            };

            var response = $.ajax(
                {
                    type: "POST",
                    url: ajaxUri,
                    data: parameters,
                    async: false
                }
            ).success(
                function (json) {
                    availableBlocks = json.properties.blocks;
                }
            );
        }

        function addBlock(e, ui) {
            var column, columnId, order, is_empty;

            column = $(".portal_tab:visible .portal_column:last");

            is_empty = $(".portal_tab:visible .portal_column:last .empty_portal_column:visible").length != 0;

            columnId = column.attr("id");
            order = column.sortable("serialize");

            var blockId = $(this).data("block");

            var parameters = {
                'application': ajaxContext,
                'go': 'block_add',
                'block': blockId,
                'column': columnId,
                'order': order
            };

            var response = $.ajax(
                {
                    type: "POST",
                    url: ajaxUri,
                    data: parameters,
                    async: false
                }
            ).success(
                function (json) {
                    if (json.result_code == 200) {
                        column.prepend(json.properties.block);

                        $("div.title a").css('display', 'none');
                        order = column.sortable("serialize");

                        bindIconsLegacy();
                        blocksDraggable();
                        checkForEmptyColumns();

                        var parameters = {
                            'application': ajaxContext,
                            'go': 'block_sort',
                            'column': columnId,
                            'order': order
                        };

                        var response = $.ajax(
                            {
                                type: "POST",
                                url: ajaxUri,
                                data: parameters,
                                async: false
                            }
                        );
                    }
                }
            );
        }

        function getSelectedPackageIndicator() {
            return $(
                '<a/>', {
                    'class': 'selected-package-indicator',
                    href: '#'
                }
            );
        }

        function getRemoveSelectedPackageFilter() {
            return $(
                '<a/>', {
                    'class': 'remove-package-filter',
                    href: '#'
                }
            );
        }

        function removePackageFilter() {
            var selectedPackage = $(this).parent();

            $("#packages-list .package").removeClass('selected-package');
            $("#packages-list .package .selected-package-indicator").hide();
            $("#packages-list .package .remove-package-filter").hide();

            $('#component-title').html(getTranslation('BrowseBlocks', null, translationContext))

            hideComponents();
        }

        function filterComponents(e, ui) {
            var selectedPackage = $(this).parent();

            var search = $("#portal-block-container input");
            search.val(getTranslation('SearchForWidgets', null, translationContext));
            search.removeClass('query');

            $("#packages-list .package").removeClass('selected-package');
            $("#packages-list .package .selected-package-indicator").hide();

            selectedPackage.addClass('selected-package');
            $('.selected-package-indicator', selectedPackage).show();
            $('.remove-package-filter', selectedPackage).show();

            hideComponents();

            $('#component-title').html(getTranslation('BrowseBlockCategory', null, translationContext));
        }

        function hideComponents() {
            var componentsList = $("#components");
            var searchQuery = $('input#package-search.query').val();

            if (typeof (searchQuery) !== 'undefined') {
                if (searchQuery == '') {
                    $('#component-title').html(getTranslation('BrowseBlocks', null, translationContext));
                }
                else {
                    $('#component-title').html(
                        getTranslation('SearchBlocks', null, translationContext) + ': ' + searchQuery
                    )
                    $("#packages-list .package").removeClass('selected-package');
                    $("#packages-list .package .selected-package-indicator").hide();
                    $("#packages-list .package .remove-package-filter").hide();
                }
            }

            var selectedPackage = $('#packages-list .package.selected-package').data('context');
            if (typeof (selectedPackage) == 'string' && selectedPackage !== 'all_packages') {
                packageId = selectedPackage;
            }
            else {
                selectedPackage = null;
            }

            var displayPackage = true;

            for (var i in availableBlocks) {
                if (typeof (selectedPackage) == 'string' && i != packageId) {
                    displayPackage = false;
                }
                else {
                    displayPackage = true;
                }

                for (var j in availableBlocks[i].components) {

                    var blockName = availableBlocks[i].components[j].name;
                    var packageName = availableBlocks[i].components[j].id.replace(/\\/g, '\\\\');

                    var block = $('div.component[data-block="' + packageName + '"]');

                    if (typeof (searchQuery) !== 'undefined' && blockName.indexOf(searchQuery) === -1) {
                        block.hide();
                    }
                    else if (typeof (searchQuery) !== 'undefined' && blockName.indexOf(searchQuery) !== -1) {
                        block.show();
                    }
                    else if (displayPackage === true) {
                        block.show();
                    }
                    else {
                        block.hide();
                    }
                }
            }

            if ($('#components .component').length == 0) {
                var noMatches = $('<div />');
                noMatches.addClass('no-matches');

                if (typeof (searchQuery) !== 'undefined') {
                    noMatches.html('Sorry, your search for "' + searchQuery + '" does not match any widgets.');
                }
                else {
                    noMatches.html(getTranslation('NoWidgetsAvailable'));
                }
                componentsList.append(noMatches)
            }
        }

        function showAllComponents(e, ui) {
            $("#components").children().show();
        }

        function addTab(e, ui) {
            e.preventDefault();

            var parameters = {
                'application': ajaxContext,
                'go': 'tab_add'
            };

            var response = $.ajax(
                {
                    type: "POST",
                    url: ajaxUri,
                    data: parameters,
                    async: false
                }
            ).success(
                function (json) {
                    $("#main .portal_tab:last").after(json.properties.html);
                    $("#tab_menu ul").append(json.properties.title);

                    bindIconsLegacy();
                    tabsSortable();
                    columnsSortable();
                    columnsResizable();
                    tabsDroppable();

                    $("#tab_menu ul li.current").removeClass('current');
                    $("#tab_menu ul li").addClass('normal');
                    $("#tab_menu ul li:last").addClass('current');
                    $("#tab_menu ul li:last").removeClass('normal');

                    $(".portal_tab").css('display', 'none');
                    var newTabId = $("#tab_menu ul li:last").attr('id');
                    newTabId = newTabId.split("_");
                    newTabId = newTabId[2];
                    $("#portal_tab_" + newTabId).css('display', 'block');
                }
            );
        }

        function addColumn(e, ui) {
            e.preventDefault();

            var tab, tabId;

            tab = $(".portal_tab:visible");
            tabId = tab.attr('id');

            var parameters = {
                'application': ajaxContext,
                'go': 'column_add',
                'tab': tabId
            };

            var response = $.ajax(
                {
                    type: "POST",
                    url: ajaxUri,
                    data: parameters,
                    async: false
                }
            ).success(
                function (json) {
                    var columnHtml, newWidths, lastColumn;

                    columnHtml = json.properties.html;
                    newWidths = json.properties.width;

                    lastColumn = $("div.portal_column:last", tab);

                    if (lastColumn.length > 0) {
                        lastColumn.css('margin-right', '1%');

                        $("div.portal_column", tab).each(
                            function (i) {
                                var newWidth = newWidths[this.id] + '%';
                                this.style.width = newWidth;
                            }
                        );

                        $("div.portal_column:last", tab).after(columnHtml);
                    }
                    else {
                        tab.append(columnHtml);
                    }

                    bindIconsLegacy();
                    columnsSortable();
                    columnsResizable();
                }
            );
        }

        function deleteTab(e, ui) {
            e.preventDefault();

            var confirmation, tab, tabId;

            confirmation = confirm(getTranslation('Confirm', null, translationContext));
            if (confirmation) {
                tab = $(this).parent().attr('id');
                tab = tab.split("_");

                tabId = tab[2];

                var parameters = {
                    'application': ajaxContext,
                    'go': 'tab_delete',
                    'tab': tabId
                };

                var response = $.ajax(
                    {
                        type: "POST",
                        url: ajaxUri,
                        data: parameters,
                        async: false
                    }
                ).success(
                    function (json) {
                        if (json.result_code == 200) {
                            $('#portal_tab_' + tabId).remove();
                            $('#tab_select_' + tabId).remove();

                            // Show the first existing tab
                            $("#tab_menu ul li.current").removeClass('current');
                            $("#tab_menu ul li").addClass('normal');
                            $("#tab_menu ul li:first").addClass('current');
                            $("#tab_menu ul li:first").removeClass('normal');

                            var newTabId = $("#tab_menu ul li:first").attr('id');
                            newTabId = newTabId.split("_");
                            newTabId = newTabId[2];
                            $("#portal_tab_" + newTabId).css('display', 'block');

                            // $("li.current a.deleteTab").css('display', 'inline');
                            // $("li.normal a.deleteTab").css('display', 'none');

                            $("#tab_menu li").unbind();
                            $("#tab_menu li:not(.current)").bind('click', showTab);
                            $("#tab_menu li.current").bind('click', editTab);
                        }
                    }
                );
            }
        }

        function editTab(e, ui) {
            e.preventDefault();
            try {
                $("#tab_menu #tab_elements").sortable("destroy");
            }
            catch (error) {
            }

            var tabTitle = $(this);
            var tab = tabTitle.parent();
            var titleInput = $('<input />');
            titleInput.attr('id', 'current-tab-title');
            titleInput.attr('type', 'text');
            titleInput.val(tabTitle.text());

            tabTitle.hide();
            tabTitle.after(titleInput);

            $("#current-tab-title").bind(
                'keypress', {}, function (e) {
                    var code = (e.keyCode ? e.keyCode : e.which);
                    // If ENTER is pressed we save the new tab title
                    if (code === 13) {

                        var tabId, newTitle;

                        tabId = $('#tab_menu li.current').attr('id').split("_");
                        tabId = tabId[2];

                        newTitle = $('#current-tab-title').val();

                        var parameters = {
                            'application': ajaxContext,
                            'go': 'tab_edit',
                            'tab': tabId,
                            'title': newTitle
                        };

                        var response = $.ajax(
                            {
                                type: "POST",
                                url: ajaxUri,
                                data: parameters,
                                async: false
                            }
                        ).success(
                            function (json) {
                                if (json.result_code == 200) {
                                    $('#tab_menu li.current a.tabTitle').html($('#current-tab-title').val());
                                    $('#current-tab-title').remove();
                                    $('#tab_menu li.current a.tabTitle').show();
                                }
                            }
                        );

                    }
                    else if (code === 27) {
                        $("#current-tab-title").remove();
                        tabTitle.show();
                    }
                }
            );
        }

        function editBlock(e, ui) {
            e.preventDefault();

            var blockEntryTitle = $(this);
            var blockTitle = blockEntryTitle.parent();
            var block = blockTitle.parent();

            var blockId = block.attr('id').split('_')[2];

            var titleInput = $('<input />');
            titleInput.addClass('block-title');
            titleInput.attr('id', 'block-' + blockId + '-title');
            titleInput.attr('type', 'text');
            titleInput.val(blockEntryTitle.text());

            blockEntryTitle.hide();
            blockEntryTitle.after(titleInput);

            titleInput.bind(
                'keypress', {}, function (e) {
                    var code = (e.keyCode ? e.keyCode : e.which);
                    // If ENTER is pressed we save the new tab title
                    if (code === 13) {
                        newTitle = titleInput.val();

                        var parameters = {
                            'application': ajaxContext,
                            'go': 'block_edit',
                            'block': blockId,
                            'title': newTitle
                        };

                        var response = $.ajax(
                            {
                                type: "POST",
                                url: ajaxUri,
                                data: parameters,
                                async: false
                            }
                        ).success(
                            function (json) {
                                if (json.result_code == 200) {
                                    blockEntryTitle.html(newTitle);
                                    titleInput.remove();
                                    blockEntryTitle.show();
                                }
                            }
                        );

                    }
                    else if (code === 27) {
                        titleInput.remove();
                        blockEntryTitle.show();
                    }
                }
            );
        }

        function deleteColumn(e, ui) {
            var column, columnId;

            column = $(this).parent().parent();
            columnId = column.attr("id").split("_");
            columnId = columnId[2];

            var parameters = {
                'application': ajaxContext,
                'go': 'column_delete',
                'column': columnId
            };

            var response = $.ajax(
                {
                    type: "POST",
                    url: ajaxUri,
                    data: parameters,
                    async: false
                }
            ).success(
                function (json) {
                    if (json.result_code == 200) {
                        var columnWidth, otherColumn, otherColumnWidth, newColumnWidth;

                        // Get the deleted column's width
                        columnWidth = column[0].style.width; // need
                        columnWidth = parseInt(columnWidth.replace('%', ''), 10);
                        column.remove();

                        // Get the last column's width
                        otherColumn = $(".portal_tab:visible .portal_column:last");

                        if (otherColumn.length > 0) {
                            var columns, total_width;
                            columns = $(".portal_tab:visible .portal_column");
                            total_width = 0;
                            columns.each(
                                function (key, e) {
                                    var width = e.style.width;
                                    width = parseInt(width.replace('%', ''), 10);
                                    total_width += width + 1;
                                }
                            )

                            otherColumnWidth = otherColumn[0].style.width; // need
                            otherColumnWidth = parseInt(otherColumnWidth.replace('%', ''), 10);

                            // Calculate the new width
                            newColumnWidth = Math.max(101 - (total_width - otherColumnWidth), otherColumnWidth);
                            newColumnWidth = Math.min(100, newColumnWidth);
                            newColumnWidth = columns.length === 1 ? 100 : newColumnWidth; // i.e.

                            // Set the new width + postback
                            otherColumn.css('margin-right', '0px');
                            otherColumn.css('width', newColumnWidth + '%');

                            var ajaxUri = getPath('WEB_PATH') + 'index.php';

                            var parameters = {
                                'application': ajaxContext,
                                'go': 'column_width',
                                'column': otherColumn.attr('id'),
                                'width': newColumnWidth
                            };

                            var response = $.ajax(
                                {
                                    type: "POST",
                                    url: ajaxUri,
                                    data: parameters,
                                    async: false
                                }
                            );
                        }
                    }
                }
            );
        }

        function saveBlockConfiguration(e, ui) {

        }

        function configureBlock(e, ui) {
            e.preventDefault();

            var configurationButton = $(this);
            var blockTitle = configurationButton.parent();
            var block = blockTitle.parent();

            var blockId = block.attr('id').split('_')[2];
            var blockEntryTitle = $('.entry-title', block);

            var parameters = {
                'application': ajaxContext,
                'go': 'block_config_form',
                'block': blockId
            };

            var response = $.ajax(
                {
                    type: "POST",
                    url: ajaxUri,
                    data: parameters,
                    async: false
                }
            ).success(
                function (json) {
                    if (json.result_code == 200) {
                        var form = $('<div />');
                        form.append(
                            $('<div />').append(
                                $('<h3 />').html(
                                    getTranslation(
                                        'ConfigureBlock', {
                                            'BLOCK': blockEntryTitle.html()
                                        }, translationContext
                                    )
                                )
                            )
                        );
                        form.append(json.properties.form);

                        loading = $.modal(
                            form, {
                                overlayId: 'home-modal-overlay',
                                containerId: 'home-modal-container',
                                closeClass: 'home-modal-close',
                                opacity: 50
                            }
                        );

                        $('form#block button[name="submit"]').bind(
                            'click', function (e, ui) {
                                e.preventDefault();

                                var submittedData = {};

                                $('form#block :input').each(
                                    function (index) {
                                        var inputElement = $(this);
                                        if (inputElement.attr('type') != 'radio' ||
                                            inputElement.prop('checked') == true) {
                                            submittedData[inputElement.attr('name')] = inputElement.val();
                                        }
                                    }
                                );

                                var parameters = {
                                    'application': ajaxContext,
                                    'go': 'block_config',
                                    'block': blockId,
                                    'data': submittedData
                                };

                                var response = $.ajax(
                                    {
                                        type: "POST",
                                        url: ajaxUri,
                                        data: parameters,
                                        async: false
                                    }
                                ).success(
                                    function (json) {
                                        if (json.result_code == 200) {
                                            $('#portal_block_' + blockId).before(json.properties.block).remove();

                                            bindIconsLegacy();
                                            blocksDraggable();
                                            checkForEmptyColumns();

                                            loading.close();
                                        }
                                    }
                                );
                            }
                        )
                    }
                }
            );
        }

        function bindIconsLegacy() {
            $("div.title a").hide();
            $("div.title").unbind();
            $("div.title").bind('mouseenter', hoverInItem);
            $("div.title").bind('mouseleave', hoverOutItem);
        }

        function bindIcons() {
            $(document).on('click', "a.closeEl", collapseItem);
            $(document).on('click', "a.deleteEl", deleteItem);
            $(document).on('click', "a.addEl", showBlockScreen);

            $(document).on('click', "#tab_menu li:not(.current)", showTab);
            $(document).on(
                'click', "#tab_menu li.current a.tabTitle", function (e, ui) {
                    e.preventDefault();
                }
            );
            $(document).on('dblclick', "#tab_menu li.current a.tabTitle", editTab);

            $(document).on('dblclick', "div.portal-block div.title div.entry-title", editBlock);
            $(document).on('click', "a.configEl", configureBlock);

            $(document).on('click', "a.addTab", addTab);
            $(document).on('click', "a.addColumn", addColumn);
            $(document).on('click', "a.deleteTab", deleteTab);

            $(document).on('click', ".deleteColumn", deleteColumn);
        }

        function getDraggableParent(e, ui) {
            return $(this).parent().parent().html();
        }

        function beginDraggable() {
            $("div.title").unbind();
        }

        function endDraggable() {
            bindIconsLegacy();
        }

        function blocksDraggable() {
            try {
                $("a.dragEl").draggable("destroy");
            }
            catch (error) {
            }
            $("a.dragEl").draggable(
                {
                    revert: true,
                    scroll: true,
                    cursor: 'move',
                    start: beginDraggable,
                    stop: endDraggable,
                    placeholder: 'blockSortHelper'
                }
            );
        }

        function processDroppedBlock(e, ui) {
            var newTab, newTabSplit, newTabId, block, blockSplit, blockId, newColumn, newColumnSplit, newColumnId, theBlock;

            // Retrieving some variables
            newTab = $(this).attr('id');
            newTabSplit = newTab.split("_");
            newTabId = newTabSplit[2];

            block = ui.draggable.attr('id');
            blockSplit = block.split("_");
            blockId = blockSplit[2];

            newColumn = $("#portal_tab_" + newTabId + " .portal_column:first").attr('id');
            newColumnSplit = newColumn.split("_");
            newColumnId = newColumnSplit[2];

            theBlock = ui.draggable.parent().parent();

            // Do the actual move + postback

            var parameters = {
                'application': ajaxContext,
                'go': 'block_move',
                'column': newColumnId,
                'block': blockId
            };

            var response = $.ajax(
                {
                    type: "POST",
                    url: ajaxUri,
                    data: parameters,
                    async: false
                }
            ).success(
                function (json) {
                    if (json.result_code == 200) {
                        // Does the column have blocks
                        var blockCount = $("#" + newColumn + " .portal-block").length;
                        if (blockCount > 0) {
                            $("#" + newColumn + " .portal-block:last").after(theBlock);
                        }
                        else {
                            $("#" + newColumn).append(theBlock);
                        }

                        checkForEmptyColumns();

                        $("#tab_menu ul li.current").removeClass('current');
                        $("#tab_menu ul li").addClass('normal');
                        $("#tab_menu ul li#tab_select_" + newTabId).addClass('current');
                        $("#tab_menu ul li#tab_select_" + newTabId).removeClass('normal');
                        $(".portal_tab").css('display', 'none');
                        $("#portal_tab_" + newTabId).css('display', 'block');

                        blocksDraggable();
                        tabsDroppable();
                    }
                }
            );
        }

        function tabsDroppable() {
            try {
                $("#tab_elements li").droppable("destroy");
            }
            catch (error) {
            }
            $("#tab_elements li.normal").droppable(
                {
                    accept: "a.dragEl",
                    drop: processDroppedBlock
                }
            );
        }

        function columnsSortable() {
            try {
                $("div.portal_column").sortable("destroy");
            }
            catch (error) {
            }
            $("div.portal_column").sortable(
                {
                    handle: 'div.title',
                    cancel: 'a',
                    opacity: 0.8,
                    forcePlaceholderSize: true,
                    cursor: 'move',
                    helper: 'original',
                    placeholder: 'blockSortHelper',
                    revert: true,
                    scroll: true,
                    connectWith: '.portal_column',
                    start: sortableStart,
                    beforeStop: sortableBeforeStop,
                    stop: sortableStop,
                    update: sortableUpdate,
                    cancel: 'input'
                }
            );
        }

        function tabsSortable() {
            try {
                $("#tab_menu #tab_elements").sortable("destroy");
            }
            catch (error) {

            }
            $("#tab_menu #tab_elements").sortable(
                {
                    cancel: 'a.deleteTab',
                    opacity: 0.8,
                    forcePlaceholderSize: true,
                    cursor: 'move',
                    helper: 'original',
                    placeholder: 'tabSortHelper',
                    revert: true,
                    scroll: true,
                    update: tabsSortableUpdate
                }
            );
        }

        function columnsResizable() {
            try {
                $("div.portal_column").resizable("destroy");
            }
            catch (error) {
            }
            $("div.portal_column").resizable(
                {
                    handles: 'e',
                    autoHide: true,
                    ghost: true,
                    preventDefault: true,
                    helper: 'ui-state-highlight',
                    stop: resizableStop
                }
            );
        }

        // Extension to jQuery selectors which only returns visible elements
        $.extend(
            $.expr[':'], {
                visible: function (a) {
                    return $(a).css('display') !== 'none';
                }
            }
        );

        $(document).ready(
            function () {
                $('#tab_actions').show();
                getAvailableBlocks();

                bindIconsLegacy();
                bindIcons();

                tabsSortable();

                blocksDraggable();
                tabsDroppable();

                columnsSortable();
                columnsResizable();
            }
        );

    }
);