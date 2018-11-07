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
}