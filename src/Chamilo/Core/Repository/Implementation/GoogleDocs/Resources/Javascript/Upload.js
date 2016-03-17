$(function() {

    function showNewFolder(e, ui) {
        e.preventDefault();
        $("div#new_folder").show();
        $("input#add_folder").hide();
    }

    $(document).ready(function() {
        
        $("div#new_folder").hide();
        $("input#add_folder").show();
        $("input#add_folder").on('click', showNewFolder);
    });

});