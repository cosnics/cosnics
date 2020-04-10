/**
 * Copyright (c) 2010, Sven Vanpoucke, Chamilo tree menu in jQuery plugin
 */

(function ($) {
    $.fn.extend({
        tree_menu: function (options) {

            // Settings list and the default values
            var defaults = {
                search: '',
                item_url: '#',
                collapsed: true
            };

            var settings = $.extend(defaults, options), self = $(this);

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
                changeExpandItemIcon($(this));
            }

            function changeExpandItemIcon(item) {
                if (item.hasClass("lastExpand")) {
                    item.removeClass("lastExpand");
                    item.addClass("lastCollapse");
                }
                else if (item.hasClass("expand")) {
                    item.removeClass("expand");
                    item.addClass("collapse");
                }
            }

            function expandItemAndLoadChildren(e) {
                var id = $('a', $(this)).attr("id");
                var parent = $(this).parent();
                var children = getChildren(id);

                if (children) {
                    parent.append(children);
                    changeExpandItemIcon($(this));
                    $(this).unbind('click');
                    processTree($(this).parent().parent().parent());
                }
            }

            function getChildren(parent_id) {
                if (settings.search == '') {
                    return '';
                }

                var ul = $('<ul></ul>');
                var response = loadChildren(parent_id);
                var tree = $.xml2json(response, true);

                if ((tree.leaf && $(tree.leaf).size() > 0)) {
                    $.each(tree.leaf, function (i, the_leaf) {
                        var expand = '';
                        if (the_leaf.has_children == '1') {
                            expand = ' class="expand"';
                        }
                        item_url = sprintf(settings.item_url, the_leaf.id);
                        var li = $(
                            '<li><div' + expand + '><a href="' + item_url + '" id="' + the_leaf.id + '" title="' +
                            the_leaf.title + '"><span class="' + the_leaf.classes + '"></span> ' + the_leaf.title +
                            '</a></div></li>');
                        $(ul).append(li);
                    });

                    return ul;
                }
            }

            function loadChildren(parent_id) {
                return $.ajax({
                    type: "GET",
                    dataType: "xml",
                    url: settings.search,
                    data: {
                        parent_id: parent_id
                    },
                    async: false
                }).responseText;
            }

            function processTree(parent) {
                $("ul li:last-child > div", parent).addClass("last");
                $("ul li:last-child > div.expand", parent).addClass(
                    "lastExpand");
                $("ul li:last-child > div.expand", parent).removeClass(
                    "expand");
                $("ul li:last-child > ul", parent).css(
                    "background-image", "none");

                $("ul li:not(:last-child):has(ul) > div", parent)
                    .addClass("collapse");
                $("ul li:last-child:has(ul) > div", parent).addClass(
                    "lastCollapse");

                // $("ul li:has(ul) > div", parent).toggle(collapseItem,
                // expandItem);
                $("ul li > div > a", parent).click(function (e) {
                    e.stopPropagation();
                });
            }

            // Apply some default collapsing in case we're not using an AJAX
            // postback.
            function collapseDefault() {
                if (settings.search == '') {
                    var id = self.attr('id');

                    $("#" + id + " ul li:has(ul) > div.collapse").trigger(
                        'click');
                    $("#" + id + " ul li:has(ul) > div.lastCollapse").trigger(
                        'click');

                    $("#" + id + " ul li.current > div.expand")
                        .trigger('click');
                    $("#" + id + " ul li.current > div.lastExpand").trigger(
                        'click');

                    $("#" + id + " ul li.current").parents('li.current_path')
                        .each(function (index) {
                            $("div:first", $(this)).trigger('click');
                        });
                }
            }

            function init() {
                processTree(self);

                // We need to select through the id of the container because of
                // a bug in jQuery where it is not possible to do DOM traversel
                // with the .live() function.
                var id = self.attr('id');
                $(document).on(
                    'click',
                    "#" + id + " ul li:not(:has(ul)) > div.expand",
                    expandItemAndLoadChildren
                );
                $(document).on(
                    'click',
                    "#" + id + " ul li:not(:has(ul)) > div.lastExpand",
                    expandItemAndLoadChildren
                );
                $(document).on('click',
                    "#" + id + " ul li:has(ul) > div.expand", expandItem
                );
                $(document).on(
                    'click',
                    "#" + id + " ul li:has(ul) > div.lastExpand",
                    expandItem
                );
                $(document).on(
                    'click',
                    "#" + id + " ul li:has(ul) > div.collapse",
                    collapseItem
                );
                $(document).on(
                    'click',
                    "#" + id + " ul li:has(ul) > div.lastCollapse",
                    collapseItem
                );

                if (settings.collapsed) {
                    collapseDefault();
                }
            }

            return this.each(init);
        }
    });
})(jQuery);
