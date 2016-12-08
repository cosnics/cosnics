$(function()
{
    if(!$('#peer_assessment_settings_form input[name="direct_subscribe_available"]').is(':checked'))
    {   
        $('#peer_assessment_settings_form input[name="unsubscribe_available"]').closest('div.row').css('display','none');
        $('#peer_assessment_settings_form select.subscription_deadline').closest('div.row').css('display','none');
        $('#peer_assessment_settings_form input[name="min_group_members"]').closest('div.row').css('display','none');
        $('#peer_assessment_settings_form input[name="max_group_members"]').closest('div.row').css('display','none');
    }
    $('#peer_assessment_settings_form input[name="direct_subscribe_available"]').click(function(el){
            $('#peer_assessment_settings_form input[name="unsubscribe_available"]').closest('div.row').toggle();
            $('#peer_assessment_settings_form select.subscription_deadline').closest('div.row').toggle();
            $('#peer_assessment_settings_form input[name="min_group_members"]').closest('div.row').toggle();
            $('#peer_assessment_settings_form input[name="max_group_members"]').closest('div.row').toggle();
    });
});
