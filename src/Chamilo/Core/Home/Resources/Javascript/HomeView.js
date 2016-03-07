$(function() {

    function showTab(e, ui) {
        var tab = $(this);
        var tabId = tab.data('tab-id');

        $('.portal-nav-tabs li').removeClass('active');
        tab.addClass('active');

        $('.portal-tab.show').switchClass('show', 'hidden');
        $('.portal-tab[data-element-id="' + tabId + '"]').switchClass('hidden', 'show');
    }

    $(document).ready(function() {
        $(document).on('click', '.portal-nav-tabs li:not(.active,.portal-actions)', showTab);
    });

});