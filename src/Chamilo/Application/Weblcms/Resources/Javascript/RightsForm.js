$(function ()
{
    $(document).ready(function ()
    {
        var specificRightsSelected = function() {
            $('.specific_rights_selector_box').show();
            $('.target-entities-container').hide();
        };

        var inheritedRightsSelected = function() {
            $('.specific_rights_selector_box').show();
            $('.target-entities-container').hide();
        };

        $(document).on('click', '.specific_rights_selector', specificRightsSelected);
        $(document).on('click', '.inherit_rights_selector', inheritedRightsSelected );

        $(document).on('click', '.other_option_selected',  function() { $('.entity_selector_box',
            $(this).closest('.right')).hide(); } );

        $(document).on('click', '.entity_option_selected', function() { $('.entity_selector_box',
            $(this).closest('.right')).show(); } );

        if($('.specific_rights_selector').prop('checked')) {
            specificRightsSelected();
        } else if($('.inherit_rights_selector').prop('checked')) {
            inheritedRightsSelected();
        }

        $('.entity_option_selected').each(function()
        {
            if($(this).prop('checked'))
            {
                $('.entity_selector_box', $(this).closest('.right')).show();
            }
        });

        var createTargetEntityItem = function(targetEntity, containerClass) {
            var newItem = $('<li class="list-group-item"></li>');
            newItem.text(targetEntity);

            $(containerClass).append(newItem);
        };

        var retrieveTargetEntities = function() {
            var publicationCategoryId = $('select[name="category_id"]').val();

            $.ajax('index.php?application=Chamilo\\Application\\Weblcms\\Ajax&go=GetTargetEntities', {
                'method': 'POST',
                'data': {
                    'course_id': 1,
                    'tool_name': 'Announcement',
                    'publication_category_id': publicationCategoryId
                }
            }).done(function(data) {
                if(data.properties.everyone) {
                    createTargetEntityItem(data.properties.everyone);
                } else {

                    $('.target-entities-user-list').html('');

                    $.each(
                        data.properties.users, function (index, user) {
                            createTargetEntityItem(user, '.target-entities-user-list');
                        }
                    );

                    $('.target-entities-course-groups-list').html('');

                    $.each(
                        data.properties.course_groups, function (index, course_group) {
                            createTargetEntityItem(course_group, '.target-entities-course-groups-list');
                        }
                    );

                    $('.target-entities-platform-groups-list').html('');

                    $.each(
                        data.properties.platform_groups, function (index, platform_group) {
                            createTargetEntityItem(platform_groups, '.target-entities-platform-groups-list');
                        }
                    );
                }

            })
        };


        $('select[name="category_id"]').on('change', retrieveTargetEntities);
        $('input[name="inherit[show_inherited_rights]"]').on('click', function() {
            $(this).hide();
            retrieveTargetEntities();
        });
    });

});