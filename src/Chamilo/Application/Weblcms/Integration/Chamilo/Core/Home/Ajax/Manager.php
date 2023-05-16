<?php
namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Ajax
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    public const ACTION_GET_ASSIGNMENT_NOTIFICATIONS = 'GetAssignmentNotifications';

    public const CONTEXT = __NAMESPACE__;

    public const DEFAULT_ACTION = self::ACTION_GET_ASSIGNMENT_NOTIFICATIONS;

    public const PARAM_ACTION = 'weblcms_home_ajax_action';
    public const PARAM_COURSE_TYPE_ID = 'course_type_id';
    public const PARAM_USER_COURSE_CATEGORY_ID = 'user_course_category_id';
}