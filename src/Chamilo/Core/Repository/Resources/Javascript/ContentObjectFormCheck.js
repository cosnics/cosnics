$(function()
{
    var buttonName;
    var buttonClass;
    
    function checkTitle(e, ui)
    {
        var ajaxUri = getPath('WEB_PATH') + 'index.php';
        var title = $("#title").val();
        var parentId = $('select[name="parent_id"]').val();
        var categoryName = $('input[name="new_category"]').val();
        var contentObjectId = $('input[name="id"]').val();
        
        if (title.length == 0)
        {
            $("#title").removeClass('input_valid');
            $("#title").addClass('input_conflict');
            $('button[name="submit_button"]').attr('class', '');
            $('button[name="submit_button"]').addClass('btn btn-danger');
            $('button[name="submit_button"]').html(getTranslation('InvalidConflict', null, 'Chamilo\\Libraries'));
            $('button[name="submit_button"]').bind('click', disableButton);
        }
        else
        {
            if (categoryName.length != 0)
            {
                $("#title").removeClass('input_warning input_conflict');
                $("#title").addClass('input_valid');
                $('button[name="submit_button"]').attr('class', '');
                $('button[name="submit_button"]').addClass(buttonClass);
                $('button[name="submit_button"]').html(buttonName);
                $('button[name="submit_button"]').unbind('click');
            }
            
            else
            {
                var parameters = {
                    'application' : 'Chamilo\\Core\\Repository\\Ajax',
                    'go' : 'check_title',
                    'title' : title,
                    'parent_id' : parentId,
                    'content_object_id' : contentObjectId
                };
                
                var response = $.ajax({
                    type : "POST",
                    url : ajaxUri,
                    data : parameters,
                    async : false
                }).success(function(json)
                {
                    if (json.result_code == 409)
                    {
                        $("#title").removeClass('input_valid input_conflict');
                        $("#title").addClass('input_warning');
                        $('button[name="submit_button"]').attr('class', '');
                        $('button[name="submit_button"]').addClass('btn btn-warning');
                        $('button[name="submit_button"]').unbind('click');
                    }
                    else
                    {
                        $("#title").removeClass('input_warning input_conflict');
                        $("#title").addClass('input_valid');
                        $('button[name="submit_button"]').attr('class', '');
                        $('button[name="submit_button"]').addClass(buttonClass);
                        $('button[name="submit_button"]').html(buttonName);
                        $('button[name="submit_button"]').unbind('click');
                    }
                });
            }
        }
    }
    
    function checkCategoryName(e, ui)
    {
        checkTitle();
        var ajaxUri = getPath('WEB_PATH') + 'index.php';
        var categoryNameElement = $('input[name="new_category"]');
        
        var categoryName = categoryNameElement.val();
        var parentId = $('#parent_id').val();
        
        if (categoryName.length == 0)
        {
            $('input[name="new_category"]').removeClass('input_valid');
            $('input[name="new_category"]').removeClass('input_conflict');
            $('button[name="submit_button"]').attr('class', '');
            $('button[name="submit_button"]').html(buttonName);
            $('button[name="submit_button"]').unbind('click');
            $('button[name="submit_button"]').addClass(buttonClass);
        }
        else
        {
            var parameters = {
                'application' : 'Chamilo\\Core\\Repository\\Ajax',
                'go' : 'check_category_name',
                'name' : categoryName,
                'parent_id' : parentId,
                'workspace_type' : categoryNameElement.data('workspace-type'),
                'workspace_id' : categoryNameElement.data('workspace-id')
            };
            
            var response = $.ajax({
                type : "POST",
                url : ajaxUri,
                data : parameters,
                async : false
            }).success(
                    function(json)
                    {
                        if (json.result_code == 409)
                        {
                            $('input[name="new_category"]').removeClass('input_valid');
                            $('input[name="new_category"]').addClass('input_conflict');
                            $('button[name="submit_button"]').attr('class', '');
                            $('button[name="submit_button"]').addClass('btn btn-danger');
                            $('button[name="submit_button"]').html(
                                    getTranslation('InvalidConflict', null, 'Chamilo\\Libraries'));
                            $('button[name="submit_button"]').bind('click', disableButton);
                        }
                        else
                        {
                            $('input[name="new_category"]').removeClass('input_conflict');
                            $('input[name="new_category"]').addClass('input_valid');
                            $('button[name="submit_button"]').attr('class', '');
                            $('button[name="submit_button"]').addClass(buttonClass);
                            $('button[name="submit_button"]').html(buttonName);
                            $('button[name="submit_button"]').unbind('click');
                        }
                    });
        }
    }
    
    function disableButton(e, ui)
    {
        e.preventDefault();
    }
    
    $(document).ready(function()
    {
        // only check duplicates on change (not while typing)
        // the onChange event fires after leaving the input field
        // the timer is no longer necessary.
        $("#title").change(function()
        {
            checkTitle();
        });
        
        // duplicate title check on change
        $("input[name='new_category']").change(function()
        {
            checkCategoryName();
        });
        
        $("select[name='parent_id']").change(function()
        {
            checkCategoryName();
        });
        
    });
    
    buttonName = $('button[name="submit_button"]').val();
    buttonClass = $('button[name="submit_button"]').attr('class');
    
});