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
     * Gets a html representation of an event for a month renderer
     *
     * @return string
     */
    public function render()
    {
        // $configuration = $this->getConfiguration();
        $startDate = $this->getEvent()->getStartDate();
        $endDate = $this->getEvent()->getEndDate();

        $eventClasses = $this->getEventClasses($startDate);
        $sourceClasses = $this->getRenderer()->getLegend()->getSourceClasses($this->getEvent()->getSource());
        $eventClasses = implode(' ', array($eventClasses, $sourceClasses));

        $html = array();

        $html[] = '<div>';

        $html[] = '<div class="list-event-item-data pull-left">';
        $html[] = '<span class="' . $eventClasses . '"></span>';

        // if ($startDate >= $configuration->getStartDate() &&
        // $startDate <= strtotime('+1 Day', $configuration->getStartDate()) &&
        // $startDate != $configuration->getStartDate())
        // {
        // $html[] = date('H:i', $startDate);
        // }
        // elseif ($startDate < $configuration->getStartDate())
        // {
        // $html[] = '&larr;';
        // }

        $html[] = '<a href="' . $this->getEvent()->getUrl() . '">';
        $html[] = htmlspecialchars($this->getEvent()->getTitle());
        $html[] = '</a>';
        $html[] = '</div>';

        $html[] = '<div class="list-event-item-time pull-right">';
        $html[] = $this->getRange();
        $html[] = '</div>';

        $html[] = '<div class="clearfix"></div>';

        $html[] = '</div>';

        // if ($startDate != $endDate && $endDate < strtotime('+1 Day', $configuration->getStartDate()) &&
        // $startDate < $configuration->getStartDate())
        // {
        // $html[] = date('H:i', $endDate);
        // }
        // elseif ($startDate != $endDate && $endDate > strtotime('+1 Day', $configuration->getStartDate()))
        // {
        // $html[] = '&rarr;';
        // }

        return implode(PHP_EOL, $html);
    }

    /**
     * Gets a html representation of an event for a list renderer
     *
     * @return string
     */
    // public function render()
    // {
    // $html = array();

    // $html[] = '<div class="row">';

    // $html[] = '<div class="col-lg-6">';
    // $html[] = $this->getTitle();
    // $html[] = '</div>';

    // $html[] = '<div class="col-lg-6">';
    // $html[] = $this->getRange();
    // $html[] = '</div>';

    // $html[] = '</div>';

    // $html[] = '<div class="' . $this->getEventClasses() . '">';
    // $html[] = '<div class="' .
    // $this->getRenderer()->getLegend()->getSourceClasses($this->getEvent()->getSource()) .
    // '">';
    // $html[] = $this->getActions();
    // $html[] = '<h4>';
    // $html[] = $this->getTitle();
    // $html[] = $this->getRange();
    // $html[] = '</h4>';
    // $html[] = $this->getContent();
    // $html[] = $this->getLocation();
    // $html[] = '</div>';
    // $html[] = '</div>';

    // return implode(PHP_EOL, $html);
    // }
    public function getTitle()
    {
        // var_dump($this->getEvent());
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
            $html[] = $this->getEvent()->getLocation();
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
            if (date('Y m d', $this->getEvent()->getStartDate()) == date('Y m d', $this->getEvent()->getEndDate()))
            {
                $dateFormat = Translation :: get('TimeNoSecFormat', null, Utilities :: COMMON_LIBRARIES);
            }

            $html[] = '<div class="calendar-event-range">' . htmlentities(
                DatetimeUtilities :: format_locale_date($dateFormat, $this->getEvent()->getStartDate()) . ' - ' .
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
