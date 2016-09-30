$(function() {

    $(document).ready(function() {

        $("[data-toggle='tooltip']").bootstrapTooltip({
            html: true,
            title: function() {
                return $(this).attr('data-content');
            },
            template: '<div class="tooltip tooltip-calendar" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
        });

    });

});