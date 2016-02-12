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
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventListRenderer extends EventRenderer
{

    /**
     * Gets a html representation of an event for a list renderer
     *
     * @return string
     */
    public function render()
    {
        $html = array();

        $html[] = '<div class="' . $this->getEventClasses() . '">';
        $html[] = '<div class="' . $this->getRenderer()->getLegend()->getSourceClasses($this->getEvent()->getSource()) .
             '">';
        $html[] = $this->getActions();
        $html[] = '<h4>';
        $html[] = $this->getTitle();
        $html[] = $this->getRange();
        $html[] = '</h4>';
        $html[] = $this->getContent();
        $html[] = $this->getLocation();
        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }

    public function getTitle()
    {
        $html = array();

        $html[] = '<a href="' . html_entity_decode($this->getEvent()->getUrl()) . '">';
        $html[] = htmlentities($this->getEvent()->getTitle());
        $html[] = '</a>';

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function getContent()
    {
        return $this->getEvent()->getContent();
    }

    /**
     *
     * @return string
     */
    public function getLocation()
    {
        $html = array();

        if ($this->getEvent()->getLocation())
        {
            $html[] = '<h4>';
            $html[] = Translation :: get('Location') . ': ' . $this->getEvent()->getLocation();
            $html[] = '</h4>';
        }

        return implode(PHP_EOL, $html);
    }

    /**
     *
     * @return string
     */
    public function getRange()
    {
        $html = array();

        $dateFormat = Translation :: get('DateTimeFormatLong', null, Utilities :: COMMON_LIBRARIES);

        if ($this->getEvent()->getEndDate() != '')
        {
            $html[] = '<div class="calendar-event-range">' . htmlentities(
                Translation :: get('From', null, Utilities :: COMMON_LIBRARIES) . ' ' .
                     DatetimeUtilities :: format_locale_date($dateFormat, $this->getEvent()->getStartDate()) . ' ' .
                     Translation :: get('Until', null, Utilities :: COMMON_LIBRARIES) . ' ' .
                     DatetimeUtilities :: format_locale_date($dateFormat, $this->getEvent()->getEndDate())) . '</div>';
        }
        else
        {
            $html[] = '<div class="calendar-event-range">' . DatetimeUtilities :: format_locale_date(
                $dateFormat,
                $this->getEvent()->getStartDate()) . '</div>';
        }

        return implode(PHP_EOL, $html);
    }

    public function getActions()
    {
        $html = array();

        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

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
