<?php
namespace Chamilo\Libraries\Calendar\Renderer\Type\View;

use Chamilo\Libraries\Calendar\Renderer\Event\Configuration;
use Chamilo\Libraries\Calendar\Renderer\Event\EventRendererFactory;
use Chamilo\Libraries\Calendar\Table\Type\MonthCalendar;
use Chamilo\Libraries\File\Redirect;
use Chamilo\Libraries\Translation\Translation;
use Chamilo\Libraries\Utilities\StringUtilities;

/**
 *
 * @package Chamilo\Libraries\Calendar\Renderer\Type\View
 * @author Hans De Bisschop <hans.de.bisschop@ehb.be>
 */
class MonthRenderer extends FullTableRenderer
{

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullTableRenderer::getNextDisplayTime()
     */
    public function getNextDisplayTime()
    {
        return strtotime('first day of next month', $this->getDisplayTime());
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullTableRenderer::getPreviousDisplayTime()
     */
    public function getPreviousDisplayTime()
    {
        return strtotime('first day of previous month', $this->getDisplayTime());
    }

    /**
     *
     * @return \Chamilo\Libraries\Calendar\Table\Type\MonthCalendar
     */
    public function initializeCalendar()
    {
        $displayParameters = $this->getDataProvider()->getDisplayParameters();
        $displayParameters[self::PARAM_TIME] = MonthCalendar::TIME_PLACEHOLDER;
        $displayParameters[self::PARAM_TYPE] = self::TYPE_DAY;
        $dayUrlTemplate = new Redirect($displayParameters);

        return new MonthCalendar($this->getDisplayTime(), $dayUrlTemplate->getUrl(), array('table-calendar-month'));
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullRenderer::renderFullCalendar()
     */
    public function renderFullCalendar()
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
                    $configuration = new Configuration();
                    $configuration->setStartDate($tableDate);

                    $eventRendererFactory = new EventRendererFactory($this, $event, $configuration);

                    $calendar->addEvent($tableDate, $eventRendererFactory->render());
                }
            }

            $tableDate = $nextTableDate;
        }

        return '<div class="month-calendar">' . $calendar->render() . '</div>';
    }

    /**
     *
     * @see \Chamilo\Libraries\Calendar\Renderer\Type\View\FullRenderer::renderTitle()
     */
    public function renderTitle()
    {
        return Translation::get(date('F', $this->getDisplayTime()) . 'Long', null, StringUtilities::LIBRARIES) . ' ' .
            date('Y', $this->getDisplayTime());
    }
}
