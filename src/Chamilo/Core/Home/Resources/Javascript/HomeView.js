$(function() {

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
    }

    function bindIcons() {
        $(document).on('click', "#tab_menu li:not(.current)", showTab);
        $(document).on('click', "#tab_menu li.current a.tabTitle", function(e, ui) {
            e.preventDefault();
        });
    }

    // Extension to jQuery selectors which only returns visible elements
    $.extend($.expr[':'], {
        visible : function(a) {
            return $(a).css('display') !== 'none';
        }
    });

    $(document).ready(function() {
        bindIcons();
    });

});