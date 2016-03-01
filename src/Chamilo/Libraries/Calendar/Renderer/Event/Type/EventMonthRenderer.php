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
class EventMonthRenderer extends EventRenderer
{

    /**
     * Gets a html representation of an event for a month renderer
     *
     * @return string
     */
    public function render()
    {
        $configuration = $this->getConfiguration();

        $startDate = $this->getEvent()->getStartDate();
        $endDate = $this->getEvent()->getEndDate();

        $fromDate = strtotime(date('Y-m-1', $this->getRenderer()->getDisplayTime()));
        $toDate = strtotime('-1 Second', strtotime('Next Month', $fromDate));

        $eventClasses = $this->getEventClasses($startDate, $fromDate, $toDate);
        $sourceClasses = $this->getRenderer()->getLegend()->getSourceClasses(
            $this->getEvent()->getSource(),
            (($startDate < $fromDate || $startDate > $toDate) ? true : false));
        $eventClasses = implode(' ', array($eventClasses, $sourceClasses));

        $html[] = '<div class="' . $eventClasses . '">';
        $html[] = '<div class="event-data">';

        if ($startDate >= $configuration->getStartDate() &&
             $startDate <= strtotime('+1 Day', $configuration->getStartDate()) &&
             $startDate != $configuration->getStartDate())
        {
            $html[] = date('H:i', $startDate);
        }
        elseif ($startDate < $configuration->getStartDate())
        {
            $html[] = '&larr;';
        }

        $html[] = '<a href="' . $this->getEvent()->getUrl() . '">';
        $html[] = htmlspecialchars($this->getEvent()->getTitle());
        $html[] = '</a>';

        if ($startDate != $endDate && $endDate < strtotime('+1 Day', $configuration->getStartDate()) &&
             $startDate < $configuration->getStartDate())
        {
            $html[] = date('H:i', $endDate);
        }
        elseif ($startDate != $endDate && $endDate > strtotime('+1 Day', $configuration->getStartDate()))
        {
            $html[] = '&rarr;';
        }

        $html[] = '</div>';

        $html[] = '</div>';

        return implode(PHP_EOL, $html);
    }
}
