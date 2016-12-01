var timer;

function handle_timer()
{
    var value = $('#start_time').val();
    value = parseInt(value);
    value++;
    $('#start_time').val(value);
    
    var max_time = $('#max_time').val();
    max_time = parseInt(max_time);
    
    var text = max_time - value;
    
    if (max_time - value < 10)
    {
        $('.time_left').attr('class', 'alert alert-danger time_left_alert');
    }
    
    $('.time').html(text);
    
    if (max_time == 0)
        return;
    
    if (value >= max_time)
    {
        alert(getTranslation('TimesUp', null, 'Chamilo/Core/Repository/ContentObject/Assessment'));
        $(".submit").click();
    }
    else
    {
        timer = setTimeout('handle_timer()', 1000);
    }
}

(function($)
{
    $(document).ready(function()
    {
        handle_timer();
    });
    
})(jQuery);
