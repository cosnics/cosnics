(function($)
{
    var defaultName = "Name";
    var defaultMaxGroups = "0";

    function selectGroupGroupNone(ev, ui)
    {
        hideGroupGroupList();
        hideGroupGroupName();
        hideGroupGroupRandom();
        hideGroupGroupMaxRegistrations();
    }

    function selectGroupGroupExisting(ev, ui)
    {
        hideGroupGroupName();
        hideGroupGroupMaxRegistrations();
        showGroupGroupList();
        showGroupGroupRandom();
    }

    function selectGroupGroupNew(ev, ui)
    {
        hideGroupGroupList();
        showGroupGroupName();
        showGroupGroupRandom();
        showGroupGroupMaxRegistrations();
    }

    function hideGroupGroupName()
    {
        $("#parent_group_name").hide();
        $("#parent_name").val(defaultName);
    }

    function hideGroupGroupList()
    {
        $("#parent_group_list").hide();
    }

    function hideGroupGroupRandom()
    {
        $("#parent_group_random").hide();
    }

    function hideGroupGroupMaxRegistrations()
    {
        $("#parent_group_max_registrations").hide();
        $("#parent_max_number_of_course_group_per_member").val(defaultMaxGroups);
    }

    function showGroupGroupName()
    {
        if($("#parent_name").val() == defaultName)
        {
            $("#parent_name").val("");
        }
        $("#parent_group_name").show();
    }

    function showGroupGroupList()
    {
        $("#parent_group_list").show();
    }

    function showGroupGroupRandom()
    {
        $("#parent_group_random").show();
    }

    function showGroupGroupMaxRegistrations()
    {
        if($("#parent_max_number_of_course_group_per_member").val() == defaultMaxGroups)
        {
            $("#parent_max_number_of_course_group_per_member").val("");
        }
        $("#parent_group_max_registrations").show();
    }

    /**
     * Event handler for when the parent course group is changed.
     *
     * The parent group's options will be set as defaults on the current form.
     */
    function handleParentCourseGroupChanged()
    {
        var parentCourseGroupId = $(this).find(":selected").val();
        //if(parentCourseGroupId > 1)
        //{
            $.ajax({
                type : "POST",
                url : "index.php",
                data : {
                    'application': 'Chamilo\\Application\\Weblcms\\Tool\\Implementation\\CourseGroup\\Ajax',
                    'go': 'get_course_group',
                    'course_group_id': parentCourseGroupId
                }
            }).done(function(data, textStatus, jqXHR) {
                if(data.result_code != 200) {
                    alert(data.result_message); // TODO :: Decent error message
                    return;
                }
                if(!data.properties || data.properties.length == 0) {
                    // TODO :: Error message
                    return;
                }

                var parentCourseGroup = data.properties;

                // Validated the response

                // If we're dealing with a root course group, we'll set default properties instead of the actual ones
                if(parentCourseGroup.is_root) {
                    parentCourseGroup.max_number_of_members = 20;
                    parentCourseGroup.self_reg_allowed = false;
                    parentCourseGroup.self_unreg_allowed = true;
                    parentCourseGroup.document_category_id = 0;
                    parentCourseGroup.forum_category_id = 0;
                }

                $("[name='max_number_of_members']").val(parentCourseGroup.max_number_of_members);
                $("[name='self_reg_allowed']").val([parentCourseGroup.self_reg_allowed]); // Note: this must match the value of the checkbox to select.
                $("[name='self_unreg_allowed']").val([parentCourseGroup.self_unreg_allowed]);
                $("[name='document_category_id']").val([parentCourseGroup.document_category_id > 0 ? 1 : 0]);
                $("[name='forum_category_id']").val([parentCourseGroup.forum_category_id > 0 ? 1 : 0]);
            });
        //}
    }

    $(document).ready( function()
    {
            $(document).on("click", "#parent_group_none", selectGroupGroupNone);
            $(document).on("click", "#parent_group_existing", selectGroupGroupExisting);
            $(document).on("click", "#parent_group_new", selectGroupGroupNew);

            if($('#parent_group_none').is(':checked'))
            {
                selectGroupGroupNone();
            }
            if($('#parent_group_existing').is(':checked'))
            {
                selectGroupGroupExisting();
            }
            if($('#parent_group_new').is(':checked'))
            {
                selectGroupGroupNew();
            }

            if(!$("[name=name1]").length && !$(".error-message").length) {
                // Only update the options when we're dealing with a new instance of the form.
                $(document).on('change', "[name='parent_id']", handleParentCourseGroupChanged);
                $("[name='parent_id']").change(); // Fire event listener to set
                $(document).off('change', "[name='parent_id']", handleParentCourseGroupChanged); // Detach event listener because the new options will override options set by the user.
            }

    });
})(jQuery);
