(function($) {

    function showOptions(event, ui) {
        var pathType = $(this).val();
        $('.path_type').hide();
        $('#path_type_' + pathType).show();

    }

    $(document).ready(function() {
        $('.path_type').hide();
        $('input[name=path_type]:radio').on('click', showOptions);
        var pathType = $('input[name=path_type]:radio:checked').val();
        $('#path_type_' + pathType).show();
    })
    
})(jQuery);