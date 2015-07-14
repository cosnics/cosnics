<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Renderer\Type\TableRenderer;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Calendar\Table\Type\MiniDayCalendar;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Calendar\Renderer\Legend;
use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 * @author Magali Gillard <magali.gillard@ehb.be>
 * @author Eduard Vossen <eduard.vossen@ehb.be>
 */
class MiniDayRenderer extends TableRenderer
{

    /**
     *
     * @var int
     */
    private $hourStep;

    /**
     *
     * @var int
     */
    private $startHour;

    /**
     *
     * @var int
     */
    private $endHour;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface $dataProvider
     * @param \Chamilo\Libraries\Calendar\Renderer\Legend $legend
     * @param int $displayTime
     * @param string $linkTarget
     * @param int $hourStep
     * @param int $startHour
     * @param int $endHour
     */
    public function __construct(CalendarRendererProviderInterface $dataProvider, Legend $legend, $displayTime,
        $linkTarget = '', $hourStep = 1, $startHour = 0, $endHour = 24)
    {
        $this->hourStep = $hourStep;
        $this->startHour = $startHour;
        $this->endHour = $endHour;

        parent :: __construct($dataProvider, $legend, $displayTime, $linkTarget);
    }

    /**
     *
     * @return int
     */
    public function getHourStep()
    {
        return $this->hourStep;
    }

    /**
     *
     * @param int $hourStep
     */
    public function setHourStep($hourStep)
    {
        $this->hourStep = $hourStep;
    }

    /**
     *
     * @return int
     */
    public function getStartHour()
    {
        return $this->startHour;
    }

    /**
     *
     * @param int $startHour
     */
    public function setStartHour($startHour)
    {
        $this->startHour = $startHour;
    }

    /**
     *
     * @return int
     */
    public function getEndHour()
    {
        return $this->endHour;
    }

    /**
     *
     * @param int $endHour
     */
    public function setEndHour($endHour)
    {
        $this->endHour = $endHour;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\Type\MiniDayCalendar
     */
    public function initializeCalendar()
    {
        return new MiniDayCalendar(
            $this->getDisplayTime(),
            $this->getHourStep(),
            $this->getStartHour(),
            $this->getEndHour());
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Renderer::render()
     */
    public function render()
    {
        $calendar = $this->getCalendar();

        $fromDate = $calendar->getStartTime();
        $toDate = $calendar->getEndTime();

        $events = $this->getEvents($this, $fromDate, $toDate);

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
                     $tableDate < $endDate && $endDate < $nextTableDate ||
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
