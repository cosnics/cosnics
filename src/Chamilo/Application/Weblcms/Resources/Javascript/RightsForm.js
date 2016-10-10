$(function ()
{
    $(document).ready(function ()
    {
        var inheritedRightsSelected = function() {
            $(this).closest('.right').find('.entity_selector_box').hide();
            $('.target-entities-container').show();
        };

        var specificEntitiesSelected = function() {
            $(this).closest('.right').find('.entity_selector_box').show();
            $('.target-entities-container').hide();
        };

        var otherOptionSelected = function() {
            $(this).closest('.right').find('.entity_selector_box').hide();
            $('.target-entities-container').hide();
        };

        $(document).on('click', '.rights_selector.inherit_rights_selector', inheritedRightsSelected );
        $(document).on('click', '.rights_selector:not(.inherit_rights_selector):not(.specific_rights_selector)',  otherOptionSelected);
        $(document).on('click', '.rights_selector.specific_rights_selector', specificEntitiesSelected );

        var initialize = function() {
            $('.specific_rights_selector:checked').each(specificEntitiesSelected);
            $('.inherit_rights_selector:checked').each(inheritedRightsSelected);
            $('.rights_selector:not(.inherit_rights_selector):not(.specific_rights_selector):checked').each(otherOptionSelected);
        };

        initialize();

        var createTargetEntityItem = function(targetEntity, containerClass) {
            var newItem = $('<li class="list-group-item"></li>');
            newItem.text(targetEntity);

            $(containerClass).append(newItem);
        };

        var retrieveTargetEntities = function() {
            var entitiesContainer = $('.target-entities-container');
            var courseId = entitiesContainer.attr('data-course-id');
            var toolName = entitiesContainer.attr('data-tool');

            var publicationCategoryId = $('select[name="category_id"]').val();

            $.ajax('index.php?application=Chamilo\\Application\\Weblcms\\Ajax&go=GetTargetEntities', {
                'method': 'POST',
                'data': {
                    'course_id': courseId,
                    'tool_name': toolName,
                    'publication_category_id': publicationCategoryId
                }
            }).done(function(data) {
                $('.target-entities-user-list > .list-group-item:not(.target-entities-default)').remove();
                $('.target-entities-course-groups-list > .list-group-item:not(.target-entities-default)').remove();
                $('.target-entities-platform-groups-list > .list-group-item:not(.target-entities-default)').remove();
                $('.target-entities-list .target-entities-default').hide();

                if(data.properties.everyone) {
                    $('.target-entities-list .target-entities-everyone').show();
                } else {

                    if(data.properties.users && data.properties.users.length > 0) {
                        $.each(
                            data.properties.users, function (index, user) {
                                createTargetEntityItem(user, '.target-entities-user-list');
                            }
                        );
                    } else {
                        $('.target-entities-user-list .target-entities-nobody').show();
                    }

                    if(data.properties.course_groups && data.properties.course_groups.length > 0) {
                        $.each(
                            data.properties.course_groups, function (index, course_group) {
                                createTargetEntityItem(course_group, '.target-entities-course-groups-list');
                            }
                        );
                    } else {
                        $('.target-entities-course-groups-list .target-entities-nobody').show();
                    }

                    if(data.properties.platform_groups && data.properties.platform_groups.length > 0) {
                        $.each(
                            data.properties.platform_groups, function (index, platform_group) {
                                createTargetEntityItem(platform_group, '.target-entities-platform-groups-list');
                            }
                        );
                    } else {
                        $('.target-entities-platform-groups-list .target-entities-nobody').show();
                    }

                }

            })
        };


        $('select[name="category_id"]').on('change', retrieveTargetEntities);
        $('input[name="inherit[show_inherited_rights]"]').on('click', function() {
            $(this).hide();
            retrieveTargetEntities();
        });

        $('#content_object_publication_form').on('reset', function() { setTimeout(function () { initialize(); })});

        retrieveTargetEntities();
    });

});