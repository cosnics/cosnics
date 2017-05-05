<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Calendar;

use Chamilo\Application\Weblcms\Tool\Interfaces\IntroductionTextSupportInterface;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;

abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager implements IntroductionTextSupportInterface
{
    const ACTION_ICAL = 'ICal';

    public static function get_allowed_types()
    {
        return array(CalendarEvent::class_name());
    }

    public function get_available_browser_types()
    {
    }
}
