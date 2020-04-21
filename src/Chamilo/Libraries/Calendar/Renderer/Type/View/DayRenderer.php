<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Renderer\Event\Configuration;
use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;
use Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface;
use Chamilo\Libraries\Calendar\Renderer\Legend;
use Chamilo\Libraries\Calendar\Table\Type\DayCalendar;
use Chamilo\Libraries\Utilities\DatetimeUtilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class DayRenderer extends FullTableRenderer
{

    /**
     *
     * @var integer
     */
    private $hourStep;

    /**
     *
     * @var integer
     */
    private $startHour;

    /**
     *
     * @var integer
     */
    private $endHour;

    /**
     *
     * @var boolean
     */
    private $hideOtherHours;

    /**
     *
     * @param \Chamilo\Libraries\Calendar\Renderer\Interfaces\CalendarRendererProviderInterface $dataProvider
     * @param \Chamilo\Libraries\Calendar\Renderer\Legend $legend
     * @param integer $displayTime
     * @param \Chamilo\Libraries\Format\Structure\ActionBar\AbstractButtonToolBarItem[] $viewActions
     * @param string $linkTarget
     * @param integer $hourStep
     * @param integer $startHour
     * @param integer $endHour
     * @param boolean $hideOtherHours
     */
    public function __construct(CalendarRendererProviderInterface $dataProvider, Legend $legend, $displayTime,
        $viewActions = array(), $linkTarget = '', $hourStep = 1, $startHour = 0, $endHour = 24, $hideOtherHours = false)
    {
        $this->hourStep = $hourStep;
        $this->startHour = $startHour;
        $this->endHour = $endHour;
        $this->hideOtherHours = $hideOtherHours;

        parent::__construct($dataProvider, $legend, $displayTime, $viewActions, $linkTarget);
    }

    /**
     *
     * @return integer
     */
    public function getHourStep()
    {
        return $this->hourStep;
    }

    /**
     *
     * @param integer $hourStep
     */
    public function setHourStep($hourStep)
    {
        $this->hourStep = $hourStep;
    }

    /**
     *
     * @return integer
     */
    public function getStartHour()
    {
        return $this->startHour;
    }

    /**
     *
     * @param integer $startHour
     */
    public function setStartHour($startHour)
    {
        $this->startHour = $startHour;
    }

    /**
     *
     * @return integer
     */
    public function getEndHour()
    {
        return $this->endHour;
    }

    /**
     *
     * @param integer $endHour
     */
    public function setEndHour($endHour)
    {
        $this->endHour = $endHour;
    }

    /**
     *
     * @return boolean
     */
    public function getHideOtherHours()
    {
        return $this->hideOtherHours;
    }

    /**
     *
     * @param boolean $endHour
     */
    public function setHideOtherHours($hideOtherHours)
    {
        $this->hideOtherHours = $hideOtherHours;
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\Type\DayCalendar
     */
    public function initializeCalendar()
    {
        return new DayCalendar(
            $this->getDisplayTime(),
            $this->getHourStep(),
            $this->getStartHour(),
            $this->getEndHour(),
            $this->getHideOtherHours(),
            array('table-calendar-day'));
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullRenderer::renderFullCalendar()
     */
    public function renderFullCalendar()
    {
        $calendar = $this->getCalendar();

        $fromDate = $calendar->getStartTime();
        $toDate = $calendar->getEndTime();

        $events = $this->getEvents($fromDate, $toDate);

        $startTime = $calendar->getStartTime();
        $endTime = $calendar->getEndTime();
        $tableDate = $startTime;

        while ($tableDate <= $endTime)
        {
            $nextTableDate = strtotime('+' . $this->getHourStep() . ' Hours', $tableDate);

            foreach ($events as $index => $event)
            {
                $startDate = $event->getStartDate();
                $endDate = $event->getEndDate();

                if ($tableDate < $startDate && $startDate < $nextTableDate ||
                     $tableDate < $endDate && $endDate < $nextTableDate ||
                     $startDate <= $tableDate && $nextTableDate <= $endDate)
                {
                    $configuration = new Configuration();
                    $configuration->setStartDate($tableDate);
                    $configuration->setHourStep($this->getHourStep());

                    $eventRendererFactory = new EventRendererFactory($this, $event, $configuration);

                    $calendar->addEvent($tableDate, $eventRendererFactory->render());
                }
            }

            $tableDate = $nextTableDate;
        }

        return $calendar->render();
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullRenderer::renderTitle()
     */
    public function renderTitle()
    {
        return DatetimeUtilities::format_locale_date('%A %d %B %Y', $this->getDisplayTime());
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullTableRenderer::getPreviousDisplayTime()
     */
    public function getPreviousDisplayTime()
    {
        return strtotime('-1 Day', $this->getDisplayTime());
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullTableRenderer::getNextDisplayTime()
     */
    public function getNextDisplayTime()
    {
        return strtotime('+1 Day', $this->getDisplayTime());
    }
}
