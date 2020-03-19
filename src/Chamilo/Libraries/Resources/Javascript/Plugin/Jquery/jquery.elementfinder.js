/**
 * Copyright (c) 2009, Hans De Bisschop, conversion to seperate (non ui-tabs
 * based) plugin
 */

(function ($) {
    $.fn
        .extend({
            elementfinder: function (options) {

                // Settings list and the default values
                var defaults = {
                    name: '',
                    search: '',
                    nodesSelectable: false,
                    loadElements: false,
                    defaultQuery: ''
                };

                var settings = $.extend(defaults, options);
                var self = this, id, originalActivatedElements, activatedElements = [], excludedElements,
                    inactiveBox, activeBox;
                var timer;

                function collapseItem(e) {
                    $("ul:first", $(this).parent()).hide();
                    if ($(this).hasClass("lastCollapse")) {
                        $(this).removeClass("lastCollapse");
                        $(this).addClass("lastExpand");
                    }
                    else if ($(this).hasClass("collapse")) {
                        $(this).removeClass("collapse");
                        $(this).addClass("expand");
                    }
                }

                function expandItem(e) {
                    $("ul:first", $(this).parent()).show();
                    if ($(this).hasClass("lastExpand")) {
                        $(this).removeClass("lastExpand");
                        $(this).addClass("lastCollapse");
                    }
                    else if ($(this).hasClass("expand")) {
                        $(this).removeClass("expand");
                        $(this).addClass("collapse");
                    }
                }

                function destroyTree() {
                    $("div", self).removeClass("last");
                    $("div", self).removeClass("collapse");
                    $("div", self).removeClass("lastCollapse");
                }

                function processFinderTree() {
                    destroyTree();
                    processTree();
                }

                function processTree() {
                    $("ul li:last-child > div", self).addClass("last");
                    $("ul li:last-child > ul", self).css(
                        "background-image", "none");

                    $("ul li:not(:last-child):has(ul) > div", self)
                        .addClass("collapse");
                    $("ul li:last-child:has(ul) > div", self).addClass(
                        "lastCollapse");

                    // $("ul li:has(ul) > div", self).toggle(collapseItem,
                    // expandItem);
                    // $("ul li:has(ul) > div > a",
                    // self).click(function(e){e.stopPropagation();});
                }

                function displayMessage(message, element) {
                    element.html(message);
                }


                function getExcludedElements() {
                    var elements = eval(settings.name + '_excluded');

                    return elements;
                }

                function getSearchResults() {
                    var query = $('#' + settings.name + '_search_field')
                        .val();

                    var response = $.ajax({
                        type: "GET",
                        dataType: "xml",
                        url: settings.search,
                        data: {
                            query: query,
                            'exclude': getExcludedElements()
                        },
                        async: false
                    }).responseText;
                    return response;
                }

                function buildElementTree(response) {
                    var ul = $('<ul class="tree-menu"></ul>');

                    var tree = $.xml2json(response, true);

                    if ((tree.node && $(tree.node).size() > 0)
                        || (tree.leaf && $(tree.leaf).size() > 0)) {
                        if (tree.node && $(tree.node).size() > 0) {
                            $.each(tree.node, function (i, the_node) {
                                var li = $('<li><div><a href="#" id="'
                                    + the_node.id + '" title="'
                                    + the_node.description + '"><span class="' + the_node.classes + '"></span> '
                                    + the_node.title
                                    + '</a></div></li>');
                                $(ul).append(li);
                                buildElement(the_node, li);
                            });
                        }

                        if (tree.leaf && $(tree.leaf).size() > 0) {
                            $.each(tree.leaf, function (i, the_leaf) {
                                var li = $('<li><div><a href="#" id="'
                                    + the_leaf.id + '" title="'
                                    + the_leaf.description + '"><span class="' + the_leaf.classes + '"></span> '
                                    + the_leaf.title
                                    + '</a></div></li>');
                                $(ul).append(li);
                            });
                        }

                        $(inactiveBox).html(ul);
                    }
                    else {
                        displayMessage(getTranslation('NoSearchResults',
                            null, 'Chamilo\\Libraries'
                        ), inactiveBox);
                    }
                }

                function buildElement(the_node, element) {
                    if ((the_node.node && $(the_node.node).size() > 0)
                        || (the_node.leaf && $(the_node.leaf).size() > 0)) {
                        var ul = $('<ul></ul>');
                        $(element).append(ul);

                        if (the_node.node && $(the_node.node).size() > 0) {
                            $
                                .each(
                                    the_node.node,
                                    function (i, a_node) {
                                        var li = $('<li><div><a href="#" id="'
                                            + a_node.id
                                            + '" title="'
                                            + a_node.description
                                            + '"><span class="' + a_node.classes + '"></span> '
                                            + a_node.title
                                            + '</a></div></li>');
                                        $(ul).append(li);
                                        buildElement(a_node, li);
                                    }
                                );
                        }

                        if (the_node.leaf && $(the_node.leaf).size() > 0) {
                            $
                                .each(
                                    the_node.leaf,
                                    function (i, a_leaf) {
                                        var li = $('<li><div><a href="#" id="'
                                            + a_leaf.id
                                            + '" title="'
                                            + a_leaf.description
                                            + '"><span class="' + a_leaf.classes + '"></span> '
                                            + a_leaf.title
                                            + '</a></div></li>');
                                        $(ul).append(li);
                                    }
                                );
                        }
                    }
                }

                function searchFieldChanged() {
                    var query = $('#' + settings.name + '_search_field')
                        .val();

                    if (query.length === 0 && !settings.loadElements) {
                        displayMessage(getTranslation('EnterSearchQuery',
                            null, 'Chamilo\\Libraries'
                        ), inactiveBox);
                    }
                    else {

                        // Filter out stars
                        var replacedQuery = str_replace('*', '', query);

                        if (replacedQuery.length == 0) {
                            displayMessage(getTranslation(
                                'SearchNoWildcards', null,
                                'Chamilo\\Libraries'
                            ), inactiveBox);
                        }
                        else {
                            if (replacedQuery.length < 3) {
                                displayMessage(getTranslation(
                                    'SearchTooShort', null,
                                    'Chamilo\\Libraries'
                                ), inactiveBox);
                            }
                            else {
                                updateElements();
                            }
                        }
                    }
                }

                function updateElements() {
                    displayMessage(
                        '<div class="element_finder_loading"><span class="fas fa-spinner fa-pulse fa-4x"></span></div>',
                        inactiveBox
                    );
                    var searchResults = getSearchResults();
                    buildElementTree(searchResults);
                    disableActivatedElements();
                    processFinderTree();
                }

                function setOriginalActivatedElements() {
                    var ul = $('<ul class="tree-menu"></ul>');
                    $.each(originalActivatedElements, function (
                        i,
                        activatedElement
                    ) {

                        activatedElements.push(activatedElement);
                        var li = $('<li><div><a href="#" id="'
                            + activatedElement.id + '"><span class="' + activatedElement.classes + '"></span> '
                            + activatedElement.title
                            + '</a></div></li>');
                        ul.append(li);
                    });

                    $("#elf_" + settings.name + "_active_hidden", self)
                        .val(serialize(activatedElements));

                    $(activeBox).html(ul);
                    processTree();
                }

                function disableElement(theElement) {
                    if (theElement.css("background-image")) {
                        if (!theElement.hasClass('disabled')) {
                            theElement.addClass('disabled');
                            theElement.css("background-image", theElement
                                .css("background-image").replace(
                                    ".png", "Na.png"));
                        }
                    }
                }

                // function disableElement(theElementObject)
                // {
                // var theElements = $('#' + theElementObject.attr('id'),
                // inactiveBox);
                //
                // $.each(theElements, function(i, theElement){
                // theElement = $(theElement);
                //
                // if(theElement.css("background-image"))
                // {
                // if (!theElement.hasClass('disabled'))
                // {
                // theElement.addClass('disabled');
                // theElement.css("background-image",
                // theElement.css("background-image").replace(".png",
                // "Na.png"));
                // }
                // }
                // });
                // }

                function disableActivatedElements() {
                    $
                        .each(
                            activatedElements,
                            function (i, activatedElement) {
                                var currentElements = $(
                                    '#'
                                    + activatedElement.id,
                                    inactiveBox
                                );

                                $
                                    .each(
                                        currentElements,
                                        function (
                                            i,
                                            currentElement
                                        ) {
                                            currentElement = $(currentElement);

                                            var currentElementParent = currentElement
                                                .parent()
                                                .parent();

                                            disableElement(currentElement);

                                            var subElements = $(
                                                'ul:first div a',
                                                currentElementParent
                                            );

                                            $
                                                .each(
                                                    subElements,
                                                    function (
                                                        i,
                                                        subElement
                                                    ) {
                                                        subElementObject = $(subElement);

                                                        // Remove
                                                        // the
                                                        // child-elements
                                                        // in
                                                        // case
                                                        // they
                                                        // were
                                                        // previously
                                                        // activated
                                                        removeActivatedElement(subElementObject
                                                            .attr('id'));
                                                        var currentSubElement = $(
                                                            '#'
                                                            + subElementObject
                                                                .attr('id'),
                                                            activeBox
                                                        );
                                                        currentSubElement
                                                            .parent()
                                                            .parent()
                                                            .remove();

                                                        // Disabled
                                                        // the
                                                        // child-elements
                                                        // in
                                                        // the
                                                        // inactive
                                                        // tree
                                                        // box
                                                        disableElement(subElementObject);
                                                    }
                                                );
                                        }
                                    );

                                // var currentElementParent =
                                // currentElement.parent().parent();
                                //
                                // disableElement(currentElement);
                                //
                                // var subElements = $('ul:first div
                                // a',
                                // currentElementParent);
                                //
                                // $.each(subElements, function(i,
                                // subElement){
                                // subElementObject = $(subElement);
                                //
                                // // Remove the child-elements in
                                // case they were
                                // previously activated
                                // removeActivatedElement(subElementObject.attr('id'));
                                // var currentSubElement = $('#' +
                                // subElementObject.attr('id'),
                                // activeBox);
                                // currentSubElement.parent().parent().remove();
                                //
                                // // Disabled the child-elements in
                                // the inactive tree
                                // box
                                // disableElement(subElementObject);
                                // });
                            }
                        );

                    $("#elf_" + settings.name + "_active_hidden", self)
                        .val(serialize(activatedElements));
                }

                function removeActivatedElement(arrayElement) {
                    for (var i = 0; i < activatedElements.length; i++) {
                        if (activatedElements[i].id == arrayElement) {
                            activatedElements.splice(i, 1);
                        }
                    }
                }

                function enableElement(theElement) {
                    if (typeof theElement.css("background-image") !== 'undefined') {
                        theElement.removeClass('disabled');
                        theElement.css("background-image", theElement.css(
                            "background-image").replace(
                            "Na.png",
                            ".png"
                        ));
                    }
                }

                // function enableElement(theElementObject)
                // {
                // var theElements = $('#' + theElementObject.attr('id'),
                // inactiveBox);
                //
                // $.each(theElements, function(i, theElement){
                // theElement = $(theElement);
                //
                // if (typeof theElement.css("background-image") !==
                // 'undefined')
                // {
                // theElement.removeClass('disabled');
                // theElement.css("background-image",
                // theElement.css("background-image").replace("Na.png",
                // ".png"));
                // }
                // });
                // }

                function deactivateElement(e) {
                    e.preventDefault();
                    var currentElement = $(
                        '#' + $(this).attr('id'),
                        inactiveBox
                    );
                    var currentElementParent = currentElement.parent()
                        .parent();

                    enableElement(currentElement);

                    var subElements = $(
                        'ul:first div a',
                        currentElementParent
                    );
                    $.each(subElements, function (i, subElement) {
                        enableElement($(subElement));
                    });

                    removeActivatedElement($(this).attr('id'));
                    $(this).parent().parent().remove();

                    $("#elf_" + settings.name + "_active_hidden", self)
                        .val(serialize(activatedElements));
                    processFinderTree();
                    return false;
                }

                function activateElement(e) {
                    e.preventDefault();
                    var elementParent = $(this).parent().parent();
                    var elementHtml = elementParent.html();

                    var elementArray = {
                        id: $(this).attr('id'),
                        classes: $(this).attr('class'),
                        description: htmlentities($(this).attr('title')),
                        title: htmlentities($(this).text())
                    };
                    activatedElements.push(elementArray);

                    var li = $('<li></li>');
                    li.append(elementHtml);
                    $('ul', li).remove();

                    $("ul:first", activeBox).append(li);

                    $("#elf_" + settings.name + "_active_hidden", self)
                        .val(serialize(activatedElements));
                    disableActivatedElements();
                    processFinderTree();
                    return false;
                }

                function resetElementFinder(e) {
                    activatedElements = [];

                    setOriginalActivatedElements();
                    if (settings.loadElements) {
                        updateElements();
                    }
                    else {
                        displayMessage(getTranslation('EnterSearchQuery',
                            null, 'Chamilo\\Libraries'
                        ), inactiveBox);
                    }

                    processFinderTree();
                }

                function showElementFinder(e) {
                    e.preventDefault();
                    $(this).hide();
                    $('#' + settings.name + '_collapse_button').show();
                    $('#tbl_' + settings.name).show();
                }

                function hideElementFinder(e) {
                    e.preventDefault();
                    $(this).hide();
                    $('#' + settings.name + '_expand_button').show();
                    $('#tbl_' + settings.name).hide();
                }

                function init() {
                    id = $(self).attr('id');
                    inactiveBox = $('#elf_' + settings.name + '_inactive');
                    activeBox = $('#elf_' + settings.name + '_active');
                    originalActivatedElements = unserialize($(
                        "#elf_" + settings.name + "_active_hidden",
                        self
                    ).val());

                    if (settings.defaultQuery !== '') {
                        $('#' + settings.name + '_search_field').val(
                            settings.defaultQuery);
                    }

                    setOriginalActivatedElements();
                    if (settings.loadElements) {
                        updateElements();
                    }
                    else {
                        displayMessage(getTranslation('EnterSearchQuery',
                            null, 'Chamilo\\Libraries'
                        ), inactiveBox);
                    }

                    $(activeBox).on("click", "a", deactivateElement);
                    $(activeBox).on("deactivateElement", "a", deactivateElement);

                    if (settings.nodesSelectable) {
                        $(inactiveBox).on("click", "a:not(.disabled)",
                            activateElement
                        );
                        $(inactiveBox).on("activate", "a:not(.disabled)",
                            activateElement
                        );
                    }
                    else {
                        $(inactiveBox).on(
                            "click",
                            "a:not(.disabled, .category)",
                            activateElement
                        );
                        $(inactiveBox).on(
                            "activate",
                            "a:not(.disabled, .category)",
                            activateElement
                        );
                        $("a.category", inactiveBox).css(
                            "cursor",
                            "default"
                        );
                    }

                    $('#' + settings.name + '_expand_button').click(
                        showElementFinder);
                    $('#' + settings.name + '_collapse_button').click(
                        hideElementFinder);

                    $('#' + settings.name + '_search_field')
                        .keypress(
                            function (event) {
                                // Avoid searches being started
                                // after every
                                // character
                                clearTimeout(timer);
                                timer = setTimeout(
                                    searchFieldChanged, 750);

                                if (event.keyCode == 13) {
                                    return false;
                                }
                            });

                    $(this).bind('update_search', updateElements);
                    $(document).on("click", ":reset", resetElementFinder);
                }

                return this.each(init);
            }
        });
})(jQuery);
