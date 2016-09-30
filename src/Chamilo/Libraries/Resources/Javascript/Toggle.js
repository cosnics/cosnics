$(document).ready(function()
{
    $(':checkbox:not(.no-toggle-style)', $('div.checkbox.no-awesome-style:not(.no-toggle-style)')).bootstrapToggle({
        on : getTranslation('ConfirmOn', {}, 'Chamilo\\Libraries'),
        off : getTranslation('ConfirmOff', {}, 'Chamilo\\Libraries'),
        size : 'small',
        width: '60px'
    });
});
