<?php
namespace Chamilo\Libraries\Calendar\Renderer\Event\Type;

use Chamilo\Libraries\Calendar\Renderer\Event\EventRenderer;
use Chamilo\Libraries\Format\Structure\Toolbar;
use Chamilo\Libraries\Platform\Translation;
use Chamilo\Libraries\Utilities\DatetimeUtilities;
use Chamilo\Libraries\Utilities\Utilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Event\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class EventListRenderer extends EventRenderer
{

    /**
     * Gets a html representation of an event for a month renderer
     *
     * @return string
     */
    public function render()
    {
        $startDate = $this->getEvent()->getStartDate();
        $endDate = $this->getEvent()->getEndDate();
        $event = $this->getEvent();
        $legend = $this->getRenderer()->getLegend();

        $sourceClasses = $legend->getSourceClasses($event->getSource());
        $eventClasses = implode(' ', array('event-container', $sourceClasses));

        $html = array();

        if (! $this->getRenderer()->isSourceVisible($event->getSource()))
        {
            $rowClasses = ' event-container-hidden';
        }
        else
        {
            $rowClasses = '';
        }

        $html[] = '<div class="row' . $rowClasses . '" data-source-key="' . $legend->addSource($event->getSource()) .
             '">';

        $html[] = '<div class="col-xs-1">';
        $html[] = '<span class="' . $eventClasses . '"></span>';
        $html[] = '</div>';

        $html[] = '<div class="col-xs-3 list-event-item-time">';
        $html[] = $this->getRange();
        $html[] = '</div>';

        $html[] = '<div class="col-xs-7 list-event-item-data">';

        $html[] = '<a href="' . $event->getUrl() . '">';
        $html[] = htmlspecialchars($event->getTitle());
        $html[] = '</a>';
        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function getRange()
    {
        $html = array();

        $dateFormat = Translation::get('DateTimeFormatLong', null, Utilities::COMMON_LIBRARIES);

        if ($this->getEvent()->getEndDate() != '')
        {
            if (date('Y m d', $this->getEvent()->getStartDate()) == date('Y m d', $this->getEvent()->getEndDate()))
            {
                $dateFormat = Translation::get('TimeNoSecFormat', null, Utilities::COMMON_LIBRARIES);
            }

            $html[] = '<div class="calendar-event-range">' . htmlentities(
                DatetimeUtilities::format_locale_date($dateFormat, $this->getEvent()->getStartDate()) . ' - ' .
                     DatetimeUtilities::format_locale_date($dateFormat, $this->getEvent()->getEndDate())) . '</div>';
        }
        else
        {
            $html[] = '<div class="calendar-event-range">' . DatetimeUtilities::format_locale_date(
                $dateFormat,
                $this->getEvent()->getStartDate()) . '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    public function getActions()
    {
        $html = array();

        $toolbar = new Toolbar(Toolbar::TYPE_HORIZONTAL);

        if ($this->getRenderer()->getDataProvider()->supportsActions())
        {
            foreach ($this->getRenderer()->getActions($this->getEvent()) as $action)
            {
                $toolbar->add_item($action);
            }
        }

        $html[] = '<div style="float: right; margin-top: 2px;">';
        $html[] = $toolbar->as_html();
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
