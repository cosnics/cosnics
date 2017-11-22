<?php

namespace Chamilo\Core\Repository\ContentObject\ExternalCalendar\Common\Rendition\Html;

use Chamilo\Core\Repository\Common\Rendition\ContentObjectRendition;
use Chamilo\Core\Repository\ContentObject\ExternalCalendar\Common\Rendition\HtmlRenditionImplementation;
use Chamilo\Core\Repository\ContentObject\ExternalCalendar\Storage\DataClass\ExternalCalendar;
use Chamilo\Libraries\File\Filesystem;
use Chamilo\Libraries\File\Path;
use Chamilo\Libraries\Platform\Session\Request;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

class HtmlDescriptionRenditionImplementation extends HtmlRenditionImplementation
{

    public function render()
    {
        return ContentObjectRendition::launch($this);
    }

    public function get_description()
    {
        $object = $this->get_content_object();
        $event_id = Request::get(ExternalCalendar::PARAM_EVENT_ID);
        if (isset($event_id))
        {
            $event = $object->get_event($event_id);

            $date_format = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);

            $html[] = '<div class="calendar_event_range" style="font-weight: bold;">';
            $html[] = Translation::get('From', null, Utilities::COMMON_LIBRARIES);
            $html[] = ' ';
            $html[] = DatetimeUtilities::format_locale_date(
                $date_format,
                $event->DTSTART->getDateTime()->getTimeStamp()
            );
            $html[] = ' ';
            $html[] = Translation::get('Until', null, Utilities::COMMON_LIBRARIES);
            $html[] = ' ';
            $html[] = DatetimeUtilities::format_locale_date($date_format, $event->DTEND->getDateTime()->getTimeStamp());
            $html[] = '</div>';

            if ($event->RRULE)
            {
                // TODO: Some kind of rendering of the repeat rules?
            }

            if ($event->description)
            {
                $html[] = $event->description[0]['value'];
            }
        }
        else
        {
            if ($object->get_path_type() == ExternalCalendar::PATH_TYPE_REMOTE)
            {
                $html[] =
                    '<div class="link_url" style="margin-top: 1em;"><a href="' . htmlentities($object->get_path()) .
                    '">' . htmlentities($object->get_path()) . '</a></div>';
            }
            else
            {
                $name = $object->get_filename();
                $url = Path::getInstance()->getBasePath(true) .
                    \Chamilo\Core\Repository\Manager::get_document_downloader_url(
                        $object->get_id(),
                        $object->calculate_security_code()
                    );

                $html[] = '<div><a href="' . Utilities::htmlentities($url) . '">' . Utilities::htmlentities($name) .
                    '</a> (' . Filesystem::format_file_size($object->get_filesize()) . ')</div>';
            }

            try
            {
                $number_of_events = $object->count_events();
                $html[] = Translation::get('EventCount') . ' : ' . $number_of_events;
            }
            catch (\Exception $ex)
            {
                $html[] =
                    '<div class="alert alert-danger">' . Translation::getInstance()->getTranslation('ICalParseError') .
                    '</div>';
            }
        }

        return implode(PHP_EOL, $html);
    }
}
