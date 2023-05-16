<?php
namespace Chamilo\Core\Notification\Ajax;

use Chamilo\Libraries\Architecture\AjaxManager;

/**
 * @package Chamilo\Core\Notification\Ajax
 * @author  Sven Vanpoucke - Hogeschool Gent
 */
abstract class Manager extends AjaxManager
{
    public const ACTION_GET_NOTIFICATIONS = 'GetNotifications';

    public const CONTEXT = __NAMESPACE__;
    public const DEFAULT_ACTION = self::ACTION_GET_NOTIFICATIONS;
}