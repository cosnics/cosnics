$(document).ready( function ()
{
    $.each($('.reporting_th'), function()
    {
        $(this).attr('style',
            'vertical-align: middle;' + 
            'text-align: center;' + 
            'height: 150px;');
    });
    
    $.each($('.reporting_th>a'), function()
    {
        $(this).html($(this).text().replace(/ /g, '&nbsp;'));
        $(this).attr('style',
            '-moz-transform: rotate(90deg);' +
            '-moz-transform-origin: 50% 50%;' +
            '-webkit-transform: rotate(90deg);' +
            '-webkit-transform-origin: 50% 50%;' +
            '-o-transform: rotate(90deg);' + 
            '-o-transform-origin: 50% 50%;' +
            '-ms-transform: rotate(90deg);' +
            '-ms-transform-origin: 50% 50%;' +
            'transform: rotate(90deg);' +
            'transform-origin: 50% 50%;');
    });
});
