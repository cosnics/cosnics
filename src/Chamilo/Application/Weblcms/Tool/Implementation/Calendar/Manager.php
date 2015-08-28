<?php
namespace Chamilo\Application\Weblcms\Tool\Implementation\Calendar;

use Chamilo\Application\Weblcms\Renderer\PublicationList\ContentObjectPublicationListRenderer;
use Chamilo\Core\Repository\ContentObject\CalendarEvent\Storage\DataClass\CalendarEvent;

abstract class Manager extends \Chamilo\Application\Weblcms\Tool\Manager
{

    public static function get_allowed_types()
    {
        return array(CalendarEvent :: CLASS_NAME);
    }

    public function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_CALENDAR;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_TABLE;
        return $browser_types;
    }
}
