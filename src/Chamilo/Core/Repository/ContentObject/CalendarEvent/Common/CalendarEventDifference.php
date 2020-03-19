<?php
namespace Chamilo\Core\Repository\ContentObject\CalendarEvent\Common;

use Chamilo\Core\Repository\Common\ContentObjectDifference;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;
use Diff;
use Diff_Renderer_Html_SideBySide;

/**
 *
 * @package repository.lib.content_object.calendar_event
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

/**
 * This class can be used to get the difference between calendar events
 */
class CalendarEventDifference extends ContentObjectDifference
{

    public function render()
    {
        $date_format = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);

        $object = $this->get_object();
        $version = $this->get_version();

        $object_string = htmlentities(
            Translation::get('From', null, Utilities::COMMON_LIBRARIES) . ' ' .
            DatetimeUtilities::format_locale_date($date_format, $object->get_start_date()) . ' ' .
            Translation::get('Until', null, Utilities::COMMON_LIBRARIES) . ' ' .
            DatetimeUtilities::format_locale_date($date_format, $object->get_end_date())
        );
        $object_string = explode(PHP_EOL, strip_tags($object_string));

        $version_string = htmlentities(
            Translation::get('From', null, Utilities::COMMON_LIBRARIES) . ' ' .
            DatetimeUtilities::format_locale_date($date_format, $version->get_start_date()) . ' ' .
            Translation::get('Until', null, Utilities::COMMON_LIBRARIES) . ' ' .
            DatetimeUtilities::format_locale_date($date_format, $version->get_end_date())
        );
        $version_string = explode(PHP_EOL, strip_tags($version_string));

        $html = array();
        $html[] = parent::render();

        $difference = new Diff($version_string, $object_string);
        $renderer = new Diff_Renderer_Html_SideBySide();

        $html[] = $difference->Render($renderer);

        return implode(PHP_EOL, $html);
    }
}
