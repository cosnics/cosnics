$(function()
{

    function showNewSeries(e, ui)
    {
        e.preventDefault();
        $("div#new_series").show();
        $("input#add_series").hide();
    }

    $(document).ready(function()
    {
        $("div#new_series").hide();
        $("input#add_series").show();
        $("input#add_series").on('click', showNewSeries);
    });

});