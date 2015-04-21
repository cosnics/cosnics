/**
 * $Id: learning_path_item.js 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.javascript
 */
/**
 * @author Sven Vanpoucke
 */
(function (
    $
    )
{
    $(
        window
    ).bind(
        'beforeunload', function (
            e
            )
        {
            var response = $.ajax(
                {
                    type:"POST",
                    url:"./application/weblcms/tool/learning_path/php/ajax/leave_item.php",
                    data:{ tracker_id:tracker_id},
                    async:false
                }
            ).responseText;

            //alert(response);
            //alert('bla');
            //$(".charttype").bind('change',handle_charttype);
        }
    );
})(
    jQuery
);