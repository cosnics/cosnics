(function($) {

    function showOptions(event, ui) {
        var pathType = $(this).val();
        $('.path_type').hide();
        $('#path_type_' + pathType).show();

    }

    $(document).ready(function() {
        $('.path_type').hide();
        $(document).on('click', 'input[name=path_type]:radio', showOptions);
        var pathType = $('input[name=path_type]:radio:checked').val();
        $('#path_type_' + pathType).show();
    })

})(jQuery);