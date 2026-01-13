<?php
namespace Chamilo\Application\Calendar\Extension\Google;

use Chamilo\Application\Calendar\Extension\Google\Service\CalendarService;
use Chamilo\Libraries\Architecture\Application\Application;

/**
 *
 * @package application\calendar
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
abstract class Manager extends Application
{
    public const ACTION_LOGIN = 'Login';
    public const ACTION_LOGOUT = 'Logout';

    public const CONTEXT = __NAMESPACE__;

    public const DEFAULT_ACTION = self::ACTION_LOGIN;

    public function getCalendarService(): CalendarService
    {
        return $this->getService(CalendarService::class);
    }
}
