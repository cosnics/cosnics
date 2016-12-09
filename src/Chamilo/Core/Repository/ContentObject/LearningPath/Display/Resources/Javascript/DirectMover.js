(function($)
{
    var positionsPerParent = null;

    var changeMoverPositions = function() {
        var parentId = $('#mover-parent-id').val();
        var positions = positionsPerParent[parentId];

        var moverDisplayOrder = $('#mover-display-order');
        moverDisplayOrder.html('');

        $.each(positions, function(index, position) {
            var option = $('<option />');
            option.val(position.displayOrder);
            option.text(position.title);

            moverDisplayOrder.append(option);
        });
    };

    $(document).ready(function()
    {
        positionsPerParent = $('#positions-per-parent').data('positions');

        $('#mover-parent-id').on('change', changeMoverPositions);
        $('#mover-close').on('click', function() { $('#mover').hide(); });
        $('.mover-open').on('click', function() { $('#mover').show(); return false; });

        changeMoverPositions();
    });

})(jQuery);