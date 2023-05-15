<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Calendar;

use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;

abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements IntroductionTextSupportInterface
{
    public const ACTION_ICAL = 'ICal';

    public const CONTEXT = __NAMESPACE__;

    public static function get_allowed_types()
    {
        return [CalendarEvent::class];
    }

    public function get_available_browser_types()
    {
    }
}
