<?php
namespace Chamilo\Libraries\Calendar\Renderer\Event\Type;

use Chamilo\Libraries\Calendar\Renderer\Event\EventRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Event\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class EventWeekRenderer extends EventRenderer
{

    /**
     * Gets a html representation of a calendar event
     *
     * @return string
     */
    public function render()
    {
        $configuration = $this->getConfiguration();

        $tableEndDate = strtotime('+' . $configuration->getHourStep() . ' hours', $configuration->getStartDate());
        $startDate = $this->getEvent()->getStartDate();
        $endDate = $this->getEvent()->getEndDate();

        $eventClasses = $this->getEventClasses($startDate);
        $sourceClasses = $this->getRenderer()->getLegend()->getSourceClasses($this->getEvent()->getSource());
        $eventClasses = implode(' ', array($eventClasses, $sourceClasses));

        $html = array();

        $html[] = '<div class="' . $eventClasses . '">';
        $html[] = '<div class="event-data">';

        if ($startDate >= $configuration->getStartDate() && $startDate <= $tableEndDate &&
             ($startDate != $configuration->getStartDate() || $endDate < $tableEndDate))
        {
            $html[] = date('H:i', $startDate);
        }
        elseif ($startDate < $configuration->getStartDate())
        {
            $html[] = '&uarr;';
        }

        $html[] = '<a href="' . $this->getEvent()->getUrl() . '">';
        $html[] = htmlspecialchars($this->getEvent()->getTitle());
        $html[] = '</a>';

        if ($startDate != $endDate)
        {
            if ($endDate < $tableEndDate && $startDate < $configuration->getStartDate())
            {
                $html[] = date('H:i', $endDate);
            }
            elseif ($endDate > $tableEndDate)
            {
                $html[] = '&darr;';
            }
        }

        $html[] = '</div>';
        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
