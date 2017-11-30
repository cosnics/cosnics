<?php
namespace Chamilo\Libraries\Calendar\Format\Renderer\Type;

use Chamilo\Libraries\Calendar\Event\Service\HtmlTableRendererFactory;
use Chamilo\Libraries\Calendar\Format\Renderer\HtmlTableRenderer;

/**
 *
 * @package Chamilo\Libraries\Calendar\Format\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MonthRenderer extends HtmlTableRenderer
{

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Format\Renderer\Renderer::render()
     */
    public function render()
    {
        $calendar = $this->getCalendar();

        $startTime = $calendar->getStartTime();
        $endTime = $calendar->getEndTime();

        $events = $this->getEvents($startTime, $endTime);
        $tableDate = $startTime;

        while ($tableDate <= $endTime)
        {
            $nextTableDate = strtotime('+1 Day', $tableDate);

            foreach ($events as $index => $event)
            {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                     $tableDate < $endDate && $endDate <= $nextTableDate ||
                     $startDate <= $tableDate && $nextTableDate <= $endDate)
                {
                    $eventRendererFactory = new HtmlTableRendererFactory($this);

                    $calendar->addEvent($tableDate, $eventRendererFactory->render($event, $tableDate));
                }
            }

            $tableDate = $nextTableDate;
        }

        return '<div class="month-calendar">' . $calendar->render() . '</div>';
    }
}
