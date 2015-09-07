<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Renderer\Type\TableRenderer;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Calendar\Table\Type\DayCalendar;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class DayRenderer extends TableRenderer
{

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\Type\DayCalendar
     */
    public function initializeCalendar()
    {
        return new DayCalendar($this->getDisplayTime());
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Renderer::render()
     */
    public function render()
    {
        $calendar = $this->getCalendar();
        $fromDate = strtotime(date('Y-m-d 00:00:00', $this->getDisplayTime()));
        $toDate = strtotime(date('Y-m-d 23:59:59', $this->getDisplayTime()));

        $events = $this->getEvents($fromDate, $toDate);

        $startTime = $calendar->getStartTime();
        $endTime = $calendar->getEndTime();
        $tableDate = $startTime;

        while ($tableDate <= $endTime)
        {
            $nextTableDate = strtotime('+' . $calendar->getHourStep() . ' Hours', $tableDate);

            foreach ($events as $index => $event)
            {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                     $tableDate < $endDate && $endDate <= $nextTableDate ||
                     $startDate <= $tableDate && $nextTableDate <= $endDate)
                {
                    $configuration = new \Chamilo\Libraries\Calendar\Renderer\Event\Configuration();
                    $configuration->setStartDate($tableDate);
                    $configuration->setHourStep($calendar->getHourStep());

                    $eventRendererFactory = new EventRendererFactory($this, $event, $configuration);

                    $calendar->addEvent($tableDate, $eventRendererFactory->render());
                }
            }

            $tableDate = $nextTableDate;
        }

        $parameters = $this->getDataProvider()->getDisplayParameters();
        $parameters[self :: PARAM_TIME] = Calendar :: TIME_PLACEHOLDER;

        $redirect = new Redirect($parameters);
        $calendar->addCalendarNavigation($redirect->getUrl());

        $html = array();
        $html[] = $calendar->render();
        $html[] = $this->getLegend()->render();
        return implode(PHP_EOL, $html);
    }
}
