<?php

namespace Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 * @package Chamilo\Application\Weblcms\Integration\Chamilo\Core\Home\Ajax
 *
 * @author Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    const PARAM_ACTION = 'weblcms_home_ajax_action';

    const ACTION_GET_ASSIGNMENT_NOTIFICATIONS = 'GetAssignmentNotifications';
    const DEFAULT_ACTION = self::ACTION_GET_ASSIGNMENT_NOTIFICATIONS;

    const PARAM_COURSE_TYPE_ID = 'course_type_id';
    const PARAM_USER_COURSE_CATEGORY_ID = 'user_course_category_id';
}