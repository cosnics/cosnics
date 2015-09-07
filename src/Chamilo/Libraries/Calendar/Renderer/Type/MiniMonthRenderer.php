<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type;

use Chamilo\Libraries\Calendar\Renderer\Type\TableRenderer;
use Chamilo\Libraries\Calendar\Table\Calendar;
use Chamilo\Libraries\Calendar\Table\Type\MiniMonthCalendar;
use Chamilo\Libraries\Format\Theme;
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
class MiniMonthRenderer extends TableRenderer
{

    /**
     * One of 3 possible values (or null): MiniMonthCalendar :: PERIOD_MONTH, MiniMonthCalendar :: PERIOD_WEEK,
     * MiniMonthCalendar :: PERIOD_DAY;
     *
     * @var integer
     */
    private $markPeriod;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface $dataProvider
     * @param int $displayTime
     * @param string $linkTarget
     * @param int $markPeriod
     */
    public function __construct(CalendarRendererProviderInterface $dataProvider, Legend $legend, $displayTime,
        $linkTarget = '', $markPeriod = null)
    {
        $this->markPeriod = $markPeriod;

        parent :: __construct($dataProvider, $legend, $displayTime, $linkTarget);
    }

    /**
     *
     * @return integer
     */
    public function getMarkPeriod()
    {
        return $this->markPeriod;
    }

    /**
     *
     * @param integer $markPeriod
     */
    public function setMarkPeriod($markPeriod)
    {
        $this->markPeriod = $markPeriod;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\Type\MiniMonthCalendar
     */
    public function initializeCalendar()
    {
        return new MiniMonthCalendar($this->getDisplayTime());
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Renderer::render()
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
                    if (! $calendar->containsEventsForTime($tableDate))
                    {
                        $marker = '<br /><div class="event_marker" style="width: 14px; height: 15px;"><img src="' . htmlspecialchars(
                            Theme :: getInstance()->getCommonImagePath('Action/Marker')) . '"/></div>';
                        $calendar->addEvent($tableDate, $marker);
                    }

                    $configuration = new \Chamilo\Libraries\Calendar\Renderer\Event\Configuration();
                    $configuration->setStartDate($tableDate);

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
        $calendar->addNavigationLinks($redirect->getUrl());

        if (! is_null($this->getMarkPeriod()))
        {
            $calendar->markPeriod($this->getMarkPeriod());
        }

        return $calendar->render();
    }
}
