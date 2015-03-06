$(document).ready(function()
    {
        $(".tree_menu_hide").bind("click", showAll);

        function showBlock()
        {
            $(".tree_menu_on_top").css('max-height','150px');
            $(".tree_menu_hide").text("showAll");
            $(".tree_menu_hide").bind("click", showAll);


            return false;
        }

        function showAll()
        {
            $(".tree_menu_on_top").css('max-height','100%');
            $(".tree_menu_on_top").height('100%');

            $(".tree_menu_hide").text("showBlock");
            $(".tree_menu_hide").bind("click", showBlock);

            return false;
        }
    });