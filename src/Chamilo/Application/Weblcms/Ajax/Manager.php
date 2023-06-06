<?php
namespace Chamilo\Application\Weblcms\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 * @package Chamilo\Application\Weblcms\Ajax
 * @author  Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author  Magali Gillard <magali.gillard@ehb.be>
 * @author  Eduard Vossen <eduard.vossen@ehb.be>
 */
abstract class Manager extends AjaxManager
{
    public const ACTION_GET_ASSIGNMENT_NOTIFICATIONS = 'GetAssignmentNotifications';
    public const ACTION_SAVE_HOTPOTATOES_SCORE = 'HotpotatoesSaveScore';
    public const ACTION_SAVE_LEARNING_PATH_HOTPOTATOES_SCORE = 'LpHotpotatoesSaveScore';
    public const ACTION_XML_COURSE_USER_GROUP_FEED = 'XmlCourseUserGroupFeed';
    public const ACTION_XML_GROUP_MENU_FEED = 'XmlPublicationsTreeFeed';

    public const CONTEXT = __NAMESPACE__;

    public const PARAM_COURSE_TYPE_ID = 'course_type_id';
    public const PARAM_USER_COURSE_CATEGORY_ID = 'user_course_category_id';
}
