<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Renderer\Type\TableRenderer;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Calendar\Table\Type\WeekCalendar;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type$WeekRenderer
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class WeekRenderer extends TableRenderer
{

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\WeekCalendar
     */
    public function initializeCalendar()
    {
        return new WeekCalendar($this->getDisplayTime(), 1);
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Renderer::render()
     */
    public function render()
    {
        $calendar = $this->getCalendar();
        $fromDate = strtotime('Last Monday', strtotime('+1 Day', strtotime(date('Y-m-d', $this->getDisplayTime()))));
        $toDate = strtotime('-1 Second', strtotime('Next Week', $fromDate));

        $events = $this->getEvents($fromDate, $toDate);

        $startTime = $calendar->getStartTime();
        $endTime = $toDate;

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
