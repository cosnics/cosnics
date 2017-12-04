<?php
namespace Chamilo\Libraries\Calendar\Event\Renderer\Type;

use Chamilo\Libraries\Calendar\Event\Event;
use Chamilo\Libraries\Calendar\Event\Renderer\EventHtmlRenderer;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Event\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class ListRenderer extends EventHtmlRenderer
{

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Event\Renderer\ViewRenderer::render()
     */
    public function render(Event $event, $startDate)
    {
        $calendarSources = $this->getCalendarSources();

        $sourceClasses = $calendarSources->getSourceClasses($event->getSource()->getTitle());
        $eventClasses = implode(' ', array('event-container', $sourceClasses));

        $html = array();

        if (! $this->getDataProvider()->isSourceVisible($event->getSource()->getTitle()))
        {
            $rowClasses = ' event-container-hidden';
        }
        else
        {
            $rowClasses = '';
        }

        $html[] = '<div class="row' . $rowClasses . '" data-source-key="' .
             $calendarSources->getSourceKey($event->getSource()->getTitle()) . '">';

        $html[] = '<div class="col-xs-1">';
        $html[] = '<span class="' . $eventClasses . '"></span>';
        $html[] = '</div>';

        $html[] = '<div class="col-xs-3 list-event-item-time">';
        $html[] = $this->getRange($event);
        $html[] = '</div>';

        $html[] = '<div class="col-xs-7 list-event-item-data">';

        if ($event->getUrl())
        {
            $html[] = '<a href="' . $event->getUrl() . '">';
        }

        $html[] = htmlspecialchars($event->getTitle());

        if ($event->getUrl())
        {
            $html[] = '</a>';
        }

        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function getRange(Event $event)
    {
        $html = array();

        $dateFormat = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);

        if ($event->getEndDate() != '')
        {
            if (date('Y m d', $event->getStartDate()) == date('Y m d', $event->getEndDate()))
            {
                $dateFormat = Translation::get('TimeNoSecFormat', null, Utilities::COMMON_LIBRARIES);
            }

            $html[] = '<div class="calendar-event-range">' . htmlentities(
                DatetimeUtilities::format_locale_date($dateFormat, $event->getStartDate()) . ' - ' .
                     DatetimeUtilities::format_locale_date($dateFormat, $event->getEndDate())) . '</div>';
        }
        else
        {
            $html[] = '<div class="calendar-event-range">' . DatetimeUtilities::format_locale_date(
                $dateFormat,
                $event->getStartDate()) . '</div>';
        }

        return implode(PHP_EOL, $html);
    }
}
